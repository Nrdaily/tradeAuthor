<?php
// app/config/alerts.php
return [
    // Enable or disable email alerts
    'enabled' => true,

    // Comma-separated admin emails to notify
    'recipients' => 'admin@example.com',

    // From email
    'from' => 'noreply@tradeauthor.com',

    // Use simple mail() by default. For production, configure SMTP in php.ini or integrate PHPMailer.
    'use_mail' => true,
];
?>