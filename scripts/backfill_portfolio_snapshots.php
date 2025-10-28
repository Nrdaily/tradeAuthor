<?php
// scripts/backfill_portfolio_snapshots.php
// Usage: php backfill_portfolio_snapshots.php --days=30
// Or: php backfill_portfolio_snapshots.php --start=2025-01-01 --end=2025-01-31

require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/func/portfolio_snapshot.php';

$opts = getopt('', ['days::', 'start::', 'end::']);
$dbConn = (new Database())->getConnection();

if (!$dbConn) {
    echo "Database connection failed\n";
    exit(1);
}

if (!empty($opts['days'])) {
    $days = intval($opts['days']);
    if ($days <= 0) { echo "--days must be a positive integer\n"; exit(1); }
    $today = new DateTime();
    for ($i = $days; $i >= 1; $i--) {
        $d = (clone $today)->sub(new DateInterval("P{$i}D"))->format('Y-m-d');
        echo "Running snapshot for {$d}...\n";
        $res = run_portfolio_snapshot_for_date($dbConn, $d);
        echo "-> {$res['message']}\n";
    }
    echo "Backfill complete.\n";
    exit(0);
}

if (!empty($opts['start']) && !empty($opts['end'])) {
    $start = DateTime::createFromFormat('Y-m-d', $opts['start']);
    $end = DateTime::createFromFormat('Y-m-d', $opts['end']);
    if (!$start || !$end) { echo "Invalid start/end dates. Use YYYY-MM-DD\n"; exit(1); }
    if ($start > $end) { echo "start must be <= end\n"; exit(1); }
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end->add($interval));
    foreach ($period as $dt) {
        $d = $dt->format('Y-m-d');
        echo "Running snapshot for {$d}...\n";
        $res = run_portfolio_snapshot_for_date($dbConn, $d);
        echo "-> {$res['message']}\n";
    }
    echo "Backfill complete.\n";
    exit(0);
}

// If no args, show usage
echo "Usage:\n";
echo "  php backfill_portfolio_snapshots.php --days=30\n";
echo "  php backfill_portfolio_snapshots.php --start=YYYY-MM-DD --end=YYYY-MM-DD\n";

?>