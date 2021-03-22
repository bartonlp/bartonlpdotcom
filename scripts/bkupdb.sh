#!/bin/bash
# Backup the database before starting.
cd /var/www
dir=bartonlp/other
bkupdate=`date +%B-%d-%y`
filename="BARTONLP_BACKUP.$bkupdate.sql"

# We backup the 'barton' database to /var/www/bartonlp/other

mysqldump --defaults-file=/home/barton/ps --user=barton --no-data barton 2>/dev/null > $dir/bartonlp.schema
mysqldump --defaults-file=/home/barton/ps --user=barton --add-drop-table barton 2>/dev/null >$dir/$filename
gzip $dir/$filename

# We backup the 'bartonphillips' database table 'stocks' to /var/www/bartonlp/other

filename="STOCK_BACKUP.$bkupdate.sql";

mysqldump --defaults-file=/home/barton/ps --user=barton --no-data bartonphillips stocks 2>/dev/null > $dir/stocks.schema
mysqldump --defaults-file=/home/barton/ps --user=barton --add-drop-table bartonphillips stocks 2>/dev/null >>$dir/$filename;
gzip $dir/$filename

# remove all old files
find $dir -mtime +30 -exec rm '{}' \;
#echo "bkupdb.sh for bartonlp.com Done"
