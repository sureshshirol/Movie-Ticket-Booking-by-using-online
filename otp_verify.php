<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'secrets.php';
require_once 'config.php';

if (!isset($_POST['otp_entered'])) {
    die("❌ No OTP provided.");
}

$entered = trim($_POST['otp_entered']);
$actual = $_SESSION['otp'] ?? '';

if ($entered !== (string)$actual) {
    die("<h2 style='color:red;'>❌ Incorrect OTP. Please try again.</h2>");
}

// ✅ OTP is correct — confirm booking
$amount = $_SESSION['amount_paid'] ?? 0;
$currency = $_SESSION['currency'] ?? 'INR';
$seats = $_SESSION['seats'] ?? 'N/A';
$movie = $_SESSION['movie_name'] ?? 'Movie Ticket';
$phone = $_SESSION['user_phone'] ?? 'Unknown';

if (isset($con)) {
    mysqli_query($con, "UPDATE tbl_bookings SET status=1 WHERE user_id=1 ORDER BY book_id DESC LIMIT 1");
}

// ✅ Generate PDF ticket
require('fpdf/fpdf.php');
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(0, 10, "🎟️ Movie Ticket Confirmation", 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(0, 10, "Movie: $movie", 0, 1);
$pdf->Cell(0, 10, "Seats: $seats", 0, 1);
$pdf->Cell(0, 10, "Amount Paid: ₹" . number_format($amount, 2) . " $currency", 0, 1);
$pdf->Cell(0, 10, "Phone: $phone", 0, 1);
$pdf->Cell(0, 10, "Status: Confirmed", 0, 1);
$pdf->Cell(0, 10, "Date: " . date("d-m-Y H:i:s"), 0, 1);

$pdfPath = __DIR__ . '/tickets/';
if (!is_dir($pdfPath)) mkdir($pdfPath, 0777, true);
$fileName = $pdfPath . 'ticket_' . time() . '.pdf';
$pdf->Output('F', $fileName);

// ✅ Send success message
echo "<h2 style='color:green;'>✅ Booking Confirmed!</h2>";
echo "<p>Your ticket has been generated successfully.</p>";
echo "<a href='tickets/" . basename($fileName) . "' target='_blank'>📄 View Ticket PDF</a>";
?>
