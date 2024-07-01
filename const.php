<?php
    // paysera connection information
    define('PAYSERA_PROJECT_ID',	244570);
    define('PAYSERA_PASSWORD',		'7ada0f6b4ace81a594c33bc2545246f7');
    define('PAYSERA_CURRENCY',		'EUR');

    // database connection information
    define('DB_NAME',		'rv3ra5m35w0h9t9i');
    define('DB_HOST',		'cvktne7b4wbj4ks1.chr7pe7iynqr.eu-west-1.rds.amazonaws.com');
    define('DB_USERNAME',	'kg7folg9dgg57207');
    define('DB_PASSWORD',	'x4syb4mqdq1pvajh');

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

    // other info
    define('COURSE_PRICE', 	100); // money in cents becouse of paysera requirements

?>
