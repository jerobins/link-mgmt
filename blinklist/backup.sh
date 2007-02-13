#!/bin/sh
# BlinkLlist bookmark backup script
# James E. Robinson, III  2005-12-08  http://www.robinsonhouse.com/
#
# Licensed under Creative Commons
# http://creativecommons.org/licenses/by-nc-sa/2.0/
#
# ... "always backup data used in third party online services...they might
# not be around next week"

# change these 

USER=userid
PASS=passwd

# nothing else to edit...

URL1='http://www.blinklist.com/users/signin'
URL2='http://www.blinklist.com/index.php?Action=User/Export/rss.php&Export=true'
HDR="Username=$USER&Password=$PASS"

JAR=$TMP/.jar.$$

curl -m 300 -L -e ';auto' -c $JAR -d $HDR -s $URL1 > /dev/null
curl -m 300 -b $JAR -c $JAR -s $URL2

/bin/rm -f $JAR
