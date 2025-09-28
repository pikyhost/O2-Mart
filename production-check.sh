#!/bin/bash

echo "🔒 PRODUCTION SECURITY CHECK"
echo "=========================="

# Check for sensitive files
SENSITIVE=$(git ls-files | grep -E "\.(env|log|sqlite|key|csv)$|app/public/|framework/|storage/app/|private/")

if [ ! -z "$SENSITIVE" ]; then
    echo "❌ CRITICAL: Sensitive files found!"
    echo "$SENSITIVE" | head -10
    echo "🚨 DO NOT PUSH TO PRODUCTION!"
    exit 1
fi

echo "✅ Safe to push to production"