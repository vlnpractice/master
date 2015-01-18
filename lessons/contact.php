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
get_messages('contact.php');
include_once("$s[phppath]/data/data_forms.php");

if (!$_POST) $_POST = $_GET; unset($_POST[vars]);//new ajax
$_POST = replace_array_text($_POST);
$x = form_control($_POST);
$in = $x[1];
if ($x[0])
{ echo stripslashes(contact_box($in[what],$in[n],'<br>'.info_line($m[errorsfound],implode('<br />',$x[0]))));
  exit;
}

$from = $in[email]; if (!$in[to]) $in[to] = $s[mail]; 
$subject = $m[subject].' '.$s[site_name];
my_send_mail($from,$from,$in[to],0,$subject,$in[text],1);
//if ($in[to]!=$s[mail]) my_send_mail($from,$from,$s[mail],0,$subject,$in[text],1);

if (!$_POST[hide_cancel]) $close_it = '<br><a href="#page_top" onclick="show_hide_div_id(0,\'contact_box'.$_POST[what].$_POST[n].'\')">'.$m[close_this_window].'</a>';
echo '<br>'.info_line($m[message_sent],$close_it);
exit;


###############################################################################
###############################################################################
###############################################################################

function form_control($in) {
global $s,$m;
//foreach ($in as $k=>$v) $in[$k] = utf8_decode($v);
//foreach ($in as $k=>$v) $in[$k] = iconv('UTF-8',$s[charset],$v);
$in = replace_array_text($in);

$in[message] = trim($in[message]);
if (!$in[message]) $chyba[] = $m[m_text];
$black = try_blacklist($in[message],"word");
if ($black) $chyba[] = $black;

$in[name] = trim($in[name]);
if (!$in[name]) $chyba[] = "$m[missing_field] $m[name]";

$in[email] = trim($in[email]);
if (!$in[email]) $chyba[] = "$m[missing_field] $m[email]";
elseif (!check_email($in[email])) $chyba[] = $m[w_email];

//if ($in[what]=='ad')
if (($in[n]) AND (is_numeric($in[n])))
{ $need_captcha = $s[message_owner_captcha];
  if ($in[what]=='u') $a = get_user_variables($in[n]);
  else $a = get_ad_variables($in[n]);
  if ($a[email]) $in[to] = $a[email]; else info_line($m[unable]);
  $in[text] = "$in[message]\n\n$m[email]: $in[email]\n$m[name]: $in[name]";
}
else
{ $need_captcha = $s[message_to_us_captcha];
  $in[text] = "$in[message]\nEmail: $in[email]\nName: $in[name]\nIP: $s[ip]\n\n";
  $in[to] = $s[mail];
}

if ($need_captcha) { $x = check_entered_captcha($in[image_control]); if ($x) $chyba[] = $x; }
return array($chyba,$in);
}

###############################################################################
###############################################################################
###############################################################################

?>