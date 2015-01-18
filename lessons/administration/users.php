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
include("$s[phppath]/data/data_forms.php");
include("$s[phppath]/data/newsletter.php");

switch ($_GET[action]) {
case 'users_home'			: users_home();
case 'user_create'			: user_create_edit(0);
case 'user_edit'			: user_create_edit($_GET[user]);
case 'user_delete'			: user_delete($_GET[user]);
case 'users_searched'		: users_searched($_GET);
case 'newsletter'			: newsletter(0);
case 'email_users'			: email_users(0);
case 'users_unapproved_show': users_unapproved_show($_GET);
}
switch ($_POST[action]) {
case 'user_created'			: user_created($_POST);
case 'user_edited'			: user_edited($_POST);
case 'emailed_users'		: emailed_users($_POST);
case 'users_approved'		: users_approved($_POST);
case 'users_approved'		: users_approved($_POST);
case 'newsletter'			: newsletter($_POST);
}

#################################################################################
#################################################################################
#################################################################################

function users_home() {
global $s;
check_admin('users');
ih();
echo $s[info];
echo page_title('Users');
$q = dq("select count(*) from $s[pr]users where confirmed = '1' and approved = '0'",0);
$pocet = mysql_fetch_row($q); $pocet = $pocet[0];
if ($pocet)
{ echo '<table border=0 width=700 cellspacing=0 cellpadding=2 class="common_table">
  <tr><td class="common_table_top_cell">Unapproved users in the queue: '.$pocet.'</td></tr>
  <tr><td align="center">Select number of users to display on a single page<br>
  <form method="get" action="users.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="users_unapproved_show">
  <select name="perpage" class="field10"><option value="0">All</option>';
  if ($pocet>5) echo '<option value="5">5</option>';
  if ($pocet>10) echo '<option value="10">10</option>';
  if ($pocet>20) echo '<option value="20">20</option>';
  if ($pocet>30) echo '<option value="30">30</option>';
  echo '</select> 
  <input type="submit" value="Submit" name="B1" class="button10">
  </form>
  </td></tr></table><br>';
}
?>
<form action="users.php" method="get"><?PHP echo check_field_create('admin') ?>
<input type="hidden" name="action" value="users_searched">
<table border=0 width=700 cellspacing="0" cellpadding="0" class="common_table">
<tr><td class="common_table_top_cell">Search for Users</td></tr>
<tr><td align="center">
<table border=0 cellspacing="0" width="100%" cellpadding="0" class="inside_table">
<tr>
<td align="left" nowrap>Number</td>
<td align="left"><input class="field10" name="n"maxlength=15 style="width:100"></td>
</tr>
<tr>
<td align="left" nowrap>Email contains </td>
<td align="left"><input class="field10" name="email" style="width:550px" maxlength=100></td>
</tr>
<tr>
<td align="left" nowrap>Any field contains </td>
<td align="left"><input class="field10" name="any" style="width:550px" maxlength=100></td>
</tr>
<tr>
<td align="left" nowrap>Name contains </td>
<td align="left"><input class="field10" name="name" style="width:550px" maxlength=100></td>
</tr>
<?PHP
if ($s[post_ads_who]==2)
{ echo '<tr>
  <td align="left" nowrap>Can post classifieds</td>
  <td align="left" nowrap>N/A<input type="radio" value="0" name="post_ads" checked> Yes<input type="radio" value="yes" name="post_ads"> No<input type="radio" value="no" name="post_ads"></td>
  </tr>';
}
echo '<tr>
<td align="left" nowrap>Number of classifieds </td>
<td align="left"><select class="field10" name="number_ads_select"><option value="more_than">>=</option><option value="less_than"><=</option></select> <input class="field10" name="number_ads" size=5 maxlength=5></td>
</tr>
<tr>
<td align="left" nowrap>Type of search </td>
<td align="left" nowrap>AND<input type="radio" value="and" name="boolean" checked> OR<input  type="radio" value="or" name="boolean"></td>
</tr>
<tr>
<td align="left" nowrap>Results per page </td>
<td align="left"><select class="field10" name="perpage"><option value="0">All</option><option value="10">10</option><option value="20">20</option><option value="50">50</option><option value="100">100</option><option value="200">200</option><option value="500">500</option></select></td>
</tr>
<tr><td align="left" nowrap>Sort by </td>
<td align="left">
<select class="field10" name="sort"><option value="email">Email</option><option value="name">Name</option><option value="joined">Date joined</option><option value="ads">Number of classifieds</option></select>
<select class="field10" name="order"><option value="asc">Ascending</option><option value="desc">Descending</option></select>
</td></tr>
<tr><td align="center" colspan=2><input type="submit" name="submit" value="Search" class="button10"></td></tr>
</table>
</td></tr></table></form>';
ift();
}


#################################################################################

function users_searched($in) {
global $s;
check_admin('users');

//foreach ($in as $k=>$v) echo "$k - $v<br>";

if ($in[n]) $where = "where n = $in[n]";
else
{ if ($in[any]) $w[] = "(email like '%$in[any]%' OR name like '%$in[any]%')";
  if ($in[email]) $w[] = " email like '%$in[email]%'";
  if ($in[name]) $w[] = " name like '%$in[name]%'";
  if ($in[newsletter]) $w[] = " news$in[newsletter] = 1 ";
  if ($in[number_ads]) { if ($in[number_ads_select]=='more_than') $more_less = '>='; elseif ($in[number_ads_select]=='less_than') $more_less = '<='; $w[] = " ads $more_less '$in[number_ads]'"; }
  if ($in[post_ads]=='yes') $w[] = ' post_ads = 1 '; elseif ($in[post_ads]=='no') $w[] = ' post_ads = 0 ';
  if ($w) $where = '('.implode(" $in[boolean] ",$w).')';
  if ($where) $where .= " AND approved = '1' and confirmed = '1'"; else $where = "approved = '1' and confirmed = '1'";
  $where = 'where '.$where;
}

//echo $where;

if (!$in[from]) $in[from] = 0; else $in[from] = $in[from] - 1;
if ($in[perpage]) $limit = " limit $in[from],$in[perpage]";

$x = dq("select count(*) from $s[pr]users $where",0);
$pocet = mysql_fetch_row($x); $pocet = $pocet[0];

if ($in[sort]) $orderby = "order by $in[sort]";
$q = dq("select * from $s[pr]users $where $orderby $in[order] $limit",1); 

ih();

if ( ($in[perpage]) AND ($pocet>$in[perpage]) )
{ $rozcesti = '<form action="users.php" method="get" name="form1">'.check_field_create('admin').'<input type="hidden" name="action" value="users_searched">';
  foreach ($in as $k => $v)
  { if ($v) $rozcesti .= '<input type="hidden" name="'.$k.'" value="'.$v.'">'; }
  $rozcesti .= 'Show users with begin of <select class="field10" name="from"><option value="1">1</option>';
  $y = ceil($pocet/$in[perpage]);  
  for ($x=1;$x<$y;$x++)
  { $od = $x*$in[perpage]+1; $rozcesti .= '<option value="'.$od.'">'.$od.'</option>'; }
  $rozcesti .= '</select>&nbsp;<input type="submit" value="Submit" name="B1" class="button10"></form>';
}

$od = $in[from]+1;
$do = $in[from]+$in[perpage]; if ($do>$pocet) $do = $pocet; if (!$in[perpage]) $do = $pocet;

echo $s[info];
echo info_line("Users Found: $pocet");
if ( ($pocet>1) AND ($od!=$do) ) echo "Showing Users $od - $do</b><br><br>\n$rozcesti";
else echo '<br><br>';

$in[returnto] = 'search'; $in[from] = $in[from] + 1;
while ($x = mysql_fetch_assoc($q)) show_one_user($x,$in);
ift();
}

#################################################################################

function user_created($in) {
global $s;
check_admin('users');
$in = $in[user][0];
$in = replace_array_text($in);
//$q = dq("select count(*) from $s[pr]users where email = '$in[email]'",1); $x = mysql_fetch_row($q); if ($x[0]) problem('Entered email is already in use.');
$in[detail] = refund_html($in[detail]);
$password = md5($in[password]);
dq("insert into $s[pr]users values(NULL,'$in[email]','$password','','$in[name]','$in[nick]','$in[company]','$in[address1]','$in[address2]','$in[address3]','$in[country]','$in[phone1]','$in[phone2]','$in[url]','$in[site_title]','','$in[detail]','$in[user1]','$in[user2]','$in[user3]','$in[showemail]','$in[news_1]','$in[news_2]','$in[news_3]','$in[news_4]','$in[news_5]','1','$s[cas]','1','$in[approved]','$in[style]','$in[post_ads]','0','0','0','0')",1);
$n = mysql_insert_id();
upload_files('u',$n,'',0,0,'');
ih();
echo info_line('User Created');
show_one_user($n);
ift();
}

#################################################################################

function show_one_user($data) {
global $s;
check_admin('users');
if (!is_array($data)) $data = get_user_variables($data);

$data = stripslashes_array($data);
$joined = datum ($data[joined],1); 
for ($x=1;$x<=5;$x++) if (($s["news_$x"]) AND ($data["news$x"])) $data[newsletters] .= $s["news_$x"].'<br />'; if (!$data[newsletters]) $data[newsletters] = '&nbsp';
if ($data[approved]) $data[approved] = 'Yes'; else $data[approved] = 'No';
if ($data[post_ads]) $post_ads = 'Yes'; else $post_ads = 'No';

echo '<table border=0 width="700" cellspacing="0" cellpadding="0" class="common_table">
<tr><td align="center">
<table border=0 width="100%" cellspacing="0" cellpadding="0" class="inside_table">
<tr>
<td align="left" nowrap width="100">Number</td>
<td align="left" width="400">'.$data[n].'&nbsp;</td>
</tr>
<tr>
<td align="left" nowrap>Email</td>
<td align="left"><a href="mailto:'.$data[email].'">'.$data[email].'</a>&nbsp;</td>
</tr>
<tr>
<td align="left" nowrap>Name</td>
<td align="left">'.$data[name].'&nbsp;</td>
</tr>
<tr>
<td align="left" nowrap>Address line 1</td>
<td align="left">'.$data[address1].'&nbsp;</td>
</tr>
<tr>
<td align="left" nowrap>Address line 2</td>
<td align="left">'.$data[address2].'&nbsp;</td>
</tr>
<tr>
<td align="left" nowrap>Address line 3</td>
<td align="left">'.$data[address3].'&nbsp;</td>
</tr>
<tr>
<td align="left" nowrap>Country </td>
<td align="left">'.$data[country].'&nbsp;</td>
</tr>
<tr>
<td align="left" nowrap>Phone 1</td>
<td align="left">'.$data[phone1].'&nbsp;</td>
</tr>
<tr>
<td align="left" nowrap>Phone 2</td>
<td align="left">'.$data[phone2].'&nbsp;</td>
</tr>
<tr>
<td align="left">Site URL</td>
<td align="left"><a target="_blank" href="'.$data[url].'">'.$data[url].'</a>&nbsp;</td>
</tr>';
if ($s[post_ads_who]==2)
{ echo '<tr>
  <td align="left" nowrap>Can post classifieds&nbsp;</td>
  <td align="left">'.$post_ads.'&nbsp;</td>
  </tr>';
}
if (($s[post_ads_who]<2) OR ($data[post_ads]))
echo '<tr>
<td align="left">Classifieds </td>
<td align="left"><a target="_blank" href="ads_list.php?action=ads_searched&owner='.$data[n].'&sort=title&order=asc&showtext=on">'.$data[ads].'</a>&nbsp;</td>
</tr>';
echo '<tr>
<td align="left" valign="top" nowrap>Joined Newsletters</td>
<td align="left">'.$data[newsletters].'&nbsp;</td>
</tr>
<tr>
<td align="left" nowrap>IP</td>
<td align="left">'.$data[ip].'&nbsp;</td>
</tr>
<tr>
<td align="left" nowrap>Joined </td>
<td align="left">'.datum($data[joined],1).'&nbsp;</td>
</tr>
<tr>
<td align="left" colspan=2 nowrap>
[<a target="_self" href="users.php?action=user_edit&user='.$data[n].'">Edit this user</a>]&nbsp;
[<a target="_self" href="orders_payments.php?action=orders_searched&user='.$data[n].'">Payments</a>]&nbsp;
[<a target="_self" href="javascript: go_to_delete(\'Are you sure?\',\'users.php?action=user_delete&user='.$data[n].'\')">Delete this user</a>]
</td>
</tr></table>
</td></tr></table>
<br>';
}

#################################################################################

function user_create_edit($n) {
global $s;
check_admin('users');

ih();
echo $s[info];

if ($_GET[action]) $current_action = $_GET[action]; else $current_action = $_POST[action];
if ($current_action != 'user_create') $user = get_user_variables($n);
else { $user[n] = 0; echo page_title('Users'); }

$user[current_action] = $current_action;
switch ($current_action) {
case 'user_create'	: $action = 'user_created'; $info = 'Create a New User'; break;
case 'user_edit'	: $action = 'user_edited'; $info = 'Edit Selected User'; break;
case 'user_edited'	: $action = 'user_edited'; $info = 'Edit Selected User'; break;
}

echo '<form enctype="multipart/form-data" action="users.php" method="post">'.check_field_create('admin').'<input type="hidden" name="action" value="'.$action.'">
<table border=0 width=700 cellspacing=0 cellpadding=0 class="common_table">
<tr><td class="common_table_top_cell">'.$info.'</td></tr>
<tr><td align="center">';
user_create_edit_form($user);
echo '</td></tr></table>
<br>
<input type="submit" name="co" value="Save" class="button10"></form>';
ift();

$joined = datum ($data[joined],1); 

}

#################################################################################

function user_create_edit_form($user) {
global $s;
check_admin('users');
$user = stripslashes_array($user);
if ($user[picture]) $current_picture = '<img src="'.$user[picture].'">';
else $current_picture = '';
$joined = datum ($user[joined],1);
$n = $user[n];
//foreach ($user as $k=>$v) echo "$k - $v<br>";

echo '<table border=0 width="100%" cellspacing="0" cellpadding="0" class="inside_table">
<input type="hidden" name="user['.$n.'][n]" value="'.$n.'">';
if (($_GET[action]=='users_unapproved_show') OR ($_POST[action]=='users_approved'))
{ echo '<tr><td align="left" colspan="2" nowrap>
  Approve it <input type="radio" name="user['.$n.'][approve]" value="yes" id="approve_'.$n.'">
  <a class="link10" href="#" onClick="uncheck_both('.$n.'); return false;">Uncheck these boxes</a><br>
  Reject it <input type="radio" name="user['.$n.'][approve]" value="no" id="reject_'.$n.'">
  and send email: <select class="field10" name="user['.$n.'][reject_email]">'.$user[reject_emails].'
  </select>
  or <input type="checkbox" name="user['.$n.'][reject_email_custom]" value="1" id="fullcust'.$n.'" onclick="show_hide_div(document.getElementById(\'fullcust'.$n.'\').checked,document.getElementById(\'test'.$n.'\'));" value="1"> Individual Message
  <tr><td align="left" colspan="2">
  <div id="test'.$n.'" style="display:none;">
  <table border=0 width=100% cellspacing=0 cellpadding=0>
  <tr>
  <td align="left">Subject</td>
  <td><input class="field10" name="user['.$n.'][email_subject]" size="70"></td>
  </tr>
  <tr>
  <td align="left" valign="top">Text<br /><span class="text10">Available variables:<br />#%email%# - User Email<br />#%name%# - Name<br /></span></td>
  <td><textarea class="field10" name="user['.$n.'][email_text]" rows="10" cols="70"></textarea></td>
  </tr>
  </table></DIV>
  </td></tr>';
}

echo '<tr>
<td align="left">Email </td>
<td align="left"><input class="field10" name="user['.$n.'][email]" style="width:550px" maxlength=255 value="'.$user[email].'"></td>
</tr>';
if ($user[current_action]=='user_create')
echo '<tr>
<td align="left">Password </td>
<td align="left"><input class="field10" name="user['.$n.'][password]" size="15" maxlength="15" value="'.$user[password].'"></td>
</tr>';
else echo '<tr>
<td align="left">Password </td>
<td align="left"><input class="field10" name="user['.$n.'][password]" size="15" maxlength="15" value=""><span class="text10"> Let blank to keep current password</span></td>
</tr>';
echo '<tr>
<td align="left">Name </td>
<td align="left"><input class="field10" name="user['.$n.'][name]" style="width:550px" maxlength=255 value="'.$user[name].'"></td>
</tr>
<tr>
<td align="left">Nick name </td>
<td align="left"><input class="field10" name="user['.$n.'][nick]" style="width:550px" maxlength=255 value="'.$user[nick].'"></td>
</tr>
<tr>
<td align="left">Company </td>
<td align="left"><input class="field10" name="user['.$n.'][company]" style="width:550px" maxlength=255 value="'.$user[company].'"></td>
</tr>
<tr>
<td align="left">Address 1 </td>
<td align="left"><input class="field10" name="user['.$n.'][address1]" style="width:550px" maxlength=255 value="'.$user[address1].'"></td>
</tr>
<tr>
<td align="left">Address 2 </td>
<td align="left"><input class="field10" name="user['.$n.'][address2]" style="width:550px" maxlength=255 value="'.$user[address2].'"></td>
</tr>
<tr>
<td align="left">Address 3 </td>
<td align="left"><input class="field10" name="user['.$n.'][address3]" style="width:550px" maxlength=255 value="'.$user[address3].'"></td>
</tr>
<tr>
<td align="left">Country </td>
<td align="left"><input class="field10" name="user['.$n.'][country]" style="width:550px" maxlength=255 value="'.$user[country].'"></td>
</tr>
<tr>
<td align="left">Phones </td>
<td align="left"><input class="field10" name="user['.$n.'][phone1]" style="width:550px" maxlength=255 value="'.$user[phone1].'"></td>
</tr>
<tr>
<td align="left">&nbsp; </td>
<td align="left"><input class="field10" name="user['.$n.'][phone2]" style="width:550px" maxlength=255 value="'.$user[phone2].'"></td>
</tr>
<tr>
<td align="left">Style </td>
<td align="left"><select name="user['.$n.'][style]" class="field10">'; echo styles_select_box($user[style]); echo '</select></td>
</tr>
<tr>
<td align="left" valign="top">Newsletters </td>
<td align="left">';
for ($x=1;$x<=5;$x++)
{ if ($user["news$x"]) $checked = ' checked'; else $checked = '';
  if ($s["news_$x"]) echo '<input type="checkbox" name="user['.$n.'][news_'.$x.']" value="1"'.$checked.'>'.$s["news_$x"].'<br />';
}
echo '</td>
</tr>
';
echo images_form_admin('u',$user,0);

echo '<tr>
<td align="left"><a target="nove" href="'.$user[url].'">URL</a> </td>
<td align="left"><input class="field10" name="user['.$n.'][url]" style="width:550px" maxlength=255 value="'.$user[url].'"></td>
</tr>
<tr>
<td align="left">Site title</td>
<td align="left"><input class="field10" name="user['.$n.'][site_title]" style="width:550px" maxlength=255 value="'.$user[site_title].'"></td>
</tr>
<tr>
<td nowrap align="left" valign="top" colspan="2">Public article </td>
</tr>
<tr>
<td nowrap align="left" valign="top" colspan="2">'.get_fckeditor('user['.$n.'][detail]',$user[detail],'AdminToolbar').'</td>
</tr>
';
    
if ($s[post_ads_who]==2)
{ echo '<tr>
  <td align="left" nowrap>Can submit classifieds </td>
  <td align="left"><input type="checkbox" name="user['.$n.'][post_ads]" value="1"'; if ($user[post_ads]) echo ' checked'; echo '></td>
  </tr>';
}
if ((!$user[n]) OR ($user[approved]))
echo '<tr>
<td align="left">Approved by admin </td>
<td align="left" nowrap><input type="checkbox" name="user['.$n.'][approved]" value="1" checked></td>
</tr>';

echo '</table>';
}

#################################################################################

function user_edited($in) {
global $s;
check_admin('users');
foreach ($in[user] as $k=>$v) { $user = $v; $user[n] = $k; user_edited_process($user); }
$s[info] = info_line('Selected user has been updated');
user_create_edit($user[n]);
}

######################################################################################

function user_edited_process($in) {
global $s;
$old = get_user_variables($in[n],'');
if ($in[joined]) $joined = ', joined = '.get_timestamp($in[joined][d],$in[joined][m],$in[joined][y],'end');
if ($in[password]) $password = "password = '".md5($in[password])."', ";
$in = replace_array_text($in);
$in[detail] = refund_html($in[detail]);
dq("update $s[pr]users set $password approved = '$in[approved]', email = '$in[email]', name = '$in[name]', nick = '$in[nick]', url = '$in[url]', site_title = '$in[site_title]', showemail = '$in[showemail]', news1 = '$in[news_1]', news2 = '$in[news_2]', news3 = '$in[news_3]', news4 = '$in[news_4]', news5 = '$in[news_5]', style = '$in[style]',  post_ads = '$in[post_ads]', company = '$in[company]', detail = '$in[detail]', address1 = '$in[address1]', address2 = '$in[address2]', address3 = '$in[address3]', phone1 = '$in[phone1]', phone2 = '$in[phone2]', country = '$in[country]' where n = '$in[n]'",1);
upload_files('u',$in[n],'',0,0,$in[delete_image],'');
update_items_for_user($in[n]);
}

######################################################################################
######################################################################################
######################################################################################

function user_delete($n) {
global $s;
check_admin('users');
user_delete_process($n);
}

#################################################################################

function user_delete_process($n) {
global $s;
$q = dq("select * from $s[pr]ads where owner = '$n'",1);
while ($x = mysql_fetch_assoc($q)) $ads_list[] = $x[n];
if ($ads_list) delete_ads_process($ads_list);
dq("delete from $s[pr]users where n = '$n'",1);
dq("delete from $s[pr]u_favorites where user = '$n'",1);
dq("delete from $s[pr]u_private_notes where user = '$n'",1);
}

#################################################################################

function styles_select_box($selected) {
global $s;
$styles = get_styles_list(0);
foreach ($styles as $k=>$v)
{ if ($v==$selected) $x = ' selected'; else $x = '';
  $a .= '<option value="'.$v.'"'.$x.'>'.$v.'</option>';
}
return $a;
}

#################################################################################
#################################################################################
#################################################################################

function newsletter_form($data) {
global $s;
check_admin('email_users');
ih();
echo $s[info];
if ($data[text]) $text = $data[text];
else $text = join ('',file("$s[phppath]/styles/_common/email_templates/newsletter.txt"));
$data[subject] = stripslashes($data[subject]); $text = stripslashes($text);
if (!$data[days]) $days = 7; else $days = $data[days];
?>
<form action="users.php" method="post"><?PHP echo check_field_create('admin') ?>
<input type="hidden" name="action" value="newsletter">
<table border="0" width="700" cellspacing="0" cellpadding="0" class="common_table">
<tr><td class="common_table_top_cell">Newsletter</td></tr>
<tr><td align="center" width="100%">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="left">&nbsp;Send it to subscribers of newsletter <br />
<?PHP 
if (!$_POST[newsletter]) $_POST[newsletter] = 1;
for ($x=1;$x<=5;$x++)
{ if ($s["news_$x"])
  { if ($s["send_newsletter_$x"]) $last = datum($s["send_newsletter_$x"],1); else $last = 'Never yet';
    echo '<input type="radio" name="newsletter" value="'.$x.'"'; if ($_POST[newsletter]==$x) echo ' checked'; echo '>'.$s["news_$x"].' <span class="text10">Sent last time: '.$last.'</span><br />'; }
}
?>
</td></tr>
<tr><td align="left">
The following variables may be used in the text field:<br />
<b>#%name%#</b> for name of the user<br />
<b>#%classifieds%#</b> for the list of classifieds created in last days (select below how many days should be included)<br />
<b>#%unsubscribe%#</b> for the URL where the user can modify his/her profile<br />
</td></tr>
<tr><td align="left"> 
Replace the variable #%classifieds%# with classifieds created in the last <input class="field10" type="text" size="2" name="days" value="<?PHP echo $days; ?>"> days</td></tr>
<tr><td align="left">Subject: <input class="field10" type="text" style="width:550px" name="subject" value="<?PHP echo $data[subject]; ?>"></td></tr>
<tr><td align="left">Text:<br /><textarea class="field10" style="width:700px" rows=20 name="text"><?PHP echo $text; ?></textarea></td></tr>
<tr><td align="left"> Message format &nbsp;&nbsp;&nbsp;<input type="radio" name="htmlmail" value="0" checked> Text &nbsp;&nbsp;&nbsp;<input type="radio" name="htmlmail" value="1"> HTML</td></tr>
<tr><td align="left"> 
<input type="radio" value="preview" name="what" checked> Show preview<br />
<input type="radio" value="send" name="what"> Send now<br />
<input type="radio" value="test" name="what"> Send only a test email to <input class="field10" type="text" style="width:550px" name="test_email" value="<?PHP echo $s[mail]; ?>"></td></tr>
<tr><td align="center"><input type=submit name=x value="Submit" class="button10"></td></tr>
</table>
</td></tr></table>
</form><br />
<?PHP
ift();
}

##############################################################################

function newsletter($data) {
global $s;
check_admin('email_users');
if ((!$data[text]) OR (!$data[subject]))     
{ if (($data[text]) OR ($data[subject])) $s[info] = info_line('Both fields are required.');
  newsletter_form($data);
}
if (!$data[days]) $data[days] = 7; $cas = ($s[cas] - $data[days] * 86400);

$where = get_where_fixed_part(0,'',0,'',$s[cas]);
$q = dq("select * from $s[pr]ads where created > '$cas' and $where order by created desc",1);
while ($item = mysql_fetch_assoc($q))
{ $item[created] = datum ($item[created],0);
  $item[url] = get_detail_page_url('a',$item[n],$item[rewrite_url],0,1);
  $cat = explode(' ',str_replace('_','',$item[c]));
  $b[classifieds] .= parse_part_of_email('newsletter_item.txt',$item);
}

if ($data[what] == 'preview')
{ $data[text] = str_replace("#%classifieds%#",$b[classifieds],$data[text]);
  $data = replace_array_text($data);
  newsletter_form($data);
  exit;
}
if ($data[what]=='test')
{ $line = $data[text]; $data[subject] = unreplace_once_html($data[subject]);
  $value[classifieds] = $b[classifieds];
  $value[name] = $address[1]; $value[email] = $data[test_email];
  $value[unsubscribe] = "$s[site_url]/user.php?action=user_login";
  foreach($value as $k => $v) $line = str_replace("#%$k%#",$v,$line);
  $line = unreplace_once_html($line);
  $line = unhtmlentities($line); $data[subject] = unhtmlentities($data[subject]);
  my_send_mail('','',$data[test_email],$data[htmlmail],$data[subject],$line,1);
  $s[info] = info_line('Test email has been sent to '.$data[test_email]);
  newsletter_form($data);
}
elseif ($data[what]=='send')
{ $emaily = dq("select * from $s[pr]users where news$data[newsletter] = '1' and approved = '1' and confirmed = '1'",1);
  $num_rows = mysql_num_rows($emaily);
  if (!$num_rows) { ih(); echo info_line('There are no subscribers of newsletter #'.$data[newsletter]); ift(); }
  ih();
  echo info_line('Newsletter has been sent to:');
  while ($address = mysql_fetch_assoc($emaily))
  { $line = $data[text]; $data[subject] = unreplace_once_html($data[subject]);
    $value[classifieds] = $b[classifieds];
    $value[name] = $address[name]; $value[email] = $address[email];
    $value[unsubscribe] = "$s[site_url]/user.php?action=user_login";
    foreach($value as $k => $v) $line = str_replace("#%$k%#",$v,$line);
    $line = unreplace_once_html($line);
    $line = unhtmlentities($line); $data[subject] = unhtmlentities($data[subject]);
    my_send_mail('','',$address[email],$data[htmlmail],$data[subject],$line,1);
    echo "$address[email]<br />\n";
  }
  unset($data);
  $fp = fopen("$s[phppath]/data/newsletter.php","w");
  $s["send_newsletter_$data[newsletter]"] = $s[cas];
  for ($x=1;$x<=5;$x++) { if (!$s["send_newsletter_$x"]) $s["send_newsletter_$x"] = 0; $data .= '$s[send_newsletter_'.$x.'] = '.$s["send_newsletter_$x"].';'; }
  fwrite ($fp,'<?PHP '.$data.' ?>');
  fclose($fp);
  chmod("$s[phppath]/data/newsletter.php",0666);

  ift();
}
exit;
}

##############################################################################
##############################################################################
##############################################################################

function email_users($data) {
global $s;
check_admin('email_users');
$data = replace_array_text($data);
ih();
echo $s[info];
echo page_title('Users'); 
?>
<form action="users.php" method="post"><?PHP echo check_field_create('admin') ?>
<input type="hidden" name="action" value="emailed_users">
<table border="0" width="700" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Email All Registered Users</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="left" colspan="2">  
The following variable can be used in the text:<br>
#%name%# for name of the user<br>
</td></tr>
<tr>
<td align="left" valign="top">Subject</td>
<td align="left" valign="top"><input class="field10" type="text" name="subject" value="<?PHP echo $data[subject]; ?>" style="width:450px"></td>
</tr>
<tr>
<td align="left" valign="top">Text</td>
<td align="left" valign="top"><textarea class="field10" rows=20 name="text" style="width:450px"><?PHP echo $data[text]; ?></textarea></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Message format </td>
<td align="left" valign="top" nowrap><input type="radio" name="htmlmail" value="0" checked> Text &nbsp;&nbsp;&nbsp;<input type="radio" name="htmlmail" value="1"> HTML</td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Action </td>
<td align="left" valign="top" nowrap><input type="radio" value="send" name="what" checked> Send now<br><input type="radio" value="test" name="what"> Send only a test email to <input class="field10" type="text" size="50" name="test_email" value="<?PHP echo $s[mail]; ?>"></td>
</tr>
<tr><td align="center" colspan="2"><input type=submit name=xx value="Send mass email" class="button10"></td></tr>
</table></td></tr></table></form><br>
<?PHP
ift();
}

##########################################################################

function emailed_users($data) {
global $s;
check_admin('email_users');
if ((!$data[text]) OR (!$data[subject])) { $s[info] = info_line('Both fields are required'); email_users($data); }
//if ($data[htmlmail]) $htmlhead = mail_html_head();
if ($data[what] == 'test')
{ $line = $data[text];
  $subject = $data[subject];
  $value[email] = $data[test_email];
  foreach ($value as $k=>$v) $line = str_replace("#%$k%#",$v,$line);
  foreach ($value as $k=>$v) $subject = str_replace("#%$k%#",$v,$subject);
  $line = unreplace_once_html($line);
  $line = unhtmlentities($line); $subject = unhtmlentities(unreplace_once_html($subject));
  //mail($data[test_email],$subject,$line,"From: $s[email_from]$htmlhead");
  my_send_mail('','',$data[test_email],$data[htmlmail],$subject,$line,1);
  $s[info] = info_line('Test email has been sent to '.$data[test_email]);
  email_users($data);
}
$emaily = dq("select * from $s[pr]users where approved = '1' and confirmed = '1'",1);
while ($address = mysql_fetch_assoc($emaily))
{ set_time_limit(300);
  $line = $data[text];
  $subject = $data[subject];
  $value[name] = $address[name]; $value[email] = $address[email]; $value[password] = $address[password];
  foreach ($value as $k=>$v) $line = str_replace("#%$k%#",$v,$line);
  foreach ($value as $k=>$v) $subject = str_replace("#%$k%#",$v,$subject);
  $line = unreplace_once_html($line);
  $line = unhtmlentities($line); $subject = unhtmlentities(unreplace_once_html($subject));
  //mail($address[email], $subject, $line, "From: $s[email_from]$htmlhead");
  my_send_mail('','',$address[email],$data[htmlmail],$subject,$line,1);
  //echo "$address[email]<br>Subject: $subject<br>Text: $line<br>From: $s[email_from]$htmlhead<br><br>";
  $seznam .= "<br>$address[email]\n";
}
ih();
echo info_line('Mass email has been sent to:',$seznam);
ift();
}

##########################################################################
##########################################################################
##########################################################################

function users_unapproved_show($in) {
global $s;
check_admin('users');
if (!$in[from]) $from = 0; else $from = $in[from] - 1;
$q = dq("select count(*) from $s[pr]users where approved = '0' and confirmed = '1'",1);
$pocet = mysql_fetch_row($q); $pocet = $pocet[0];
if (!$pocet) { ih(); echo $s[info].info_line('<br><br>No one user in the queue'); ift(); }
$show[0] = $from + 1;
$show[1] = $from + $in[perpage]; if ($show[1]>$pocet) $show[1] = $pocet; if (!$in[perpage]) $show[1] = $pocet;

if (($in[perpage]) AND ($pocet>$in[perpage]))
{ $rozcesti = '
  <form action="users.php" method="get" name="form1">'.check_field_create('admin').'
  <input type="hidden" name="action" value="users_unapproved_show">
  <input type="hidden" name="perpage" value="'.$in[perpage].'">
  Show users with begin of&nbsp;&nbsp;<select class="field10" name="from"><option value="1">1</option>';
  $y = ceil($pocet/$in[perpage]);  
  for ($x=1;$x<$y;$x++)
  { $od = $x * $in[perpage] + 1;
    $rozcesti .= "<option value=\"$od\">$od</option>";
  }
  $rozcesti .= '</select>&nbsp;&nbsp;<input type="submit" value="Submit" name="B1" class="button10">
  </form>';
}

if ($in[perpage]) $limit = " limit $from,$in[perpage]";
$q = dq("select * from $s[pr]users where approved = '0' and confirmed = '1' order by n $limit",1);

$reject_emails = '<option value="0">None</option>';
$dr = opendir("$s[phppath]/styles/_common/email_templates");
while ($x = readdir($dr))
{ if ((strstr($x,'reject_user_')) AND (is_file("$s[phppath]/styles/_common/email_templates/$x")))
  $reject_emails .= "<option value=\"$x\">$x</option>";
}
closedir ($dr);

ih();
echo '<SCRIPT language=JavaScript>
function show_email_form(cislo) {
reject = eval("document.muj.reject_" + cislo);
approve = eval("document.muj.approve_" + cislo);
show_email = eval("document.muj.show_email_" + cislo);
vrstva = eval("vrstva_" + cislo);
if (vrstva.style.display == "none" ) 
{ vrstva.style.display = ""; 
  reject.checked = true;
  approve.checked = false;
  show_email.checked = true;
}
else vrstva.style.display =  "none";
}
function uncheck_both(cislo) {
reject = eval("document.muj.reject_" + cislo);
approve = eval("document.muj.approve_" + cislo);
approve.checked = false; reject.checked = false;
}
</SCRIPT>';

echo $s[info].info_line('Users in the Queue: '.$pocet.', Showing Users '.$show[0].' - '.$show[1]).$rozcesti;

while ($x = mysql_fetch_assoc($q)) { $user[$x[n]] = $x; $numbers[] = $x[n]; }

echo '<form action="users.php" method="post" name="muj">'.check_field_create('admin').'
<input type="hidden" name="action" value="users_approved">
<input type="hidden" name="perpage" value="'.$in[perpage].'">
<input type="hidden" name="from" value="'.$from.'">';

foreach ($user as $k=>$v)
{ $v[reject_emails] = $reject_emails;
  echo '<table border=0 width=700 cellspacing=0 cellpadding=2 class="common_table"><tr><td align="center">';
  user_create_edit_form($v);
  echo '</td></tr></table><br>';
}
echo '<input type="submit" name="submit" value="Submit" class="button10"></form>';
ift();
}

##################################################################################

function users_approved($in) {
global $s;
check_admin('users');
$s[info] = '';
foreach ($in[user] as $key=>$value)
{ if (!$in[user][$key][approve]) continue;
  $user = $value; $user[n] = $key;
  $oznamit = 0;
  $q = dq("select * from $s[pr]users where n = '$user[n]'",1);
  $old_data = mysql_fetch_assoc($q); $user = array_merge((array)$old_data,(array)$user);
  if ($user[approve]=='yes')
  { $user[approved] = 1; user_edited_process($user);
    $s[info] .= 'User '.$user[email].' has been approved';
    $oznamit = 1;
  }
  elseif ($user[approve]=='no')  // reject
  { $s[info] .= 'User '.$user[email].' has been rejected';
    $oznamit = 1;
    user_delete_process($user[n]);
  }
  // send emails
  if (!$oznamit) continue;
  unset($email_sent); $user[to] = $user[email];
  if ($user[approve]=='no')
  { if ($user[reject_email]) { $email_sent = 1; mail_from_template($user[reject_email],$user); }
    elseif (($user[reject_email_custom]) AND ($user[email_subject]) AND ($user[email_text]))
    { $email_sent = 1;
      while (list($k,$v) = each($user)) $user[email_text] = str_replace("#%$k%#",$v,$user[email_text]);
	  //mail($user[to],unhtmlentities($user[email_subject]),unhtmlentities($user[email_text]),"From: $s[email_from]");
      my_send_mail('','',$user[to],1,unhtmlentities($user[email_subject]),unhtmlentities($user[email_text]),1);
	}
  }
  elseif (($user[approve]=='yes') AND ($s[i_user_approved])) { $email_sent = 1; mail_from_template('user_approved.txt',$user); }
  if ($email_sent) $s[info] .= '. Email sent.<br>'; else $s[info] .= '. Email not sent.<br>';
}
users_unapproved_show($in);
}

######################################################################################
######################################################################################
######################################################################################

?>