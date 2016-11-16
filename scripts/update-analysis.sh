#!/bin/bash
# update all of the analysis files
for x in Applitec Allnatural Bartonlp Bartonphillips GranbyRotary Messiah Weewx ALL
do
wget -qO- http://www.bartonlp.com/analysis.php?siteupdate=$x >/dev/null
done

wget -qO- http://www.bartonlp.org/analysis.php?siteupdate=BartonlpOrg >/dev/null

echo "Analysis files updated in /var/www/bartonphillipsnet/analysis";
