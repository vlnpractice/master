<?PHP

#################################################
##                                             ##
##              Gold Classifieds               ##
##         http://www.abscripts.com/           ##
##         e-mail: mail@abscripts.com          ##
##                                             ##
##                                             ##
##               version:  4.0                 ##
##            copyright (c) 2011               ##
##                                             ##
##  This script is not freeware nor shareware  ##
##    Please do no distribute it by any way    ##
##                                             ##
#################################################

// rebuild.php?action=XXX[&key=$s[secretword]&result=1]

include('./common.php');
if (($s[secretword]) AND ($_GET[word]!=$s[secretword])) { echo 'Missing or wrong key.'; exit; }
include("./administration/rebuild_functions.php");
daily_job($_GET[result]);

?>