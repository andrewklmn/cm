#!/bin/bash
cd /var/www/html/service
php -f collector.php
echo "Last collection  was colleted at: `date`" > /tmp/collector.log
