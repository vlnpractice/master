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

include('./ads_functions.php');

switch ($_GET[action]) {
case 'ads_queue_home'			: ads_queue_home();
case 'ads_queue_show'			: ads_queue_show($_GET);
case 'ads_categories_import'	: ads_categories_import();
case 'ads_import_form'			: ads_import_form();
case 'ads_import_form1'			: ads_import_form1($_GET);
case 'ads_editor_picks'			: ads_editor_picks();
case 'ads_duplicate_form'		: ads_duplicate_form();
case 'ads_duplicate'			: ads_duplicate($_GET[what]);
case 'ads_search'				: ads_search();
case 'ads_searched'				: ads_searched($_GET);
case 'ads_updated_multiple'		: ads_updated_multiple($_GET);
}
switch ($_POST[action]) {
case 'ads_approved'				: ads_approved($_POST);
case 'ads_imported'				: ads_imported($_POST);
case 'ads_import_count'			: ads_import_count($_POST);
case 'ads_edited_multiple'		: ads_edited_multiple($_POST);
case 'ads_updated_multiple'		: ads_updated_multiple($_POST);
}

#############################################################################

function ads_import_form() {
global $s;
$s[max_cats] = 1;
ih();
echo page_title('Import Ads');
echo '<form method="get" action="ads_list.php">'.check_field_create('admin').'
<input type="hidden" name="action" value="ads_import_form1">
<table border="0" width="600" cellspacing="0" cellpadding="0" class="common_table">
<tr><td class="common_table_top_cell" colspan="2">Step #1 - Select a Category</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">';
echo str_replace('Categories','Import ads to category',categories_rows_form('c'));
echo '<tr><td colspan="2" align="center"><input type="submit" name="co" value="Continue" class="button10"></td></tr>
</td></tr>
</table></td></tr>
</table></form>';
ift();
}

#############################################################################

function ads_import_form1($in) {
global $s;
//foreach ($in[ad][0][categories][0] as $k=>$v) echo "$k - $v<br>";
ih();
echo page_title('Import Ads');
echo '<form enctype="multipart/form-data" method="post" action="ads_list.php">'.check_field_create('admin').'
<input type="hidden" name="action" value="ads_import_count">
<input type="hidden" name="c" value="'.$in[ad][0][categories][0].'">
<table border="0" width="500" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Step #2 - Enter Details</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">

<tr><td align="center" colspan="4">Structure of your file</td></tr>';
list($all_user_items_list,$avail_val) = get_category_usit($in[ad][0][categories][0],0,0);
$pocet = 13 + count($all_user_items_list);
for ($x=1;$x<=$pocet;$x++)
{ if ($x%2) echo '<tr>';
  ?>
  <td align="left" nowrap>Rank #<?PHP echo $x ?></td>
  <td align="left"><select name="rank[<?PHP echo $x ?>]" class="field10">
  <option value="0">None</option>
  <option value="area">Area title</option>
  <option value="title">Title</option>
  <option value="description">Description</option>
  <option value="detail">Detailed entry</option>
  <option value="url">URL</option>
  <option value="pub_phone1">Phone 1</option>
  <option value="pub_phone2">Phone 2</option>
  <option value="name">Owner's name</option>
  <option value="email">Owner's email</option>
  <option value="password">Password</option>
  <option value="created">Date created</option>
  <option value="t1">Valid from</option>
  <option value="t2">Valid to</option>
  <?PHP
  foreach ($all_user_items_list as $k=>$v) echo '<option value="user_item_'.$v[usit_n].'">'.$v[description].'</option>';
  echo '</select></td>';
  if (!$x%2) echo '</tr>';
}
echo '<tr><td align="left" colspan="4" nowrap>Separator (it separates individual values on the same line) 
<select name="separator" class="field10"><option value="|">| (recommended)</option><option value=",">,</option><option value=";">;</option></select><br />
<span class="text10">The character which will be used as separator can\'t be used in any value </span>
</td></tr>
<tr>
<td align="left" colspan="4" nowrap>Each value is enclosed by 
<select name="enclosed_by" class="field10">
<option value="0">None (recommended)</option><option value="&quot;">&quot;</option><option value="&#039;">&#039;</option>
</select></td></tr>
<tr>
<td align="left">File </td>
<td align="left" colspan="3"><input type="file" maxlength="255" name="file_upload" class="field10" style="width:450px"></td>
</tr>
<tr><td colspan="4" align="center"><input type="submit" name="co" value="Continue" class="button10"></td></tr>
</td></tr>
</table></td></tr>
</table></form>';
ift();
}

#############################################################################

function ads_import_count($in) {
global $s;
$filename = $s[phppath].'/data/imported';
if (file_exists($filename)) unlink ($filename);
move_uploaded_file($_FILES[file_upload][tmp_name],$filename);
if (file_exists($filename)) chmod($filename,0644);

foreach ($in[rank] as $k=>$v) if (!$v) unset ($in[rank][$k]);
$in[enclosed_by] = stripslashes(htmlspecialchars($in[enclosed_by],ENT_QUOTES));

ih();
$f = fopen ($filename,"r") or problem("Unable to read file<br />'$filename'");
while (!feof($f))
{ $line = fgets($f,100000);
  if (!trim($line)) continue;
  $pocet++;
}
echo info_line('The file you uploaded has '.$pocet.' lines - ads');
if ($pocet<100) 
{ echo '<form method="post" action="ads_list.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="ads_imported">
  <input type="hidden" name="separator" value="'.$in[separator].'">
  <input type="hidden" name="enclosed_by" value="'.$in[enclosed_by].'">
  <input type="hidden" name="c" value="'.$in[c].'">
  <input type="hidden" name="total" value="'.$pocet.'">';
  foreach ($in[rank] as $k=>$v) echo '<input type="hidden" name="rank['.$k.']" value="'.$v.'">';
  echo '<input type="submit" name="submit" value="Continue" class="button10"></form>';
}
else
{ echo 'If your PHP is running in a safe mode, you can\'t add all these ads at once. This is because scripts in safe mode can\'t run longer that the time which is set in the PHP configuration (default is 30 sec. but sometimes it is only 5 - 10 sec.). 
  If you are sure that PHP IS NOT running in a safe mode click the button <b>Import all ads</b> and be patient, it may take 15 minutes or more to add a bigger database.<br /><br />
  If PHP is running is a safe mode or if you are not sure, click the button <b>Import 100 ads</b>.
  <table border=0 width=350 cellspacing=10 cellpadding=0>
  <form method="post" action="ads_list.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="ads_imported">
  <input type="hidden" name="total" value="'.$pocet.'">
  <input type="hidden" name="separator" value="'.$in[separator].'">
  <input type="hidden" name="c" value="'.$in[c].'">
  <input type="hidden" name="enclosed_by" value="'.$in[enclosed_by].'">';
  foreach ($in[rank] as $k=>$v) echo '<input type="hidden" name="rank['.$k.']" value="'.$v.'">';
  echo '<td align="center"><input type="submit" name="submit" value="Import all ads" class="button10"></td>
  </form>
  <form method="post" action="ads_list.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="ads_imported">
  <input type="hidden" name="total" value="'.$pocet.'">
  <input type="hidden" name="separator" value="'.$in[separator].'">
  <input type="hidden" name="enclosed_by" value="'.$in[enclosed_by].'">
  <input type="hidden" name="c" value="'.$in[c].'">
  <input type="hidden" name="from" value="1">
  <input type="hidden" name="step" value="100">';
  foreach ($in[rank] as $k=>$v) echo '<input type="hidden" name="rank['.$k.']" value="'.$v.'">';
  echo '<td align="center"><input type="submit" name="submit" value="Import 100 ads" class="button10"></td>
  </form>
  </tr></table>
  ';
}
echo info_line('<br /><br />Important note:','Never hit your browser\'s "Reload" or "Back" button, otherwise some ads may be created more than once.');
ift();
}

#############################################################################

function ads_imported($in) {
global $s;


if ($in[enclosed_by])
{ $in[enclosed_by] = str_replace(chr(92),'',$in[enclosed_by]);
  if ($in[enclosed_by]=="'") $in[enclosed_by] = '&#039;'; elseif ($in[enclosed_by]=='"') $in[enclosed_by] = '&quot;';
  if ($in[enclosed_by]=='&#039;') $enclosed_by = "'"; elseif ($in[enclosed_by]=='&quot;') $enclosed_by = '"';
}

list($all_user_items_list,$avail_val) = get_category_usit($in[c],0,0);

//foreach ($all_user_items_list[1] as $k=>$v) echo "$k - $v<br>";exit;

ih();
echo '<br />';
$time0 = time(); $pocet = 0;
$f = fopen("$s[phppath]/data/imported",'r');
while (!feof($f))
{ $pocet ++; if ($pocet<$in[from]) { $line = fgets($f,10000); continue; }
  set_time_limit(600);
  if (time()>$time0+30) { header('X-pmaPing: Pong'); $time0 = time(); }
  if (time()>($time1+10)) { $time1=time(); echo ' Working ... '.str_repeat (' ',4000); flush(); }
  $line = fgets($f,100000);
  if (!trim($line)) continue;
  $line = str_replace(chr(92),'',$line);
  $pole = explode($in[separator],trim($line));
  if ($enclosed_by)
  { foreach ($pole as $k=>$v) $pole[$k] = ereg_replace("^".$enclosed_by,'',ereg_replace($enclosed_by."$",'',$v)); }
  $pole = replace_array_text($pole);
  unset ($user_items);
  foreach ($in[rank] as $k=>$v)
  { $x = $k - 1; $$v = $pole[$x];
    if (strstr($v,'user_item_')) { $y = str_replace('user_item_','',$v); $user_items[$y] = $pole[$x]; }
  }
  
  foreach ($all_user_items_list as $k=>$v)
  { if (($v[item_type]=='text') OR ($v[item_type]=='textarea')) $in_usit['user_item_'.$v[n]] = $user_items[$v[usit_n]];
    elseif ($v[item_type]=='checkbox') $in_usit['user_item_'.$v[n]] = '';
    elseif (($v[item_type]=='select') OR ($v[item_type]=='radio'))
    { foreach ($avail_val[$v[n]] as $avail_val_k=>$avail_val_v)
      foreach ($avail_val_v as $k1=>$v1) if (trim($v1)==$user_items[$v[usit_n]]) { $in_usit['user_item_'.$v[n]] = $avail_val_k; break 2; }
      if (!$in_usit['user_item_'.$v[n]]) $in_usit['user_item_'.$v[n]] = $all_user_items_list[$v[usit_n]][def_value_code];
	}
    if (!$in_usit['user_item_'.$v[n]]) $in_usit['user_item_'.$v[n]] = $all_user_items_list[$v[usit_n]][def_value_text];
  }
  
  if (!$created) $cas = $s[cas]; else $cas = make_time($created);
  if ($t1) $t1 = make_time($t1); else $t1 = 0; if ($t2) $t2 = make_time($t2); else $t2 = 0;
  if (!$password) $password = substr(md5(substr($url,1).substr($title,2).substr($description,3)),15);
  if (!$email) $email = $s[mail];
  unset($x); $q = dq("select * from $s[pr]cats where n = '$in[c]'",1); $x = mysql_fetch_assoc($q); $c_path = $x[path_n]; $cat = '_'.$in[c].'_';
  unset($x); $area = trim($area); $q = dq("select * from $s[pr]areas where title = '$area'",1); $x = mysql_fetch_assoc($q); $a_path = $x[path_n]; $area = '_'.$x[n].'_';
  $detail = refund_html($detail);
  $rewrite_url = discover_rewrite_url($title,0);
  $n = get_new_ad_n();
  dq("insert into $s[pr]ads values ('$n','$title','$description','$detail','$keywords','','$address','$youtube_video','$offer_wanted','$price','$cat','$c_path','$area','$a_path','$url','$name','$email','$pub_phone1','$pub_phone2','$in[country]','$in[region]','$in[city]','$in[zip]','$in[latitude]','$in[longitude]','0','$cas','$in[edited]','$t1','$t2','enabled','1','$rewrite_url','0','0','0','$x_bold_by','$x_featured_by','$x_home_page_by','$x_featured_gallery_by','$x_highlight_by','$x_pictures_by','$in[x_pictures_max]','$x_files_by','$in[x_files_max]','$x_paypal_by','$in[x_paypal_email]','$in[x_paypal_currency]','$in[x_paypal_price]','$in[x_paypal_disable]','$in[x_paypal_disabled]')",1);
  dq("insert into $s[pr]ads_stat values ('$n','0','0','0','0','0','$created')",1);
  get_geo_data($address,$n,0);
  add_update_user_items($n,0,ad_created_edited_get_usit($in[c],$in_usit));
  if (!$s[dont_recount]) recount_ads_cats_areas($c_path,'',$a_path,'');
  update_item_index('ad',$n);
  update_item_image1('a',$n);
  if (($in[step]) AND ($pocet>=($in[from]+$in[step]-1))) break;
  //exit;
}
$to = $in[from] + $in[step] - 1; $from = $to+1;
if (($in[step]) AND ($in[total]>$to))
{ echo 'Ads '.$in[from].' to '.$to.' created<br />
  <form method="post" action="ads_list.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="ads_imported">
  <input type="hidden" name="total" value="'.$in[total].'">
  <input type="hidden" name="separator" value="'.$in[separator].'">
  <input type="hidden" name="enclosed_by" value="'.$in[enclosed_by].'">
  <input type="hidden" name="c" value="'.$in[c].'">
  <input type="hidden" name="from" value="'.$from.'">
  <input type="hidden" name="step" value="'.$in[step].'">';
  foreach ($in[rank] as $k=>$v) echo '<input type="hidden" name="rank['.$k.']" value="'.$v.'">';
  echo '<td align="center"><input type="submit" name="submit" value="Import next 100 ads" class="button10"></td></form>';
}
else echo info_line('Total of '.($pocet-1).' ads created.');
exit;
}

#############################################################################

function ads_categories_import() {
global $s;
ih();
echo '<br><br><br><br><br><br><a href="ads_list.php?action=ads_import_form">Import ads</a><br><br><br><a href="categories.php?action=categories_import_form">Import categories</a>';
ift();
}

#############################################################################
#############################################################################
#############################################################################

function ads_edited_multiple($in) {
global $s;
foreach ($in[ad] as $k=>$v) { $ad = $v; $ad[n] = $k; ad_edited_process($ad); }
ih();
echo info_line('Entered changes have been saved');
echo '<a href="'.$_SERVER[HTTP_REFERER].'">Back</a>';
ift();
exit;
}

#################################################################################

function ads_updated_multiple($in) {
global $s;
//foreach ($in[ad] as $k=>$v) echo "$k - $v<br>";
//exit;

if ((!$in[to_do]) OR (!$in[ad])) header("Location: $_SERVER[HTTP_REFERER]");
$query = 'n = \''.implode('\' OR n = \'',$in[ad]).'\'';
ih();

if ($in[to_do]=='delete')
{ echo info_line('Total of '.count($in[ad]).' ads will be deleted. Continue?');
  echo '<form method="post" action="ads_list.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="ads_updated_multiple">
  <input type="hidden" name="to_do" value="deleted">
  <input type="hidden" name="back" value="'.$_SERVER[HTTP_REFERER].'">';
  foreach ($in[ad] as $k=>$v) echo '<input type="hidden" name="ad[]" value="'.$v.'">';
  echo '<input type="submit" name="submit" value="Yes, continue" class="button10"></form>';
}
else
{ $q = dq("select c_path,a_path from $s[pr]ads where $query and status != 'queue'",1);
  while ($x=mysql_fetch_assoc($q)) { $a_paths[] = $x[a_path]; $c_paths[] = $x[c_path]; }
  if ($in[to_do]=='enable')
  { dq("update $s[pr]ads set status = 'enabled' where $query",1);
    $info = 'Ads Enabled: '.mysql_affected_rows();
  }
  elseif ($in[to_do]=='disable')
  { dq("update $s[pr]ads set status = 'disabled' where $query",1);
    $info = 'Ads Disabled: '.mysql_affected_rows();
  }
  elseif ($in[to_do]=='deleted')
  { delete_ads_process($in[ad]);
    $info = 'Ads Deleted: '.count($in[ad]);
  }
  if (!$s[dont_recount]) recount_ads_cats_areas(implode(' ',$c_paths),'',implode(' ',$a_paths),'');
  echo info_line($info);
}
if ($in[back]) $back = $in[back]; else $back = $_SERVER[HTTP_REFERER];
echo '<br><br><a href="'.$back.'">Back</a>';
ift();
}

##################################################################################
##################################################################################
##################################################################################

function ads_queue_home() {
global $s;
ih();
echo page_title('Queue');
$q = dq("select count(*) from $s[pr]ads where status = 'queue'",1);
$count = mysql_fetch_row($q); $count = $count[0];
if (!$count) echo '<br><br><br>No one classified ad in the queue';
else
{ echo '<form method="get" action="ads_list.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="ads_queue_show">
  <table border="0" width="500" cellspacing="0" cellpadding="0" class="common_table">
  <tr><td class="common_table_top_cell" nowrap>Classified ads in the queue: '.$count.'</td></tr>
  <tr><td align="center">
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
  <tr><td align="center" nowrap>Select the number of classified ads to show on each page.</td></tr>
  <tr><td align="center" nowrap>
  <select name="perpage" class="field10"><option value="0">All</option>';
  if ($count>5) echo '<option value="5">5</option>';
  if ($count>10) echo '<option value="10">10</option>';
  if ($count>20) echo '<option value="20">20</option>';
  if ($count>30) echo '<option value="30">30</option>';
  echo '</select> 
  <input type="submit" value="Submit" name="B1" class="button10">
  </td></tr></table>
  </td></tr></table></form>';
}
echo '<br><br>';
comments_unapproved_info();
ift();
}

##################################################################################

function comments_unapproved_info($what) {
global $s;
$q = dq("select count(*) from $s[pr]comments where approved = '0'",1);
$count = mysql_fetch_row($q);
if (!$count[0]) echo '<br><br>No one comment in the queue';
else
{ echo '  <form method="get" action="comments.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="comments_queue_show">
  <table border="0" width="500" cellspacing="0" cellpadding="0" class="common_table">
  <tr><td class="common_table_top_cell" nowrap>Comments in the queue: '.$count[0].'</td></tr>
  <tr><td align="center">
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
  <tr><td align="center" nowrap>Select the number of comments to show on each page.<br><br>
  <select name="perpage" class="field10"><option value="0">All</option>';
  if ($count[0]>20) echo '<option value="20">20</option>';
  if ($count[0]>50) echo '<option value="50">50</option>';
  if ($count[0]>100) echo '<option value="100">100</option>';
  echo '</select> 
  <input type="submit" value="Submit" name="B1" class="button10">
  </td></tr></table>
  </td></tr></table></form>';
}
}

##################################################################################
##################################################################################
##################################################################################

function ads_queue_show($in) {
global $s;
if (!$in[from]) $from = 0; else $from = $in[from] - 1;

$q = dq("select count(*) from $s[pr]ads where status = 'queue'",1);
$count = mysql_fetch_row($q); $count = $count[0];
if (!$count) { ih(); echo $s[info].info_line('<br><br>No one classified ad in the queue'); ift(); }
$show[0] = $from + 1;
$show[1] = $from + $in[perpage]; if ($show[1]>$count) $show[1] = $count; if (!$in[perpage]) $show[1] = $count;

if (($in[perpage]) AND ($count>$in[perpage]))
{ $rozcesti = '
  <form action="ads_list.php" method="get" name="form1">'.check_field_create('admin').'
  <input type="hidden" name="action" value="ads_queue_show">
  <input type="hidden" name="perpage" value="'.$in[perpage].'">
  Show classifieds with begin of&nbsp;&nbsp;<select class="field10" name="from"><option value="1">1</option>';
  $y = ceil($count/$in[perpage]);  
  for ($x=1;$x<$y;$x++)
  { $od = $x * $in[perpage] + 1;
    $rozcesti .= "<option value=\"$od\">$od</option>";
  }
  $rozcesti .= '</select>&nbsp;&nbsp;<input type="submit" value="Submit" name="B1" class="button10">
  </form><br>';
}

if ($in[perpage]) $limit = " limit $from,$in[perpage]";
$q = dq("select * from $s[pr]ads where status = 'queue' order by n $limit",1);
while ($x = mysql_fetch_assoc($q)) { $ads[$x[n]] = $x; $numbers[] = $x[n]; }

$reject_emails = '<option value="0">None</option>';
$dr = opendir("$s[phppath]/styles/_common/email_templates");
while ($x = readdir($dr))
{ if ((strstr($x,'reject_ad_')) AND (is_file("$s[phppath]/styles/_common/email_templates/$x")))
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

echo $s[info].page_title('Classified Ads in The Queue: '.$count.', Showing Classified Ads '.$show[0].' - '.$show[1]).$rozcesti;
$s[queue] = 1;
echo '<form enctype="multipart/form-data" action="ads_list.php" method="post" name="muj">'.check_field_create('admin').'
<input type="hidden" name="action" value="ads_approved">
<input type="hidden" name="perpage" value="'.$in[perpage].'">
<input type="hidden" name="from" value="'.$from.'">';
foreach ($ads as $k=>$ad)
{ $ad[reject_emails] = $reject_emails;
  ad_create_edit_form($ad);
}
echo '<input type="submit" name="submit" value="Submit" class="button10"></form>';
ift();
}

##################################################################################

function ads_approved($in) {
global $s;
include($s[phppath].'/data/data_forms.php');
foreach ($in[ad] as $n=>$ad)
{ if (!$ad[approve]) continue;
//foreach ($ad as $k=>$v) echo "$k - $v<br>";//exit;
/*  if ((!$administrator[allads]) AND ((count($ad[categories])>1) OR (!in_array($ad[categories][0],$s[allowed_cats_l]))) )
  { $s[info] .= "ad <a target=\"_blank\" href=\"$ad[url]\">$ad[title]</a> skipped. You do not have permissions to add a ad to selected category/categories.<br>";
    continue;
  }*/
  $old = get_ad_variables($n,0);
  unset($email_sent);
  if ($ad[approve]=='yes')
  { $s[no_copy_to_queue] = 1;
    upload_files('a',$n,$ad,1,0,$ad[delete_image],$ad[delete_file]);

    $created = get_timestamp($ad[created][d],$ad[created][m],$ad[created][y],'start',$ad[created_time]);
    if ($ad[mark_edited]) $edited = "edited = '$s[cas]',"; else $edited = ''; 
    $t1 = get_timestamp($ad[t1][d],$ad[t1][m],$ad[t1][y],'start');
    $t2 = get_timestamp($ad[t2][d],$ad[t2][m],$ad[t2][y],'end');
    $ad = replace_array_text($ad);
    $ad[detail] = refund_html($ad[detail]);
    $ad[categories] = array_unique($ad[categories]);
    $c_path = ad_edit_get_categories($ad[categories]);
    unset($x); foreach ($ad[categories] as $k => $v) if ($v) $x[] = '_'.$v.'_'; $c = implode(' ',$x);
    $a_path = ad_edit_get_areas($ad[areas]);
    unset($x); foreach ($ad[areas] as $k => $v) if ($v) $x[] = '_'.$v.'_'; $a = implode(' ',$x);
    $en_cats = has_some_enabled_categories($categories);
    if (!$ad[rewrite_url]) $ad[rewrite_url] = discover_rewrite_url($ad[title],0);
    if ($ad[enabled]) $status = 'enabled'; else $status = 'disabled';
    $user_data = get_usern($ad[email]);
    $ad[zip] = str_replace(' ','',$ad[zip]);

    foreach ($s[extra_options] as $k=>$v)
    { $variable_name = 'x_'.$v.'_by';
      $$variable_name = get_timestamp($ad['x_'.$v.'_by'][d],$ad['x_'.$v.'_by'][m],$ad['x_'.$v.'_by'][y],'end');
    }
    $x_pictures_by = get_timestamp($ad[x_pictures_by][d],$ad[x_pictures_by][m],$ad[x_pictures_by][y],'end');
    $x_files_by = get_timestamp($ad[x_files_by][d],$ad[x_files_by][m],$ad[x_files_by][y],'end');
    /*
    if ($old[n]) // edited existing ad
    { dq("delete from $s[pr]ads where n = '$n' and status != 'queue'",1);
      $q = dq("select * from $s[pr]files where item_n = '$n' and what = 'a' and queue = '1'",1);
      while ($x=mysql_fetch_assoc($q)) $new_files[$x[n]] = $x[filename];
      $q = dq("select * from $s[pr]files where item_n = '$n' and what = 'a' and queue = '0'",1);
      while ($x=mysql_fetch_assoc($q)) $old_files[$x[n]] = $x[filename];
      foreach ($old_files as $k=>$v) if (!in_array($v,$new_files)) delete_file_process($k,'',0,0,0);
      dq("delete from $s[pr]files where item_n = '$n' and what = 'a' and queue = '0'",1);
    }*/
    //exit;
    dq("delete from $s[pr]files where item_n = '$n' and what = 'a' and queue = '0'",1);
    dq("update $s[pr]files set queue = '0' where item_n = '$n' and what = 'a'",1);
    dq("delete from $s[pr]ads where n = '$n' and status != 'queue'",1);
    dq("insert into $s[pr]ads values ('$n','$ad[title]','$ad[description]','$ad[detail]','$ad[keywords]','','$ad[address]','$ad[youtube_video]','$ad[offer_wanted]','$ad[price]','$c','$c_path','$a','$a_path','$ad[url]','$ad[name]','$ad[email]','$ad[pub_phone1]','$ad[pub_phone2]','$ad[country]','$ad[region]','$ad[city]','$ad[zip]','$ad[latitude]','$ad[longitude]','$user_data[0]','$created','$ad[edited]','$t1','$t2','enabled','$en_cats','$ad[rewrite_url]','$ad[clicks_total]','0','0','$x_bold_by','$x_featured_by','$x_home_page_by','$x_featured_gallery_by','$x_highlight_by','$x_pictures_by','$ad[x_pictures_max]','$x_files_by','$ad[x_files_max]','$x_paypal_by','$ad[x_paypal_email]','$ad[x_paypal_currency]','$ad[x_paypal_price]','$ad[x_paypal_disable]','$ad[x_paypal_disabled]')",1);
    get_geo_data($ad[address],$n,0);
    add_update_user_items($n,0,ad_created_edited_get_usit($ad[categories][0],$ad));
    if (!$s[dont_recount]) recount_ads_cats_areas($c_path,'',$a_path,'');
    recount_ads_for_owner($user_data[0]);
    update_item_index('ad',$n);
    update_item_image1('a',$n);
    $s[info] .= 'Classified ad #'.$n.' - '.$ad[title].' has been approved.';
    if ($s[i_approved])
    { $email_vars = get_variables_ad_email($n,$ad);
      if ($email_vars[to]) { mail_from_template('ad_approved.txt',$email_vars); $email_sent = 1; }
    }
  }
  elseif ($ad[approve]=='no')  // reject
  { $q = dq("select * from $s[pr]files where item_n = '$n' and what = 'a' and queue = '1'",1);
    while ($x=mysql_fetch_assoc($q)) $queue_files[$x[n]] = $x[filename];
    $q = dq("select * from $s[pr]files where item_n = '$n' and what = 'a' and queue = '0'",1);
    while ($x=mysql_fetch_assoc($q)) $old_files[$x[n]] = $x[filename];
    foreach ($queue_files as $k=>$v) if (!in_array($v,$old_files)) delete_file_process($k,'',0,0,0);
    dq("delete from $s[pr]files where item_n = '$n' and what = 'a' and queue = '1'",1);
	$s[info] .= 'Classified ad #'.$n.' - '.$ad[title].' has been rejected.';
    if ($ad[reject_email])
    { $email_vars = get_variables_ad_email($n,$ad);
      if ($email_vars[to]) { mail_from_template($ad[reject_email],$email_vars); $email_sent = 1; }
    }
    elseif (($ad[reject_email_custom]) AND ($ad[email_subject]) AND ($ad[email_text]))
    { $email_vars = get_variables_ad_email($n,$ad);
	  foreach ($email_vars as $k=>$v) $ad[email_text] = str_replace("#%$k%#",$v,$ad[email_text]);
      my_send_mail('','',$email_vars[to],0,unhtmlentities($ad[email_subject]),unhtmlentities($ad[email_text]),1);
	  $email_sent = 1;
	}
	if (!$old[n]) delete_ads_process($n);
  }
  if ($email_sent) $s[info] .= ' Email sent.<br>'; else $s[info] .= ' Email not sent.<br>';
  dq("delete from $s[pr]ads_usit where n = '$n' and queue = '1'",1);
  dq("delete from $s[pr]ads where n = '$n' and status = 'queue'",1);
}
ads_queue_show($in);
}


#################################################################################

function get_variables_ad_email($n,$ad) {
global $s;
include("$s[phppath]/styles/_common/messages/common.php");
if ($ad[email]) $ad[to] = $ad[email]; else { $owner = get_user_variables($ad[owner]); $ad[to] = $owner[email]; }
if ($ad[offer_wanted]) $ad[ad_type] = $m[$ad[offer_wanted]]; else $ad[ad_type] = $m[na];
$x = list_of_categories_for_item('ad',0,$ad[categories],', ',0); $ad = array_merge((array)$ad,(array)$x);
$x = list_of_areas_for_item($ad[areas],', ',0); $ad = array_merge((array)$ad,(array)$x);
$ad[n] = $n;
return $ad;
}

#################################################################################
#################################################################################
#################################################################################

function ads_duplicate_form() {
global $s;
ih();
echo $s[info];
echo page_title('Search for Duplicate Classifieds','This function may take a long time if you have a bigger database.');
?>
<table border=0 width=400 cellspacing=10 cellpadding=0 class="common_table">
<form action="ads_list.php" method="get" name="form1"><?PHP echo check_field_create('admin') ?>
<input type="hidden" name="action" value="ads_duplicate">
<tr><td align="center">Action 
<select name="what" class="field10">
<option value="show">Show duplicate ads</option>
<option value="delete">Delete duplicate ads</option>
</select><br><br>
<input type="submit" name="submit" value="Check Now!" class="button10"></td></tr>
</form></table></td></tr></table><br>
<?PHP
ift();
}

##################################################################################

function ads_duplicate($what) {
global $s;
//echo $what; exit;
ih();
echo '<span id="processing"><span class="text13a_bold"><b>Searching for duplicate ads. It may take some time if you have a bigger database.<br>Please wait ...</b></span>'.str_repeat(' ',5000).'.</span>';
flush();
set_time_limit(1000);
$q = dq("select t1.url,t1.n from $s[pr]ads as t1,$s[pr]ads as t2 
where t1.url = t2.url AND t1.n != t2.n group by t1.url",1); 
$count = mysql_num_rows($q); if (!$count) $count = 0;

echo "<script>processing.style.display='none'</script>";
echo $s[info].info_line($count.' Duplicate Classifieds Found');
if (!$count) ift();

if ($what=='show')
{ echo '<script language="Javascript">
  <!--
  function checkAll(formId,cName,check) { for (i=0,n=formId.elements.length;i<n;i++) if (formId.elements[i].className.indexOf(cName) !=-1) formId.elements[i].checked = check; }
  -->
  </script>
  <span class="text10">
  <a class="ad10" href="javascript:void(0);" onclick="checkAll(document.getElementById(\'myform\'),\'bbb\',true);">Check</a>
  <a class="ad10" href="javascript:void(0);" onclick="checkAll(document.getElementById(\'myform\'),\'bbb\',false);">uncheck</a> all checkboxes
  </span>
  <form method="get" action="ads_list.php" name="myform">'.check_field_create('admin').'<input type="hidden" name="action" value="ads_updated_multiple">';
  while ($url = mysql_fetch_row($q))
  { echo info_line('URL: <a target="_blank" href="'.$url[0].'">'.$url[0].'</a>');
    $q1 = dq("select * from $s[pr]ads where url = '$url[0]'",1);
    prepare_and_display_ads_duplicate($q1);
  }
  echo 'Action to do with selected ads: 
  <select name="to_do" class="field10"><option value="0">No action</option>
  <option value="enable">Enable</option>
  <option value="disable">Disable</option>
  <option value="delete">Delete</option>
  </select><input type="submit" name="submit" value="Submit" class="button10"></form>';
}
if ($what=='delete')
{ while ($url = mysql_fetch_row($q))
  { dq("delete from $s[pr]ads where url = '$url[0]' AND not(n = '$url[1]')",1);
    $affected = $affected + mysql_affected_rows();
  }
  echo info_line($affected.' Classifieds Deleted');
  if ($affected) echo 'Now run function <a href="rebuild.php?action=reset_rebuild_home">Recount all classified ads.</a>';
}
ift();
}

##################################################################################

function prepare_and_display_ads_duplicate($q) {
global $s;
while ($x = mysql_fetch_assoc($q)) { $ads[$x[n]] = $x; $ad_numbers[] = $x[n]; }
if (!$ad_numbers[0]) return false;
$x = 'AND (n = \''.implode('\' OR n = \'',$ad_numbers).'\')';
$q = dq("select * from $s[pr]ads_usit where queue = '0' $x",1);
while ($x = mysql_fetch_assoc($q))
{ $ads[$x[n]]['user_item_'.$x[item_n]][code] = $x[value_code];
  $ads[$x[n]]['user_item_'.$x[item_n]][text] = $x[value_text];
}
foreach ($ads as $k=>$v) { $v[show_checkbox] = 1; show_one_ad($v); }
}

##################################################################################
##################################################################################
##################################################################################

function ads_search() {
global $s;
$categories = categories_selected('ad',0,1,1,0,0);
$categories_first = categories_selected('ad_first',0,1,1,0,0);
$areas = areas_selected(0,1,1);
$areas_first = select_list_first_areas('','');
ih();
echo '<form method="get" action="ads_list.php">'.check_field_create('admin').'
<input type="hidden" name="action" value="ads_searched">
<table border="0" width="500" cellspacing="0" cellpadding="0" class="common_table">
<tr><td class="common_table_top_cell">Search for Classifieds</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr>
<td align="left" valign="top">Number <span class="text10"><br>If you enter an ad number, other fields in this form will be ignored.<br></span></td>
<td align="left" valign="top"><input class="field10" name="n" size=10 maxlength=10></td></tr>
<tr>
<td align="left" valign="top" nowrap>Category</td>
<td align="left" valign="top"><select  class="field10" name="c"><option value="0">Any category</option>'.$categories.'</select></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Category</td>
<td align="left" valign="top" nowrap><select class="field10" name="bigboss"><option value="0">Any category</option>'.$categories_first.'</select> incl. subcategories</td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Area</td>
<td align="left" valign="top"><select  class="field10" name="a"><option value="0">Any area</option>'.$areas.'</select></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Area</td>
<td align="left" valign="top" nowrap><select class="field10" name="a_bigboss">'.str_replace('All areas','Any area',$areas_first).'</select> incl. subareas</td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Any field contains </td>
<td align="left" valign="top"><input class="field10" name="any" maxlength="100" style="width:550px"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Title contains </td>
<td align="left" valign="top"><input class="field10" name="title" maxlength="100" style="width:550px"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Description contains </td>
<td align="left" valign="top"><input class="field10" name="description" maxlength="100" style="width:550px"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Detailed entry contains </td>
<td align="left" valign="top"><input class="field10" name="detail" maxlength="100" style="width:550px"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>URL contains </td>
<td align="left" valign="top"><input class="field10" name="url" maxlength="100" style="width:550px"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Contact name contains </td>
<td align="left" valign="top"><input class="field10" name="name" maxlength="100" style="width:550px"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Contact email contains </td>
<td align="left" valign="top"><input class="field10" name="email" maxlength="100" style="width:550px"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Contact phones contain </td>
<td align="left" valign="top"><input class="field10" name="pub_phone" maxlength="100" style="width:550px"></td>
</tr>';
//echo user_defined_items_form('l','search_form');
echo '
<tr>
<td align="left" valign="top" nowrap>Ad type </td>
<td align="left" valign="top" nowrap>N/A<input type="radio" name="offer_wanted" value="0" checked>&nbsp; Offer<input type="radio" name="offer_wanted" value="offer">&nbsp; Wanted<input type="radio" name="offer_wanted" value="wanted"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Enabled </td>
<td align="left" valign="top" nowrap>N/A<input type="radio" name="enabled" value="0" checked>&nbsp; Yes<input type="radio" name="enabled" value="yes">&nbsp; No<input type="radio" name="enabled" value="no"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Validity </td>
<td align="left" valign="top"><select class="field10" name="valid"><option value="0">N/A</option><option value="yes">Classifieds which are currently valid</option><option value="no">Classifieds which are currently not valid</option><option value="future">Classifieds which will valid in the future</option><option value="past">Classifieds which have been valid in the past</option></select></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Bold </td>
<td align="left" valign="top" nowrap>N/A<input type="radio" name="x_bold" value="0" checked>&nbsp; Yes<input type="radio" name="x_bold" value="yes">&nbsp; No<input type="radio" name="x_bold" value="no"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Highlighted </td>
<td align="left" valign="top" nowrap>N/A<input type="radio" name="x_highlight" value="0" checked>&nbsp; Yes<input type="radio" name="x_highlight" value="yes">&nbsp; No<input type="radio" name="x_highlight" value="no"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Featured </td>
<td align="left" valign="top" nowrap>N/A<input type="radio" name="x_featured" value="0" checked>&nbsp; Yes<input type="radio" name="x_featured" value="yes">&nbsp; No<input type="radio" name="x_featured" value="no"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Home page placement </td>
<td align="left" valign="top" nowrap>N/A<input type="radio" name="x_home_page" value="0" checked>&nbsp; Yes<input type="radio" name="x_home_page" value="yes">&nbsp; No<input type="radio" name="x_home_page" value="no"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Featured gallery </td>
<td align="left" valign="top" nowrap>N/A<input type="radio" name="x_featured_gallery" value="0" checked>&nbsp; Yes<input type="radio" name="x_featured_gallery" value="yes">&nbsp; No<input type="radio" name="x_featured_gallery" value="no"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Allowed extra pictures </td>
<td align="left" valign="top" nowrap>N/A<input type="radio" name="x_pictures" value="0" checked>&nbsp; Yes<input type="radio" name="x_pictures" value="yes">&nbsp; No<input type="radio" name="x_pictures" value="no"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Allowed extra files </td>
<td align="left" valign="top" nowrap>N/A<input type="radio" name="x_files" value="0" checked>&nbsp; Yes<input type="radio" name="x_files" value="yes">&nbsp; No<input type="radio" name="x_files" value="no"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Paypal "Buy now" button </td>
<td align="left" valign="top" nowrap>N/A<input type="radio" name="x_paypal" value="0" checked>&nbsp; Yes<input type="radio" name="x_paypal" value="yes">&nbsp; No<input type="radio" name="x_paypal" value="no"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Type of search </td>
<td align="left" valign="top" nowrap>AND <input type="radio" value="and" name="boolean" checked> OR <input type="radio" value="or" name="boolean"></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Results per page </td>
<td align="left" valign="top"><select class="field10" name="perpage">
<option value="0">All</option><option value="10">10</option><option value="20">20</option>
<option value="50">50</option><option value="100">100</option>
<option value="200">200</option><option value="500">500</option></select>
</td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Sort by </td>
<td align="left" valign="top">
<select class="field10" name="sort"><option value="title">Title</option><option value="clicks_total">Popularity (clicks this month)</option><option value="created">Date created</option><option value="name">Contact name</option></select>
<select class="field10" name="order"><option value="asc">Ascending</option><option value="desc">Descending</option></select>
</td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Edit forms </td>
<td align="left" valign="top"><input type="checkbox" name="edit_forms" value="1"></td>
</tr>
<tr><td colspan=2 align="center"><input type="submit" value="Search" name="B1" class="button10"></td></tr>
</table></td></tr></table></form>';
ift();
}

##################################################################################
##################################################################################
##################################################################################

function ads_searched($in) {
global $s;

//foreach ($in as $k=>$v) echo "$k - $v<br>";

foreach ($in as $k=>$v)
{ if (is_array($v)) foreach ($v as $k1=>$v1) { if ($v1) $x[] = $k.'['.$k1.']='.$v1; }
  elseif ($v) $x[] = "$k=$v";
}
$referral = implode('&',$x);
if (!$in[boolean]) $in[boolean] = 'and';
ih();

if ($in[n]) $where = "n = '$in[n]'";
else
{ if ($in[c]) $w[] = "c like '%\_$in[c]\_%'";
  if ($in[bigboss]) $w[] = "c_path like '%\_$in[bigboss]\_%'";
  if ($in[area]) $w[] = "a like '%\_$in[area]\_%'";
  if ($in[a]) $w[] = "a like '%\_$in[a]\_%'";
  if ($in[a_bigboss]) $w[] = "a_path like '%\_$in[a_bigboss]\_%'";
  if ($in[any])
  { if (!$w[0]) $only_any = 1;
    $w[] = "(title like '%$in[any]%' OR description like '%$in[any]%' OR detail like '%$in[any]%' OR url like '%$in[any]%' OR name like '%$in[any]%' OR email like '%$in[any]%' OR pub_phone1 like '%$in[any]%' OR pub_phone2 like '%$in[any]%')";
  }
  if (strstr($in[title],'|')) $w[] = "title like '".str_replace('|','',$in[title])."%'"; elseif ($in[title]) $w[] = "title like '%$in[title]%'";
  if ($in[description]) $w[] = "description like '%$in[description]%'";
  if ($in[detail]) $w[] = "detail like '%$in[detail]%'";
  if ($in[url]) $w[] = "url like '%$in[url]%'";
  if ($in[name]) $w[] = "name like '%$in[name]%'";
  if ($in[email]) $w[] = "email like '%$in[email]%'";
  if ($in[pub_phone]) $w[] = "(pub_phone1 like '%$in[pub_phone]%' OR pub_phone2 like '%$in[pub_phone]%')";
  
  if ($in[username]) { $user_data = get_usern($in[email]); if ($user_data[0]) $w[] = "owner = '$user_data[0]'"; }
  if ($in[owner]) $w[] = "owner = '$in[owner]'";
  
  if ($in[offer_wanted]) $w[] = "offer_wanted = '$in[offer_wanted]'";
  if ($in[enabled]=='yes') $w[] = "status = 'enabled'"; elseif ($in[enabled]=='no') $w[] = "status = 'disabled'";
  switch ($in[valid])
  { case 'yes'		: $w[] = "(t1 < '$s[cas]' AND (t2 > '$s[cas]' OR t2 = 0))"; break;
    case 'no'		: $w[] = "(t1 > '$s[cas]' OR (t2 < '$s[cas]' AND t2 != 0))"; break;
    case 'future'	: $w[] = "t1 > '$s[cas]'"; break;
    case 'past'		: $w[] = "(t2 < '$s[cas]' AND t2 != 0)"; break;
  }
  if ($in[x_bold]=='yes') $w[] = "x_bold_by > '$s[cas]'"; elseif ($in[x_bold]=='no') $w[] = "x_bold_by < '$s[cas]'";
  if ($in[x_highlight]=='yes') $w[] = "x_highlight_by > '$s[cas]'"; elseif ($in[x_highlight]=='no') $w[] = "x_highlight_by < '$s[cas]'";
  if ($in[x_featured]=='yes') $w[] = "x_featured_by > '$s[cas]'"; elseif ($in[x_featured]=='no') $w[] = "x_featured_by < '$s[cas]'";
  if ($in[x_home_page]=='yes') $w[] = "x_home_page_by > '$s[cas]'"; elseif ($in[x_home_page]=='no') $w[] = "x_home_page_by < '$s[cas]'";
  if ($in[x_featured_gallery]=='yes') $w[] = "x_featured_gallery_by > '$s[cas]'"; elseif ($in[x_featured_gallery]=='no') $w[] = "x_featured_gallery_by < '$s[cas]'";
  if ($in[x_pictures]=='yes') $w[] = "x_pictures_by > '$s[cas]'"; elseif ($in[x_pictures]=='no') $w[] = "x_pictures_by < '$s[cas]'";
  if ($in[x_files]=='yes') $w[] = "x_files_by > '$s[cas]'"; elseif ($in[x_files]=='no') $w[] = "x_files_by < '$s[cas]'";
  if ($in[x_paypal]=='yes') $w[] = "(x_paypal_by > '$s[cas]' and x_paypal_email != '')"; elseif ($in[x_paypal]=='no') $w[] = "(x_paypal_by < '$s[cas]' or x_paypal_email = '')";
  if ($w) $where = '('.join(" $in[boolean] ",$w). ") and status != 'queue'"; else $where = "status != 'queue'";
}

//echo $where;

if (!$in[from]) $in[from] = 0; else $in[from] = $in[from] - 1;
if ($in[perpage]) $limit = " limit $in[from],$in[perpage]";

$x = dq("select count(*) from $s[pr]ads where $where",1);
$count = mysql_fetch_row($x); $count = $count[0];

if (!$count) no_result('classified ad');

if ($in[sort]) $orderby = "order by $in[sort] $in[order]";
$q = dq("select * from $s[pr]ads where $where $orderby $limit",1);

if (($in[perpage]) AND ($count>$in[perpage]))
{ $rozcesti = '<form method="get" action="ads_list.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="ads_searched">';
  foreach ($in as $k => $v)
  { if ($v)
    { if (is_array($v)) foreach ($v as $k1=>$v1) { if ($v1) $rozcesti .= '<input type="hidden" name="'.$k.'['.$k1.']" value="'.$v1.'">'; }
      else $rozcesti .= '<input type="hidden" name="'.$k.'" value="'.$v.'">';
    }
  }
  $rozcesti .= 'Show classifieds with begin of <select class="field10" name="from"><option value="1">1</option>';
  $y = ceil($count/$in[perpage]);  
  for ($x=1;$x<$y;$x++) { $od = $x*$in[perpage]+1; $rozcesti .= '<option value="'.$od.'">'.$od.'</option>'; }
  $rozcesti .= '</select>&nbsp;&nbsp;<input type="submit" value="Submit" name="B1" class="button10"></form><br>';
}

$od = $in[from]+1;
$do = $in[from]+$in[perpage]; if ($do>$count) $do = $count; if (!$in[perpage]) $do = $count;

echo $s[info];
echo info_line("Classifieds Found: $count");
if (($count>1) AND ($od!=$do)) echo 'Showing Classifieds '.$od.' - '.$do.'<br><br>'.$rozcesti;
else echo '<br><br>';
echo '</span>';
$in[from] = $in[from] + 1;
prepare_and_display_ads($q,$in[edit_forms]);
ift();
}

######################################################################################
######################################################################################
######################################################################################

function prepare_and_display_ads($q,$edit_forms) {
global $s;
while ($x = mysql_fetch_assoc($q)) { $ads[$x[n]] = $x; $ad_numbers[] = $x[n]; }
if (!$ad_numbers[0]) return false;

if ($edit_forms) echo '<form enctype="multipart/form-data" method="post" action="ads_list.php">'.check_field_create('admin').'<input type="hidden" name="action" value="ads_edited_multiple">';
else echo '<script language="Javascript">
<!--
function checkAll(formId,cName,check) { for (i=0,n=formId.elements.length;i<n;i++) if (formId.elements[i].className.indexOf(cName) !=-1) formId.elements[i].checked = check; }
-->
</script>
<span class="text10">
<a class="link10" href="javascript:void(0);" onclick="checkAll(document.getElementById(\'myform\'),\'bbb\',true);">Check</a>
<a class="link10" href="javascript:void(0);" onclick="checkAll(document.getElementById(\'myform\'),\'bbb\',false);">uncheck</a> all checkboxes<br><br>
</span>
<form method="get" action="ads_list.php" name="myform">'.check_field_create('admin').'<input type="hidden" name="action" value="ads_updated_multiple">';
foreach ($ads as $k=>$ad)
{ if ($edit_forms) { $ad[current_action] = 'ad_edit'; $ad[update_no_check] = '1'; ad_create_edit_form($ad); }
  else { $ad[show_checkbox] = 1; show_one_ad($ad); }
}
if (!$ad_numbers) ift();
if (!$edit_forms)
{ echo 'Action to do with selected ads: 
  <select name="to_do" class="field10"><option value="0">No action</option>
  <option value="enable">Enable</option>
  <option value="disable">Disable</option>
  <option value="delete">Delete</option>
  </select> ';
}
echo '<input type="submit" name="submit" value="Submit" class="button10"></form>';
}

######################################################################################
######################################################################################
######################################################################################

?>