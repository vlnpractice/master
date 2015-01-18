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
$s[selected_menu] = 6;

include('./ad_create_edit_functions.php');
include('./ad_statistic_functions.php');
include_once("$s[phppath]/data/data_forms.php");
get_messages('user.php');
$s[u_v_nick] = $s[u_r_nick] = $s[u_v_email] = $s[u_r_email] = 1;

if ($_GET[user])
{ $x = explode('-',base64_decode($_GET[user]));
  $x[0] = round($x[0]);
  if ($x[0]>0)
  { $user_vars = get_user_variables($x[0]);
	if (($user_vars[n]) AND ($user_vars[password]=md5($x[1])))
	{ $_SESSION[GC_u_email] = $user_vars[email];
$_SESSION[GC_u_password] = $user_vars[password];
$_SESSION[GC_u_name] = $user_vars[name];
$_SESSION[GC_u_n] = $user_vars[n];
$_SESSION[GC_u_style] = $user_vars[style];
$s[GC_u_email] = $user_vars[email];
$s[GC_u_password] = $user_vars[password];
$s[GC_u_name] = $user_vars[name];
$s[GC_u_n] = $user_vars[n];
$s[GC_u_style] = $s[GC_style] = $user_vars[style];

		
	}
  }
}

switch ($_GET[action]) {
//case 'user_login'					: user_login($_GET);
//case 'user_confirmed'				: user_confirmed($_GET);
//case 'resend_confirmation_email'	: resend_confirmation_email($_GET);
case 'user_log_off'					: user_log_off();
//case 'user_remind'					: user_remind();
//case 'user_edit'					: user_edit();
case 'user_favorites'				: user_favorites();
case 'ad_create'					: ad_create($_GET);
case 'ad_features_edit'				: ad_features_edit($_GET);
case 'ad_edit'						: ad_edit($_GET);
case 'ad_statistic'					: ad_statistic($_GET);
case 'ad_reset_statistic'			: ad_reset_statistic($_GET[n]);
case 'ad_delete'					: ad_delete($_GET[n]);
case 'ad_confirm'					: ad_confirm($_GET);
case 'ad_paypal_enable'				: ad_paypal_enable($_GET);
case 'increase'						: increase($_GET);
case 'show_classifieds'				: show_classifieds($_GET);
case 'orders'						: orders();
}
switch ($_POST[action]) {
case 'user_login'					: user_login($_POST);
//case 'user_joined'					: user_joined($_POST);
//case 'user_edited'					: user_edited($_POST);
//case 'user_reminded'				: user_reminded($_POST);
//case 'using_temporary_password'		: using_temporary_password($_POST);
case 'ad_create'					: ad_create($_POST);
case 'ad_created'					: ad_created($_POST);
case 'ad_edited'					: ad_edited($_POST);
case 'ad_features_edited'			: ad_features_edited($_POST);
case 'ad_features_edited_confirmed'	: ad_features_edited_confirmed($_POST);
}
if (($_SESSION[GC_u_n]) OR ($_COOKIE[GC_u_n])) user_home_page();
user_join();

#########################################################################

function increase($in) {
global $s,$m;

/*
user.php?action=increase&n=13&x=d41d8cd98f00b204e9800998ecf8427e-75f44dcaa542af08a99a349c60745563
*/

if (!is_numeric($in[n])) exit;
$ad = get_ad_variables($in[n]);
if (!$ad[n]) { $s[info] = info_line('Incorrect link.'); user_login_form($in); }

$x = explode('-',$in[x]);
$email = md5($ad[email]); $title = md5($ad[title]);
if (($x[0]!=$email) OR ($x[1]!=$title)) { $s[info] = info_line('Incorrect link.'); user_login_form($in); }

dq("update $s[pr]ads set t2 = t2 + (86400*30) where n = '$in[n]'",1);
$s[info] = iot('Ad increased');
$not_include = 1;
include("$s[phppath]/classified.php");
show_ad_details($ad[n],0,1);
}


################################################################################

function orders() {
global $s,$m;
check_logged_user();
$q = dq("select * from $s[pr]ads_orders where user = '$s[GC_u_n]' order by n desc",1);
while ($b=mysql_fetch_assoc($q))
{ $ad_vars = get_ad_variables($b[ad]);
  $b[ad_url] = get_detail_page_url('ad',$ad_vars[n],$ad_vars[rewrite_url],0);
  $b[ad_title] = $ad_vars[title];
  if ($b[paid]) { $b[paid] = $m[yes]; $b[pdf] = '<a href="'.$s[site_url].'/invoice.php?n='.$b[n].'&f=Invoice">Invoice</a>'; }
  else { $b[paid] = $m[no]; $b[pdf] = $m[na]; }
  $b[date] = datum($b[time]);
  
  foreach ($s[extra_options] as $k=>$v) if ($ad['x_'.$v.'_by']>$s[cas]) $extra[] = $m['xtra_'.$v];
  if ($ad[x_pictures_by]>$s[cas]) $extra[] = "$m[xtra_pictures] $ad[x_pictures_max]";;
  if ($ad[x_files_by]>$s[cas]) $extra[] = "$m[xtra_files] $ad[x_files_max]";;
  if (!$extra) $ad[extra_features] = 'N/A'; else $ad[extra_features] = implode(', ',$extra);
  unset($extra);
  
  $a[orders] .= parse_part('user_orders.txt',$b);
}
$a[info] = $s[info];
page_from_template('user_orders.html',$a);
}

#########################################################################
#########################################################################
#########################################################################

function user_join($in) {
global $s,$m;

if ($in) $s = array_merge($s,(array)$in);
$in[n] = 0;
$in = user_form_get_variables($in);
if ($s[u_v_captcha]) $in[field_captcha_test] = parse_part('form_captcha_test.txt',$a);
//$in[field_terms] = parse_part('form_field_terms.txt',$x);
page_from_template('user_join.html',$in);
}

#########################################################################

function user_joined($in) {
global $s,$m;
$x = user_form_control($in); $in = $x[1];
if ($x[0])
{ $in[info] = info_line($m[errorsfound],implode('<br>',$x[0]));
  foreach ($in as $k => $v) { if ($v=='1') $in[$k] = ' checked'; }
  user_join($in);
}
$in[code] = user_write_to_db($in);
if ($s[user_must_confirm])
{ send_confirmation_email($in);
  $in[hide_continue_begin] = '<!--'; $in[hide_continue_end] = '-->'; 
}
else { $in[hide_confirmation_begin] = '<!--'; $in[hide_confirmation_end] = '-->'; }
$in[days] = $s[user_unconfirmed_delete_after];
user_joined_or_edited($in);
}

############################################################################

function send_confirmation_email($in) {
global $s;
$in[to] = $in[email];
$code = substr(md5(md5($in[password]).$in[code]),10,25);
$in[confirm_url] = "$s[site_url]/user.php?action=user_confirmed&amp;n=$s[user_n]&amp;password=$in[password]&amp;code=$code";
$in[days] = $s[user_unconfirmed_delete_after];
mail_from_template('user_confirm.txt',$in);
}

############################################################################

function resend_confirmation_email($in) {
global $s,$m;
if (!is_numeric($in[n])) exit;
dq("update $s[pr]users set joined = '$s[cas]' where n = '$in[n]' AND confirmed = '0'",1);
$q = dq("select * from $s[pr]users where n = '$in[n]' AND confirmed = '0'",1);
$user = mysql_fetch_assoc($q);
if (!$user[n]) problem($m[no_account]);
$code = substr(md5($user[password].$user[joined]),10,25);
$in[to] = $user[email];
$in[confirm_url] = "$s[site_url]/user.php?action=user_confirmed&n=$in[n]$n&password=$in[password]&code=$code";
mail_from_template('user_confirm.txt',$in);
$in[info] = info_line($m[mail_sent]);
user_login($in);
}

############################################################################

function user_confirmed($in) {
global $s,$m;
if (!is_numeric($in[n])) exit;
if (!$s[ip]) $s[ip] = 1;
$password = md5($in[password]);
$q = dq("select * from $s[pr]users where n = '$in[n]' AND password = '$password' AND confirmed = '0'",1);
$user = mysql_fetch_assoc($q);
if (!$user[n]) problem($m[no_account]);
$code = substr(md5($user[password].$user[joined]),10,25);
if ($code!=$in[code]) problem($m[w_confirm]);
dq("update $s[pr]users set ip = '$s[ip]', confirmed = '1' where n = '$user[n]'",1);
unset($s[user_must_confirm]);

$in[hide_confirmation_begin] = '<!--'; $in[hide_confirmation_end] = '-->';
if ($s[i_admin_user_joined])
{ $user[action] = 'joined';
  for ($x=1;$x<=3;$x++) $address[] = $user["address$x"]; $address[] = $user[country]; $user[address] = implode(", ",$address);
  for ($x=1;$x<=3;$x++) $phone[] = $user["phone$x"]; $user[phones] = implode(", ",$phone);
  mail_from_template('user_joined_edited_admin.txt',$user);
}

user_joined_or_edited($in);
}

############################################################################
############################################################################
############################################################################

function user_edit() {
global $s,$m;
$user = check_logged_user();
$user = array_merge((array)$user,(array)$in);
$user = user_form_get_variables($user);
if ($in[action]=='user_edited') $user[username] = $user[password] = '';
if (!$user[username]) $user[username] = $s[GC_u_username]; if (!$user[password]) $user[password] = $s[GC_u_password];
if (($user[username]!=$s[GC_u_username]) OR ($user[password]!=$s[GC_u_password])) problem($m[login_error]);
$user[info] = $s[info];
page_from_template('user_edit.html',$user);
}

############################################################################

function user_edited($in) {
global $s,$m;
check_logged_user();

$x = user_form_control($in); $in = $x[1];
if ($x[0])
{ $s[info] = info_line($m[errorsfound],implode('<br>',$x[0]));
  user_edit();
}
if ($s[i_user_who_edited]) { $in[to] = $s[GC_u_email]; mail_from_template('user_edited_user.txt',$in); }
user_write_to_db($in);

$in[n] = $s[GC_u_n];
upload_files('u',$s[GC_u_n],'',0,1,$in[delete_image],'');

$s[GC_u_style] = $s[GC_style] = $in[style];
$s[info] = info_line($m[data_saved]);
user_edit();
}

############################################################################
############################################################################
############################################################################

function user_write_to_db($in) {
global $s;
if ($_COOKIE[GC_u_email]) $cookie = 1;
setcookie(GC_u_email,false); setcookie(GC_u_password,false); setcookie(GC_u_name,false); setcookie(GC_u_style,false);
unset($_SESSION[GC_u_email],$_SESSION[GC_u_password],$_SESSION[GC_u_name],$_SESSION[GC_u_style]);
    
if ($s[GC_u_email])
{ check_logged_user();//echo kk;exit;
  if ($in[password]) $password = "password = '".md5($in[password])."',";
  dq("update $s[pr]users set $password email = '$in[email]', name = '$in[name]', nick = '$in[nick]', company = '$in[company]', address1 = '$in[address1]', address2 = '$in[address2]', address3 = '$in[address3]', country = '$in[country]', phone1 = '$in[phone1]', phone2 = '$in[phone2]', url = '$in[url]', site_title = '$in[site_title]', style = '$in[style]', detail = '$in[detail]', news1 = '$in[news1]', news2 = '$in[news2]', news3 = '$in[news3]', news4 = '$in[news4]', news5 = '$in[news5]' where n = '$s[GC_u_n]'",1);
  $user_vars = get_user_variables($s[GC_u_n]);
  if ($cookie)
  { setcookie(GC_u_password,$user_vars[password],$s[cas]+31536000); 
    setcookie(GC_u_name,$user_vars[name],$s[cas]+31536000); 
    setcookie(GC_u_email,$user_vars[email],$s[cas]+31536000); 
    setcookie(GC_u_style,$user_vars[style],$s[cas]+31536000);
  }
  $_SESSION[GC_u_password] = $user_vars[password];
  $_SESSION[GC_u_name] = $user_vars[name];
  $_SESSION[GC_u_email] = $user_vars[email];
  $_SESSION[GC_u_style] = $user_vars[style];
  $s[GC_u_email] = $user_vars[email];
  $s[GC_u_password] = $user_vars[password];
  $s[GC_u_name] = $user_vars[name];
  $s[GC_u_style] = $s[GC_style] = $user_vars[style];
}
else
{ if (!$s[user_no_auto]) $approved = 1;
  $joined = $s[cas];
  $password = md5($in[password]);
  if (!$s[user_must_confirm]) { $confirmed = 1; $ip = $s[ip]; }
  dq("insert into $s[pr]users values (NULL,'$in[email]','$password','','$in[name]','$in[nick]','$in[company]','$in[address1]','$in[address2]','$in[address3]','$in[country]','$in[phone1]','$in[phone2]','$in[url]','$in[site_title]','','$in[detail]','$in[user1]','$in[user2]','$in[user3]','$in[showemail]','$in[news1]','$in[news2]','$in[news3]','$in[news4]','$in[news5]','$ip','$joined','$confirmed','$approved','$in[style]','1','0','0','0','0')",1);
  $n = $s[user_n] = mysql_insert_id();
  if ((!$s[user_must_confirm]) AND ($s[i_admin_user_joined]))
  { $in[action] = 'joined';
    $in[n] = mysql_insert_id();
    for ($x=1;$x<=3;$x++) $address[] = $in["address$x"]; $address[] = $in[country]; $in[address] = implode(", ",$address);
    for ($x=1;$x<=3;$x++) $phone[] = $in["phone$x"]; $in[phones] = implode(", ",$phone);
    mail_from_template('user_joined_edited_admin.txt',$in);
  }
  upload_files('u',$n,'',0,1,'','');
  if ($s[user_must_confirm]) return $joined;
}
}

############################################################################

function user_form_control($in) {
global $s,$m;

if (!$s[GC_u_n]) // new user
{ //if ($s[user_one_acc])
  { $q = dq("select count(*) from $s[pr]users where email = '$in[email]'",0);
    $x = mysql_fetch_row($q);
    if ($x[0]) { $s[info] = info_line($m[already_member]); user_login_form(); }
  }
  if ($s[u_v_captcha]) { $x = check_entered_captcha($in[image_control]); if ($x) $problem[] = $x; }
  if ((!trim($in[password])) OR (!eregi("^[a-z0-9]{5,15}$",$in[password]))) $problem[] = $m[w_pass];
//  if (!$in[terms]) $problem[] = 'Please read and agree to the Terms and Conditions';
}
elseif ((trim($in[password])) AND (!eregi("^[a-z0-9]{5,15}$",$in[password]))) $problem[] = $m[w_pass];

if (($s[u_r_name]) AND (!trim($in[name]))) $problem[] = "$m[missing_field] $m[name]";
if (!trim($in[email])) $problem[] = "$m[missing_field] $m[email]";
else
{ if (!check_email($in[email])) $problem[] = $m[w_email];
  $black = try_blacklist($in[email],'email'); if ($black) $problem[] = $black;
}
if (!trim($in[nick])) $problem[] = "$m[missing_field] $m[nick]";

if (($s[u_r_company]) AND (!trim($in[company]))) $problem[] = "$m[missing_field] $m[company]";
if (($s[u_r_address1]) AND (!trim($in[address1]))) $problem[] = "$m[missing_field] $m[address1]";
if (($s[u_r_address2]) AND (!trim($in[address2]))) $problem[] = "$m[missing_field] $m[address2]";
if (($s[u_r_address3]) AND (!trim($in[address3]))) $problem[] = "$m[missing_field] $m[address3]";
if (($s[u_r_country]) AND (!trim($in[country]))) $problem[] = "$m[missing_field] $m[country]";
if (($s[u_r_phone1]) AND (!trim($in[phone1]))) $problem[] = "$m[missing_field] $m[phone1]";
if (($s[u_r_phone2]) AND (!trim($in[phone2]))) $problem[] = "$m[missing_field] $m[phone2]";
if ($s[u_r_site_info])
{ if (!trim($in[url])) $problem[] = "$m[missing_field] $m[url]";
  if (!trim($in[site_title])) $problem[] = "$m[missing_field] $m[site_title]";
}
if ($in[url])
{ $checked_url = check_url($in[url],0); if ($checked_url[1]) $problem[] = $checked_url[1]; else $in[url] = $checked_url[0];
  $black = try_blacklist($in[url],'url'); if ($black) $problem[] = $black;
}
if ($in[site_title])
{ if ($s[r_title]) $in[site_title] = ucwords(my_strtolower($in[site_title]));
  $black = try_blacklist($in[site_title],'word'); if ($black) $problem[] = $black;
}
if (($s[u_r_detail]) AND (!trim(strip_tags($in[detail])))) $problem[] = "$m[missing_field] $m[public_article]";

$in = replace_array_text($in);
$in[detail] = refund_html($in[detail]);

return array($problem,$in);
}

############################################################################

function user_form_get_variables($in) {
global $s,$m;

$x[item_name] = $m[email]; $x[item_name] .= " *"; $x[field_name] = 'email'; $x[field_value] = $in[email]; $x[field_maxlength] = 255; $in[field_email] = parse_part('form_field.txt',$x);
$x[item_name] = $m[password]; $x[item_name] .= " *"; $x[field_name] = 'password'; $x[password] = $in[password]; $x[field_maxlength] = 15; $in[field_password] = parse_part('form_password.txt',$x);
$x[item_name] = $m[nick]; $x[item_name] .= " *"; $x[field_name] = 'nick'; $x[field_value] = $in[nick]; $x[field_maxlength] = 255; $in[field_nick] = parse_part('form_field.txt',$x);
if ($s[u_v_name]) { $x[item_name] = $m[name]; if ($s[u_r_name]) $x[item_name] .= " *"; $x[field_name] = 'name'; $x[field_value] = $in[name]; $x[field_maxlength] = 255; $in[field_name] = parse_part('form_field.txt',$x); }
if ($s[u_v_company]) { $x[item_name] = $m[company]; if ($s[u_r_company]) $x[item_name] .= " *"; $x[field_name] = 'company'; $x[field_value] = $in[company]; $x[field_maxlength] = 255; $in[field_company] = parse_part('form_field.txt',$x); }
 if ($s[u_v_detail]) { $x[item_name] = $m[public_article]; $x[field_name] = 'detail'; $x[field_value] = $in[detail]; $x[field_maxlength] = $s[u_max_detail]; $x[field_maxlength_now] = $s[u_max_detail] - strlen($in[detail]); if ($s[u_details_html_editor]) { $x[html_editor] = get_fckeditor('detail',$in[detail],'./FCKeditor/','PublicToolbar',700,500); $in[field_detail] = parse_part('form_detail_html.txt',$x); } else $in[field_detail] = parse_part('form_detail_textarea.txt',$x); }
if ($s[u_v_address1]) { $x[item_name] = $m[address1]; if ($s[u_r_address1]) $x[item_name] .= " *"; $x[field_name] = 'address1'; $x[field_value] = $in[address1]; $x[field_maxlength] = 255; $in[field_address1] = parse_part('form_field.txt',$x); }
if ($s[u_v_address2]) { $x[item_name] = $m[address2]; if ($s[u_r_address2]) $x[item_name] .= " *"; $x[field_name] = 'address2'; $x[field_value] = $in[address2]; $x[field_maxlength] = 255; $in[field_address2] = parse_part('form_field.txt',$x); }
if ($s[u_v_address3]) { $x[item_name] = $m[address3]; if ($s[u_r_address3]) $x[item_name] .= " *"; $x[field_name] = 'address3'; $x[field_value] = $in[address3]; $x[field_maxlength] = 255; $in[field_address3] = parse_part('form_field.txt',$x); }
if ($s[u_v_country]) { $x[item_name] = $m[country]; if ($s[u_r_country]) $x[item_name] .= " *"; $x[field_name] = 'country'; $x[field_value] = $in[country]; $x[field_maxlength] = 255; $in[field_country] = parse_part('form_field.txt',$x); }
if ($s[u_v_phone1]) { $x[item_name] = $m[phone1]; if ($s[u_r_phone1]) $x[item_name] .= " *"; $x[field_name] = 'phone1'; $x[field_value] = $in[phone1]; $x[field_maxlength] = 255; $in[field_phone1] = parse_part('form_field.txt',$x); }
if ($s[u_v_phone2]) { $x[item_name] = $m[phone2]; if ($s[u_r_phone2]) $x[item_name] .= " *"; $x[field_name] = 'phone2'; $x[field_value] = $in[phone2]; $x[field_maxlength] = 255; $in[field_phone2] = parse_part('form_field.txt',$x); }
if ($s[u_v_site_info])
{ $x[item_name] = $m[url]; if ($s[u_r_site_info]) $x[item_name] .= " *"; $x[field_name] = 'url'; $x[field_value] = $in[url]; $x[field_maxlength] = 255; $in[field_site_info] = parse_part('form_field.txt',$x);
  $x[item_name] = $m[site_title]; if ($s[u_r_site_info]) $x[item_name] .= " *"; $x[field_name] = 'site_title'; $x[field_value] = $in[site_title]; $x[field_maxlength] = 255; $in[field_site_info] .= parse_part('form_field.txt',$x);
}

if ($s[u_v_newsletters])
{ $x[content] = '';
  for ($y=1;$y<=5;$y++)
  { if ($in["news$y"]) $checked = ' checked'; else $checked = '';
    if ($s['news_'.$y]) $x[content] .= '<input type="checkbox" name="news'.$y.'" value="1"'.$checked.'> '.$s['news_'.$y].'<br />';
  }
  $x[item_name] = $m[newsletters];
  $in[field_newsletters] = parse_part('form_no_field.txt',$x);
}
if ($s[u_v_styles])
{ $styles = get_styles_list(0);
  if (!$in[style]) $in[style] = $s[def_style];
  foreach ($styles as $k=>$v)
  { if (is_dir("$s[phppath]/styles/$v"))
    { if ($v==$in[style]) $x = ' selected'; else $x = '';
      $styles .= '<option value="'.$v.'"'.$x.'>'.str_replace('_',' ',$v).'</option>';
    }
  }
  $x[content] = '<select name="style" class="field10">'.$styles.'</select>';
  $x[item_name] = $m[style];
  $in[field_styles] = parse_part('form_no_field.txt',$x);
}
else $in[field_styles] = '<input type="hidden" name="style" value="'.$s[def_style].'">';

list($images) = get_item_files('u',$in[n],0);
if (($s[u_image_small_w_users]) AND ($s[u_image_small_h_users])) { $x[hide_max_size_begin] = '<!--'; $x[hide_max_size_end] = '-->'; }
else { $x[max_image_w] = $s[u_image_max_w_users]; $x[max_image_h] = $s[u_image_max_h_users]; $x[max_image_bytes] = $s[u_image_max_bytes_users]; }
for ($y=1;$y<=$s[u_max_pictures_users];$y++)
{ $x[field_name] = 'image_upload['.$in[n].']['.$y.']';
  $x[image_n] = $y;
  $in[field_pictures] .= parse_part('form_user_picture.txt',$x);
  if (($in[n]) AND ($images[$in[n]][$y][url]))
  { $big_file = preg_replace("/\/$in[n]-/","/$in[n]-big-",$images[$in[n]][$y][url]);
    $x[current_picture] = image_preview_code($images[$in[n]][$y][n],$images[$in[n]][$y][url],$big_file);
    $in[field_pictures] .= parse_part('form_picture_current.txt',$x);
  }
}

return $in;
}

############################################################################

function check_url($url,$check) {
global $s,$m;
if ((eregi("\.", $url)) AND (!eregi("^http://.*", $url))) $url = "http://$url";
if (!eregi("^http://.*", $url)) return array (0,"$m[w_url] $url.");
if (strlen($url) > 255) return array (0,"$m[l_url] $url.");
$parsedurl = parse_url($url);
if ( (!$parsedurl[scheme]) OR (!$parsedurl[host]) ) return array (0,"$m[w_url] $url");
if ($check)
{ $lines = file($url);
  if ( (!$lines) AND (!eregi("^.*/$", $url))  ) $lines = file($url.'/');
  if (!$lines) return array (0,"$m[noconnect_url] $url.");
  foreach ($lines as $k=>$v) $obsah .= $v."\n";
  if (!(strlen($obsah))) return array (0,"$m[noconnect_url] $url.");
  return array ($obsah,0);
}
return array($url,0);
}

############################################################################
############################################################################
############################################################################

function user_login($in) {
global $s,$m;
$s['no_test'] = 1;
$in = replace_array_text($in);
if (!$in[email]) user_login_form($in);
elseif (!$in[password]) user_login_form($in);
if ($s[user_login_captcha]) { $x = check_entered_captcha($in[image_control]); if ($x) { $s[info] = info_line($x); user_login_form($in); } }
$q = dq("select * from $s[pr]users where email = '$in[email]' AND approved = '1'",1);
$user = mysql_fetch_assoc($q);

$password = md5($in[password]);
if ((!$user[n]) OR ($user[password]!=$password) OR (!$user[confirmed]))
{ if ($user[password_temp]==$in[password]) page_from_template('user_using_temporary_password.html',$user);
  $s[info] = info_line($m[w_user]);
  check_if_too_many_logins('users',"$s[pr]users",$in[email],$in[password]);
  user_login_form($in);
}
/*
if (($user[n]) AND (!$user[confirmed]))
{ $s[info] = info_line('Your account was not yet confirmed. <a href="'.$s[site_url].'/user.php?action=resend_confirmation_email&n='.$user[n].'&password='.$in[password].'">Click here to resend the confirmation email.</a>');
  user_login_form($in);
}*/

if ($in[remember_me])
{ setcookie(GC_u_email,$user[email],$s[cas]+31536000); 
  setcookie(GC_u_password,$user[password],$s[cas]+31536000); 
  setcookie(GC_u_name,$user[name],$s[cas]+31536000); 
  setcookie(GC_u_n,$user[n],$s[cas]+31536000);
  setcookie(GC_u_style,$user[style],$s[cas]+31536000);
}
$_SESSION[GC_u_email] = $user[email];
$_SESSION[GC_u_password] = $user[password];
$_SESSION[GC_u_name] = $user[name];
$_SESSION[GC_u_n] = $user[n];
$_SESSION[GC_u_style] = $user[style];
$s[GC_u_email] = $user[email];
$s[GC_u_password] = $user[password];
$s[GC_u_name] = $user[name];
$s[GC_u_n] = $user[n];
$s[GC_u_style] = $s[GC_style] = $user[style];
if ($_SESSION[GC_ad_create]) { $data[c] = $_SESSION[GC_ad_create]; ad_create($data); }
$s[info] = info_line($m[you_are_logged_in]);
user_home_page();
}

#########################################################################

function ad_delete($n) {
global $s,$m;
if (($n) AND ($_SESSION[just_deleted]!=$n))
{ check_ad_owner($n,0);
  delete_ads_process($n);
}
$s[info] = info_line($m[ad_deleted]);
user_home_page();
}

#########################################################################

function using_temporary_password($in) {
global $s;
$in = replace_array_text($in);
$q = dq("select * from $s[pr]users where n = '$in[n]' and password_temp = '$in[password_temp]' AND approved = '1' AND confirmed = '1'",1);
$user = mysql_fetch_assoc($q);
$password = md5($in[password]);
if ($user[n]) dq("update $s[pr]users set password = '$password', password_temp = '' where n = '$in[n]'",1);
$user[password] = $in[password];
user_login_form($user);
}

#########################################################################

function user_log_off() {
global $s;
unset($_SESSION[GC_u_email],$_SESSION[GC_u_password],$_SESSION[GC_u_name],$_SESSION[GC_u_n],$_SESSION[GC_u_style]);
setcookie(GC_u_email,false);
setcookie(GC_u_password,false);
setcookie(GC_u_name,false);
setcookie(GC_u_n,false);
setcookie(GC_u_style,false);
unset($s[GC_u_password],$s[GC_u_name],$s[GC_u_email],$s[GC_u_n],$s[GC_u_style]);
$_SESSION[GC_style] = $s[GC_style];
$s[back] = $_SERVER[HTTP_REFERER];
page_from_template('user_logoff.html',$a);
}

#########################################################################
#########################################################################
#########################################################################

function user_remind($in) {
global $s,$m;
page_from_template('user_remind.html',$in);
}

#########################################################################

function user_reminded($in) {
global $s,$m;
if (!check_email($in[email])) { $in[info] = $m[no_account]; page_from_template('user_remind.html',$in); }
$q = dq("select * from $s[pr]users where email = '$in[email]'",1);
$user = mysql_fetch_assoc($q);
if (!$user[email]) { $in[info] = $m[no_account]; page_from_template('user_remind.html',$in); }
$user[password] = get_random_password($user[n].$user[name]);
dq("update $s[pr]users set password_temp = '$user[password]' where n = '$user[n]'",1);
$user[to] = $user[email];
mail_from_template('user_password_remind.txt',$user);
$s[info] = info_line($m[mail_sent]);
user_login_form();
}

#########################################################################
#########################################################################
#########################################################################

function check_ad_owner($n,$queue) {
global $s,$m;
check_logged_user();
$ad = get_ad_variables($n,$queue);
if ((!$queue) AND (!$ad[n]))
{ $ad = get_ad_variables($n,1);
  //if ($ad[owner]==$s[GC_u_n]) problem($m[queue_not_available]);
}
if ($ad[owner]!=$s[GC_u_n]) problem($m[dont_right_edit]);
return $ad;
}

###############################################################################
###############################################################################
###############################################################################

function user_favorites() {
global $s,$m;

$q = dq("select $s[pr]cats.* from $s[pr]u_favorites,$s[pr]cats where $s[pr]u_favorites.user = '$s[GC_u_n]' and $s[pr]u_favorites.what = 'c' and $s[pr]u_favorites.n = $s[pr]cats.n order by $s[pr]cats.title",1);
while ($item = mysql_fetch_assoc($q))
$a[favorite_categories] .= '<a href="'.category_url('ad',$item[n],$item[alias_of],1,$item[rewrite_url]).'">'.$item[title].'</a>&nbsp;&nbsp;&nbsp;<a href="favorites.php?action=remove&what=c&n='.$item[n].'" title="'.$m[fav_remove].'">x</a><br>';

$q = dq("select $s[pr]ads.* from $s[pr]u_favorites,$s[pr]ads where $s[pr]u_favorites.user = '$s[GC_u_n]' and $s[pr]u_favorites.what = 'ad' and $s[pr]u_favorites.n = $s[pr]ads.n order by $s[pr]ads.title",1);
while ($item = mysql_fetch_assoc($q))
$a[favorite_ads] .= '<a href="'.get_detail_page_url('ad',$item[n],$item[rewrite_url],0).'">'.$item[title].'</a>&nbsp;&nbsp;&nbsp;<a href="favorites.php?action=remove&what=ad&n='.$item[n].'" title="'.$m[fav_remove].'">x</a><br>';

if (!$a[favorite_categories]) $a[favorite_categories] = $m[fav_none].'<br>';
if (!$a[favorite_ads]) $a[favorite_ads] = $m[fav_none].'<br>';
page_from_template('user_favorites.html',$a);
}


###############################################################################

function user_joined_or_edited($form) {
global $s,$m;
for ($x=1;$x<=5;$x++) if (($s['news_'.$x]) AND ($form['news'.$x])) $form[newsletters] .= $s['news_'.$x].'<br />';
if ($s[LUG_u_n])
{ $s[info] = info_line($m[data_saved]);
  user_edit();
}
else page_from_template('user_joined.html',$form);
}

###############################################################################
###############################################################################
###############################################################################

?>