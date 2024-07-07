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

    // Other info
    $money          = COURSE_PRICE;

    // Custom PDO options.
    $options = array(
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES  => false
    );
    
    // Connect to MySQL and instantiate our PDO object.
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);
    
    // Set MySQL session time zone
    $pdo->exec("SET time_zone = '+03:00';"); // Adjust to your MySQL server's time zone
    
    // Create our INSERT SQL query.
    $sql = "INSERT INTO $table ($orderID, $name, $surname, $email, $phone, $paymentStatus, $paidSum, $data) VALUES (:id, :name, :surname, :email, :phone, :payment_status, :paid_sum, :data)";
    
    // Prepare our statement.
    $statement = $pdo->prepare($sql);
    
    // GET MAX ID FOR MAKING ORDER ID
    $pdo2 = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);
    $data2 = $pdo2->prepare("SELECT MAX(id) as id FROM $table LIMIT 1;");
    $data2->execute();
    $row2 = $data2->fetch();
    
    // Make sure only 1 result is returned
    if($data2->rowCount() == 1){
        $id_from_db = $row2['id'] + 1; // because always returns id - 1
        $order_id_from_db = '0000' . strval($id_from_db);
    }

    // Close the statement used to get max ID
    $data2 = null;

    // Bind user's entered values in form to our arguments
    $orderID        = $order_id_from_db;
    $name           = $_POST['name'];
    $surname        = $_POST['surname'];
    $email          = $_POST['email'];
    $phone          = $_POST['phone'];
    $paymentStatus  = 0; // because user has not paid yet, he will pay only on callback.php
    $paidSum        = COURSE_PRICE / 100; // becouse paysera is counting in cents, but we have double in DB
    
    // Against SQL injections
    $statement->bindValue(':id',            $orderID);
    $statement->bindValue(':name',          $name);
    $statement->bindValue(':surname',       $surname);
    $statement->bindValue(':email',         $email);
    $statement->bindValue(':phone',         $phone);
    $statement->bindValue(':payment_status',0);  // Initial value, since the payment is not done yet
    $statement->bindValue(':paid_sum',      $paidSum);
    
    // Adjust timestamp manually if necessary
    $timestamp = date("Y-m-d H:i:s", strtotime("+3 hours"));
    $statement->bindValue(':data', $timestamp);

    // Execute the statement and insert our values.
    $inserted = $statement->execute();

    // Close the statement and connection
    $statement = null;
    $pdo = null;

    // Because PDOStatement::execute returns a TRUE or FALSE value,
    // we can easily check to see if our insert was successful.
    // if($inserted){
    // echo 'Row inserted!<br>';
    // }

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
}
?>
