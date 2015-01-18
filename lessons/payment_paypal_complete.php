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
$s['no_test'] = 1;
$s[selected_menu] = 6;
//$s[my_test] = 1;
get_messages('payment_process.php');
set_time_limit(60);
if (!$_POST[item_number]) $_POST = $_GET;
if ($_POST[item_number]) finish_payment_paypal();
header("Location: $s[site_url]/user.php?action=user_home_page"); exit;

##################################################################################
##################################################################################
##################################################################################

function  finish_payment_paypal() {
global $s,$m;
if (!is_numeric($_POST[item_number])) exit;
auto_payment_done($_POST[item_number]);
}

##################################################################################
##################################################################################
##################################################################################

?>