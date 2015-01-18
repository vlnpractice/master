<?PHP

#################################################
##                                             ##
##               Link Up Gold                  ##
##       http://www.phpwebscripts.com/         ##
##       e-mail: info@phpwebscripts.com        ##
##                                             ##
##                                             ##
##               version:  8.0                 ##
##            copyright (c) 2012               ##
##                                             ##
##  This script is not freeware nor shareware  ##
##    Please do no distribute it by any way    ##
##                                             ##
#################################################

include('./articles_functions.php');

switch ($_GET[action]) {
case 'articles_search'			: articles_search();
case 'articles_searched'		: articles_searched($_GET);
case 'articles_unapproved_home'	: articles_unapproved_home();
case 'articles_unapproved_show'	: articles_unapproved_show($_GET);
case 'articles_updated_multiple': articles_updated_multiple($_GET);
case 'articles_editor_picks'	: articles_editor_picks();
}
switch ($_POST[action]) {
case 'articles_approved'		: articles_approved($_POST);
case 'articles_edited_multiple'	: articles_edited_multiple($_POST);
case 'articles_updated_multiple': articles_updated_multiple($_POST);
}

#################################################################################
#################################################################################
#################################################################################

function articles_edited_multiple($in) {
global $s;
check_admin('all_articles');
foreach ($in[article] as $k=>$v) { $article = $v; $article[n] = $k; article_edited_process($article); }
ih();
echo info_line('Entered changes have been saved');
echo '<a href="'.$_SERVER[HTTP_REFERER].'">Back</a>';
ift();
exit;
}

#################################################################################

function articles_updated_multiple($in) {
global $s;
check_admin('all_articles');

if ((!$in[to_do]) OR (!$in[article])) header("Location: $_SERVER[HTTP_REFERER]");
$x = 'n = \''.implode('\' OR n = \'',$in[article]).'\'';
ih();

if ($in[to_do]=='delete')
{ echo info_line('Total of '.count($in[article]).' articles will be deleted. Continue?');
  echo '<form method="get" action="articles.php">
  <input type="hidden" name="action" value="articles_updated_multiple">
  <input type="hidden" name="to_do" value="deleted">
  <input type="hidden" name="back" value="'.$_SERVER[HTTP_REFERER].'">';
  foreach ($in[article] as $k=>$v) echo '<input type="hidden" name="article[]" value="'.$v.'">';
  echo '<input type="submit" name="submit" value="Submit" class="button10"></form><br /><br /><br />';
}
else
{ if ($in[to_do]=='enable')
  { dq("update $s[pr]articles set status = 'enabled' where $x",1);
    $info = info_line(mysql_affected_rows().' articles have been enabled');
  }
  elseif ($in[to_do]=='disable')
  { dq("update $s[pr]articles set status = 'disabled' where $x",1);
    $info = info_line(mysql_affected_rows().' articles have been disabled');
  }
  elseif ($in[to_do]=='deleted')
  { delete_items('a',$in[article]);
    $info = info_line('Total of '.count($in[article]).' articles have been deleted');
  }
  elseif ($in[to_do]=='move')
  { dq("update $s[pr]articles set c = '_".$in[category]."_' where $x",1);
    foreach ($in[article] as $k => $v) update_item_index('a',$v);
    $info = info_line(mysql_affected_rows().' articles have been moved');
  }
  $info .= info_line('When you finish the editing, go to <a href="rebuild.php?action=reset_rebuild_home"><b>reset/rebuild</b></a> and run function "Recounts all links and articles"');
  echo $info;
}
if ($in[back]) $back = $in[back]; else $back = $_SERVER[HTTP_REFERER];
echo '<a href="'.$back.'">Back</a>';
ift();
exit;
}

#################################################################################
#################################################################################
#################################################################################

function articles_editor_picks() {
global $s;
check_admin('all_articles');
ih();
$q = dq("select * from $s[pr]articles where pick > 0 order by pick desc",1);
$pocet = mysql_num_rows($q);
if (!$pocet)
{ echo info_line('No one article is marked as editor\'s pick');
  ift(); 
}
echo info_line($pocet.' articles found');
while ($data = mysql_fetch_assoc($q)) show_one_article($data);
ift();
}

#################################################################################
#################################################################################
#################################################################################

function articles_unapproved_home() {
global $s;
ih();
echo $s[info];
echo page_title('Queue for Articles');
echo '<table border="0" width="900" cellspacing="0" cellpadding="0" class="common_table">
<tr><td align="center" width="100%">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td class="common_table_top_cell" nowrap>Articles</td></tr>
<tr><td align="center" width="100%">';
$q = dq("select count(*) from $s[pr]articles where status = 'queue' $s[allowed_cats_query_a]",1);
$pocet = mysql_fetch_row($q); $pocet = $pocet[0];
if (!$pocet) echo 'No one new article in the queue';
else
{ echo 'Articles in the queue: '.$pocet.
  '<br />Select number of articles to display on one page<br />
  <form method="get" action="articles.php">
  <input type="hidden" name="action" value="articles_unapproved_show">
  <input type="hidden" name="what" value="new">
  <select class="select10" name="perpage"><option value="0">All</option>';
  if ($pocet>5) echo '<option value="5">5</option>';
  if ($pocet>10) echo '<option value="10">10</option>';
  if ($pocet>20) echo '<option value="20">20</option>';
  if ($pocet>30) echo '<option value="30">30</option>';
  echo '</select> 
  <input type="submit" value="Submit" name="B1" class="button10">
  </form>';
}
echo '</td></tr>';
if ($s[admin_all_cats_a]) comments_unapproved_info('a');
echo '</table>
</td></tr></table>';
ift();
}

#################################################################################

function articles_unapproved_show($in) {
global $s;
if (!$in[from]) $from = 0; else $from = $in[from] - 1;

$q = dq("select count(*) from $s[pr]articles where status = 'queue' $s[allowed_cats_query_a]",1);
$pocet = mysql_fetch_row($q); $pocet = $pocet[0];
if (!$pocet) { ih(); echo $s[info].info_line('No one article in the queue'); ift(); }
$show[0] = $from + 1;
$show[1] = $from + $in[perpage]; if ($show[1]>$pocet) $show[1] = $pocet; if (!$in[perpage]) $show[1] = $pocet;

if ( ($in[perpage]) AND ($pocet>$in[perpage]) )
{ $rozcesti = '
  <form action="articles.php" method="get" name="form1">
  <input type="hidden" name="action" value="articles_unapproved_show">
  <input type="hidden" name="perpage" value="'.$in[perpage].'">
  <input type="hidden" name="what" value="'.$in[what].'">
  Show articles with begin of&nbsp;&nbsp;<select class="select10" name="from"><option value="1">1</option>';
  $y = ceil($pocet/$in[perpage]);  
  for ($x=1;$x<$y;$x++)
  { $od = $x * $in[perpage] + 1;
    $rozcesti .= "<option value=\"$od\">$od</option>";}
  $rozcesti .= '</select>&nbsp;&nbsp;<input type="submit" value="Submit" name="B1" class="button10">
  </form><br />';
}

if ($in[perpage]) $limit = " limit $from,$in[perpage]";
$q = dq("select * from $s[pr]articles where status = 'queue' $s[allowed_cats_query_a] order by n $limit",1);
$reject_emails = get_reject_emails_list('reject_article_');

ih();
echo $s[info].info_line('Articles in the queue, showing articles '.$show[0].' - '.$show[1]).$rozcesti;

while ($x = mysql_fetch_assoc($q)) { $articles[$x[n]] = $x; $numbers[] = $x[n]; }
$x = 'AND (n = \''.implode('\' OR n = \'',$numbers).'\')';
$q = dq("select * from $s[pr]usit_values where use_for = 'a_q' $x",1);
while ($x = mysql_fetch_assoc($q))
{ $articles[$x[n]][user_items][$x[item_n]][value_code] = $x[value_code];
  $articles[$x[n]][user_items][$x[item_n]][value_text] = $x[value_text];
}

echo '<form ENCTYPE="multipart/form-data" action="articles.php" method="post" name="muj">'.check_field_create('admin').'
<input type="hidden" name="action" value="articles_approved">
<input type="hidden" name="perpage" value="'.$in[perpage].'">
<input type="hidden" name="from" value="'.$from.'">
<input type="hidden" name="what" value="'.$in[what].'">';

foreach ($articles as $k=>$v)
{ $v[reject_emails] = $reject_emails;
  article_create_edit_form($v);
}
echo '<input type="submit" name="submit" value="Submit" class="button10"></form>';
ift();
}

#################################################################################

function articles_approved($in) {
global $s;

foreach ($in[article] as $n=>$article)
{ if (!$in[article][$n][approve]) continue;
  $article[n] = $n;
  $oznamit = 0;
  $old = get_item_variables('a',$n);
  if (!check_admin_categories('a',$article[categories]))
  { $s[info] .= "Article $article[title] skipped. You do not have permissions to add an article to selected category/categories.<br />";
    continue;
  }
  if ($article[approve]=='yes')
  { if ($old[n]) // updated
    { $q = dq("select * from $s[pr]files where what = 'a' and item_n = '$n' and queue = '0'",1);
      while ($files=mysql_fetch_assoc($q))
      { unlink(str_replace($s[site_url],$s[phppath],$files[filename]));
        if ($files[file_type]=='image') unlink(str_replace($s[site_url],$s[phppath],preg_replace("/\/$n-/","/$n-big-",$files[filename])));
      }
      dq("delete from $s[pr]files where what = 'a' and item_n = '$n' and queue = '0'",1);
	  dq("update $s[pr]files set queue = '0' where what = 'a' and item_n = '$n' and queue = '1'",1);
	  dq("delete from $s[pr]articles where n = '$n' and status = 'queue'",1);
      dq("update $s[pr]articles set status = 'enabled' where n = '$n' and status = 'queue'",1);
	  dq("delete from $s[pr]usit_values where n = '$n' and use_for = 'a_q'",1);
	  dq("delete from $s[pr]usit_search where n = '$n' and use_for = 'a_q'",1);
	  $article[mark_updated] = 1;
   	  article_edited_process($article);
    }
	else // new article
	{ dq("update $s[pr]articles set status = 'enabled' where n = '$n' and status = 'queue'",1);
	  dq("update $s[pr]files set queue = '0' where what = 'a' and item_n = '$n' and queue = '1'",1);
	  dq("update $s[pr]usit_values set use_for = 'a' where n = '$n' and use_for = 'a_q'",1);
	  dq("update $s[pr]usit_search set use_for = 'a' where n = '$n' and use_for = 'a_q'",1);
	  article_edited_process($article);
      dq("insert into $s[pr]u_to_email values('a','$n')",1);
    }
    $s[info] .= 'Article #'.$n.' - <a target="_blank" href="'.$article[url].'">'.$article[title].'</a> has been approved';
    $oznamit = 1;
  }
  elseif ($article[approve]=='no')  // reject
  { if (!$old[n]) $is_new = 1;
    delete_queued_item('a',$n,$is_new);
	$s[info] .= 'Article <a target="_blank" href="'.$article[url].'">'.$article[title].'</a> has been rejected';
    $oznamit = 1;
  }
  // send emails
  if (!$oznamit) continue;
  $article[to] = $article[email];
  if ($article[approve]=='no')
  { if ($article[reject_email]) { $email_sent = 1; mail_from_template($article[reject_email],$article); }
    elseif (($article[reject_email_custom]) AND ($article[email_subject]) AND ($article[email_text]))
    { foreach ($article as $k=>$v) $article[email_text] = str_replace("#%$k%#",$v,$article[email_text]);
	  my_send_mail('','',$article[email],0,unhtmlentities($article[email_subject]),unhtmlentities($article[email_text]),1);
	  $email_sent = 1;
	}
  }
  elseif (($article[approve]=='yes') AND ($s[a_i_approved]))
  { $y = list_of_categories_for_item('a',0,$article[categories],"\n",1); $article[categories] = $y[categories_names]; $article[categories_urls] = $y[categories_urls];
    $article[detail_url] = get_detail_page_url('a',$article[n],'',0,1);
    mail_from_template('article_approved.txt',$article);
    $email_sent = 1;
  }
  if ($email_sent) $s[info] .= '. Email sent.<br />'; else $s[info] .= '. Email not sent.<br />';
}
if ($s[info]) $s[info] .= '<br>';
articles_unapproved_show($in);
}

#################################################################################
#################################################################################
#################################################################################

function articles_search() {
global $s;
$categories = categories_selected('a',0,1,1,0,0);
$categories_first = categories_selected('a_first',0,1,1,0,0);
ih();
echo '<form method="GET" action="articles.php">
<input type="hidden" name="action" value="articles_searched">
<table border="0" width="900" cellspacing="0" cellpadding="0" class="common_table">
<tr><td class="common_table_top_cell">Search for Articles</td></tr>
<tr><td align="center" width="100%">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr>
<td align="left">Number <span class="text10">&nbsp;&nbsp;&nbsp;If you enter a number here, it will find this article, not depending if it meets any other criteria<br /></span></td>
<td align="left"><input class="field10" name="n" style="width:100px" maxlength=10></td>
</tr>
<tr>
<td align="left" nowrap>Category</td>
<td align="left"><select class="select10" name="category"><option value="0">Any category</option>'.$categories.'</select></td>
</tr>
<tr>
<td align="left" nowrap>Category</td>
<td align="left" nowrap><select class="select10" name="bigboss"><option value="0">Any category</option>'.$categories_first.'</select> incl. subcategories</td>
</tr>
<tr><td align="left" nowrap>Any field contains </td>
<td align="left"><input class="field10" name="any" style="width:650px;" maxlength=100></td></tr>
<tr><td align="left" nowrap>Title contains </td>
<td align="left"><input class="field10" name="title" style="width:650px;" maxlength=100></td></tr>
<tr><td align="left" nowrap>Subtitle contains </td>
<td align="left"><input class="field10" name="description" style="width:650px;" maxlength=100></td></tr>
<tr><td align="left" nowrap>Text contains </td>
<td align="left"><input class="field10" name="text" style="width:650px;" maxlength=100></td></tr>';
echo user_defined_items_form('a','search_form');
echo '<tr>
<td align="left" nowrap>Owner\'s username </td>
<td align="left"><input class="field10" name="username" style="width:650px;" maxlength=100></td>
</tr>
<tr>
<td align="left" nowrap>Contact name contains </td>
<td align="left"><input class="field10" name="name" style="width:650px;" maxlength=100></td>
</tr>
<tr>
<td align="left" nowrap>Contact email contains </td>
<td align="left"><input class="field10" name="email" style="width:650px;" maxlength=100></td>
</tr>
<tr>
<td align="left" nowrap>Rating (0-5)</td>
<td align="left" nowrap>From <input  class="field10" name="rating_f" size=4 maxlength=2> To <input  class="field10" name="rating_t" size=4 maxlength=2></td>
</tr>
<tr>
<td align="left" nowrap>Votes </td>
<td align="left" nowrap>From <input  class="field10" name="votes_f" size=4 maxlength=2> To <input  class="field10" name="votes_t" size=4 maxlength=2></td>
</tr>
<tr><td align="left" nowrap>Number of reads </td>
<td align="left" nowrap>From <input  class="field10" name="read_f" size=4 maxlength=10> To <input  class="field10" name="read_t" size=4 maxlength=10> times</td></tr>
<tr><td align="left" nowrap>Enabled </td>
<td align="left" nowrap>Yes<input type="radio" value="yes" name="enabled"> No<input type="radio" value="no" name="enabled"> Both<input type="radio" value="0" name="enabled" checked></td></tr>
<tr><td align="left" nowrap>Validity </td>
<td align="left"><select class="select10" name="valid">
<option value="0">N/A</option><option value="yes">Articles which are currently valid</option>
<option value="no">Articles which are currently not valid</option><option value="future">Articles which will valid in the future</option><option value="past">Articles which have been valid in the past</option></select>
</td></tr>
<tr><td align="left" nowrap>Type of search </td>
<td align="left" nowrap>AND <input type="radio" value="and" name="boolean" checked> OR <input type="radio" value="or" name="boolean"></td></tr>
<tr><td align="left" nowrap>Results per page </td>
<td align="left"><select class="select10" name="perpage">
<option value="0">All</option><option value="5">5</option><option value="10">10</option>
<option value="20">20</option><option value="50">50</option>
<option value="100">100</option><option value="1000">1000</option></select>
</td></tr>
<tr><td align="left" nowrap>Sort by </td>
<td align="left"><select class="select10" name="sort">
<option value="title">Title</option><option value="rating">Rating</option>
<option value="votes">No of votes</option><option value="hits_m">Popularity (# of reads current month)</option>
<option value="created">Date created</option><option value="name">Contact name</option>
</select>
<select class="select10" name="order">
<option value="asc">Ascending</option><option value="desc">Descending</option>
</select></td></tr>
<tr><td align="left" nowrap>Show full text </td>
<td align="left"><input type="checkbox" name="showtext" checked>
</td></tr>
<tr><td align="left" nowrap>Edit forms </td>
<td align="left"><input type="checkbox" name="edit_forms" value="1"></td></tr>
<tr><td colspan=2 align="center"><input type="submit" value="Search" name="B1" class="button10"></td></tr>
</table></td></tr></table></form><br />';
exit;
}

#################################################################################

function articles_searched($data) {
global $s;
if (!$data[boolean]) $data[boolean] = 'and';
foreach ($data as $k=>$v)
{ if (is_array($v)) foreach ($v as $k1=>$v1) { if ($v1) $x[] = $k.'['.$k1.']='.$v1; }
  elseif ($v) $x[] = "$k=$v";
}
$referral = implode('&',$x);

ih();
if (!$data[n])
{ if ($data[bigboss]) $w[] = "c_path like '%\_$data[bigboss]\_%'";
  if ($data[skip]) $w[] = "n != '$data[skip]' ";
  if ($data[exact_title]) $w[] = "title = '$data[exact_title]' ";
  if ($data[title]) $w[] = "title like '%$data[title]%' ";
  if ($data[description]) $w[] = "description like '%$data[description]%' ";
  if ($data[text]) $w[] = "text like '%$data[text]%' ";
  if ($data[owner]) $w[] = "owner = '$data[owner]'";
  elseif ($data[username]) { $user = get_user_variables(0,$data[username]); $w[] = "owner = '$user[n]'"; }
  if ($data[name]) $w[] = "name like '%$data[name]%' ";
  if ($data[email]) $w[] = "email like '%$data[email]%' ";
  if ($data[category]) $w[] = "c like '%\_$data[category]\_%'";
  if (($data[rating_f]) OR ($data[rating_t]))
  { if (!$data[rating_f]) $data[rating_f] = 0; $x1 = "rating >= $data[rating_f]";
    if (!$data[rating_t]) $data[rating_t] = 10; $x2 = "rating <= $data[rating_t]"; 
    $w[] = "($x1 AND $x2)";
  }
  if (($data[votes_f]) OR ($data[votes_t]))
  { if (!$data[votes_f]) $data[votes_f] = 0; $x1 = "votes >= $data[votes_f]";
    if (!$data[votes_t]) $data[votes_t] = 1000000; $x2 = "votes <= $data[votes_t]"; 
    $w[] = "($x1 AND $x2) ";
  }
  if (($data[read_f]) OR ($data[read_t]))
  { if (!$data[read_f]) $data[read_f] = 0; $x1 = "hits >= $data[read_f]";
    if (!$data[read_t]) $data[read_t] = 1000000; $x2 = "hits <= $data[read_t]"; 
    $w[] = " ($x1 AND $x2) ";
  }
  switch ($data[valid])
  { case 'yes'		: $w[] = "(t1 < '$s[cas]' AND (t2 > '$s[cas]' OR t2 = 0))"; break;
    case 'no'		: $w[] = "(t1 > '$s[cas]' OR (t2 < '$s[cas]' AND t2 != 0))"; break;
    case 'future'	: $w[] = "t1 > '$s[cas]'"; break;
    case 'past'		: $w[] = "(t2 < '$s[cas]' AND t2 != 0)"; break;
  }
  if ($data[enabled]=='yes') $w[] = "status = 'enabled'";
  elseif ($data[enabled]=='no') $w[] = "status = 'disabled'";
  if ($data[any])
  { if (!$w[0]) $only_any = 1;
    $w[] = "(title like '%$data[any]%' OR description like '%$data[any]%' OR text like '%$data[any]%' OR name like '%$data[any]%' OR email like '%$data[any]%')";
  }
  if ($w) $where = ' where '.join (" $data[boolean] ", $w).' ';
  //echo $where;
  $list_of_numbers = searched_get_list_of_numbers('a',$where,$data[any],$only_any,$data[user_item],$data[boolean]);
  if (!$list_of_numbers) no_result('article');
  elseif ($data[active])
  { unset($x);
    if ($data[active]=='yes') $x = "(status = 'enabled' AND (t1<$s[cas] OR t1=0) AND (t2>$s[cas] OR t2=0))";
    elseif ($data[active]=='no') $x = "(status = 'disabled' OR t1>$s[cas] OR (t2<$s[cas] AND t2>0))";
    if ($x)
    { if ($list_of_numbers) $list_of_numbers .= " AND $x ";
      else $list_of_numbers = $x; // jedina podminka bylo active
    }
  }
  if ($list_of_numbers) $where = "where ($list_of_numbers) and (status = 'enabled' or status = 'disabled')";
  else no_result('article');
}
else $where = "where n = '$data[n]' and (status = 'enabled' or status = 'disabled')";
if ($where) $where .= $s[allowed_cats_query_a]; else $where = " where 1 $s[allowed_cats_query_a] ";
//echo $where;

if (!$data[from]) $data[from] = 0; else $data[from] = $data[from] - 1;
if ($data[perpage]) $limit = " limit $data[from],$data[perpage]";

$x = dq("select count(*) from $s[pr]articles $where",1);
$pocet = mysql_fetch_row($x); $pocet = $pocet[0];

if (!$pocet) no_result('article');

if ($data[sort]) $orderby = "order by $data[sort]";
$q = dq("select * from $s[pr]articles $where group by n $orderby $data[order] $limit",1);

if (($data[perpage]) AND ($pocet>$data[perpage]))
{ $rozcesti = '<form method="get" action="articles.php">
  <input type="hidden" name="action" value="articles_searched">';
  foreach ($data as $k => $v)
  { if ($v)
    { if (is_array($v)) foreach ($v as $k1=>$v1) { if ($v1) $rozcesti .= '<input type="hidden" name="'.$k.'['.$k1.']" value="'.$v1.'">'; }
      else $rozcesti .= '<input type="hidden" name="'.$k.'" value="'.$v.'">';
    }
  }
  $rozcesti .= 'Show artices with begin of <select class="select10" name="from"><option value="1">1</option>';
  $y = ceil($pocet/$data[perpage]);  
  for ($x=1;$x<$y;$x++) { $od = $x*$data[perpage]+1; $rozcesti .= '<option value="'.$od.'">'.$od.'</option>'; }
  $rozcesti .= '</select>&nbsp;<input type="submit" value="Submit" name="B1" class="button10"></form><br />';
}

$od = $data[from]+1;
$do = $data[from]+$data[perpage]; if ($do>$pocet) $do = $pocet; if (!$data[perpage]) $do = $pocet;

echo $s[info].'<span class="text13a_bold">Articles Found: '.$pocet;
if (($pocet>1) AND ($od!=$do)) echo ', Showing Articles '.$od.' - '.$do.'</b></span><br /><br />'.$rozcesti;
else echo '<br /><br />';
echo '</span>';
$data[from] = $data[from] + 1;
prepare_and_display_articles($q,$data[edit_forms]);
ift();
}

######################################################################################
######################################################################################
######################################################################################

function prepare_and_display_articles($q,$edit_forms) {
global $s;

while ($x = mysql_fetch_assoc($q)) { $items[$x[n]] = $x; $numbers[] = $x[n]; }
if (!$numbers[0]) return false;

$x = 'AND (n = \''.implode('\' OR n = \'',$numbers).'\')';
$q = dq("select * from $s[pr]usit_values where use_for = 'a' $x",1);
while ($x = mysql_fetch_assoc($q))
{ $items[$x[n]]['user_item_'.$x[item_n]][code] = $x[value_code];
  $items[$x[n]]['user_item_'.$x[item_n]][text] = $x[value_text];
}
if ($edit_forms) echo '<form enctype="multipart/form-data" method="post" action="articles.php">'.check_field_create('admin').'<input type="hidden" name="action" value="articles_edited_multiple">';
else echo show_check_uncheck_all().'<form method="get" action="articles.php" id="myform"><input type="hidden" name="action" value="articles_updated_multiple">';
foreach ($items as $k=>$v)
{ if ($edit_forms) { $v[current_action] = 'article_edit'; $v[update_no_check] = '1'; article_create_edit_form($v); }
  else { $v[show_checkbox] = 1; show_one_article($v); }
}
if (!$numbers) ift();
if (!$edit_forms)
{ echo 'Action to do with selected articles: 
  <select class="select10" name="to_do"><option value="0">No action</option>
  <option value="enable">Enable</option>
  <option value="disable">Disable</option>
  <option value="move">Move to category</option>
  <option value="delete">Delete</option>
  </select> 
  <select class="select10" name="category">'.categories_selected('a',0,1,1,0,0).'</select>
  ';
}
echo '<input type="submit" name="submit" value="Submit" class="button10"></form>';
ift();
}



#################################################################################
#################################################################################
#################################################################################

?>