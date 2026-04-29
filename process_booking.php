<?php
ob_start(); // start output buffering

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include header
include('header.php');

// Redirect to login if user not logged in
if (!isset($_SESSION['user'])) {
    header('location:login.php');
    exit;
}
?>

<link rel="stylesheet" href="validation/dist/css/bootstrapValidator.css"/>
<script type="text/javascript" src="validation/dist/js/bootstrapValidator.js"></script>

<?php
include('form.php');
$frm = new formBuilder;
?>

<div class="content">
    <div class="wrap">
        <div class="content-top">
            <h3>Payment</h3>

            <form action="bank.php" method="post" id="form1">
                <div class="col-md-4 col-md-offset-4" style="margin-bottom:50px">
                    <div class="form-group">
                        <label class="control-label">Name on Card</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Card Number</label>
                        <input type="text" class="form-control" name="number" required title="Enter 16 digit card number">
                    </div>

                    <div class="form-group">
                        <label class="control-label">Expiration date</label>
                        <input type="date" class="form-control" name="date" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label">CVV</label>
                        <input type="text" class="form-control" name="cvv" required>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-success">Make Payment</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="clear"></div>
    </div>
</div>

<?php include('footer.php'); ?>

<?php
// --------------------
// PHP logic section
// --------------------

// Retrieve and sanitize form data
$seats  = isset($_POST['seats']) ? $_POST['seats'] : '';
$amount = isset($_POST['amount']) ? $_POST['amount'] : 0;
$date   = isset($_POST['date']) ? $_POST['date'] : '';

include('config.php');

$_SESSION['screen'] = isset($_POST['screen']) ? $_POST['screen'] : '';
$_SESSION['seats']  = $seats;
$_SESSION['amount'] = $amount;
$_SESSION['date']   = $date;

// Redirect to bank page after setting session variables
header('location:bank.php');
exit;

ob_end_flush(); // flush output buffer at the end
?>
