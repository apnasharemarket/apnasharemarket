<?php
// Make sure error reporting is disabled on production to avoid information leaks
error_reporting(0);
ini_set('display_errors', 0);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method.";
    exit;
}

// Sanitize and validate inputs
function clean_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

$name = isset($_POST["name"]) ? clean_input($_POST["name"]) : '';
$email = isset($_POST["email"]) ? clean_input($_POST["email"]) : '';
$message = isset($_POST["message"]) ? clean_input($_POST["message"]) : '';

// Validate inputs
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400); // Bad Request
    echo "Please fill in all required fields.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400); // Bad Request
    echo "Invalid email address.";
    exit;
}

// Prepare the email
$to = "shantanuraj931@gmail.com";
$subject = "New Enquiry from {$name}";
$body = "You have received a new enquiry from your website.\n\n".
        "Name: {$name}\n".
        "Email: {$email}\n\n".
        "Message:\n{$message}\n";

$headers = "From: {$name} <{$email}>\r\n" .
           "Reply-To: {$email}\r\n" .
           "X-Mailer: PHP/" . phpversion();

// Send the email
$mail_sent = mail($to, $subject, $body, $headers);

if ($mail_sent) {
    // Success response - can redirect or show plain text
    // Redirect to homepage with success query param (make sure your home page can handle this if you want)
    header("Location: index.html?status=success");
    exit;
} else {
    http_response_code(500); // Internal Server Error
    echo "Failed to send your enquiry. Please try again later.";
    exit;
}
?>
