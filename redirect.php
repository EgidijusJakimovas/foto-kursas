<?php
 
require_once('WebToPay.php');
 
try {
    $self_url = 'https://foto-kursas-930ec9144443.herokuapp.com/';

    $request = WebToPay::redirectToPayment([
        'projectid' => 244531,
        'sign_password' => 'Labaslabas123*',
        'orderid' => 'max-max-'.rand(1000000, 9999999),
        'p_email' => $_POST['elpastas'],
		'p_firstname' => $_POST['vardas'],
		'p_lastname' => $_POST['pavarde'],
        'amount' => 489,
        'currency' => 'EUR',
        'country' => 'LT',
        'accepturl' => self_url() . '/accept.php',
        'cancelurl' => self_url() . '/cancel.php',
        'callbackurl' => self_url() . '/callback.php',
        'test' => 1,
    ]);
} catch (Exception $exception) {
    echo get_class($exception) . ':' . $exception->getMessage();
}
