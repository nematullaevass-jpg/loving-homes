<?php
// Simple authentication
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) && ($_POST['password'] ?? '') !== 'paw123') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $error = "Invalid password!";
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Login - Paw Suites</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <div style="max-width: 400px; margin: 100px auto; padding: 20px;">
            <h2>Paw Suites Admin</h2>
            <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
            <p style="margin-top: 20px; font-size: 0.9em; color: #666;">
                Hint: The password is "paw123"
            </p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$_SESSION['loggedin'] = true;

// Read bookings from file
$bookings = [];
$filename = 'bookings.txt';

if (file_exists($filename)) {
    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_reverse($lines); // Show newest first
    
    foreach ($lines as $line) {
        $bookings[] = $line;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management - Paw Suites</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .booking-item { 
            background: white; 
            margin: 10px 0; 
            padding: 15px; 
            border-left: 4px solid #18bc9c;
            border-radius: 4px;
        }
        .booking-meta { color: #666; font-size: 0.9em; margin-bottom: 10px; }
        .booking-actions { margin-top: 10px; }
        .status-badge { 
            padding: 2px 8px; 
            border-radius: 12px; 
            font-size: 0.8em; 
            background: #ecf0f1; 
            display: inline-block;
        }
        .status-new { background: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Paw Suites Admin</div>
            <ul class="nav-links">
                <li><a href="index.html">View Site</a></li>
                <li><a href="admin-bookings.php">Bookings</a></li>
                <li><a href="?logout=1">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Booking Management</h1>
        <p>Total Bookings: <strong><?php echo count($bookings); ?></strong></p>
        
        <?php if (empty($bookings)): ?>
            <p>No bookings yet.</p>
        <?php else: ?>
            <div class="bookings-list">
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-item">
                        <div class="booking-meta">
                            <?php 
                            // Extract timestamp
                            if (preg_match('/\[(.*?)\]/', $booking, $matches)) {
                                echo "<strong>Submitted:</strong> " . $matches[1];
                            }
                            ?>
                            <span class="status-badge status-new">NEW</span>
                        </div>
                        <div class="booking-details">
                            <?php
                            // Parse the booking data
                            $parts = explode('|', $booking);
                            foreach ($parts as $part) {
                                if (strpos($part, ':') !== false) {
                                    list($key, $value) = explode(':', $part, 2);
                                    echo "<p><strong>" . trim($key) . ":</strong> " . trim($value) . "</p>";
                                }
                            }
                            ?>
                        </div>
                        <div class="booking-actions">
                            <button onclick="markContacted(this)">Mark as Contacted</button>
                            <button onclick="deleteBooking(this)" style="background: #e74c3c;">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2023 Paw Suites. Admin Panel</p>
    </footer>

    <script>
        function markContacted(button) {
            const bookingItem = button.closest('.booking-item');
            const statusBadge = bookingItem.querySelector('.status-badge');
            statusBadge.textContent = 'CONTACTED';
            statusBadge.className = 'status-badge';
            statusBadge.style.background = '#fff3cd';
            statusBadge.style.color = '#856404';
            button.disabled = true;
            button.textContent = 'Contacted';
        }
        
        function deleteBooking(button) {
            if (confirm('Are you sure you want to delete this booking?')) {
                button.closest('.booking-item').style.display = 'none';
                // In a real application, you would send AJAX request to delete from server
            }
        }
        
        // Auto-refresh every 30 seconds to check for new bookings
        setInterval(() => {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>