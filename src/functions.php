<?php

function generateVerificationCode() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function getRegisteredEmails() {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return [];
    return file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

function registerEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return;

    $emails = getRegisteredEmails();
    $emailsLower = array_map('strtolower', $emails);

    if (!in_array(strtolower($email), $emailsLower)) {
        file_put_contents(__DIR__ . '/registered_emails.txt', $email . PHP_EOL, FILE_APPEND);
    }
}

function unsubscribeEmail($email) {
    $emails = getRegisteredEmails();
    $updated = array_filter($emails, fn($e) => strtolower(trim($e)) !== strtolower(trim($email)));
    file_put_contents(__DIR__ . '/registered_emails.txt', implode(PHP_EOL, $updated) . PHP_EOL);
}

function sendVerificationEmail($email, $code) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return;

    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers  = "From: GitHub Timeline <mayankbhardwaj8894@gmail.com>\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    mail($email, $subject, $message, $headers);
}

function sendUnsubscribeVerification($email, $code) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return;

    $subject = "Confirm Unsubscription";
    $message = "<p>To confirm unsubscription, use this code: <strong>$code</strong></p>";
    $headers  = "From: GitHub Timeline <mayankbhardwaj8894@gmail.com>\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    mail($email, $subject, $message, $headers);
}

function fetchGitHubTimeline() {
    return [
        ['event' => 'Push', 'user' => 'octocat'],
        ['event' => 'Fork', 'user' => 'monalisa'],
    ];
}

function sendGitHubUpdatesToSubscribers() {
    $emails = getRegisteredEmails();
    $timeline = fetchGitHubTimeline();

    $html = "<h2>GitHub Timeline Updates</h2>";
    $html .= "<table border='1' cellpadding='6' cellspacing='0' style='border-collapse:collapse;'>";
    $html .= "<tr><th>Event</th><th>User</th></tr>";

    foreach ($timeline as $item) {
        $html .= "<tr><td>{$item['event']}</td><td>{$item['user']}</td></tr>";
    }
    $html .= "</table>";

    foreach ($emails as $email) {
        $unsubscribeLink = "http://localhost/github-timeline-dist34/src/unsubscribe.php?unsubscribe_email=" . urlencode($email);
        $fullMessage = $html . "<p style='margin-top:20px'><a href='$unsubscribeLink' style='display:inline-block;background:#dc3545;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;'>Unsubscribe</a></p>";

        $subject = "Latest GitHub Updates";
        $headers  = "From: GitHub Timeline <mayankbhardwaj8894@gmail.com>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        mail($email, $subject, $fullMessage, $headers);
    }
}
