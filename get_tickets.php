<?php
include('../../db.php');

// Safely get movie_id and date from GET
$movie_id = isset($_GET['movie_id']) ? $_GET['movie_id'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;

// Check if both values exist
if ($movie_id === null || $date === null) {
    die("Missing parameters: movie_id or date not provided");
}

// 🔍 FIX: Verify the correct column name in your database
// In phpMyAdmin, run: SHOW COLUMNS FROM tickets;
// Replace 'date' below with the real column name (like 'show_date' or 'booking_date')

$query = "SELECT * FROM tickets WHERE movie_id='$movie_id' AND show_date='$date'"; // change 'show_date' to your actual column name
$result = mysqli_query($con, $query);

// If query fails, show SQL error for debugging
if (!$result) {
    die("SQL Error: " . mysqli_error($con));
}

// Output as JSON
$tickets = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tickets[] = $row;
}

header('Content-Type: application/json');
echo json_encode($tickets);
?>
