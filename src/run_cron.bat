@echo off
SCHTASKS /Create /TN "GitHub.CRON" ^
/TR ""C:\xampp\php\php.exe" "C:\xampp\htdocs\github-timeline-dist34\src\cron.php"" ^
/SC MINUTE /MO 5 /F