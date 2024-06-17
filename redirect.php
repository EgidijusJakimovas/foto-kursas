<?php
 
require_once('WebToPay.php');
 
try {
    $self_url = 'https://webtopay-78a0e21bd487.herokuapp.com/';

    $request = WebToPay::redirectToPayment([
        'projectid' => 0,
        'sign_password' => 123,
        'orderid' => 'max-max-'.rand(1000000, 9999999),
        'amount' => 1000,
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
