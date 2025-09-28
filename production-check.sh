#!/bin/bash

echo "=== CURRENT REMOTE STATUS ==="
git fetch origin
git status

echo -e "\n=== WHAT WILL BE PULLED ==="
git log HEAD..origin/clean --oneline

echo -e "\n=== FILES THAT WILL CHANGE ==="
git diff HEAD..origin/clean --name-only

echo -e "\n=== BACKUP CRITICAL FILES ==="
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
cp database/database.sqlite database/database.sqlite.backup.$(date +%Y%m%d_%H%M%S) 2>/dev/null || echo "No SQLite file to backup"

echo -e "\n=== SAFE PULL COMMAND ==="
echo "git pull origin clean"