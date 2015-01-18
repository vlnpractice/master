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
$s[selected_menu] = 5;
get_messages('tell_friend.php');
include_once("$s[phppath]/data/data_forms.php");

if (!$_POST) $_POST = $_GET; unset($_POST[vars]);//new ajax
$_POST = replace_array_text($_POST);
$x = form_control($_POST);
$in = $x[1];
if ($x[0])
{ echo stripslashes(tell_friend_box($in[n],'<br>'.info_line($m[errorsfound],implode('<br />',$x[0]))));
  exit;
}

$in[from] = $in[email];
mail_from_template('tell_friend.txt',$in);

echo stripslashes(tell_friend_box($in[n],'<br>'.info_line($m[message_sent])));
exit;

###############################################################################
###############################################################################
###############################################################################

function form_control($in) {
global $s,$m;
//foreach ($in as $k=>$v) $in[$k] = utf8_decode($v);
//foreach ($in as $k=>$v) $in[$k] = iconv('UTF-8',$s[charset],$v);
$in = replace_array_text($in);

$in[comment] = trim($in[comment]);
if (!$in[comment]) $chyba[] = $m[m_text];
$black = try_blacklist($in[comment],"word");
if ($black) $chyba[] = $black;

$in[name] = trim($in[name]);
if (!$in[name]) $chyba[] = "$m[missing_field] $m[name]";

$in[email] = trim($in[email]);
if (!$in[email]) $chyba[] = "$m[missing_field] $m[email]";
elseif (!check_email($in[email])) $chyba[] = $m[w_email];

$in[friend_email] = trim($in[friend_email]);
if (!$in[friend_email]) $chyba[] = $m[m_friend_email];
elseif (!check_email($in[friend_email])) $chyba[] = $m[w_friend_email];

if ($s[tell_friend_captcha]) { $x = check_entered_captcha($in[image_control]); if ($x) $chyba[] = $x; }

$in[to] = $in[friend_email]; $s[email_from] = $s[mail];

return array ($chyba,$in);
}

###############################################################################
###############################################################################
###############################################################################

?>