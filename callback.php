<?php
// FOR PAYSERA
require_once('WebToPay.php');

// FOR CREDENTIALS
require_once 'const.php';

try {
    // TURN OFF DISPLAYING ERROR MESSAGES 
    error_reporting(0);
    ini_set('display_errors', 0);

    // Validate and parse the payment response
    $response = WebToPay::validateAndParseData(
        $_REQUEST,
        PAYSERA_PROJECT_ID,
        PAYSERA_PASSWORD
    );

    // Extract response data
    $response_order_id  = $response['orderid'];
    $amount             = $response['amount'];
    $currency           = $response['currency'];

    // Database information
    $host       = DB_HOST;
    $user       = DB_USERNAME;
    $pass       = DB_PASSWORD;
    $database   = DB_NAME;

    // Database 'payments' table info
    $table          = DB_TABLE_ORDERS;
    $orderID        = DB_TABLE_ORDERS_COLUMN_ID;
    $name           = DB_TABLE_ORDERS_COLUMN_NAME;
    $surname        = DB_TABLE_ORDERS_COLUMN_SURNAME;
    $email          = DB_TABLE_ORDERS_COLUMN_EMAIL;
    $phone          = DB_TABLE_ORDERS_COLUMN_PHONE;
    $paymentStatus  = DB_TABLE_ORDERS_COLUMN_PAYMENT_STATUS;
    $paidSum        = DB_TABLE_ORDERS_COLUMN_PAYMENT_SUM;
    $data           = DB_TABLE_ORDERS_COLUMN_TIMESTAMP;

    // Custom PDO options.
    $options = array(
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES  => false
    );

    // Connect to MySQL and instantiate our PDO object.
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);

    // Retrieve the order from the database
    $data = $pdo->prepare("SELECT * FROM $table WHERE $orderID = :response_order_id LIMIT 1;");
    $data->bindParam(":response_order_id", $response_order_id);
    $data->execute();
    $row = $data->fetch();

    // Make sure only 1 result is returned
    if ($data->rowCount() == 1) {
        // Get values
        $id        = $row[$orderID];
        $email     = $row[$email];
        $price     = $row[$paidSum];
        $paid      = $row[$paymentStatus];
    } else {
        throw new Exception('Order not found');
    }

    // Verify payment status and update the database if the payment is successful
    if ($response['status'] === '1' || $response['status'] === '3') {
        // Verify the amount and currency
        if ($price * 100 != $amount) {
            throw new Exception('Wrong payment amount');
        }
        if (PAYSERA_CURRENCY != $currency) {
            throw new Exception('Wrong payment currency');
        }
        if ($id != $response_order_id) {
            throw new Exception('Wrong payment id');
        }

        echo 'OK';

        // Update the payment status if it was not already updated
        if ($paid == 0) {
            // Update the payment status in the database
            $update = $pdo->prepare("UPDATE $table SET $paymentStatus = 1 WHERE $orderID = :response_order_id;");
            $update->bindParam(':response_order_id', $response_order_id);
            $update->execute();
            
            // Check if the update was successful
            if ($update->rowCount() > 0) {
                echo "Payment status updated successfully.";
            } else {
                echo "Payment status update failed.";
            }
        }
    } else {
        throw new Exception('Payment was not successful');
    }
} catch (Exception $exception) {
    echo "SQL exception on 'callback.php' file. Enable error-reporting for more info.";
    // TODO: comment in production
    echo get_class($exception) . ':' . $exception->getMessage();
}
?>
