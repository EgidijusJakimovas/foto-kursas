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

<!DOCTYPE html>
<html lang="">
  <head>
    <title>Apmokėjimas - Fotokursas.lt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="verify-paysera" content="67286da624b639b8633b9cb2630a4cd1" />
    <link rel="stylesheet" id="css" href="styles.css" type="text/css" media="all" />
    <style>
      body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        background-color: aliceblue;
        margin: 0;
        padding: 20px;
      }
      .container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
      }
      .user-details {
        flex: 1 1 400px;
        margin-right: 20px;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }
      .order-details {
        flex: 1 1 calc(50% - 40px);
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      }
      h2 {
        text-align: center;
        color: #333333;
        margin-top: 0;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
      }
      table th,
      table td {
        border: 1px solid #dddddd;
        padding: 12px;
        text-align: left;
      }
      table th {
        background-color: #f2f2f2;
      }
      .total-row {
        font-weight: bold;
      }
      a {
        color: #fc9002;
        text-decoration: none;
      }
      form {
        display: flex;
        flex-direction: column;
      }
      label {
        margin-bottom: 10px;
      }
      input[type="text"],
      input[type="email"],
      input[type="tel"],
      input[type="submit"] {
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #cccccc;
        box-sizing: border-box;
      }
      input[type="submit"] {
        background-color: #292929;
        color: #ffffff;
        cursor: pointer;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="user-details">
        <h2>Užsakymo Informacija</h2>
        <form method="post" action="redirect.php">
          <label for="name">Vardas:</label>
          <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($name); ?>">
          <label for="surname">Pavardė:</label>
          <input type="text" id="surname" name="surname" required value="<?php echo htmlspecialchars($surname); ?>">
          <label for="email">El. paštas:</label>
          <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
          <label for="phone">Telefono numeris:</label>
          <input type="tel" id="phone" name="phone" required value="<?php echo htmlspecialchars($phone); ?>">
          <input type="submit" value="Pateikti užsakymą">
        </form>
      </div>
    </div>
  </body>
</html>
