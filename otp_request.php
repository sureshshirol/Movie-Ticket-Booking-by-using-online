<?php
session_start();
require_once 'secrets.php';
if (!isset($_SESSION['last_booking_id'])) { echo '<p>No booking found. Complete payment first.</p>'; exit; }
$otp = rand(100000,999999);
$_SESSION['otp_code'] = (string)$otp;
$_SESSION['otp_expires'] = time() + 300;
$to = $_SESSION['user_phone'] ?? '';
if (defined('TWILIO_SID') && TWILIO_SID != '' && $to != '') {
    $sid = TWILIO_SID; $token = TWILIO_AUTH_TOKEN; $from = TWILIO_FROM;
    $msg = 'Your OTP for booking is: ' . $otp;
    $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Messages.json';
    $data = http_build_query(['To'=>$to,'From'=>$from,'Body'=>$msg]);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_USERPWD, $sid . ':' . $token);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpcode >=200 && $httpcode < 300) {
        echo '<p>OTP sent to ' . htmlspecialchars($to) . '</p>';
        echo '<a href="otp_verify.php">Verify OTP</a>';
    } else {
        echo '<p>Failed to send SMS. For testing, your OTP is: <strong>' . $otp . '</strong></p>';
        echo '<a href="otp_verify.php">Verify OTP</a>';
    }
} else {
    echo '<p>Twilio not configured or phone missing. For testing, OTP is: <strong>' . $otp . '</strong></p>';
    echo '<form method="post" action="otp_verify.php"><label>Enter OTP: <input name="otp_input"/></label><button>Verify</button></form>';
}
?>