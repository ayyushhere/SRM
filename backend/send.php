<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load PHPMailer files
require '../config/PHPMailer/Exception.php';
require '../config/PHPMailer/PHPMailer.php';
require '../config/PHPMailer/SMTP.php';

// Check if form was submitted
if(isset($_POST['submit'])) {
    // Get form data and sanitize it
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);
    
    // Create an instance of PHPMailer
    $mail = new PHPMailer(true); // true enables exceptions for better error handling
    
    try {
        // ==========================================
        // IMPORTANT: Gmail SMTP Server Configuration
        // ==========================================
        $mail->isSMTP();                                       // Send using SMTP protocol
        $mail->Host       = 'smtp.gmail.com';                  // Gmail SMTP server
        $mail->SMTPAuth   = true;                              // Enable SMTP authentication
        $mail->Username   = 'ayushkumar0211a@gmail.com';       // Your Gmail address
        $mail->Password   = 'hemb wifo uljp xljq';            // Your Gmail app password (not your regular password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;    // Use TLS encryption
        $mail->Port       = 587;                               // TCP port to connect to Gmail SMTP
        
        // If emails are not sending, try these troubleshooting steps:
        // 1. Make sure 'Less secure app access' is ON in your Google account settings
        // 2. Or create an App Password if you have 2-factor authentication enabled
        // 3. Check that the password is correct and hasn't expired
        // 4. Verify your Gmail account isn't blocked for security reasons
        
        // For debugging SMTP issues, uncomment these lines:
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;               // Enable verbose debug output
        // $mail->Debugoutput = function($str, $level) {error_log($str);}; // Log to error_log

        // ==========================================
        // Email content configuration
        // ==========================================
        
        // Email sender and recipient setup
        $mail->setFrom($email, $name);                         // Set the sender (shows as From in the email)
        $mail->addReplyTo($email, $name);                      // Set reply-to address
        $mail->addAddress('ayushkumar0211a@gmail.com');        // Add the recipient email address
        
        // Email content setup
        $mail->isHTML(true);                                   // Set email format to HTML
        $mail->Subject = $subject;
        
        // Build HTML body with clean formatting
        $htmlBody = "<h2>Contact Form Submission</h2>";
        $htmlBody .= "<p><strong>Name:</strong> $name</p>";
        $htmlBody .= "<p><strong>Email:</strong> $email</p>";
        $htmlBody .= "<p><strong>Subject:</strong> $subject</p>";
        $htmlBody .= "<p><strong>Message:</strong></p>";
        $htmlBody .= "<p>" . nl2br($message) . "</p>";
        
        $mail->Body = $htmlBody;
        $mail->AltBody = "Name: $name\nEmail: $email\nSubject: $subject\nMessage: $message"; // Plain text alternative
        
        // Send the email
        $mail->send();
        
        // Success - redirect with success message
        header("Location: ../frontend/index.html?status=success#contact");
        exit();
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Mailer Error: " . $mail->ErrorInfo);
        
        // Failed - redirect with error message
        header("Location: ../frontend/index.html?status=error#contact");
        exit();
    }
} else {
    // If accessed directly without form submission
    header("Location: ../frontend/index.html");
    exit();
}