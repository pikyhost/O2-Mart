#!/bin/bash
# Run this once to setup permanent git security

# Create pre-commit hook
cat > .git/hooks/pre-commit << 'EOF'
#!/bin/bash
SENSITIVE=$(git diff --cached --name-only | grep -E "\.(env|log|sqlite|csv)$|storage/app/|app/public/|framework/")
if [ ! -z "$SENSITIVE" ]; then
    echo "🚨 BLOCKED: Sensitive files detected!"
    echo "$SENSITIVE"
    git reset HEAD $SENSITIVE
    exit 1
fi
exit 0
EOF

chmod +x .git/hooks/pre-commit

# Set global git ignore
git config core.excludesfile ~/.gitignore_global
echo -e "*.env*\n*.sqlite*\n*.log\nstorage/\napp/public/\nframework/" > ~/.gitignore_global

echo "✅ Git security setup complete - you'll never commit sensitive files again!"