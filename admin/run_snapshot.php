<?php
// admin/run_snapshot.php
require_once 'session.php';
require_once '../app/config/database.php';
require_once '../app/func/portfolio_snapshot.php';

$database = new Database();
$db = $database->getConnection();

$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_snapshots'])) {
    try {
        $res = run_portfolio_snapshots($db, ['logToFile' => true]);
        $status = $res['message'];
    } catch (Exception $e) {
        $status = "Error running snapshots: " . $e->getMessage();
    }
}

// Get last snapshot time
$lastStmt = $db->prepare("SELECT MAX(created_at) FROM portfolio_snapshots");
$lastStmt->execute();
$lastRun = $lastStmt->fetchColumn();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Run Portfolio Snapshot</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require_once 'nav.php'; ?>
    <div class="main-content">
        <div class="container">
            <h1>Run Portfolio Snapshot</h1>
            <?php if ($status): ?>
                <div class="card">
                    <p><?php echo htmlspecialchars($status); ?></p>
                </div>
            <?php endif; ?>

            <div class="card">
                <p>Last snapshot run: <?php echo $lastRun ? date('M j, Y g:i A', strtotime($lastRun)) : 'Never'; ?></p>
                <form method="POST" action="">
                    <button type="submit" name="run_snapshots" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Run Snapshots Now</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>