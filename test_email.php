
<?php
// Test script specifically for Namecheap SMTP
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "<h1>Testing Namecheap SMTP Connection</h1>";

// Namecheap SMTP details
$smtpHost = 'premium279.web-hosting.com';
$smtpPort = 465;
$smtpUsername = 'verify@tradeauthor.com';
$smtpPassword = 'blEi4mI62_JF';

try {
    $mail = new PHPMailer(true);
    
    // Server settings
    $mail->isSMTP();
    $mail->Host       = $smtpHost;
    $mail->Port       = $smtpPort;
    $mail->SMTPAuth   = true;
    $mail->Username   = $smtpUsername;
    $mail->Password   = $smtpPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL for port 465
    $mail->SMTPDebug  = 2; // Enable verbose debug output
    $mail->Timeout    = 30; // 30 second timeout
    $mail->Debugoutput = function($str, $level) {
        echo "<p><strong>SMTP Debug:</strong> $str</p>";
    };
    
    // Recipients
    $mail->setFrom('verify@tradeauthor.com', 'Trade Author');
    $mail->addAddress('ndashiramadan0@gmail.com'); // CHANGE THIS to your test email
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from Trade Author (Namecheap)';
    $mail->Body    = 'This is a test email to verify Namecheap SMTP is working.';
    $mail->AltBody = 'This is a test email to verify Namecheap SMTP is working.';
    
    echo "<h2>Attempting to send email...</h2>";
    
    if ($mail->send()) {
        echo "<p style='color: green; font-size: 18px;'>✅ SUCCESS: Test email sent successfully via Namecheap SMTP!</p>";
    } else {
        echo "<p style='color: red; font-size: 18px;'>❌ FAILED: " . $mail->ErrorInfo . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-size: 18px;'>❌ EXCEPTION: " . $e->getMessage() . "</p>";
}

// Test connection without sending email
echo "<h2>Testing SMTP Connection Only</h2>";
try {
    $testMail = new PHPMailer(true);
    $testMail->isSMTP();
    $testMail->Host = $smtpHost;
    $testMail->Port = $smtpPort;
    $testMail->SMTPAuth = true;
    $testMail->Username = $smtpUsername;
    $testMail->Password = $smtpPassword;
    $testMail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $testMail->SMTPDebug = 2;
    $testMail->Timeout = 15;
    
    // Just connect, don't send
    $testMail->Debugoutput = function($str, $level) {
        echo "<p><strong>Connection Debug:</strong> $str</p>";
    };
    
    if ($testMail->smtpConnect()) {
        echo "<p style='color: green;'>✅ SMTP Connection Successful!</p>";
        $testMail->smtpClose();
    } else {
        echo "<p style='color: red;'>❌ SMTP Connection Failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Connection Exception: " . $e->getMessage() . "</p>";
}
?>
