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
get_messages('comment.php');
include("$s[phppath]/data/data_forms.php");
if (!$_POST) $_POST = $_GET; unset($_POST[vars]);//new ajax
if ($_POST[action]=='comment_entered') comment_entered($_POST);
elseif ($_GET[n]) comments_show($_GET);
exit;

############################################################################
############################################################################
############################################################################

function comments_show($in) {
global $s,$m;
if ($s[charset]!='UTF-8') foreach ($in as $k=>$v) $in[$k] = iconv('UTF-8',$s[charset],$v);
$in = replace_array_text($in);
echo comments_get($in[n],0);
exit;
}

############################################################################

function comment_problem($n,$info) {
global $s,$m;
if ($s[charset]!='UTF-8') $info = iconv('UTF-8',$s[charset],$info);
echo '<br>'.info_line($info,'<br><a href="#a_comments_show" onclick="javascript:parse_ajax_request(document.getElementById(\'comments_show_form'.$n.'\'),\''.$s[site_url].'/comments.php?&n='.$n.'\',\'comments_show_box'.$n.'\'); check_show_hide_div(\'comments_show_box'.$n.'\'); show_hide_div_id(0,\'enter_comment_box'.$n.'\');">'.$m[show_comments].'</a>');
exit;
}

############################################################################

function comment_entered($in) {
global $s,$m;
if ($s[charset]!='UTF-8') foreach ($in as $k=>$v) $in[$k] = iconv('UTF-8',$s[charset],$v);
$x = comment_form_control($in);
$in = $x[1];
if ($x[0])
{ $a = enter_comment_box($in[n],info_line($m[errorsfound],implode('<br />',$x[0])));
  if ($s[charset]!='UTF-8') $a = iconv($s[charset],'UTF-8',$a);
  echo stripslashes($a);
  exit;
}
$in[comment_n] = write_to_db($in);
$in = replace_array_text($in);
//if ($s[l_i_new])
{ $in[ip] = $s[ip];
  $in[to] = $in[from] = $s[mail];
  $ad = get_ad_variables($in[n]);
  $in[url] = get_detail_page_url('ad',$ad[n],$ad[rewrite_url],$ad[category]);
  mail_from_template('comment_added_admin.txt',$in);
  if ($ad[email]) { $in[to] = $ad[email]; mail_from_template('comment_added_owner.txt',$in); }
}

$a = info_line($m[comment_entered],'<br><a href="#a_comments_show" onclick="javascript:parse_ajax_request(document.getElementById(\'comments_show_form'.$in[n].'\'),\''.$s[site_url].'/comments.php?n='.$in[n].'\',\'comments_show_box'.$in[n].'\'); check_show_hide_div(\'comments_show_box'.$in[n].'\'); show_hide_div_id(0,\'enter_comment_box'.$in[n].'\');">'.$m[show_comments].'</a>');
//if ($s[charset]!='UTF-8') $a = iconv($s[charset],'UTF-8',$a);
echo "<br>$a";
exit;
}

############################################################################

function comment_form_control($in) {
global $s,$m;
$a[url] = get_detail_page_url($in[n],$a[rewrite_url],0,1);
$in = array_merge((array)$a,(array)$in);
//foreach ($in as $k=>$v) $in[$k] = utf8_decode($v);
//foreach ($in as $k=>$v) $in[$k] = iconv('UTF-8',$s[charset],$v);

if ($s[GC_u_n])
{ $user = get_user_variables($s[GC_u_n]);
  $in[name] = $user[name]; $in[email] = $user[email];
}
elseif ($s[register_com]) comment_problem($in[n],$m[no_logged]);

if ($s[comm_v_captcha]) { $x = check_entered_captcha($in[image_control]); if ($x) $problem[] = $x; }
 
if (!trim($in[comment])) $problem[] = $m[m_comment];
elseif (strlen($in[comment])>$s[m_comment]) $problem[] = "$m[l_comment] $s[m_comment] $m[characters].";
$black = try_blacklist($in[comment],'word'); if ($black) $problem[] = $black;

if (($s[comm_r_name]) AND (!trim($in[name]))) $problem[] = "$m[missing_field] $m[name]";
elseif (strlen($in[name])>255) $problem[] = $m[l_name];

if (($s[comm_r_email]) AND (!trim($in[email]))) $problem[] = "$m[missing_field] $m[email]";
elseif (strlen($in[email])>255) $problem[] = $m[l_email];
elseif (($in[email]) AND (!check_email($in[email]))) $problem[] = $m[w_email];
if (try_blacklist($in[email],'email')) $problem[] = $black;

$in = replace_array_text($in);
if (($s[com_duplicate]) AND (!$problem))
{ $x = check_duplicate($in[email],$in[n]);
  if ($x) $problem[] = $x;
}
return array($problem,$in);
}

############################################################################

function check_duplicate($email,$n) {
global $s,$m;
if ($s[GC_u_n])
{ $q = dq("select count(*) from $s[pr]comments where user = '$s[GC_u_username]' AND item_no = '$n'",1);
  $x = mysql_fetch_row($q);
}
else
{ $q = dq("select count(*) from $s[pr]comments where (email = '$email' OR ip = '$s[ip]') AND item_no = '$n'",1);
  $x = mysql_fetch_row($q);
}
if ($x[0]) comment_problem($n,$m[com_dupl]);
if ($_COOKIE[comment_c][$n]) problem ($m[com_dupl]);
setcookie ("comment_c[$n]",$s[cas],$s[cas]+31536000);
return false;
}

############################################################################

function write_to_db($form) {
global $s,$m;
dq("insert into $s[pr]comments values (NULL,'$form[comtitle]','$form[comment]','$form[n]','$form[name]','$form[email]','$s[cas]','$s[ip]','$s[com_autoapr]','$s[GC_u_username]')",0);
$cislo = mysql_insert_id();
if ($s[com_autoapr])
{ $q = dq("select count(*) from $s[pr]comments where item_no = '$form[n]' AND approved = '1'",0);
  $x = mysql_fetch_row($q);
  dq("update $s[pr]ads set comments = '$x[0]' where n = '$form[n]'",1);
}
if ($s[GC_u_n])
{ $q = dq("select count(*) from $s[pr]comments where user = '$s[GC_u_username]'",1);
  $data = mysql_fetch_row($q);
  //for ($x=0;$x<=4;$x++)
  //if (($data[0]>=$s['u_rank_f_'.$x]) AND ($data[0]<=$s['u_rank_t_'.$x])) { $rank = $x; break; }
  //dq("update $s[pr]users set rank = '$rank', reviews = '$data[0]' where n = '$s[GC_u_n]'",1);
}

return $cislo;
}

############################################################################
############################################################################
############################################################################

?>