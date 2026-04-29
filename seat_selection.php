<?php
session_start();
require_once 'config.php';
require_once 'secrets.php';

// ✅ Ensure base amount is always numeric
$base_amount = 250; // default price per seat

if (isset($_SESSION['amount']) && is_numeric($_SESSION['amount'])) {
    $base_amount = floatval($_SESSION['amount']);
} elseif (isset($_POST['amount']) && is_numeric($_POST['amount'])) {
    $base_amount = floatval($_POST['amount']);
}

// ✅ Store important session data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['movie_id'] = $_POST['movie_id'] ?? '';
    $_SESSION['screen_id'] = $_POST['screen_id'] ?? '';
    $_SESSION['show_id'] = $_POST['show_id'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>🎟️ Select Your Seats</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<style>
body {
  background-color: #f8f9fa;
  font-family: 'Poppins', sans-serif;
  text-align: center;
}
.container { margin-top: 50px; }
.screen {
  width: 80%;
  margin: 20px auto;
  background: #000;
  color: #fff;
  padding: 8px;
  border-radius: 5px;
  text-align: center;
}
.seat {
  width: 40px; height: 40px; margin: 5px;
  background-color: #ccc; border-radius: 6px;
  text-align: center; line-height: 40px;
  display: inline-block; cursor: pointer;
  transition: 0.2s; font-size: 14px;
}
.seat:hover { background-color: #b2b2b2; }
.seat.selected { background-color: #28a745; color: #fff; }
.seat.booked { background-color: #dc3545; color: #fff; cursor: not-allowed; }
.row-seats { margin-bottom: 10px; }
.summary { margin-top: 25px; }
button { margin-top: 20px; }
</style>
</head>
<body>
<div class="container">
  <h2>🎫 Select Your Seats</h2>
  <p>Click on the seats you wish to book</p>

  <div class="screen">SCREEN</div>

  <form action="bank.php" method="POST" id="seatForm">
    <div id="seatGrid" class="d-flex flex-column align-items-center mb-3"></div>
    <div class="summary">
      <p><strong>Selected Seats:</strong> <span id="selectedSeatsDisplay">None</span></p>
      <p><strong>Total Amount (₹):</strong> <span id="totalAmount">0.00</span></p>
    </div>
    <input type="hidden" name="selected_seats" id="selectedSeats">
    <input type="hidden" name="amount" id="amountField" value="<?php echo htmlspecialchars($base_amount); ?>">
    <button class="btn btn-success">Proceed to Payment</button>
  </form>
</div>

<script>
const rows = ['A', 'B', 'C', 'D', 'E']; // Rows
const cols = 10; // Seats per row
const seatGrid = document.getElementById('seatGrid');
const selectedSeatsInput = document.getElementById('selectedSeats');
const selectedSeatsDisplay = document.getElementById('selectedSeatsDisplay');
const totalAmountDisplay = document.getElementById('totalAmount');

// ✅ Force numeric base price
let baseAmount = parseFloat(document.getElementById('amountField').value);
if (isNaN(baseAmount) || baseAmount <= 0) {
  baseAmount = 250; // fallback ₹250 per seat
}

let selectedSeats = [];

// Example booked seats (later from DB)
const bookedSeats = ['B5', 'B6', 'C3'];

rows.forEach(row => {
  const rowDiv = document.createElement('div');
  rowDiv.classList.add('row-seats');
  for (let c = 1; c <= cols; c++) {
    const seatId = row + c;
    const seat = document.createElement('div');
    seat.classList.add('seat');
    seat.textContent = seatId;

    if (bookedSeats.includes(seatId)) {
      seat.classList.add('booked');
    }

    seat.addEventListener('click', () => {
      if (seat.classList.contains('booked')) return;

      seat.classList.toggle('selected');
      if (seat.classList.contains('selected')) {
        selectedSeats.push(seatId);
      } else {
        selectedSeats = selectedSeats.filter(s => s !== seatId);
      }

      selectedSeatsInput.value = selectedSeats.join(',');
      selectedSeatsDisplay.textContent = selectedSeats.length ? selectedSeats.join(', ') : 'None';

      // ✅ Calculate total safely
      const total = (selectedSeats.length * baseAmount).toFixed(2);
      totalAmountDisplay.textContent = total;
      document.getElementById('amountField').value = total;
    });

    rowDiv.appendChild(seat);
  }
  seatGrid.appendChild(rowDiv);
});
</script>
</body>
</html>
