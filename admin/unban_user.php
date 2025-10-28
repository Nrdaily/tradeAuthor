<?php
// unban_user.php 
require_once 'session.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
$user_id = $_POST['user_id'] ?? null;
if (!$user_id) {
    http_response_code(400);
    echo json_encode(['error' => 'User ID required']);
    exit;
}
$update = $db->prepare("UPDATE users SET banned = 0 WHERE id = ?");
if ($update->execute([$user_id])) {
    echo json_encode(['success' => true, 'message' => 'User unbanned successfully!']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to unban user']);
}
