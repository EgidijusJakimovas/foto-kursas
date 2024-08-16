<?php
// FOR PAYSERA
require_once('WebToPay.php');

// FOR CREDENTIALS
require_once 'const.php';

try {
    // Set PHP timezone to match your local timezone
    date_default_timezone_set('Europe/Vilnius'); // Adjust to your local timezone

    // Database info
    $host       = DB_HOST;
    $user       = DB_USERNAME;
    $pass       = DB_PASSWORD;
    $database   = DB_NAME;

    // Database table info
    $table          = DB_TABLE_ORDERS;
    $orderIDColumn  = DB_TABLE_ORDERS_COLUMN_ID; // Column name
    $nameColumn     = DB_TABLE_ORDERS_COLUMN_NAME; // Column name
    $surnameColumn  = DB_TABLE_ORDERS_COLUMN_SURNAME; // Column name
    $emailColumn    = DB_TABLE_ORDERS_COLUMN_EMAIL; // Column name
    $phoneColumn    = DB_TABLE_ORDERS_COLUMN_PHONE; // Column name
    $paymentStatusColumn = DB_TABLE_ORDERS_COLUMN_PAYMENT_STATUS; // Column name
    $paidSumColumn  = DB_TABLE_ORDERS_COLUMN_PAYMENT_SUM; // Column name
    $dataColumn     = DB_TABLE_ORDERS_COLUMN_DATA; // Column name
    $userIDColumn   = DB_TABLE_ORDERS_COLUMN_USER_ID; // Column name

    // Custom PDO options.
    $options = array(
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES  => false
    );
    
    // Connect to MySQL and instantiate our PDO object.
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);
    
    // Kill existing connections
    $stmt = $pdo->query("SHOW PROCESSLIST");
    $connections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($connections as $connection) {
        if ($connection['User'] == $user && $connection['Command'] != 'Sleep' && $connection['Id'] != $pdo->query("SELECT CONNECTION_ID()")->fetchColumn()) {
            $pdo->exec("KILL {$connection['Id']}");
        }
    }

    // Fetch the user_id based on the email address
    $email = $_POST['email'];
    $userQuery = $pdo->prepare("SELECT `id` FROM `users` WHERE `email` = :email LIMIT 1");
    $userQuery->bindValue(':email', $email, PDO::PARAM_STR);
    $userQuery->execute();

    $user = $userQuery->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found with email: " . $email);
    }

    // Get the user_id
    $userID = $user['id'];

    // Create our INSERT SQL query.
    $sql = "INSERT INTO `$table` (`$nameColumn`, `$surnameColumn`, `$emailColumn`, `$phoneColumn`, `$paymentStatusColumn`, `$paidSumColumn`, `$dataColumn`, `$userIDColumn`) 
            VALUES (:name, :surname, :email, :phone, :payment_status, :paid_sum, :data, :user_id)";
    
    // Prepare our statement.
    $statement = $pdo->prepare($sql);
    
    // Bind user's entered values in form to our arguments
    $name           = $_POST['name'];
    $surname        = $_POST['surname'];
    $phone          = $_POST['phone'];
    $paymentStatus  = 0; // because user has not paid yet, he will pay only on callback.php
    $paidSum        = COURSE_PRICE / 100; // because Paysera counts in cents, but we have double in DB

    // Bind values to the statement
    $statement->bindValue(':name',          $name);
    $statement->bindValue(':surname',       $surname);
    $statement->bindValue(':email',         $email);
    $statement->bindValue(':phone',         $phone);
    $statement->bindValue(':payment_status',$paymentStatus);
    $statement->bindValue(':paid_sum',      $paidSum);
    $statement->bindValue(':user_id',       $userID);
    
    // Adjust timestamp manually if necessary
    $timestamp = date("Y-m-d H:i:s");
    $statement->bindValue(':data', $timestamp);

    // Execute the statement and insert our values.
    $inserted = $statement->execute();
    
    // Get the last inserted ID
    $order_id_from_db = $pdo->lastInsertId();

    // Close the statement and connection
    $statement = null;
    $pdo = null;

    // PAYSERA PAYMENT
    function getSelfUrl(): string {
        return 'https://foto-kursas-930ec9144443.herokuapp.com';
    }

    WebToPay::redirectToPayment([
        'projectid'     => PAYSERA_PROJECT_ID,
        'sign_password' => PAYSERA_PASSWORD,
        'orderid'       => $order_id_from_db,
        'amount'        => COURSE_PRICE,
        'currency'      => 'EUR',
        'country'       => 'LT',
        'p_email'       => $email,
        'accepturl'     => getSelfUrl() . '/accept.php',
        'cancelurl'     => getSelfUrl() . '/cancel.php',
        'callbackurl'   => getSelfUrl() . '/callback.php',
        'test'          => 0,
    ]);
} catch (Exception $exception) {
    echo "SQL exception on 'redirect.php' file. Enable error-reporting for more info.";
    echo get_class($exception) . ':' . $exception->getMessage();
    error_reporting(1);
    ini_set('display_errors', 1);
}

?>
