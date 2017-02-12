#!/bin/bash
# update all of the analysis files
# Why am I using wget? The analysis.php is a web based program not a
# cli so I have to run it via apache via the internet. Wget does that.
# I run one via www.bartonlp.com and the other via www.bartonlp.org

echo "Do Analysis"

for x in Applitec Allnatural Bartonlp Bartonphillips BartonphillipsOrg GranbyRotary Messiah Weewx ALL
do
echo "$x";
wget -qO- http://www.bartonlp.com/analysis.php?siteupdate=$x >/dev/null
done

echo "BartonlpOrg"
wget -qO- http://www.bartonlp.org/analysis.php?siteupdate=BartonlpOrg >/dev/null
echo "Analysis files updated in /var/www/bartonphillipsnet/analysis";
