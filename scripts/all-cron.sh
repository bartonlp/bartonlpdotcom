#!/bin/bash
# BLP 2021-02-26 -- we don't do checktracker2.php for now
# This file does most of the cron jobs so I don't get inandated with
# email.

# update Sitemap.xml
# Updatesitemap bartonlp, bartonphillips.com and tysonweb

/var/www/bartonphillips.com/scripts/updatesitemap.sh
/var/www/tysonweb/scripts/updatesitemap.sh

# Backup jobs
# Look at /etc/apache2/sites-enabled for the directories associated with the domains.

# NOTE: we only need to do the bkupdb.sh in /var/www/bartonlp/scripts
# as it does both the 'barton' database
# and the 'bartonphillips stocks' database and table.
/var/www/bartonlp/scripts/bkupdb.sh

# We need to backup the 'allnatural' database
/var/www/allnaturalcleaningcompany/scripts/bkupdb.sh

# Cleanup the 'tracker', 'bots2', 'daycounts' and 'counter2'
/var/www/bartonlp/scripts/cleanuptables.php

# Do the analysis
# This will update all of the files in https://bartonphillips.net/analysis
# The files are used by webstats.php.
/var/www/bartonlp/scripts/update-analysis.sh

# checktracker bartonlp
# Looks at tracker for js=0 and then updates bots and bots2
/var/www/bartonlp/scripts/checktracker2.php

echo "all-cron.sh, All Done"



