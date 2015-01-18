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
check_admin('search_log');

switch ($_GET[action]) {
case 'search_log_info'		: search_log_info();
case 'search_log'			: search_log($_GET);
case 'search_record_delete'	: search_record_delete($_GET);
}

#################################################################################
#################################################################################
#################################################################################

function search_log_info($what) {
global $s;
ih();
$q = dq("select count(*) from $s[pr]log_search",1);
$pocet = mysql_fetch_row($q); $pocet = $pocet[0];
if (!$pocet) { echo info_line('No one record found, search log is empty'); ift(); }

echo page_title('Search Log');
echo '<form method="get" action="search_log.php">'.check_field_create('admin').'
<input type="hidden" name="action" value="search_log">
<table border="0" width="500" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Records found: '.$pocet.'</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center" nowrap>Select number of records to display on one page.</td></tr>
<tr><td align="center" nowrap>
<select name="perpage" class="field10"><option value="0">All</option>';
if ($pocet>20) echo '<option value="20">20</option>';
if ($pocet>50) echo '<option value="50">50</option>';
if ($pocet>100) echo '<option value="100">100</option>';
if ($pocet>250) echo '<option value="250">250</option>';
echo '</select> 
<input type="submit" value="Submit" name="B1" class="button10">
</span></td></tr></table></td></tr></table></form>
<br>
<form action="search_log.php" method="get" name="form1">'.check_field_create('admin').'
<input type="hidden" name="action" value="search_record_delete">
<table border="0" width="500" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Delete Records</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center" colspan=2>Delete records with <input class="field10" name="lessthan" size=5 maxlength=5> or less searching</td></tr>
<tr><td align="center" colspan=2><input type="submit" name="submit" value="Submit" class="button10"></td></tr>
<tr><td align="center">
<a href="search_log.php?action=search_record_delete&all=1">Delete all search log records</a>
</td></tr></table></td></tr></table></form><br>';
ift();
}

#################################################################################

function search_log($in) {
global $s;
$q = dq("select count(*) from $s[pr]log_search",1);
$pocet = mysql_fetch_row($q); $pocet=$pocet[0];
if (!$pocet) search_log_info();
if (!$in[from]) $from=0; else $from=$in[from]-1;
$show[0] = $from + 1;
$show[1] = $from+$in[perpage]; if ($show[1]>$pocet) $show[1] = $pocet; if (!$in[perpage]) $show[1] = $pocet;

if ( ($in[perpage]) AND ($pocet>$in[perpage]) )
{ $rozcesti = '
  <form action="search_log.php" method="get">'.check_field_create('admin').'
  <input type="hidden" name="action" value="search_log">
  <input type="hidden" name="perpage" value="'.$in[perpage].'">
  Show records with begin of&nbsp;&nbsp;<select name="from" class="field10"><option value="1">1</option>';
  $y = ceil($pocet/$in[perpage]);  
  for ($x=1;$x<$y;$x++)
  { $od = $x*$in[perpage]+1;
    $rozcesti .= "<option value=\"$od\">$od</option>";
  }
  $rozcesti .= '</select>&nbsp;&nbsp;<input type="submit" value="Submit" name="B1" class="button10">
  </form>';
}

if ($in[perpage]) $limit = " limit $from,$in[perpage]";
$q = dq("select * from $s[pr]log_search order by count desc $limit",1);
$returnto = urlencode("search_log.php?$_SERVER[QUERY_STRING]");

ih();
echo $s[info];
echo info_line('Records found: '.$pocet.', showing records '.$show[0].' - '.$show[1]);
echo $rozcesti.'<table border=0 width=300 cellspacing=5 cellpadding=0 class="common_table">
<tr><td align="center"><table border=0 width=250 cellspacing=2 cellpadding=0>
<tr><td align="left" nowrap>Phrase</td>
<td align="center" nowrap>No. of searches</td>
<td align="center" nowrap>&nbsp;</td>
</tr>';
while ($x = mysql_fetch_array($q))
{ $x = stripslashes_array($x);
  echo '<tr>
  <td align="left" nowrap>'.$x[word].'</td>
  <td align="center">'.$x[count].'</td>
  <td align="center"><a href="search_log.php?action=search_record_delete&n='.$x[n].'&returnto='.$returnto.'">Delete</a></td>
  </tr>';
}
echo '</table></td></tr></table><br><br><br>';
ift();
}

##################################################################################

function search_record_delete($in) {
global $s;
if ($in[lessthan]) dq("delete from $s[pr]log_search where count <= '$in[lessthan]'",1);
elseif ($in[all]) dq("delete from $s[pr]log_search",1);
else dq("delete from $s[pr]log_search where n = '$in[n]'",1);
if ($in[returnto]) header("Location: $in[returnto]");
else search_log_info($in[what]);
}

##########################################################################
##########################################################################
##########################################################################

?>