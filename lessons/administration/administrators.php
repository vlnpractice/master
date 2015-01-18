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
check_admin('admins');

switch ($_POST[action]) {
case 'admin_created'		: admin_created($_POST);
case 'admin_delete'			: admin_delete($_POST);
case 'admin_edit'			: admin_edit($_POST);
case 'admin_edited'			: admin_edited($_POST);
case 'admin_edited_cats'	: admin_edited_cats($_POST);
}
admins_home();

##################################################################################
##################################################################################
##################################################################################

function admins_home($in) {
global $s;
ih();
echo $s[info];
echo page_title('Administrators'); 
$in[action] = 'admin_created';
$in[head] = 'Create A New Administrator';
admin_create_edit_form($in);
echo '<form action="administrators.php" method="post">'.check_field_create('admin').'
<input type="hidden" name="action" value="admin_edit">
<table border="0" width="600" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Existing Administrators</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align=center><select class="field10" name="admin">'.select_admins().'</select></td></tr>
<tr><td align="center">Action: <input type="radio" name="action" value="admin_edit" checked>Edit&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="action" value="admin_delete">Delete</td></tr>
<tr><td align=center><input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</table></td></tr></table></form>';
ift();
}

#################################################################################

function admin_created($in) {
global $s;
if ((!$in[username]) OR (!$in[password]) OR (!$in[email])) $problem[] = 'Some of required fields left blank.';
if (((strlen($in[username])) < 6) OR ((strlen($in[username])) > 15)) $problem[] = 'Username must be 6-15 characters long.';
if (((strlen($in[password])) < 6) OR ((strlen($in[password])) > 15)) $problem[] = 'Password must be 6-15 characters long.';
$in[name] = replace_once_text($in[name]);
if ($problem)
{ $s[info] = info_line('One or more errors found. Please try again.',implode('<br>',$problem));
  admins_home($in);
}
$in[password] = md5($in[password]);
dq("insert into $s[pr]admins values (NULL,'$in[username]','$in[password]','$in[email]','$in[name]','0')",1);
$n = mysql_insert_id();
foreach ($in[rights] as $k=>$v) dq("insert into $s[pr]admins_rights values ('$n','$v')",1);
$s[info] = info_line('New administrator has been created.');
admins_home();
}

#################################################################################

function admin_edit($in) {
global $s;
$in[action] = 'admin_edited';
$in[head] = 'Edit Selected Administrator';
ih();
admin_create_edit_form($in);
echo '<a href="administrators.php?action=admins_home">Back to previous page</a><br><br>';
ift();
}

#################################################################################

function admin_create_edit_form($in) {
global $s;
if ($in[action]=='admin_created') { $user = $in; $rights = $in[rights]; }
else
{ $q = dq("select * from $s[pr]admins where n = '$in[admin]'",1); $user = mysql_fetch_assoc($q);
  $q = dq("select * from $s[pr]admins_rights where admin = '$in[admin]'",1); while ($x = mysql_fetch_assoc($q)) $rights[] = $x[action];
}
$all_rights = array(
'ads'=>'Create/view/edit/delete classified ads',
'categories'=>'Create/view/edit/delete categories',
'areas'=>'Create/view/edit/delete areas',
'blacklist'=>'View/edit blacklist',
'polls'=>'Create/view/edit/delete polls',
'prices'=>'View/edit prices',
'board_comments'=>'View/edit/delete messages on the Board + user comments',
'templates'=>'Edit/translate templates',
'messages'=>'Edit/translate messages',
'admins'=>'Create/view/edit/delete administrators',
'database_tools'=>'Access to Database tools',
'configuration'=>'View/edit Configuration',
'reset_rebuild'=>'All options on the Reset/rebuild screen',
'users'=>'Create/view/edit/delete registered users, orders',
'email_users'=>'Email registered users',
'search_log'=>'View/delete search log');
echo '<form action="administrators.php" method="post">'.check_field_create('admin').'
<input type="hidden" name="action" value="'.$in[action].'">
<input type="hidden" name="n" value="'.$user[n].'">
<table border="0" width="600" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">'.$in[head].'</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center">';
if ($in[action]=='admin_created')
echo '<tr>
<td align="left" nowrap>Username&nbsp;&nbsp;</td>
<td align="left" colspan=2><input class="field10" size=15 name="username" value="'.$user[username].'" maxlength=15></td>
</tr>
<tr>
<td align="left" nowrap>Password&nbsp;&nbsp;</td>
<td align="left" colspan=2><input class="field10" size=15 name="password" maxlength=15></td>
</tr>';
else echo '<tr>
<td align="left" nowrap>Username&nbsp;&nbsp;</td>
<td align="left" colspan=2>'.$user[username].'</td>
</tr>
<tr>
<td align="left" nowrap>Password&nbsp;&nbsp;</td>
<td align="left" colspan=2><input class="field10" size=15 name="password" maxlength=15> <span class="text10">Leave it blank if you don\'t want to change it</span></td>
</tr>';
echo '<tr>
<td align="left" nowrap>Email&nbsp;&nbsp;</td>
<td align="left" colspan=2><input class="field10" size=70 name="email" maxlength="255" value="'.$user[email].'"></td>
</tr>
<tr>
<td align="left" nowrap>Name&nbsp;&nbsp;</td>
<td align="left" colspan=2><input class="field10" size=70 name="name" maxlength="255" value="'.$user[name].'"></td>
</tr>
<tr>
<td align="left" valign="top">Privilegies<br><span class="text10">This administrator<br>can view/edit<br>these items</span></td>
<td align="left" nowrap>';
foreach ($all_rights as $k=>$v)
{ echo '<input type="checkbox" name="rights[]" value="'.$k.'"';
  if (in_array($k,$rights)) echo ' checked';
  echo '>'.$v.'<br>';
}
echo '</td></tr>
<tr><td align="center" colspan=3><input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</table></td></tr></table></form><br>';
}

#################################################################################

function admin_edited($in) {
global $s;
if (!$in[n]) problem('An error has occurred. Please select the administrator again.');
if (!$in[email]) $problem[] = 'Email left blank';
if ($in[password])
{ if ( ((strlen($in[password])) < 6) OR ((strlen($in[password])) > 15) ) $problem[] = 'Password must be 6-15 characters long.';
  $in[password] = md5($in[password]); 
  $password = " ,password='$in[password]'";
}
$in[name] = replace_once_text($in[name]);
if ($problem)
{ $s[info] = info_line('One or more errors found. Please try again.',implode('<br>',$problem));
  $in[admin] = $in[n]; admin_edit($in);
}
dq("update $s[pr]admins set email = '$in[email]' $password, name = '$in[name]' where n = '$in[n]'",1);
dq("delete from $s[pr]admins_rights where admin = '$in[n]'",1);
foreach ($in[rights] as $k=>$v) dq("insert into $s[pr]admins_rights values ('$in[n]','$v')",1);
$s[info] = info_line('Administrator has been edited');
$in[admin] = $in[n];
admin_edit($in);
}

#################################################################################

function admin_delete($form) {
global $s;
$q = dq("select username from $s[pr]admins where n = '$form[admin]'",1); $user = mysql_fetch_row($q);
if (!$user[0]) problem("Selected administrator does not exist.");
if (($_SESSION[GC_admin_user]==$user[0]) OR ($_COOKIE[GC_admin_user]==$user[0])) problem('You can not delete your account');
if (!$form[ok])
{ ih();
  echo '<br><table border=0 width=500 cellspacing=10 cellpadding=2 class="common_table">
  <form action="administrators.php" method="post">'.check_field_create('admin').'
  <input type="hidden" name="action" value="admin_delete">
  <input type="hidden" name="ok" value="1">
  <input type="hidden" name="admin" value="'.$form[admin].'">
  <tr><td align="center" nowrap><span class="text13a_bold">You are about to delete administrator '.$user[0].'. Are you sure?</span></td></tr>
  <tr><td align="center"><input type="submit" name="submit" value="Yes, delete this administrator" class="button10"></td></tr>
  </form></table>';
  ift();
}
dq("delete from $s[pr]admins where n = '$form[admin]'",1);
dq("delete from $s[pr]admins_rights where admin = '$form[admin]'",1);
$s[info] = info_line('Selected administrator has been deleted');
admins_home();
}

#################################################################################

function select_admins() {
global $s;
$q = dq("select * from $s[pr]admins order by username",1);
while ($a=mysql_fetch_assoc($q)) $x .= '<option value="'.$a[n].'">'.$a[username].'</option>';
return $x;
}

##################################################################################
##################################################################################
##################################################################################

?>