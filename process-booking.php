<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to HTML
header('Content-Type: text/html; charset=UTF-8');

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $dogName = htmlspecialchars(trim($_POST['dogName']));
    $package = htmlspecialchars(trim($_POST['package']));
    $checkin = htmlspecialchars(trim($_POST['checkin']));
    $message = htmlspecialchars(trim($_POST['message']));
    $newsletter = isset($_POST['newsletter']) ? 'Yes' : 'No';
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($dogName)) {
        $errors[] = "Dog's name is required";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If there are errors, show them
    if (!empty($errors)) {
        showErrorPage($errors);
        exit;
    }
    
    // Process the booking
    processBooking($name, $email, $phone, $dogName, $package, $checkin, $message, $newsletter);
    
} else {
    // If someone tries to access this page directly
    header("Location: contact.html");
    exit;
}

function processBooking($name, $email, $phone, $dogName, $package, $checkin, $message, $newsletter) {
    // Create data string for saving
    $timestamp = date('Y-m-d H:i:s');
    $data = "[$timestamp] | Name: $name | Email: $email | Phone: $phone | Dog: $dogName | Package: $package | Check-in: $checkin | Newsletter: $newsletter | Message: $message\n";
    
    // Save to file
    $filename = 'bookings.txt';
    file_put_contents($filename, $data, FILE_APPEND | LOCK_EX);
    
    // In a real application, you would also:
    // 1. Send email notifications
    // 2. Save to database
    // 3. Send confirmation email to customer
    
    // Show success page
    showSuccessPage($name, $dogName);
}

function showSuccessPage($name, $dogName) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booking Received - Paw Suites</title>
        <link rel="stylesheet" href="styles.css">
        <style>
            .success-container { text-align: center; padding: 40px; }
            .success-icon { font-size: 4rem; color: #18bc9c; margin-bottom: 20px; }
            .booking-details { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: left; }
        </style>
    </head>
    <body>
        <header>
            <nav>
                <div class="logo">Paw Suites</div>
                <ul class="nav-links">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="facilities.html">Facilities</a></li>
                    <li><a href="packages.html">Packages</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <div class="success-container">
                <div class="success-icon">✓</div>
                <h1>Thank You, <?php echo $name; ?>!</h1>
                <p>We've received your booking inquiry for <strong><?php echo $dogName; ?></strong> and we're excited to help!</p>
                
                <div class="booking-details">
                    <h3>What Happens Next?</h3>
                    <ul>
                        <li>We'll contact you within 24 hours to confirm availability</li>
                        <li>We'll discuss any special requirements for <?php echo $dogName; ?></li>
                        <li>We'll provide you with a detailed quote</li>
                        <li>You'll receive a welcome package with everything you need to know</li>
                    </ul>
                </div>
                
                <p><strong>Need immediate assistance?</strong> Call us at +960 123-4567</p>
                
                <div style="margin-top: 30px;">
                    <a href="index.html" class="button">Return to Homepage</a>
                    <a href="packages.html" class="button" style="background: #3498db;">View Packages</a>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2023 Paw Suites. All rights reserved.</p>
        </footer>
    </body>
    </html>
    <?php
}

function showErrorPage($errors) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - Paw Suites</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
        <header>
            <nav>
                <div class="logo">Paw Suites</div>
                <ul class="nav-links">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="facilities.html">Facilities</a></li>
                    <li><a href="packages.html">Packages</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <h1>Oops! There was a problem with your submission.</h1>
            
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h3>Please correct the following errors:</h3>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <p>Please <a href="contact.html">go back to the contact form</a> and try again.</p>
        </main>

        <footer>
            <p>&copy; 2023 Paw Suites. All rights reserved.</p>
        </footer>
    </body>
    </html>
    <?php
}
?>