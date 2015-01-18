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
if (!is_numeric($_GET[n])) exit;

$ad = get_ad_variables($_GET[n]);
if (($ad[x_paypal_by]>$s[cas]) AND ($ad[x_paypal_email]) AND ($ad[x_paypal_currency]) AND ($ad[x_paypal_price]) AND ($ad[x_paypal_disable]))
{ dq("update $s[pr]ads set x_paypal_disabled = 1 where n = '$_GET[n]'",0); 
  $ad[to] = $ad[email];
  mail_from_template('ad_disabled_paypal.txt',$ad);
}

?>