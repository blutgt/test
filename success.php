<?php
require __DIR__ . '/vendor/autoload.php';

use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

// Include your PayPal credentials
$clientId = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_SECRET_KEY';

// Set up the PayPal API context
$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential($clientId, $clientSecret)
);

// Get the payment ID and payer ID from the query parameters
$paymentId = $_GET['paymentId'] ?? null;
$payerId = $_GET['PayerID'] ?? null;

if (!$paymentId || !$payerId) {
    die("Payment failed or canceled by user.");
}

try {
    // Retrieve the payment object from PayPal
    $payment = Payment::get($paymentId, $apiContext);

    // Execute the payment
    $execution = new PaymentExecution();
    $execution->setPayerId($payerId);
    $result = $payment->execute($execution, $apiContext);

    // Verify payment status
    if ($result->getState() === "approved") {
        echo "<h1>Payment Successful!</h1>";
        echo "<p>Thank you for your purchase.</p>";

        // Here, you can save order details to your database or process further
    } else {
        echo "<h1>Payment Not Approved</h1>";
    }
} catch (Exception $ex) {
    echo "<h1>Error:</h1>";
    echo "<p>" . $ex->getMessage() . "</p>";
}
?>