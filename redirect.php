<?php

// Include Paysersa library
require_once('WebToPay.php');

// Include credentials
require_once 'const.php';

try {
    // Set PHP timezone to match your local timezone
    date_default_timezone_set('Europe/Vilnius'); // Adjust to your local timezone

    // Database connection parameters
    $host = DB_HOST;
    $user = DB_USERNAME;
    $pass = DB_PASSWORD;
    $database = DB_NAME;

    // Table and column names
    $table = DB_TABLE_ORDERS;
    $orderID = DB_TABLE_ORDERS_COLUMN_ID;
    $name = DB_TABLE_ORDERS_COLUMN_NAME;
    $surname = DB_TABLE_ORDERS_COLUMN_SURNAME;
    $email = DB_TABLE_ORDERS_COLUMN_EMAIL;
    $phone = DB_TABLE_ORDERS_COLUMN_PHONE;
    $paymentStatus = DB_TABLE_ORDERS_COLUMN_PAYMENT_STATUS;
    $paidSum = DB_TABLE_ORDERS_COLUMN_PAYMENT_SUM;
    $data = DB_TABLE_ORDERS_COLUMN_DATA;

    // Other variables
    $money = COURSE_PRICE;

    // Custom PDO options
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    // Connect to MySQL using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);

    // Set MySQL session time zone
    $pdo->exec("SET time_zone = '+03:00';"); // Adjust to your MySQL server's time zone

    // Prepare INSERT SQL statement
    $sql = "INSERT INTO $table ($orderID, $name, $surname, $email, $phone, $paymentStatus, $paidSum, $data) VALUES (:id, :name, :surname, :email, :phone, :payment_status, :paid_sum, :data)";

    // Prepare statement
    $statement = $pdo->prepare($sql);

    // Get max ID for order ID
    $pdo2 = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);
    $data2 = $pdo2->prepare("SELECT MAX(id) as id FROM $table LIMIT 1;");
    $data2->execute();
    $row2 = $data2->fetch();

    // Ensure only 1 result is returned
    if ($data2->rowCount() == 1) {
        $id_from_db = $row2['id'] + 1; // Increment max ID
        $order_id_from_db = '0000' . strval($id_from_db);
    }

    // Close the statement used to get max ID
    $data2 = null;

    // Bind user-entered form data to variables
    $orderID = $order_id_from_db;
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $paymentStatus = 0; // Initial payment status, user will pay on callback.php
    $paidSum = COURSE_PRICE / 100; // Convert from cents to euros

    // Bind values to statement
    $statement->bindValue(':id', $orderID);
    $statement->bindValue(':name', $name);
    $statement->bindValue(':surname', $surname);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':phone', $phone);
    $statement->bindValue(':payment_status', $paymentStatus);
    $statement->bindValue(':paid_sum', $paidSum);

    // Adjust timestamp if necessary
    $timestamp = date("Y-m-d H:i:s", strtotime("+3 hours"));
    $statement->bindValue(':data', $timestamp);

    // Execute the statement to insert data
    $inserted = $statement->execute();

    // Close statement and connection
    $statement = null;
    $pdo = null;

    // Check if insert was successful
    if ($inserted) {
        // Redirect to Paysera payment page
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
    } else {
        echo "Failed to insert order into database.";
    }
} catch (PDOException $exception) {
    echo "PDO exception on 'redirect.php' file. Enable error-reporting for more info.";
    // TODO: Hide errors in production
    echo get_class($exception) . ':' . $exception->getMessage();
} catch (Exception $exception) {
    echo "General exception on 'redirect.php' file. Enable error-reporting for more info.";
    // TODO: Hide errors in production
    echo get_class($exception) . ':' . $exception->getMessage();
}
?>
