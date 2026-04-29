<?php
require_once 'libs/fpdf.php';
require_once 'secrets.php';
// PHPMailer simplified autoload
require_once 'libs/PHPMailer/PHPMailer.php';
require_once 'libs/PHPMailer/Exception.php';

if (!isset($_GET['booking_id'])) { echo '<p>No booking id provided.</p>'; exit; }
$booking_id = $_GET['booking_id'];
$bookingsFile = __DIR__ . '/bookings.json';
if (!file_exists($bookingsFile)) { echo '<p>No bookings.</p>'; exit; }
$bookings = json_decode(file_get_contents($bookingsFile), true);
$booking = null;
foreach ($bookings as $b) { if ($b['id'] === $booking_id) { $booking = $b; break; } }
if (!$booking) { echo '<p>Not found.</p>'; exit; }

// Generate PDF
$pdf = new FPDF_Core();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Online Movie Ticket Booking Receipt',0,1,'C');
$pdf->Ln(5);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Booking ID: ' . $booking['id'],0,1);
$pdf->Cell(0,8,'Amount: ' . ($booking['amount_total']/100) . ' ' . strtoupper($booking['currency']),0,1);
$pdf->Cell(0,8,'Email: ' . $booking['customer_email'],0,1);
$pdf->Cell(0,8,'Phone: ' . $booking['phone'],0,1);
$pdf->Cell(0,8,'Date: ' . date('Y-m-d H:i:s', $booking['created']),0,1);

$receiptsDir = __DIR__ . '/receipts';
if (!is_dir($receiptsDir)) mkdir($receiptsDir, 0777, true);
$filename = $receiptsDir . '/receipt_' . $booking['id'] . '.pdf';
$pdf->Output('F', $filename);

echo '<h2>Booking Confirmed ✅</h2>';
echo '<p>Booking ID: ' . htmlspecialchars($booking['id']) . '</p>';
echo '<p><a href="receipts/' . urlencode(basename($filename)) . '" target="_blank">Download receipt</a></p>';

// Send email using PHPMailer simplified wrapper or SMTP if configured
if (defined('SMTP_HOST') && SMTP_USER != '') {
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = 'tls';
    $mail->Port = SMTP_PORT;
    $mail->From = SMTP_FROM_EMAIL;
    $mail->FromName = 'Online Movie Booking';
    $mail->addAddress($booking['customer_email']);
    $mail->Subject = 'Your Booking Receipt - ' . $booking['id'];
    $mail->Body = 'Please find attached your receipt.';
    // Attach file as string
    $pdfString = file_get_contents($filename);
    $mail->addStringAttachment($pdfString, basename($filename));
    $sent = $mail->send();
    if ($sent) echo '<p>Receipt emailed to ' . htmlspecialchars($booking['customer_email']) . '</p>';
    else echo '<p>Failed to send email via PHPMailer.</p>';
} else {
    echo '<p>SMTP not configured. Set SMTP_USER in secrets.php to enable email sending.</p>';
}
?>