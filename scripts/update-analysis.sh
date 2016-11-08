#!/bin/bash
# update all of the analysis files
for x in Applitec Allnatural Bartonlp Bartonphillips GranbyRotary Messiah Weewx ALL
do
wget -qO- http://bartonlp.com/html/analysis.php?siteupdate=$x >/dev/null
done

wget -qO- http://bartonlp.org/analysis.php?siteupdate=BartonlpOrg >/dev/null

echo "Analysis files updated in /var/www/bartonphillipsnet/analysis";
