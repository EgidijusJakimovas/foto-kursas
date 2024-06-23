<?php

require_once('WebToPay.php');

function getSelfUrl(): string
{
    return 'http://127.0.0.1:5500';
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $vardas = $_POST['vardas'] ?? '';
        $pavarde = $_POST['pavarde'] ?? '';
        $elpastas = $_POST['elpastas'] ?? '';

        $self_url = 'https://foto-kursas-930ec9144443.herokuapp.com/';

        WebToPay::redirectToPayment([
            'projectid' => 244531,
            'sign_password' => 'Labaslabas123*',
            'orderid' => 'max-max-' . rand(1000000, 9999999),
            'p_email' => $elpastas,
            'p_firstname' => $vardas,
            'p_lastname' => $pavarde,
            'amount' => 489,
            'currency' => 'EUR',
            'country' => 'LT',
            'accepturl' => getSelfUrl() . '/accept.php',
            'cancelurl' => getSelfUrl() . '/cancel.php',
            'callbackurl' => getSelfUrl() . '/callback.php',
            'test' => 1,
        ]);
    } else {
        echo 'Invalid request method.';
    }
} catch (Exception $exception) {
    echo get_class($exception) . ':' . $exception->getMessage();
}
