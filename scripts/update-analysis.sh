#!/bin/bash
# BLP 2017-11-01 -- this is now run via all-cron.sh in this directory.
# update all of the analysis files
# Why am I using wget? The analysis.php is a web based program not a
# cli so I have to run it via apache via the internet. Wget does that.
# I run one via www.bartonlp.com and the other via www.bartonlp.org

echo "update-analysis.sh Start"

for x in Applitec Allnatural Bartonlp Bartonphillips BartonphillipsOrg GranbyRotary Messiah Weewx ALL
do
echo "$x";
wget -qO- http://www.bartonlp.com/analysis.php?siteupdate=$x >/dev/null
done

echo "BartonlpOrg"
wget -qO- http://www.bartonlp.org/analysis.php?siteupdate=BartonlpOrg >/dev/null

echo "Rpi"
wget -qO- http://www.bartonphillips.dyndns.org:8080/analysis.php?siteupdate=Rpi >/dev/null

#echo "Rpi2"
#wget -qO- http://www.bartonphillips.dyndns.org:5080/analysis.php?siteupdate=Rpi2 >/dev/null

echo "Hp-envy"
wget -qO- http://www.bartonphillips.dyndns.org:4080/analysis.php?siteupdate=Hp-envy >/dev/null
