<?php
// admin/session.php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login');
    exit;
}

// Include database connection
require_once '../app/config/database.php';
$database = new Database();
$db = $database->getConnection();

// Get admin data
$query = "SELECT username FROM admins WHERE id = :admin_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':admin_id', $_SESSION['admin_id']);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$_SESSION['admin_username'] = $admin['username'];

// UPDATE admins SET password_hash = '$2y$10$r3B7W7W7W7W7W7W7W7W7OeW7W7W7W7W7W7W7W7W7W7W7W7W7W7W7W7W7W7W7W' WHERE username = 'admin';
?>