#!/bin/bash

echo "🔧 Fixing production zoom image issues..."

# Fix specific rim 56 first
echo "Fixing rim 56..."
php artisan rim:fix-image-conversions --id=56

# Check all rims for missing conversions
echo "Checking all rims for missing conversions..."
php artisan rim:fix-image-conversions

# Clear caches
echo "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo "✅ Production zoom fix completed!"