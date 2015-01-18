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
check_admin('blacklist');

switch ($_GET[action]) {
case 'blacklist_home'		: blacklist_home(0);
case 'blacklist_updated'	: blacklist_updated($_GET); // do not delete
}
switch ($_POST[action]) {
case 'blacklist_updated'	: blacklist_updated($_POST);
}

##################################################################################
##################################################################################
##################################################################################

function blacklist_home($data) {
global $s;
$q = dq("select * from $s[pr]blacklist",0);
while ($x = mysql_fetch_row($q))
{ $x[1] = stripslashes($x[1]);
  $d[$x[0]][] = $x[1];
}
$word = implode('<br>',$d[word]);
$email = implode('<br>',$d[email]);
$ip = implode('<br>',$d[ip]);

ih();
echo page_title('Blacklist');
echo '<table border="0" width="600" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="3" class="common_table_top_cell">Items on the Blacklist</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr>
<td align=center nowrap width="33%"><span class="text10a_bold">Words/phrases</span></td>
<td align=center nowrap width="33%"><span class="text10a_bold">Emails</span></td>
<td align=center nowrap width="33%"><span class="text10a_bold">IP addresses</span></td>
</tr>
<tr>
<td align=center valign=top nowrap><span class="text10">'.$word.'</span></td>
<td align=center valign=top nowrap><span class="text10">'.$email.'</span></td>
<td align=center valign=top nowrap><span class="text10">'.$ip.'</span></td>
</tr></table>
</td></tr></table>
<br>
<form action="blacklist.php" method="post">'.check_field_create('admin').'
<input type="hidden" name="action" value="blacklist_updated">
<table border="0" width="600" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Update Your Blacklist</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center">One item per line</td></tr>
<tr><td align="center"><textarea class="field10" rows="10" name="item" style="width:550px"></textarea></td></tr>
<tr><td align="center"><select class="field10" size=1 name="addremove">
<option value="add">Add to blacklist</option>
<option value="remove">Remove from blacklist</option></select> 
<select class="field10" size=1 name="what">
<option value="word">As a word/phrase</option>
<option value="email">As an email</option>
<option value="ip">As an IP address</option></td>
</tr>
<tr><td align="center"><input type="submit" name="A1" value="Submit" class="button10"></td></tr>
</table></td></tr>
</table></form>
<br>
<table border="0" width="600" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">ow it works</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="left">
<b>Words/phrases</b> Nobody will be able to use these words in classified ads, comments, message board.<br>
<b>Emails</b> Nobody will be able to use these emails in classified ads, in user\'s info, email of a comment or message.<br>
<b>IP addresses</b> Nobody with one of the listed IP addresses will be able to enter your directory<br>
</td></tr>
</table></td></tr>
</table>';
ift();
}

##################################################################################

function blacklist_updated($data) {
global $s;
$items = explode("\n",$data[item]);
if ($data[addremove] == 'add') 
{ foreach ($items as $k=>$v)
  { $v = trim($v);
    if($v) dq("insert into $s[pr]blacklist values('$data[what]','$v')",1);
  }
}
if ($data[addremove] == 'remove') 
{ foreach ($items as $k=>$v)
  { $v = trim($v);
    dq("delete from $s[pr]blacklist where phrase = '$v' and what = '$data[what]'",1);
  }
}
$s[info] = info_line('Blacklist has been updated');
blacklist_home();
}

#################################################################################
#################################################################################
#################################################################################

?>