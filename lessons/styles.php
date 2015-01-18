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

include('./common.php');
$_SESSION['GC_style'] = ereg_replace(' ','&nbsp;',$_GET[style]);

if (strstr($_SERVER[HTTP_REFERER],$s[site_url])) $url = $_SERVER[HTTP_REFERER];
else $url = "$s[site_url]/";

header("Location: $url");

?>