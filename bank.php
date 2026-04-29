<?php
session_start();
require_once 'secrets.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Secure Movie Payment</title>
  <script src="https://js.stripe.com/v3/"></script>
  <style>
    body {
      background: #f7f8fc;
      font-family: "Poppins", sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .payment-box {
      background: white;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
      padding: 40px;
      text-align: center;
      width: 400px;
    }
    h2 {
      margin-bottom: 20px;
      color: #222;
    }
    label {
      display: block;
      font-weight: 600;
      margin-top: 10px;
      text-align: left;
    }
    input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-top: 5px;
      margin-bottom: 15px;
      font-size: 14px;
    }
    button {
      background-color: #4a63e7;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 12px 20px;
      width: 100%;
      cursor: pointer;
      font-size: 16px;
      font-weight: 600;
      transition: 0.3s;
    }
    button:hover {
      background-color: #3547c7;
    }
    .note {
      color: #666;
      font-size: 13px;
      margin-top: 10px;
    }
  </style>
</head>
<body>

  <div class="payment-box">
    <h2>🎬 Proceed to Payment</h2>
    <form id="payment-form">
      <label>Phone (for OTP):</label>
      <input type="text" id="phone" name="phone" placeholder="+91XXXXXXXXXX" required />

      <label>Amount (INR):</label>
      <input type="number" id="amount" name="amount" value="250" required />

      <button type="button" id="checkout-button">Pay Securely</button>
      <p class="note">Your payment will be securely processed by Stripe in test mode.</p>
    </form>
  </div>

  <script>
    const stripe = Stripe("<?php echo STRIPE_PUBLISHABLE_KEY; ?>");
    const checkoutBtn = document.getElementById("checkout-button");

    checkoutBtn.addEventListener("click", async () => {
      const phone = document.getElementById("phone").value.trim();
      const amount = parseFloat(document.getElementById("amount").value.trim());
      const movie_name = "Movie Ticket";

      if (!phone.match(/^\+?\d{10,13}$/)) {
        alert("⚠️ Enter a valid phone number (with country code, e.g. +91636XXXXXXX)");
        return;
      }
      if (isNaN(amount) || amount <= 0) {
        alert("⚠️ Enter a valid payment amount.");
        return;
      }

      try {
        const response = await fetch("http://localhost/OnlineMovieTicketBS-PHP-v2-full-fixed/create_checkout_session.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ phone: phone, amount: amount * 100, movie_name: movie_name })
        });

        const data = await response.json();

        if (data.error) {
          alert("❌ Error: " + data.error);
          return;
        }

        if (!data.id || !data.url) {
          alert("⚠️ Error: Not a valid URL returned by Stripe session.");
          console.error("Returned data:", data);
          return;
        }

        // ✅ Redirect to Stripe Checkout
        window.location.href = data.url;

      } catch (err) {
        console.error("Fetch error:", err);
        alert("❌ Unable to connect to payment server.");
      }
    });
  </script>
</body>
</html>
