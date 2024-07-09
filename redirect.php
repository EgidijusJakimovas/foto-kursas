<?php
require_once('WebToPay.php');
require_once 'const.php';

try {
    // Set PHP timezone
    date_default_timezone_set('Europe/Vilnius');

    // Database info
    $host = DB_HOST;
    $user = DB_USERNAME;
    $pass = DB_PASSWORD;
    $database = DB_NAME;
    
    // Database table info
    $table = DB_TABLE_ORDERS;
    $orderID = DB_TABLE_ORDERS_COLUMN_ID;
    $name = DB_TABLE_ORDERS_COLUMN_NAME;
    $surname = DB_TABLE_ORDERS_COLUMN_SURNAME;
    $email = DB_TABLE_ORDERS_COLUMN_EMAIL;
    $phone = DB_TABLE_ORDERS_COLUMN_PHONE;
    $paymentStatus = DB_TABLE_ORDERS_COLUMN_PAYMENT_STATUS;
    $paidSum = DB_TABLE_ORDERS_COLUMN_PAYMENT_SUM;
    $data = DB_TABLE_ORDERS_COLUMN_DATA;

    // Course price in cents
    $money = COURSE_PRICE;

    // Custom PDO options
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    );

    // Connect to MySQL and instantiate our PDO object
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);

    // Set MySQL session time zone
    $pdo->exec("SET time_zone = '+03:00';");

    // Create our INSERT SQL query
    $sql = "INSERT INTO $table ($orderID, $name, $surname, $email, $phone, $paymentStatus, $paidSum, $data) VALUES (:id, :name, :surname, :email, :phone, :payment_status, :paid_sum, :data)";

    // Prepare our statement
    $statement = $pdo->prepare($sql);

    // GET MAX ID FOR MAKING ORDER ID
    $pdo2 = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);
    $data2 = $pdo2->prepare("SELECT MAX(id) as id FROM $table LIMIT 1;");
    $data2->execute();
    $row2 = $data2->fetch();

    // Make sure only 1 result is returned
    if ($data2->rowCount() == 1) {
        $id_from_db = $row2['id'] + 1;
        $order_id_from_db = '0000' . strval($id_from_db);
    }

    // Close the statement used to get max ID
    $data2 = null;

    // Bind user's entered values in form to our arguments
    $orderID = $order_id_from_db;
    $name = 'N/A'; // Example static data, replace with actual form data if available
    $surname = 'N/A'; // Example static data, replace with actual form data if available
    $email = $_POST['email'];
    $phone = 'N/A'; // Example static data, replace with actual form data if available
    $paymentStatus = 0;
    $paidSum = $money / 100;

    // Bind values to prevent SQL injections
    $statement->bindValue(':id', $orderID);
    $statement->bindValue(':name', $name);
    $statement->bindValue(':surname', $surname);
    $statement->bindValue(':email', $email);
    $statement->bindValue(':phone', $phone);
    $statement->bindValue(':payment_status', 0);
    $statement->bindValue(':paid_sum', $paidSum);

    // Adjust timestamp manually if necessary
    $timestamp = date("Y-m-d H:i:s", strtotime("+3 hours"));
    $statement->bindValue(':data', $timestamp);

    // Execute the statement and insert our values
    $inserted = $statement->execute();

    // Close the statement and connection
    $statement = null;
    $pdo = null;

    // Function to get the base URL
    function getSelfUrl(): string {
        return 'https://foto-kursas-930ec9144443.herokuapp.com';
    }

    // Redirect to Paysera payment
    WebToPay::redirectToPayment([
        'projectid' => PAYSERA_PROJECT_ID,
        'sign_password' => PAYSERA_PASSWORD,
        'orderid' => $orderID,
        'amount' => COURSE_PRICE,
        'currency' => 'EUR',
        'country' => 'LT',
        'p_email' => $email,
        'accepturl' => getSelfUrl() . '/accept.php',
        'cancelurl' => getSelfUrl() . '/cancel.php',
        'callbackurl' => getSelfUrl() . '/callback.php',
        'test' => 0,
    ]);
} catch (Exception $exception) {
    echo "SQL exception on 'redirect.php' file. Enable error-reporting for more info.";
    echo get_class($exception) . ':' . $exception->getMessage();
}
?>
