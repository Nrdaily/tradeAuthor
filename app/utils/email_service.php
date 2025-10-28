
<?php
// Manual PHPMailer inclusion
require_once __DIR__ . '/../../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $smtpHost;
    private $smtpPort;
    private $smtpUsername;
    private $smtpPassword;
    private $smtpFromEmail;
    private $smtpFromName;
    
    public function __construct() {
        // Namecheap SMTP configuration
        $this->smtpHost = 'premium279.web-hosting.com';
        $this->smtpPort = 465;
        $this->smtpUsername = 'verify@tradeauthor.com';
        $this->smtpPassword = 'blEi4mI62_JF';
        $this->smtpFromEmail = 'verify@tradeauthor.com';
        $this->smtpFromName = 'Trade Author';
    }
    
    // Send verification email
    public function sendVerificationEmail($email, $name, $token, $userId) {
        $subject = "Verify Your Trade Author Account";
        $verificationLink = $this->getBaseUrl() . "/verify_email.php?token=" . $token . "&id=" . $userId;
        
        $message = $this->getVerificationEmailTemplate($name, $verificationLink);
        
        return $this->sendEmail($email, $subject, $message);
    }
    
    // Send password reset email
    public function sendPasswordResetEmail($email, $name, $token) {
        $subject = "Reset Your Trade Author Password";
        $resetLink = $this->getBaseUrl() . "/reset_password.php?token=" . $token;
        
        $message = $this->getPasswordResetEmailTemplate($name, $resetLink);
        
        return $this->sendEmail($email, $subject, $message);
    }
    
    // Enhanced email sending with PHPMailer
    private function sendEmail($to, $subject, $message) {
        try {
            $mail = new PHPMailer(true);
            
            // Server settings for Namecheap
            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->Port       = $this->smtpPort;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtpUsername;
            $mail->Password   = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL for port 465
            $mail->SMTPDebug = 0; // Set to 2 for debugging
            $mail->Timeout    = 30; // 30 second timeout instead of default 300
            
            // Recipients
            $mail->setFrom($this->smtpFromEmail, $this->smtpFromName);
            $mail->addAddress($to);
            $mail->addReplyTo('verify@tradeauthor.com', 'Trade Author Support');
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;
            $mail->AltBody = $this->htmlToText($message);
            
            // Send email
            if ($mail->send()) {
                error_log("Email sent successfully to: " . $to);
                return true;
            } else {
                error_log("Email sending failed: " . $mail->ErrorInfo);
                return $this->sendEmailFallback($to, $subject, $message);
            }
            
        } catch (Exception $e) {
            error_log("PHPMailer Exception: " . $e->getMessage());
            return $this->sendEmailFallback($to, $subject, $message);
        }
    }
    
    // Fallback to basic mail function
    private function sendEmailFallback($to, $subject, $message) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Trade Author <" . $this->smtpFromEmail . ">" . "\r\n";
        $headers .= "Reply-To: verify@tradeauthor.com" . "\r\n";
        
        if (mail($to, $subject, $message, $headers)) {
            error_log("Fallback email sent to: " . $to);
            return true;
        } else {
            error_log("Fallback email also failed for: " . $to);
            return false;
        }
    }
    
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['PHP_SELF']);
        return $protocol . "://" . $host . $path;
    }
    
    private function htmlToText($html) {
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
    
    private function getVerificationEmailTemplate($name, $verificationLink) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #ff6b35, #ff8e53); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 20px; }
                .button { display: inline-block; background: #ff6b35; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
                .security-note { background: #fff3e0; border-left: 4px solid #ff6b35; padding: 10px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Trade Author</h1>
                    <p>Professional Trading Platform</p>
                </div>
                <div class='content'>
                    <h2>Welcome to Trade Author, {$name}!</h2>
                    <p>Thank you for creating your trading account. To start trading, please verify your email address by clicking the button below:</p>
                    
                    <div style='text-align: center;'>
                        <a href='{$verificationLink}' class='button'>Verify Email Address</a>
                    </div>
                    
                    <p>Or copy and paste this link in your browser:</p>
                    <p style='word-break: break-all; color: #666; font-size: 14px;'>{$verificationLink}</p>
                    
                    <div class='security-note'>
                        <p><strong>Security Notice:</strong></p>
                        <ul>
                            <li>This verification link expires in 24 hours</li>
                            <li>Never share this link with anyone</li>
                            <li>If you didn't create this account, please ignore this email</li>
                        </ul>
                    </div>
                    
                    <p>Once verified, you'll have access to:</p>
                    <ul>
                        <li>Real-time market data and charts</li>
                        <li>Professional trading tools</li>
                        <li>Secure portfolio management</li>
                        <li>24/7 customer support</li>
                    </ul>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Trade Author. All rights reserved.</p>
                    <p>This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    private function getPasswordResetEmailTemplate($name, $resetLink) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #ff6b35, #ff8e53); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { padding: 20px; }
                .button { display: inline-block; background: #ff6b35; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
                .security-note { background: #fff3e0; border-left: 4px solid #ff6b35; padding: 10px; margin: 15px 0; }
                .warning { background: #ffebee; border-left: 4px solid #ff3d00; padding: 10px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Trade Author</h1>
                    <p>Password Reset Request</p>
                </div>
                <div class='content'>
                    <h2>Hello {$name},</h2>
                    <p>We received a request to reset your Trade Author account password.</p>
                    
                    <div style='text-align: center;'>
                        <a href='{$resetLink}' class='button'>Reset Password</a>
                    </div>
                    
                    <p>Or copy and paste this link in your browser:</p>
                    <p style='word-break: break-all; color: #666; font-size: 14px;'>{$resetLink}</p>
                    
                    <div class='security-note'>
                        <p><strong>Security Notice:</strong></p>
                        <ul>
                            <li>This link expires in 1 hour</li>
                            <li>Never share this link with anyone</li>
                            <li>If you didn't request this reset, your account may be at risk</li>
                        </ul>
                    </div>
                    
                    <div class='warning'>
                        <p><strong>Important:</strong> If you didn't request this password reset, please contact our support team immediately and secure your account.</p>
                    </div>
                    
                    <p>For security reasons, this request was initiated from IP: {$_SERVER['REMOTE_ADDR']}</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Trade Author. All rights reserved.</p>
                    <p>This is an automated security message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
?>