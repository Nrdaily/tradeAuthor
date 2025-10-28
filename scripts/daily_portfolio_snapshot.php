<?php
// scripts/daily_portfolio_snapshot.php
// Run this script daily (via cron or Windows Task Scheduler) to capture each user's portfolio USD value snapshot.

?>
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/func/portfolio_snapshot.php';

$dbObj = new Database();
$db = $dbObj->getConnection();

$result = run_portfolio_snapshots($db, ['logToFile' => true]);
echo $result['message'];

?>