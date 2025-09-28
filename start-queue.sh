#!/bin/bash
cd /home/tohf/public_html/o2mart
nohup /usr/bin/php83 artisan queue:work --tries=3 --timeout=90 > storage/logs/queue.log 2>&1 &
echo "Queue worker started with PID: $!"