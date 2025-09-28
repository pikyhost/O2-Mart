#!/bin/bash

# Remove files from git tracking that should be ignored
git rm --cached .env
git rm --cached database/database.sqlite
git rm --cached -r storage/app/private/
git rm --cached -r storage/app/public/
git commit -m "Remove tracked files that should be ignored"