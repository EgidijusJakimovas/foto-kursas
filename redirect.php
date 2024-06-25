<?php

require_once('WebToPay.php');

// Define your static URL
function getSelfUrl(): string
{
    return 'https://foto-kursas-930ec9144443.herokuapp.com';
}

// Generate a unique auto-incrementing order ID
function generateOrderId(): string
{
    static $lastOrderId = 1000000; // Initial value for the order ID
    $lastOrderId++;
    return 'max-max-' . $lastOrderId;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $vardas = $_POST['vardas'] ?? '';
        $pavarde = $_POST['pavarde'] ?? '';
        $elpastas = $_POST['elpastas'] ?? '';

        $orderId = generateOrderId();

        WebToPay::redirectToPayment([
            'projectid' => 244570,
            'sign_password' => '7ada0f6b4ace81a594c33bc2545246f7',
            'orderid' => $orderId,
            'p_email' => $elpastas,
            'p_firstname' => $vardas,
            'p_lastname' => $pavarde,
            'amount' => 48900,
            'currency' => 'EUR',
            'country' => 'LT',
            'accepturl' => getSelfUrl() . '/accept.php',
            'cancelurl' => getSelfUrl() . '/cancel.php',
            'callbackurl' => getSelfUrl() . '/callback.php',
            'test' => 0,
        ]);
    }
} catch (Exception $exception) {
    echo get_class($exception) . ':' . $exception->getMessage();
}
?>
