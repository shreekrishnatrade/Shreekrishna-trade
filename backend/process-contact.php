<?php
// backend/process-contact.php
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_response('Invalid request method');
}

$name = clean_input($_POST['name'] ?? '');
$email = clean_input($_POST['email'] ?? '');
$subject = clean_input($_POST['subject'] ?? '');
$message = clean_input($_POST['message'] ?? '');

// Validation
if (empty($name) || empty($email) || empty($message)) {
    error_response('Please fill all required fields');
}

if (!validate_email($email)) {
    error_response('Invalid email address');
}

try {
    // Insert into contact_messages table
    $sql = "INSERT INTO contact_messages (name, email, subject, message, status) VALUES (?, ?, ?, ?, 'new')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $email, $subject, $message]);

    $message_id = $pdo->lastInsertId();

    // Log activity
    log_activity($pdo, 'Contact Form', "New message from $name", null);

    // Send confirmation email to user
    $emailSubject = "Message Received - " . SITE_NAME;
    $emailBody = "
    <h2>Thank You for Contacting Us!</h2>
    <p>Dear $name,</p>
    <p>We have received your message and will get back to you shortly.</p>
    <p><strong>Your Message:</strong></p>
    <p>" . nl2br(htmlspecialchars($message)) . "</p>
    <hr>
    <p>Best regards,<br>" . SITE_NAME . "</p>
    ";
    send_email($email, $emailSubject, $emailBody);

    // Send notification to admin
    $adminEmailBody = "
    <h2>New Contact Form Submission</h2>
    <p><strong>From:</strong> $name ($email)</p>
    <p><strong>Subject:</strong> $subject</p>
    <p><strong>Message:</strong></p>
    <p>" . nl2br(htmlspecialchars($message)) . "</p>
    ";
    send_email(ADMIN_EMAIL, "New Contact: $subject", $adminEmailBody);

    success_response('Thank you! Your message has been sent successfully.', ['message_id' => $message_id]);

} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage());
    error_response('Failed to send message. Please try again later.');
}
?>