<?php
ob_start();
session_start();
require_once 'config.php';
require_once 'secrets.php';

// =============================
// Safe form variable handling
// =============================
$movie_id   = $_POST['movie_id']   ?? '';
$screen     = $_POST['screen']     ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date   = $_POST['end_date']   ?? ''; // prevents undefined array key warning
$seats      = $_POST['seats']      ?? '';
$amount     = $_POST['amount']     ?? 0;
$time       = $_POST['time']       ?? '';
$date       = $_POST['date']       ?? '';

// Optional debugging
// echo "<pre>"; print_r($_POST); exit;

// =============================
// Save booking details in session
// =============================
$_SESSION['movie_id']   = $movie_id;
$_SESSION['screen']     = $screen;
$_SESSION['start_date'] = $start_date;
$_SESSION['end_date']   = $end_date;
$_SESSION['seats']      = $seats;
$_SESSION['amount']     = $amount;
$_SESSION['time']       = $time;
$_SESSION['date']       = $date;

// =============================
// (Optional) Insert booking record in DB before payment
// =============================
if (!empty($movie_id) && !empty($seats)) {
    $stmt = $con->prepare("INSERT INTO bookings (movie_id, screen, seats, amount, booking_date, show_time, start_date, end_date) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)");
    $stmt->bind_param("issdsss", $movie_id, $screen, $seats, $amount, $time, $start_date, $end_date);
    $stmt->execute();
}

// =============================
// Redirect to Payment (bank.php)
// =============================
header("Location: seat_selection.php");
exit;

ob_end_flush();
?>
