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
    $orderID        = DB_TABLE_ORDERS_COLUMN_ID;
    $name           = DB_TABLE_ORDERS_COLUMN_NAME;
    $surname        = DB_TABLE_ORDERS_COLUMN_SURNAME;
    $email          = DB_TABLE_ORDERS_COLUMN_EMAIL;
    $phone          = DB_TABLE_ORDERS_COLUMN_PHONE;
    $paymentStatus  = DB_TABLE_ORDERS_COLUMN_PAYMENT_STATUS;
    $paidSum        = DB_TABLE_ORDERS_COLUMN_PAYMENT_SUM;
    $data           = DB_TABLE_ORDERS_COLUMN_DATA;
    $userID        = DB_TABLE_ORDERS_COLUMN_USER_ID;


    // Other info
    $money          = COURSE_PRICE;

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
    
    // GET MAX ID FOR MAKING ORDER ID
    $data2 = $pdo->prepare("SELECT MAX(id) as id FROM $table LIMIT 1;");
    $data2->execute();
    $row2 = $data2->fetch();
    
    // Make sure only 1 result is returned
    if ($data2->rowCount() == 1) {
        $id_from_db = $row2['id'] + 1; // because always returns id - 1
        $order_id_from_db = '0000' . strval($id_from_db);
    }

    // Close the statement used to get max ID
    $data2 = null;

    // Create our INSERT SQL query.
    $sql = "INSERT INTO $table ($orderID, $name, $surname, $email, $phone, $paymentStatus, $paidSum, $data, $userID) VALUES (:id, :name, :surname, :email, :phone, :payment_status, :paid_sum, :data, :user_id)";
    
    // Prepare our statement.
    $statement = $pdo->prepare($sql);
    
    // Bind user's entered values in form to our arguments
    $orderID        = $order_id_from_db;
    $name           = $_POST['name'];
    $surname        = $_POST['surname'];
    $email          = $_POST['email'];
    $phone          = $_POST['phone'];
    $paymentStatus  = 0; // because user has not paid yet, he will pay only on callback.php
    $paidSum        = COURSE_PRICE / 100; // becouse paysera is counting in cents, but we have double in DB
    $userID         = $_POST['user_id'];

    // Against SQL injections
    $statement->bindValue(':id',            $orderID);
    $statement->bindValue(':name',          $name);
    $statement->bindValue(':surname',       $surname);
    $statement->bindValue(':email',         $email);
    $statement->bindValue(':phone',         $phone);
    $statement->bindValue(':payment_status',0);  // Initial value, since the payment is not done yet
    $statement->bindValue(':paid_sum',      $paidSum);
    $statement->bindValue(':user_id',       $userID);
    
    // Adjust timestamp manually if necessary
    $timestamp = date("Y-m-d H:i:s");
    $statement->bindValue(':data', $timestamp);

    // Execute the statement and insert our values.
    $inserted = $statement->execute();

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
        'orderid'       => $orderID,
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
    //TODO: HIDE IT IN PRODUCTION
    echo get_class($exception) . ':' . $exception->getMessage();
    error_reporting(1);
    ini_set('display_errors', 1);
}

?>
