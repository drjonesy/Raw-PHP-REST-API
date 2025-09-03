#!/bin/bash
set -e
source .env

echo "Building Hugo site..."
cd hugo_ssg
hugo --config ./hugo.yaml
cd ..

REMOTE_DIR="/public_html/api/docs"

# Deploy with lftp using plain FTP
echo "Deploying to $FTP_HOST..."
lftp -e "
set ftp:ssl-allow no;
open -u $FTP_USER,$FTP_PASS $FTP_HOST
mirror -R --parallel=5 --exclude .DS_Store --exclude .git --exclude .gitignore ./hugo_ssg/public $REMOTE_DIR
bye
"