#!/bin/sh
# Furl bookmark backup script
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

URL=http://www.furl.net/user/login
HDR="forwardTo=/exportXML.jsp&noTemp=&useFull=&username=$USER&password=$PASS&Submit=Log+in&t=&p=0&ti=&u=&r=&eid=&stype=&fromComplete=&c=&didLogin=1"

JAR=$TMP/.jar.$$

curl -m 300 -L -e ";auto" -c $JAR -d $HDR -s $URL

/bin/rm -f $JAR
