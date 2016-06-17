#!/bin/bash
# update all of the analysis files
for x in Applitec Bartonlp Bartonphillips Conejoskiclub Endpolio GranbyRotary Messiah Puppiesnmore Weewx ALL
do
wget -qO- http://bartonlp.com/html/analysis.php?siteupdate=$x >/dev/null
done

echo "Analysis files updated in /var/www/bartonlp/analysis";
