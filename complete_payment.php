<?php
// =======================
// COMPLETE PAYMENT SCRIPT
// =======================

// Include dependencies
require_once 'vendor/autoload.php';
require_once 'secrets.php';
require_once 'stripe_config.php';
require_once 'config.php'; // your DB connection file

session_start();

// Verify session ID
if (!isset($_GET['session_id'])) {
    die("<h2 style='color:red'>❌ Invalid access: session_id missing.</h2>");
}

$session_id = $_GET['session_id'];

try {
    // Retrieve payment session from Stripe
    $session = \Stripe\Checkout\Session::retrieve($session_id);
    $payment_intent = \Stripe\PaymentIntent::retrieve($session->payment_intent);

    $amount = $payment_intent->amount / 100;
    $currency = strtoupper($payment_intent->currency);
    $status = $payment_intent->status;

    $phone = $_SESSION['user_phone'] ?? '+919999999999';
    $seats = $_SESSION['seats'] ?? 'N/A';
    $show_id = $_SESSION['show_id'] ?? 0;
    $user_id = $_SESSION['user_id'] ?? 0;
    $ticket_id = 'T' . strtoupper(substr(md5(uniqid()), 0, 6));
    $today = date('Y-m-d');

    // Save booking to database
    $stmt = $con->prepare("INSERT INTO tbl_bookings(ticket_id, user_id, show_id, amount, ticket_date, seats, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $status_val = 1; // 1 = paid
    $stmt->bind_param('siiissi', $ticket_id, $user_id, $show_id, $amount, $today, $seats, $status_val);
    $stmt->execute();

    // Generate OTP
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;

    // =======================
    // SEND OTP VIA TWILIO SMS
    // =======================
    $twilio_sent = false;
    try {
        $twilio = new Twilio\Rest\Client(TWILIO_SID, TWILIO_AUTH_TOKEN);

        // Format phone number in E.164 (India default if missing)
        if (!preg_match('/^\+/', $phone)) {
            $phone = '+91' . preg_replace('/\D/', '', $phone);
        }

        $twilio->messages->create($phone, [
            'from' => TWILIO_FROM,
            'body' => "🎟️ Your movie ticket OTP is $otp. Please verify to confirm your booking."
        ]);
        $twilio_sent = true;
    } catch (\Twilio\Exceptions\RestException $e) {
        error_log("Twilio RestException: " . $e->getMessage());
        $twilio_sent = false;
    } catch (Exception $e) {
        error_log("Twilio general error: " . $e->getMessage());
        $twilio_sent = false;
    }

    // ==========================
    // OPTIONAL: EMAIL RECEIPT
    // ==========================
    try {
        $user_email = $_SESSION['user_email'] ?? 'sureshshirol70@gmail.com';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_FROM_EMAIL, 'Movie Ticket Booking');
        $mail->addAddress($user_email);

        $mail->isHTML(true);
        $mail->Subject = "🎫 Your Ticket Booking Confirmation";
        $mail->Body = "
            <h3>Payment Successful!</h3>
            <p>Ticket ID: <b>$ticket_id</b></p>
            <p>Seats: $seats</p>
            <p>Amount: ₹$amount</p>
            <p>Date: $today</p>
            <p>Your OTP for verification is: <b>$otp</b></p>
        ";
        $mail->send();
    } catch (Exception $e) {
        error_log('Mail error: ' . $e->getMessage());
    }

    // ==========================
    // OPTIONAL: PDF RECEIPT
    // ==========================
    require_once('fpdf/fpdf.php');
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->Cell(0, 10, 'Movie Ticket Receipt', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Ln(10);
    $pdf->Cell(0, 10, "Ticket ID: $ticket_id", 0, 1);
    $pdf->Cell(0, 10, "Seats: $seats", 0, 1);
    $pdf->Cell(0, 10, "Amount: Rs. $amount", 0, 1);
    $pdf->Cell(0, 10, "Date: $today", 0, 1);
    $pdf->Cell(0, 10, "Status: $status", 0, 1);
    $pdf->Output('F', "documents/receipt_$ticket_id.pdf");

} catch (Exception $e) {
    echo "<h2 style='color:red'>❌ Payment verification failed: " . htmlspecialchars($e->getMessage()) . "</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payment Complete</title>
<style>
body { font-family: Arial, sans-serif; background: #f9f9f9; text-align: center; padding-top: 50px; }
.container { background: white; width: 50%; margin: auto; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
h1 { color: green; }
p { font-size: 16px; }
input[type=text] { padding: 10px; width: 50%; border: 1px solid #ccc; border-radius: 5px; }
button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
button:hover { background: #0056b3; }
</style>
</head>
<body>
<div class="container">
  <h1>✅ Payment Successful</h1>
  <p><b>Ticket ID:</b> <?= $ticket_id ?></p>
  <p><b>Seats:</b> <?= htmlspecialchars($seats) ?></p>
  <p><b>Amount Paid:</b> ₹<?= $amount ?></p>
  <p><b>Status:</b> <?= htmlspecialchars($status) ?></p>
  <p><b>Date:</b> <?= $today ?></p>

  <?php if ($twilio_sent): ?>
      <p>An OTP has been sent to your registered phone number.</p>
  <?php else: ?>
      <p style="color:orange;">⚠️ Could not send OTP via Twilio (trial accounts can only message verified numbers).</p>
      <p><b>For development:</b> Your OTP is <b><?= $otp ?></b></p>
  <?php endif; ?>

  <form action="otp_verify.php" method="post">
      <input type="text" name="otp_entered" placeholder="Enter OTP" required>
      <button type="submit">Verify OTP</button>
  </form>
</div>
</body>
</html>
