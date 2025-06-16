<?php 
session_start();
require_once 'functions.php';

$message = "";
$color = "red";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['unsubscribe_email'])) {
        $email = filter_var($_POST['unsubscribe_email'], FILTER_VALIDATE_EMAIL);
        if ($email) {
            $_SESSION['unsubscribe_email'] = $email;
            $code = generateVerificationCode();
            $_SESSION['unsubscribe_code'] = $code;
            sendUnsubscribeVerification($email, $code);
            $message = "Verification code sent to <strong>" . htmlspecialchars($email) . "</strong>.";
            $color = "green";
        } else {
            $message = "Invalid email address.";
        }
    }

    if (isset($_POST['unsubscribe_verification_code'])) {
        $inputCode = trim($_POST['unsubscribe_verification_code']);
        if (isset($_SESSION['unsubscribe_code']) && $inputCode === $_SESSION['unsubscribe_code']) {
            unsubscribeEmail($_SESSION['unsubscribe_email']);
            $message = "✅ <strong>" . htmlspecialchars($_SESSION['unsubscribe_email']) . "</strong> has been unsubscribed.";
            $color = "green";
            unset($_SESSION['unsubscribe_email'], $_SESSION['unsubscribe_code']);
        } else {
            $message = "Invalid verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe</title>
</head>
<body>
    <h2>Unsubscribe from GitHub Timeline Emails</h2>
    <p style="color:<?= $color ?>;"><?= $message ?></p>

    <!-- Always Visible Email Form -->
    <form method="POST">
        <label>Enter your email:</label><br>
        <input type="email" name="unsubscribe_email" required><br><br>
        <button type="submit" id="submit-unsubscribe">Send Verification Code</button>
    </form>

    <!-- Always Visible Verification Code Form -->
    <form method="POST" style="margin-top: 20px;">
        <label>Enter the verification code:</label><br>
        <input type="text" name="unsubscribe_verification_code" maxlength="6" required><br><br>
        <button type="submit" id="verify-unsubscribe">Confirm Unsubscribe</button>
    </form>
</body>
</html>
