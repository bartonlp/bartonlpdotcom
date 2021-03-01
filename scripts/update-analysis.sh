#!/bin/bash
# BLP 2017-11-01 -- this is now run via all-cron.sh in this directory.
# update all of the analysis files
# Why am I using wget? The analysis.php is a web based program not a
# cli so I have to run it via apache via the internet. Wget does that.
# I run one via www.bartonlp.com and the other via www.bartonlp.org

#echo "update-analysis.sh Start"

for x in Allnatural Bartonlp Bartonphillips BartonphillipsOrg ALL
do
echo "$x";
wget -qO- https://www.bartonlp.com/analysis.php?siteupdate=$x >/dev/null
done

echo "BartonLP"
wget -qO- --no-check-certificate http://www.bartonlp.org/analysis.php?siteupdate=BartonLP >/dev/null

echo "Tysonweb"
wget -qO- --no-check-certificate http://www.newbern-nc.info/analysis.php?siteupdate=Tysonweb 