<?php
// admin/ajax_run_snapshot.php
require_once 'session.php';
require_once '../app/config/database.php';
require_once '../app/func/portfolio_snapshot.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $db = (new Database())->getConnection();
    $res = run_portfolio_snapshots($db, ['logToFile' => true]);

    echo json_encode([
        'success' => true,
        'message' => $res['message'] ?? 'Snapshots completed',
        'count' => $res['count'] ?? null,
    ]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit;
}

?>
