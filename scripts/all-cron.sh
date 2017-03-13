#!/bin/bash
# This file does most of the cron jobs so I don't get inandated with
# email.
# update Sitemap.xml
/var/www/bartonlp/scripts/updatesitemap.sh
echo "*************************************************\n"
/var/www/bartonphillips.com/scripts/updatesitemap.sh
echo "*************************************************\n"
/var/www/granbyrotary.org/scripts/updatesitemap.sh
echo "*************************************************\n"

# Backup jobs 
# Granby Rotary bkupdb.sh
/var/www/bartonlp/scripts/bkupdb.sh
echo "*************************************************\n"
/var/www/granbyrotary.org/scripts/bkupdb.sh
echo "*************************************************\n"
/var/www/allnaturalcleaningcompany/scripts/bkupdb.sh
echo "*************************************************\n"
/var/www/bartonphillips.com/scripts/bkupdb.sh
echo "*************************************************\n"

/var/www/bartonlp/scripts/checktracker.php
echo "*************************************************\n"
/var/www/bartonlp/scripts/cleanuptables.php
echo "*************************************************\n"
/var/www/bartonlp/scripts/update-analysis.sh
echo "*************************************************\n"


