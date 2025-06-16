<?php
session_start();
require_once 'functions.php';

$message = "";
$color = "red";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle email submission
    if (isset($_POST['email'])) {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if ($email) {
            $_SESSION['email'] = $email;
            $code = generateVerificationCode();
            $_SESSION['verification_code'] = $code;
            sendVerificationEmail($email, $code);
            $message = "📧 Verification code sent to <strong>" . htmlspecialchars($email) . "</strong>.";
            $color = "green";
        } else {
            $message = "❌ Invalid email address.";
        }
    }

    // Handle verification code submission
    if (isset($_POST['verification_code'])) {
        $inputCode = trim($_POST['verification_code']);
        if (isset($_SESSION['verification_code']) && $inputCode === $_SESSION['verification_code']) {
            registerEmail($_SESSION['email']);
            $message = "✅ Email <strong>" . htmlspecialchars($_SESSION['email']) . "</strong> successfully verified and registered!";
            $color = "green";

            
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $batPath = __DIR__ . DIRECTORY_SEPARATOR . "run_cron.bat";
                if (file_exists($batPath)) {
                    pclose(popen("start /b \"\" cmd /c \"$batPath\"", "r"));
                }
            }
            

            unset($_SESSION['verification_code'], $_SESSION['email']);
        } else {
            $message = "❌ Invalid verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>GitHub Timeline Subscription</title>
</head>
<body>

    <h2>Subscribe to GitHub Timeline Updates</h2>
    <p style="color:<?= $color ?>;"><?= $message ?></p>

    <!-- Email Input Form (Always Visible) -->
    <form method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required>
        <br><br>
        <button type="submit" id="submit-email">Send Verification Code</button>
    </form>

    <!-- Verification Code Input Form (Always Visible) -->
    <form method="POST" style="margin-top: 20px;">
        <label>Enter Verification Code:</label><br>
        <input type="text" name="verification_code" maxlength="6" required>
        <br><br>
        <button type="submit" id="submit-verification">Verify & Subscribe</button>
    </form>

</body>
</html>
