#!/bin/bash

echo "Setting up AI Summary Application..."

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "Composer is not installed. Please install Composer first."
    exit 1
fi

# Create Laravel project
composer create-project laravel/laravel summary-ai
cd summary-ai

# Create database (Update credentials in .env as needed)
echo "Creating database..."
mysql -u root -e "CREATE DATABASE IF NOT EXISTS summary_ai;" 2>/dev/null || echo "Please create database manually"

# Copy the provided files to their respective locations
# (You'll need to manually copy the code files to the appropriate directories)

# Run migrations
php artisan migrate

# Generate application key
php artisan key:generate

# Install dependencies
composer install

# Create storage link
php artisan storage:link

echo "Setup complete!"
echo "1. Update your .env file with database credentials"
echo "2. Start server: php artisan serve"
echo "3. Visit: http://localhost:8000"