Running daily portfolio snapshots

This repository includes `scripts/daily_portfolio_snapshot.php` which captures each user's portfolio total USD value and stores it in the `portfolio_snapshots` table.

Why use snapshots
- Provides accurate historical portfolio charts (exact daily values)
- Faster dashboard rendering (reads snapshots instead of recomputing from transactions)

How to run manually
- From the project root (Windows PowerShell):

```powershell
php .\scripts\daily_portfolio_snapshot.php
```

- From the project root (Linux/macOS):

```bash
php scripts/daily_portfolio_snapshot.php
```

Scheduling
- Linux/macOS (cron, run at midnight daily):

```cron
0 0 * * * cd /path/to/Cryptpro && /usr/bin/php scripts/daily_portfolio_snapshot.php >> /var/log/cryptpro_snapshot.log 2>&1
```

- Windows (Task Scheduler):
  - Create a basic task to run daily.
  - Action: Start a program
  - Program/script: C:\\php\\php.exe (path to your PHP CLI)
  - Add arguments: "C:\\xampp\\htdocs\\Cryptpro\\scripts\\daily_portfolio_snapshot.php"
  - Start in: C:\\xampp\\htdocs\\Cryptpro

Admin manual trigger
- Admins can trigger snapshots from the admin panel:
  - Login to the admin dashboard and click "Run Snapshots" -> "Run Snapshots Now"

Notes
- Ensure PHP CLI has access to the same php.ini and PDO extensions as your web server environment.
- The script will create the `portfolio_snapshots` table automatically if it doesn't exist.

If you want, I can add logging to a file and an email alert on failure.