#!/usr/local/bin/php
<?php
#__date__ = "2005.12.22"
#__version__ = "1.0"
#__author__ = "James E. Robinson, III <www.robinsonhouse.com>"
#__copyright__ = "Copyright 2005, James E. Robinson, III"
#__license__ = "BSD"

# Grab latest scuttle bookmarks and post to weblog
# uses mtsend by Scott Yang from:
# http://scott.yang.id.au/2005/05/update-mtsendpy-10-has-been-released/

# Configuration:

# blinklist userid to get new links for:
$username = "jerobins";
# minimum number of new links before a weblog post is created:
$minreq = 3;
# maximum number in case of spewing
$toomany = 12;
# file holding last modified time
$statfile = "/home/jerobins/logs/lastupdated";

# End Configuration

# mtsend posting format -
#   [header1]: [value1]
#   [header2]: [value2]
#   [header3]: [value3]
#   -----
#   BODY:
#   ....

# valid headers -
#   TITLE:
#   ALLOW COMMENTS: 0/1
#   ALLOW PINGS: 0/1
#   CATEGORY:
#   CONVERT BREAKS: 0/1/customised text filter name

$date = date("Y-m-d");

$header = "TITLE: links for $date\n"
        . "ALLOW COMMENTS: 0\n"
        . "ALLOW PINGS: 0\n"
        . "CATEGORY: Links\n"
        . "CONVERT BREAKS: 1\n"
        . "-----\n";

$curtime = time();
$daily = 24*60*60;

if ( ! file_exists($statfile) ) {
   # set initial last-updated date to yesterday
   $yesterday = $curtime - $daily;
   touch($statfile, $yesterday);
}

$lastmodified = filemtime($statfile);
$cnt = 0;

$body = "BODY:\n" . '<ul class="delicious">' . "\n";

$rssurl = "http://www.blinklist.com/$username/rss.xml";

$entries = simplexml_load_file($rssurl);

#date_default_timezone_set('EST5EDT');
putenv('TZ=EST5EDT');
$dfmt = '%a, %d %b %Y %T -0500'; # 'Thu, 22 Dec 2005 17:33:37 -0500'

foreach ($entries->channel->item as $item) {
   // title, description, link, pubDate
   $tstamp = strtotime($item->pubDate);

   if ( $tstamp < $lastmodified ) {
      continue;
   }

   $cnt += 1;
   $body .= "<li>\n";
   $body .= "\t" . '<div class="delicious-link"><a href="'
            . $item->link . '">' . $item->title . '</a>'
            . "</div>\n";
   if (!empty($item->description) and (trim($item->description) != "")) {
      $body .= "\t" . '<div class="delicious-extended">'
               . $item->description
               . "</div>\n";
   }
   $body .= "</li>\n";
}

$body .= "</ul>\n";

if ( $cnt >= $minreq ) {
   if ( $cnt > $toomany ) {
      # work-around for mtsend feature allowing empty post
      echo "too_many_$cnt:\n";
   } else {
      echo $header . $body;
      touch($statfile);
   }
} else {
   # work-around for mtsend feature allowing empty post
   echo "too_few_$cnt:\n";
}

?>
