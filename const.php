<?php
    // paysera connection information
    define('PAYSERA_PROJECT_ID',	'244570');
    define('PAYSERA_PASSWORD',		'7ada0f6b4ace81a594c33bc2545246f7');
    define('PAYSERA_CURRENCY',		'EUR');

    // database connection information
    define('DB_NAME',		'n9teib9it8m8u2z3');
    define('DB_HOST',		'b8rg15mwxwynuk9q.chr7pe7iynqr.eu-west-1.rds.amazonaws.com');
    define('DB_USERNAME',	'vo3l7cqkori4bdkn');
    define('DB_PASSWORD',	'vciaqvbz60v0z7jy');

    // database tables information
    define('DB_TABLE_ORDERS', 'orders');
    define('DB_TABLE_ORDERS_COLUMN_ID',				'id');
	define('DB_TABLE_ORDERS_COLUMN_NAME',			'name');
    define('DB_TABLE_ORDERS_COLUMN_SURNAME',		'surname');
    define('DB_TABLE_ORDERS_COLUMN_EMAIL', 			'email');
	define('DB_TABLE_ORDERS_COLUMN_PHONE', 			'phone');
    define('DB_TABLE_ORDERS_COLUMN_PAYMENT_STATUS', 'payment_status');
    define('DB_TABLE_ORDERS_COLUMN_PAYMENT_SUM', 	'paid_sum');
    define('DB_TABLE_ORDERS_COLUMN_TIMESTAMP', 		'data');

    // other info
    define('COURSE_PRICE', 	100); // money in cents becouse of paysera

?>
