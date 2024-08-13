<?php
    // paysera connection information
    define('PAYSERA_PROJECT_ID',	244570);
    define('PAYSERA_PASSWORD',		'7ada0f6b4ace81a594c33bc2545246f7');
    define('PAYSERA_CURRENCY',		'EUR');

    // database connection information
    define('DB_NAME',		'xejygo9toc5f5tj2');
    define('DB_HOST',		'gi6kn64hu98hy0b6.chr7pe7iynqr.eu-west-1.rds.amazonaws.com');
    define('DB_USERNAME',	'k42upy09mrfc1nzz');
    define('DB_PASSWORD',	'pz9q2be24fhs6lfq');

    // database tables information
    define('DB_TABLE_ORDERS',                       'orders');
    define('DB_TABLE_ORDERS_COLUMN_ID',				'id');
	define('DB_TABLE_ORDERS_COLUMN_NAME',			'name');
    define('DB_TABLE_ORDERS_COLUMN_SURNAME',		'surname');
    define('DB_TABLE_ORDERS_COLUMN_EMAIL', 			'email');
	define('DB_TABLE_ORDERS_COLUMN_PHONE', 			'phone');
    define('DB_TABLE_ORDERS_COLUMN_PAYMENT_STATUS', 'payment_status');
    define('DB_TABLE_ORDERS_COLUMN_PAYMENT_SUM', 	'paid_sum');
    define('DB_TABLE_ORDERS_COLUMN_DATA', 		    'data');
    define('DB_TABLE_ORDERS_COLUMN_USER_ID', 		'user_id');

    // other info
    define('COURSE_PRICE', 	100); // money in cents becouse of paysera requirements

?>
