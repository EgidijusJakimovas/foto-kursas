<?php
	require_once 'WebToPay.php';

	require_once 'payment-header.php';

	require_once '../const.php';

	try {
		// Database info
		$host 		= DB_HOST;
		$user 		= DB_USERNAME;
		$pass 		= DB_PASSWORD;
		$database 	= DB_NAME;
		
		// Database table info
		$table 			= DB_TABLE_PAYMENTS;
		$uzsakymasDB	= DB_TABLE_PAYMENTS_COLUMN_UZSAKYMAS;
		$tevo1DB 		= DB_TABLE_PAYMENTS_COLUMN_TEVO_1;
		$apmokejimasDB 	= DB_TABLE_PAYMENTS_COLUMN_APMOKEJIMAS;
		$sumaDB 		= DB_TABLE_PAYMENTS_COLUMN_SUMA;

		//Custom PDO options.
		$options = array(
			PDO::ATTR_ERRMODE 			=> PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_EMULATE_PREPARES 	=> false
		);
		
		//Connect to MySQL and instantiate our PDO object.
		$pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);
		
		//Create our INSERT SQL query.
		$sql = "INSERT INTO $table (`$uzsakymasDB`, `$tevo1DB`, `$apmokejimasDB`, `$sumaDB`) VALUES (:order_id, :tevo1, :paid, :money)";
		
		//Prepare our statement.
		$statement = $pdo->prepare($sql);
		
		//GET MAX ID FOR MAKING ORDER ID
		$pdo2 = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $user, $pass, $options);
		$data2 = $pdo2->prepare("SELECT MAX(id) as id FROM $table LIMIT 1;");
		$data2->execute();
		$row2 = $data2->fetch();
		
		// Make sure only 1 result is returned
		if($data2->rowCount() == 1){
			$id_from_db = $row2['id'] + 1; // because allways returns id - 1
			$order_id_from_db = '0000';
			$order_id_from_db .= strval($id_from_db);
		}

		//Bind user's entered values in form to our arguments
		$order_id 		= $order_id_from_db;
		$tevo1 			= $_POST['tevo1'];
		$paid 			= 0; 					// because user has not paid yet, he will pay only on callback.php
		
		// Against SQL injections
		$statement->bindValue(':order_id', 		$order_id);
		$statement->bindValue(':tevo1', 		$tevo1);
		$statement->bindValue(':paid', 			$paid);
		$statement->bindValue(':money', 		$money);

		//Execute the statement and insert our values.
		$inserted = $statement->execute();

		//Because PDOStatement::execute returns a TRUE or FALSE value,
		//we can easily check to see if our insert was successful.
		//if($inserted){
			//echo 'Row inserted!<br>';
		//}

		// PAYSERA PAYMENT
		$self_url = 'url adresas';

		WebToPay::redirectToPayment([
			'projectid' 			=> PAYSERA_PROJECT_ID,
			'sign_password' 		=> PAYSERA_PASSWORD,
			'orderid' 				=> $order_id,
			'amount' 				=> $money * 100, // returning cents (for paysera) from euros (from DB)
			'currency' 				=> PAYSERA_CURRENCY,
			'country' 				=> 'LT',
			'p_email'				=> $email,
			'p_firstname'			=> $tevo1,
			'p_lastname'			=> $tevo2,
			'accepturl' 			=> $self_url.'/accept.php',
			'cancelurl' 			=> $self_url.'/cancel.php',
			'callbackurl' 			=> $self_url.'/callback.php',
			'test' 					=> 0,
		]);
	} catch (Exception $exception) {
		echo "sql exception on 'go.php' file. Enable error-reporting for more info.";
		//TODO: HIDE IT IN PRODUCTION
		echo get_class($exception) . ':' . $exception->getMessage();
	}

?>
