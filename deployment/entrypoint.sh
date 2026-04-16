#!/bin/bash

# Clear existing cache
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

# Run migrations
php artisan migrate --force 2>/dev/null || true

# Seed database if needed
php artisan db:seed --force 2>/dev/null || true

# Create storage links
php artisan storage:link 2>/dev/null || true

# Start supervisord
supervisord -c /etc/supervisor/conf.d/supervisord.conf
