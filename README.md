# Movie-Ticket-Booking-by-using-online
A web-based application developed using PHP, MySQL, HTML, CSS, and JavaScript that allows users to browse movies, select show timings, choose seats, and book tickets. The system includes features like dynamic seat selection, booking flow management, and responsive user interface design for a smooth user experience.

📌 Features
🎥 Browse Now Showing and Upcoming Movies
⏰ View Show Timings
💺 Interactive Seat Selection
💳 Secure Card Payment Integration
🔐 OTP Verification for transactions
🧾 Booking Confirmation & Receipt
👤 User-friendly interface

🛠️ Tech Stack
Frontend:
HTML5
CSS3
JavaScript
Backend:
PHP
Database:
MySQL
Server:
XAMPP (Apache)

📂 Project Structure
project-root/
│
├── admin/                # Admin panel files
├── assets/               # CSS, JS, images
├── includes/             # Common PHP files (config, DB)
├── pages/                # Main user pages
├── theatre/              # Theatre management
├── index.php             # Homepage
└── README.md

⚙️ Installation & Setup
1.Clone the repository
git clone https://github.com/your-username/movie-ticket-booking.git

2.Move project to XAMPP
C:\xampp\htdocs\

3.Start XAMPP
Apache ✅
MySQL ✅

4.Import Database
Open phpMyAdmin
Create a database (e.g., movie_db)
Import .sql file

5.Configure Database
Go to:
includes/config.php
Update:
$conn = mysqli_connect("localhost", "root", "", "movie_db");

6.Run the Project
Open browser:
http://localhost/your-project-folder/

Booking Flow
Movie Selection → Show Time → Seat Selection → Payment → OTP Verification → Confirmation
