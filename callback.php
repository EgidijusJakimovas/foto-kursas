<?php
	// FOR PAYSERA
	require_once('WebToPay.php');
	require_once 'const.php';

	try {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		
		$response = WebToPay::validateAndParseData(
			$_REQUEST,
			PAYSERA_PROJECT_ID,
			PAYSERA_PASSWORD
		);
		
		$response_order_id = $response['orderid'];
		$amount = $response['amount'];
		$currency = $response['currency'];

		$host = DB_HOST;
		$user = DB_USERNAME;
		$pass = DB_PASSWORD;
		$database = DB_NAME;
		
		$table = DB_TABLE_ORDERS;
        $orderID = DB_TABLE_ORDERS_COLUMN_ID;
        $paymentStatus = DB_TABLE_ORDERS_COLUMN_PAYMENT_STATUS;
        $paidSum = DB_TABLE_ORDERS_COLUMN_PAYMENT_SUM;

		$options = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_EMULATE_PREPARES => false
		);
		
		$pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);
		$data = $pdo->prepare("SELECT * FROM $table WHERE $orderID = (:response_order_id) LIMIT 1;");
		$data->bindParam(":response_order_id", $response_order_id);
		$data->execute();
		$row = $data->fetch();

		if($data->rowCount() == 1) {
			$order_id = $row["$orderID"];
			$price = $row["$paidSum"];
			$paid = $row["$paymentStatus"]; // Fetch current payment status

			echo "Response Order ID: $response_order_id<br>";
			echo "Price from DB: $price<br>";
			echo "Order ID from DB: $order_id<br>";
		} else {
			throw new Exception("Order not found in the database");
		}

		$pdo = null;
		
		if ($response['status'] === '1' || $response['status'] === '3') {
			if ($price * 100 != $amount) {
				throw new Exception('Wrong payment amount');
			}
		
			if (PAYSERA_CURRENCY != $currency) {
				throw new Exception('Wrong payment currency');
			}
			
			if ($order_id != $response_order_id) {
				throw new Exception('Wrong payment id');
			}

			echo 'OK';

			if ($paid == 0) {
				echo "Updating payment status for Order ID: $response_order_id<br>";
				
				$pdo2 = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);
				$data2 = $pdo2->prepare("UPDATE $table SET $paymentStatus = ? WHERE $orderID = ?;");
				$paid2 = 1;
				$data2->bindParam(1, $paid2, PDO::PARAM_INT);
				$data2->bindParam(2, $response_order_id);
				$data2->execute();

				$pdo2 = null;
			}
			
		} else {
			throw new Exception('Payment was not successful');
		}
	} catch (Exception $exception) {
		echo "SQL exception: " . $exception->getMessage();
	}
?>
