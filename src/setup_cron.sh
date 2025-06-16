#!/bin/bash
# This script should set up a CRON job to run cron.php every 5 minutes.
# You need to implement the CRON setup logic here.
#!/bin/bash
CRON_JOB="*/5 * * * * php $(pwd)/cron.php"
(crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
echo "CRON job set to run every 5 minutes."
