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

include_once('./common.php');
check_admin('board_comments');

switch ($_GET[action]) {
case 'comments_view'		: comments_view($_GET);
case 'comment_edit'			: comment_edit($_GET[n]);
case 'comment_delete'		: comment_delete($_GET[n]);
case 'comments_queue_show' 	: comments_queue_show($_GET);
}
switch ($_POST[action]) {
case 'comment_approved'		: comment_approved($_POST);
case 'comment_edited'		: comment_edited($_POST);
}

##################################################################################
##################################################################################
##################################################################################

function comments_queue_show($in) {
global $s;
if (!$in[from]) $from = 0; else $from = $in[from] - 1;
$q = dq("select count(*) from $s[pr]comments where approved = '0'",1);
$pocet = mysql_fetch_row($q);
if (!$pocet[0]) { ih(); echo info_line('<br><br><b>No one comment in the queue'); ift(); }

$show[0] = $from + 1;
$show[1] = $from + $in[perpage]; if ($show[1]>$pocet[0]) $show[1] = $pocet[0]; if (!$in[perpage]) $show[1] = $pocet[0];
if (($in[perpage]) AND ($pocet[0]>$in[perpage]))
{ $rozcesti = '<form action="comments.php" method="get" name="form1">'.check_field_create('admin').'
  <input type="hidden" name="action" value="comments_queue_show">
  <input type="hidden" name="perpage" value="'.$in[perpage].'">
  Show comments with begin of&nbsp;&nbsp;<select class="field10" name="from"><option value="1">1</option>';
  $y = ceil($pocet[0]/$in[perpage]);  
  for ($x=1;$x<$y;$x++)
  { $od = $x*$in[perpage]+1;
    $rozcesti .= '<option value="'.$od.'">'.$od.'</option>';
  }
  $rozcesti .= '</select>&nbsp;&nbsp;<input type="submit" value="Submit" name="B1" class="button10"></form>';
}
if ($in[perpage]) $limit = " limit $from,$in[perpage]";
$q = dq("select * from $s[pr]comments where approved = '0' order by n $limit",1);

ih();
echo '<SCRIPT language=JavaScript>
function uncheck_both(cislo) {
reject = eval("document.muj.reject_" + cislo);
approve = eval("document.muj.approve_" + cislo);
approve.checked = false; reject.checked = false;
}
</SCRIPT>';
echo $s[info].'<span class="text13a_bold"><b>'.$pocet[0].' comments in the queue';
if (($show[0]) AND ($show[1])) echo ", showing comments $show[0] - $show[1]";
echo '<br>'.$rozcesti.'<form action="comments.php" method="post" name="muj">'.check_field_create('admin').'
<input type="hidden" name="action" value="comment_approved">
<input type="hidden" name="perpage" value="'.$in[perpage].'">
<input type="hidden" name="from" value="'.$from.'">';
while ($comment = mysql_fetch_assoc($q))
{ $comment = stripslashes_array($comment);
  echo '<table border=0 width=600 cellspacing=10 cellpadding=0 class="common_table">
  <tr><td align="center">
  <table border=0 width=400 cellspacing=2 cellpadding=0>
  <input type="hidden" name="n[]" value="'.$comment[n].'">
  <tr><td align="left" colspan=2 nowrap>
  Approve it <input type="radio" name="approve['.$comment[n].']" value="yes" id="approve_'.$comment[n].'">&nbsp;&nbsp;&nbsp;
  Reject it <input type="radio" name="approve['.$comment[n].']" value="no" id="reject_'.$comment[n].'">&nbsp;&nbsp;&nbsp;
  <a class="link10" href="#" onClick="uncheck_both('.$comment[n].'); return false;">Uncheck these boxes</a>
  </td></tr>'.
  get_item_info($comment[item_no])
  .'<tr><td align="left" width="100">Comment</td>
  <td align="left" width="300">'.$comment[text].'</td></tr>
  </table></td></tr></table><br>';
}
  
echo '<input type="submit" name="co" value="Save" class="button10"></FORM>';
ift();
}

######################################################################################

function comment_approved($in) {
global $s;
foreach ($in as $k=>$v) $$k = $v;
foreach ($n as $key => $c)
{ if (!$approve[$c]) continue;
  $comment = get_comment_variables($c);
  if ($approve[$c]=='yes')
  { dq("update $s[pr]comments set approved = '1' where n = '$c'",1);
    recount_comments_for_item($comment[item_no]);
  }
  else dq("delete from $s[pr]comments where n = '$c'",1);
}
comments_queue_show($in);
}

######################################################################################

function get_comment_variables($n) {
global $s;
$q = dq("select * from $s[pr]comments where n = '$n'",1);
return mysql_fetch_assoc($q);
}

######################################################################################
######################################################################################
######################################################################################

function comments_view($in) {
global $s;
$q = dq("select * from $s[pr]comments where item_no = '$in[ad]' and approved = 1 order by time desc",1);
while ($comment=mysql_fetch_assoc($q)) 
{ $comment = stripslashes_array($comment);
  $comment[date] = datum ($comment[time],1);
  $comments .= "<tr><td align=\"left\" colspan=2>
  $comment[text]<br>Posted by: <a href=\"mailto:$comment[email]\">$comment[name]</a>, Added: $comment[date]</span>&nbsp;&nbsp;
  <a target=\"_self\" href=\"comments.php?action=comment_edit&n=$comment[n]\" title=\"Edit this comment\">Edit</a>&nbsp;&nbsp;
  <a target=\"_self\" href=\"comments.php?action=comment_delete&n=$comment[n]\" title=\"Delete this comment\">Delete</a>&nbsp;&nbsp;
  </td></tr>";
}
if (!$comments) $comments = '<tr><td align="left" nowrap>No one comment found</td></tr>';

ih();
echo '<table border=0 width=98% cellspacing=0 cellpadding=2 class="common_table">
<tr><td class="common_table_top_cell">Comments</td></tr>
'.get_item_info($in[item_no])
.get_options_for_item($in[item_no]).$comments.'</td></tr></table><br>';
ift();
}

######################################################################################

function comment_edit($n) {
global $s;
$comment = get_comment_variables($n);
$comment = stripslashes_array($comment);
ih();
echo $s[info];
$added = datum ($comment[time],1);

echo '<table border=0 width=600 cellspacing=0 cellpadding=5 class="common_table">
<tr><td align="center" class="common_table_top_cell">Edit Selected Comment</td></tr>
<tr><td align="center">
<table border=0 width=550 cellspacing=2 cellpadding=0>
<form method="post" action="comments.php">'.check_field_create('admin').'
<input type="hidden" name="action" value="comment_edited">
<input type="hidden" name="n" value="'.$comment[n].'">
<tr><td align="left" colspan=2>Comment #'.$comment[n].', Added: '.$added.' by <a href="mailto:'.$comment[email].'">'.$comment[name].'</a>, IP: '.$comment[ip].'</td></tr>
'.get_item_info($comment[item_no]).get_options_for_item($comment[item_no]).'
<tr>
<td align="left" valign="top">Text</td>
<td align="left"><textarea class="field10" name="text" rows=5 cols=60>'.$comment[text].'</textarea></td>
</tr>
<tr>
<td align="left" nowrap>Author </td>
<td align="left"><input class="field10" name="name" size=60 maxlength=150 value="'.$comment[name].'"></td>
</tr>
<tr>
<td align="left">Email</td>
<td align="left"><input class="field10" name="email" size=60 maxlength=150 value="'.$comment[email].'"></td>
</tr>
<tr><td align="center" colspan=2><input type="submit" name="co" value="Save changes" class="button10">
</td></tr></FORM>
</table>
</td></tr></table><br>';
ift();
}

##################################################################################

function comment_edited($in) {
global $s;
$in = replace_array_text($in);
dq("update $s[pr]comments set text = '$in[text]', name = '$in[name]', email = '$in[email]' where n = '$in[n]'",1);
$s[info] = info_line('Selected comment has been updated');
comment_edit($in[n]);
}

##################################################################################

function comment_delete($n) {
global $s;
$comment = get_comment_variables($n);
dq("delete from $s[pr]comments where n = '$n'",1);
recount_comments_for_item($comment[item_no]);
ih(); 
echo info_line('Selected comment has been deleted');
echo '<br><br><a href="'.$_SERVER[HTTP_REFERER].'">Back</a>';
ift();
}

##################################################################################
##################################################################################
##################################################################################

function recount_comments_for_item($n) {
global $s;
$q = dq("select count(*) from $s[pr]comments where item_no = '$n' and approved = '1'",1);
$x = mysql_fetch_row($q);
dq("update $s[pr]ads set comments = '$x[0]' where n = '$n'",1);
}

######################################################################################

function get_item_info($n) {
global $s;
$ad = get_ad_variables($n);
return '<tr><td align="left" colspan="2">Classified ad #'.$n.' - <a target="_blank" href="ads_list.php?action=ads_searched&n='.$ad[n].'&boolean=and&sort=title&order=asc">'.$ad[title].'</a></td></tr>';
}

######################################################################################

function get_options_for_item($n) {
return '<tr><td align="left" colspan=2>
<a href="ad_details.php?action=ad_edit&n='.$n.'">Edit this classified ad</a>&nbsp;&nbsp;
<a href="ad_details.php?action=ad_delete&n='.$n.'">Delete this classified ad</a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="ad_details.php?action=ad_copy&n='.$n.'">Copy this classified ad</a>
</td></tr>';
}

######################################################################################
######################################################################################
######################################################################################


?>