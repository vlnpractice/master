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
check_admin('board_comments');

switch ($_GET[action]) {
case 'board'				: board();
case 'board_del_msg'		: board_del_msg($_GET[cas]);
}

#################################################################################
#################################################################################
#################################################################################

function board() {
global $s;
$q = dq("select * from $s[pr]board order by time desc limit $s[board]",0);
ih();
echo $s[info];
echo '<table border="0" width="500" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Messages on the Message Board</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">';
while ($p=mysql_fetch_array($q)) 
{ $p = stripslashes_array($p);
  $p[date] = datum ($p[time],1);
  if ($p[user]) { $p[link] = "users.php?action=user_edit&user=$p[user]"; $p[name] = $p[user]; }
  else $p[link] = "mailto:$p[email]";
  echo "<tr><td align=\"left\">Name</td>
  <td align=\"left\" nowrap><a href=\"$p[link]\">$p[name]</a></td></tr>
  <tr><td align=\"left\">Message</td>
  <td align=\"left\">$p[text]</td></tr>
  <tr><td align=\"left\">IP</td>
  <td align=\"left\">$p[ip]</td></tr>
  <tr><td align=\"left\">Time</td>
  <td align=\"left\">$p[date]</td></tr>
  <tr><td align=\"left\" colspan=\"2\">
  <a href=\"board.php?action=board_del_msg&cas=$p[time]\">Delete this message</a></td></tr>";
}
echo '</table></td></tr></table>';
ift();
}

#################################################################################


function board_del_msg($cas) {
global $s;
dq("delete from $s[pr]board where time = '$cas'",1);
$s[info] = info_line('Selected message has been deleted');
board();
}

#################################################################################
#################################################################################
#################################################################################


?>