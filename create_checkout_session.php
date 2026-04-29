<?php
require 'vendor/autoload.php';
require_once 'secrets.php';

header('Content-Type: application/json');

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

try {
    $phone = $_POST['phone'] ?? '';
    $amount = intval($_POST['amount'] ?? 0);
    $movie_name = $_POST['movie_name'] ?? 'Movie Ticket';

    if ($amount <= 0) throw new Exception("Invalid amount");
    if (empty($phone)) throw new Exception("Phone number required");

    session_start();
    $_SESSION['user_phone'] = $phone;
    $_SESSION['movie_name'] = $movie_name;

    $YOUR_DOMAIN = 'http://localhost/OnlineMovieTicketBS-PHP-v2-full-fixed';

    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'inr',
                'product_data' => [
                    'name' => $movie_name,
                ],
                'unit_amount' => $amount,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/complete_payment.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $YOUR_DOMAIN . '/payment_failed.php',
    ]);

    echo json_encode(['id' => $checkout_session->id, 'url' => $checkout_session->url]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
