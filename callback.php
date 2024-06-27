<?php
	// FOR PAYSERA
	require_once('WebToPay.php');

	// FOR CREDENTIALS
	require_once 'const.php';

	try {
		// TURN OFF DISPLAYING ERROR MESSAGES 
		error_reporting(0);
		ini_set('display_errors', 0);
		
		$response = WebToPay::validateAndParseData(
			$_REQUEST,
			PAYSERA_PROJECT_ID,
			PAYSERA_PASSWORD
		);
		
		$response_order_id 	= $response['orderid'];
		$amount 			= $response['amount'];
		$currency 			= $response['currency'];
		
		// Database information
		$host 		= DB_HOST;
		$user 		= DB_USERNAME;
		$pass 		= DB_PASSWORD;
		$database 	= DB_NAME;
		
		// Database 'payments' table info
		$table          = DB_TABLE_ORDERS;
        $orderID        = DB_TABLE_ORDERS_COLUMN_ID;
        $paymentStatus  = DB_TABLE_ORDERS_COLUMN_PAYMENT_STATUS;
        $paidSum        = DB_TABLE_ORDERS_COLUMN_PAYMENT_SUM;
		
		//Custom PDO options.
		$options = array(
			PDO::ATTR_ERRMODE 			=> PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_EMULATE_PREPARES 	=> false
		);
		
		//Connect to MySQL and instantiate our PDO object.
		$pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);
		$data = $pdo->prepare("SELECT * FROM $table WHERE $orderID = (:response_order_id) LIMIT 1;");
		$data->bindParam(":response_order_id", $response_order_id);
		$data->execute();
		$row = $data->fetch();

		// Make sure only 1 result is returned
		if($data->rowCount() == 1){
			// Get values
			$order_id 		= $row["$orderID"];
			$price 			= $row["$paidSum"];
		} 
		
		if ($response['status'] === '1' || $response['status'] === '3') {
			
			// multiple by 100, because $money comes from DB (where it is stored in Eur) and $amount comes from paysera (where it is cents)
			if ($price * 100		!= $amount) {
				throw new Exception('Wrong payment amount');
			}
		
			if (PAYSERA_CURRENCY 	!= $currency) {
				throw new Exception('Wrong payment amount');
			}
			
			if ($order_id 			!= $response_order_id) {
				throw new Exception('Wrong payment id');
			}

			echo 'OK';

			// Order wasn't paid yet (if $paid == 0)- so updatind DB. 
			// Otherwise (if $paid == 1)- doing nothing (saving resources and not spaming), 
			// because this script tends to run twice (for some reason that only paysera knows).
			if ($paid == 0) {
				// *********************************************
				// 		DATABASE UPDATE WITH PAID INFO
				// *********************************************
				
				//Connect to MySQL and instantiate our PDO object.
				$pdo2 = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);
				$data2 = $pdo2->prepare("UPDATE $table SET $paymentStatus = ? WHERE $orderID = ?;");
				$paid2 = 1;
				$data2->bindParam(1, $paid2, PDO::PARAM_INT); // everything OK, so user paid
				$data2->bindParam(2, $response_order_id);
				$data2->execute();
			}
			
		} else {
			throw new Exception('Payment was not successful');
		}
	} catch (Exception $exception) {
		echo "sql exception on 'callback.php' file. Enable error-reporting for more info.";
		// TODO: comment in production
		// echo get_class($exception) . ':' . $exception->getMessage();
	}
?>
