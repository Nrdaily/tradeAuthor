<?php
require_once 'session.php';
require_once '../app/config/database.php';

$database = new Database();
$db = $database->getConnection();

$logFile = __DIR__ . '/../logs/portfolio_snapshot.log';
$logs = [];
if (file_exists($logFile)) {
    $lines = array_reverse(explode("\n", trim(file_get_contents($logFile))));
    foreach ($lines as $line) {
        if (trim($line) === '') continue;
        $logs[] = $line;
        if (count($logs) >= 100) break;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Snapshot Logs</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require_once 'nav.php'; ?>
    <div class="main-content">
        <div class="container">
            <h1>Portfolio Snapshot Logs</h1>
            <div class="card">
                <?php if (empty($logs)): ?>
                    <p>No logs found.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($logs as $line): ?>
                            <li><?php echo htmlspecialchars($line); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>