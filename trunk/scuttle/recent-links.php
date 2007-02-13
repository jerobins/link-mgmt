<?php
#__date__ = "06/08/2005"
#__version__ = "1.0"
#__author__ = "James E. Robinson, III <www.robinsonhouse.com>"
#__copyright__ = "Copyright 2005, James E. Robinson, III"
#__license__ = "BSD"

# Grab latest scuttle bookmarks and post to weblog
# uses mtsend by Scott Yang from:
# http://scott.yang.id.au/2005/05/update-mtsendpy-10-has-been-released/

# Configuration:

# scuttle userid to get new links for:
$username = "jerobins";
# minimum number of new links before a weblog post is created:
$minreq = 3;

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


# End Configuration

require_once('header.inc.php');

$statfile = "/home/jerobins/var/marks/lastupdated";
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

$bookmarkservice =& ServiceFactory::getServiceInstance('BookmarkService');
$userservice =& ServiceFactory::getServiceInstance('UserService');

$userinfo = $userservice->getUserByUsername($username);
$userid =& $userinfo['uId'];

$dtstart = gmdate("Y-m-d H:i:s", $lastmodified);
$dtend = NULL;

$bookmarks =& $bookmarkservice->getBookmarks(0, NULL, $userid, NULL, NULL, NULL, NULL, $dtstart, $dtend);

//	Set up the XML file and output all the tags.
foreach ($bookmarks['bookmarks'] as $r) {
   $cnt += 1;
   $body .= "<li>\n";
   $body .= "\t" . '<div class="delicious-link"><a href="'
            . $r['bAddress'] . '">' . $r['bTitle'] . '</a>'
            . "</div>\n";
   if (!is_null($r['bDescription']) and (trim($r['bDescription']) != "")) {
      $body .= "\t" . '<div class="delicious-extended">'
               . $r['bDescription']
               . "</div>\n";
   }
   $taglist = '';
   if (count($r['categories']) > 0) {
      foreach($r['categories'] as $tag) {
         $taglist .= convertTag($tag) .' ';
      }
      $taglist = substr($taglist, 0, -1);
   } else {
      $taglist = 'system:unfiled';
   }
   $body .= "\t" . '<div class="delicious-tags">(tags: '
            . $taglist
            . ")</div>\n";
   $body .= "</li>\n";
}

$body .= "</ul>\n";

if ( $cnt >= $minreq ) {
   echo $header . $body;
   touch($statfile);
} else {
   # work-around for mtsend feature allowing empty post
   echo "bogus: abort\n";
}

?>
