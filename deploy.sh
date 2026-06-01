#!/bin/bash
cd /home/shrang/laravel-app
git pull origin main
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
systemctl restart shrang-worker
echo "Deploy complete. Worker restarted."
