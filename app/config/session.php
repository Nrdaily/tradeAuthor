<?php
// session.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Include database connection
require_once 'database.php';
$database = new Database();
$db = $database->getConnection();

// Check if user is logged in and not banned
if (isset($_SESSION['user_id'])) {
    $query = "SELECT banned, banned_reason FROM users WHERE id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && $user['banned']) {
        // Store ban reason in session for banned page
        $_SESSION['ban_reason'] = $user['banned_reason'];
        header('Location: banned.php');
        exit;
    }
}

// Get user data
$query = "SELECT first_name, last_name, email FROM users WHERE id = :user_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
$_SESSION['user_email'] = $user['email'];

