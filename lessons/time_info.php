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

error_reporting(0);
if (!is_numeric($_GET[hour])) exit;
switch ($_GET[action]) {
case 'image'	: image($_GET[hour]);
}

function image($hour) {
header ("Location: images/time_symbols/$hour.png");
}

?>