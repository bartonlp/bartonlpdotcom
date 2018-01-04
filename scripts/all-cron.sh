#!/bin/bash
# This file does most of the cron jobs so I don't get inandated with
# email.
# update Sitemap.xml
echo "Updatesitemap bartonlp, bartonphillips.com and granbyrotary.org"
/var/www/bartonlp/scripts/updatesitemap.sh
echo "****************************"
/var/www/bartonphillips.com/scripts/updatesitemap.sh
echo "****************************"
/var/www/granbyrotary.org/scripts/updatesitemap.sh
echo "****************************"
# Backup jobs 
echo "Bkupdb bartonlp, granbyrotary.org, allnaturalcleaningcompany, bartonphillips.com and bartonphillipsnet"
/var/www/bartonlp/scripts/bkupdb.sh
echo "****************************"
/var/www/granbyrotary.org/scripts/bkupdb.sh
echo "****************************"
/var/www/allnaturalcleaningcompany/scripts/bkupdb.sh
echo "****************************"
/var/www/bartonphillips.com/scripts/bkupdb.sh
echo "****************************"
/var/www/bartonphillipsnet/scripts/bkupdb.sh
echo "****************************"
# cleanup, analysis and check
echo "cleanuptables bartonlp"
/var/www/bartonlp/scripts/cleanuptables.php
echo "****************************"
echo "update-analysis bartonlp"
/var/www/bartonlp/scripts/update-analysis.sh
echo "****************************"
# echo "checktracker bartonlp"
# /var/www/bartonlp/scripts/checktracker2.php
# echo "****************************"
echo "All Done"



