#!/bin/bash

# Set your project root (adjust if needed)
PROJECT_PATH="/Applications/XAMPP/xamppfiles/htdocs/gloryservant_v2"

cd "$PROJECT_PATH" || { echo "❌ Path not found"; exit 1; }

echo "🔧 Fixing permissions for Laravel project: $PROJECT_PATH"

# Ownership: current user and Apache (_www)
sudo chown -R $(whoami):_www storage bootstrap/cache

# Writable by user and Apache
sudo chmod -R 775 storage bootstrap/cache

# Ensure log file exists and is writable
if [ ! -f storage/logs/laravel.log ]; then
  echo "📄 Creating laravel.log..."
  touch storage/logs/laravel.log
fi

sudo chown $(whoami):_www storage/logs/laravel.log
sudo chmod 664 storage/logs/laravel.log

# Clear caches (safe to run always)
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "✅ Permissions fixed!"
