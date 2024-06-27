<?php
require_once 'WebToPay.php';
require_once 'const.php';

try {
    // Set PHP timezone to match your local timezone
    date_default_timezone_set('Europe/Vilnius'); // Adjust to your local timezone

    // Database info

    //TODO: ta pati padaryti kaip 29 eiluteje
    $host       = 'b8rg15mwxwynuk9q.chr7pe7iynqr.eu-west-1.rds.amazonaws.com';
    $user       = 'vo3l7cqkori4bdkn';
    $pass       = DB_PASSWORD;
    $database   = 'n9teib9it8m8u2z3';
    
    // Database table info
    $table          = 'orders';
    $orderID        = 'id';
    $name           = 'name';
    $surname        = 'surname';
    $email          = 'email';
    $phone          = 'phone';
    $paymentStatus  = 'payment_status';
    $paidSum        = 'paid_sum';
    $data           = 'data';

    // Other info
    $money          = COURSE_MONEY;

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

    // Bind user's entered values in form to our arguments
    $orderID        = $order_id_from_db;
    $name           = $_POST['name'];
    $surname        = $_POST['surname'];
    $email          = $_POST['email'];
    $phone          = $_POST['phone'];
    $paymentStatus  = 0; // because user has not paid yet, he will pay only on callback.php
    $paidSum        = $_POST['paid_sum'];
    
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
        'projectid'     => 244570,
        'sign_password' => '7ada0f6b4ace81a594c33bc2545246f7',
        'orderid'       => $orderID,
        // 'amount'       => $paidSum * 100, // returning cents (for paysera) from euros (from DB)
        'amount'       => COURSE_MONEY,
        'currency'      => 'EUR',
        'country'       => 'LT',
        'p_firstname'   => $name,
        'p_lastname'    => $surname,
        'p_email'       => $email,
        'p_phone'       => $phone,
        'accepturl'     => getSelfUrl() . '/accept.php',
        'cancelurl'     => getSelfUrl() . '/cancel.php',
        'callbackurl'   => getSelfUrl() . '/callback.php',
        'test'          => 0,
    ]);
} catch (Exception $exception) {
    echo "SQL exception on 'go.php' file. Enable error-reporting for more info.";
    //TODO: HIDE IT IN PRODUCTION
    echo get_class($exception) . ':' . $exception->getMessage();
}
?>
