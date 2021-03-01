#!/bin/bash
# BLP 2021-02-26 -- we don't do checktracker2.php for now
# This file does most of the cron jobs so I don't get inandated with
# email.
# update Sitemap.xml
#echo "Updatesitemap bartonlp, bartonphillips.com
/var/www/bartonlp/scripts/updatesitemap.sh
#echo "****************************"
/var/www/bartonphillips.com/scripts/updatesitemap.sh
# Backup jobs 
#echo "Bkupdb bartonlp, allnaturalcleaningcompany, bartonphillips.com and bartonphillipsnet"
/var/www/bartonlp/scripts/bkupdb.sh
#echo "****************************"
/var/www/allnaturalcleaningcompany/scripts/bkupdb.sh
#echo "****************************"
/var/www/bartonphillips.com/scripts/bkupdb.sh
#echo "****************************"
/var/www/bartonphillipsnet/scripts/bkupdb.sh
#echo "****************************"
# Cleanup the 'tracker', 'bots2', 'daycounts', 'logagent2' and 'counter2'
#echo "cleanuptables bartonlp"
/var/www/bartonlp/scripts/cleanuptables.php
#echo "****************************"
#echo "update-analysis bartonlp"
/var/www/bartonlp/scripts/update-analysis.sh

# BLP 2021-02-26 -- comment out
#echo "****************************"
# echo "checktracker bartonlp"
# /var/www/bartonlp/scripts/checktracker2.php
# echo "****************************"
#echo "All Done"



