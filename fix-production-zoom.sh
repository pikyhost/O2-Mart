#!/bin/bash

echo "🔧 Fixing production zoom image issues..."

# Check rims from ID 12231 (imported products)
echo "Checking rims from ID 12231 for missing conversions..."
/usr/bin/php83 artisan rim:fix-image-conversions --from=12231

# Clear caches
echo "Clearing caches..."
/usr/bin/php83 artisan cache:clear
/usr/bin/php83 artisan config:clear
/usr/bin/php83 artisan view:clear

echo "✅ Production zoom fix completed!"