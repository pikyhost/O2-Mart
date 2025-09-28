#!/bin/bash

# Remove storage files from git tracking
git rm -r --cached storage/app/public/ 2>/dev/null || echo "Already removed"
git rm -r --cached storage/logs/ 2>/dev/null || echo "Already removed" 
git rm -r --cached storage/framework/ 2>/dev/null || echo "Already removed"

# Commit the removal
git add .gitignore
git commit -m "Remove storage files from tracking and fix gitignore"

# Push the fix
git push origin main