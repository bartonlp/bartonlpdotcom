#!/bin/bash
# Backup the database before starting.
cd /var/www
dir=bartonlp/other
bkupdate=`date +%B-%d-%y`
filename="BARTONLP_BACKUP.$bkupdate.sql"

mysqldump --defaults-file=bartonlp/scripts/ps --user=root --no-data barton 2>/dev/null > $dir/bartonlp.schema
mysqldump --defaults-file=bartonlp/scripts/ps --user=root --add-drop-table barton 2>/dev/null >$dir/$filename
gzip $dir/$filename

# add remove all old files
find $dir -ctime +30 -exec rm '{}' \;
#echo "bkupdb.sh for bartonlp.com Done"

