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
case 'ad_create_step_1'		: ad_create_step_1(0);
case 'ad_create_step_2'		: ad_create_step_2($_GET);
case 'ad_edit'				: ad_create_edit($_GET[n]);
case 'ad_delete'			: ad_delete($_GET[n]);
case 'ad_copy'				: ad_create_edit($_GET[n]);
case 'ad_manage'			: ad_manage($_GET);
}
switch ($_POST[action]) {
case 'ad_created'			: ad_created($_POST);
case 'ad_edited'			: ad_edited($_POST);
}
show_one_ad($_GET[n]);

##################################################################################
##################################################################################
##################################################################################

function ad_create_step_1($n) {
global $s;
$s[max_cats] = 1;
$ad[n] = $n;
ih();
echo page_title('Create a New Ad');
echo '<form method="get" action="ad_details.php">'.check_field_create('admin').'
<input type="hidden" name="action" value="ad_create_step_2">
<table border="0" width="720" cellspacing="0" cellpadding="0" class="common_table">
<tr><td class="common_table_top_cell" colspan="2">Step #1 - Select a Category</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">';
echo categories_rows_form('ad',$ad);
echo '<tr><td colspan="2" align="center"><input type="submit" name="co" value="Continue" class="button10"></td></tr>
</td></tr>
</table></td></tr>
</table></form>';
ift();
}

######################################################################################

function ad_create_step_2($in) {
global $s;
ih();
if ($in[n]) $n = $in[n]; else $n = 0;
$a[c] = $in[ad][0][categories][0];
echo page_title('Create a New Ad: Step #2 - Enter Classified Ad Details');
echo '<form enctype="multipart/form-data" method="post" action="ad_details.php">'.check_field_create('admin').'
<input type="hidden" name="action" value="ad_created">
<table border=0 width=750 cellspacing=0 cellpadding=0>
';
ad_create_edit_form($a);
echo '</td></tr>
<tr><td align="center"><br><input type="submit" name="co" value="Continue" class="button10"></td></tr>
</td></tr>
</table></form><br>';
ift();
}

######################################################################################
######################################################################################
######################################################################################

function ad_create_edit($n) {
global $s;
if ($_GET[action]) $current_action = $_GET[action]; else $current_action = $_POST[action];
if ($current_action != 'ad_create')
{ $ad = get_ad_variables($n);
  $usit = get_ad_usit_variables($n,0);
  $ad = array_merge((array)$ad,(array)$usit);
}
else $ad[n] = 0;

$ad[current_action] = $current_action;
switch ($current_action) {
case 'ad_create'	: $action = 'ad_created'; $info = 'Create a New Classified Ad'; break;
case 'ad_edit'		: $action = 'ad_edited'; $info = 'Edit Selected Classified Ad'; break;
case 'ad_edited'	: $action = 'ad_edited'; $info = 'Edit Selected Classified Ad'; break;
case 'ad_copy'		: $action = 'ad_created'; $info = 'Copy Selected Classified Ad'; $ad[old_n] = $ad[n]; $ad[n] = 0; break;
}
ih();
echo info_line($info);
echo '<table border=0 width="600" cellspacing="0" cellpadding="0">
<form enctype="multipart/form-data" action="ad_details.php" method="post">'.check_field_create('admin').'
<input type="hidden" name="action" value="'.$action.'">
<input type="hidden" name="ad[0][load_n]" value="'.$ad[old_n].'">
<tr><td align="center">';
ad_create_edit_form($ad);
echo '</td></tr>
<tr><td align="center"><br><input type="submit" name="co" value="Save" class="button10"></td></tr>
</td></tr>
</form></table><br>';
ift();
}

######################################################################################
######################################################################################
######################################################################################

function ad_created($in) {
global $s;

$in = $in[ad][0];
//foreach ($in as $k=>$v) echo "$k - $v<br>";exit;
if ($in[username]) $user_data = get_usern($in[email]);
$created = get_timestamp($in[created][d],$in[created][m],$in[created][y],'start',$in[created_time]);
$t1 = get_timestamp($in[t1][d],$in[t1][m],$in[t1][y],'start');
$t2 = get_timestamp($in[t2][d],$in[t2][m],$in[t2][y],'end');

$c_path = ad_edit_get_categories($in[categories]);
unset($x); foreach ($in[categories] as $k => $v) if ($v) $x[] = '_'.$v.'_'; $c = implode(' ',$x);
$a_path = ad_edit_get_areas($in[areas]);
unset($x); foreach ($in[areas] as $k => $v) if ($v) $x[] = '_'.$v.'_'; $a = implode(' ',$x);

$in = replace_array_text($in);
$in[detail] = refund_html($in[detail]);
$en_cats = has_some_enabled_categories($categories);
if (!$in[rewrite_url]) $in[rewrite_url] = discover_rewrite_url($in[title],0);
if ($in[enabled]) $status = 'enabled'; else $status = 'disabled';
$owner = get_usern($in[email]);
$in[zip] = str_replace(' ','',$in[zip]);

foreach ($s[extra_options] as $k=>$v)
{ $variable_name = 'x_'.$v.'_by';
  $$variable_name = get_timestamp($in['x_'.$v.'_by'][d],$in['x_'.$v.'_by'][m],$in['x_'.$v.'_by'][y],'end');
}
$x_pictures_by = get_timestamp($in[x_pictures_by][d],$in[x_pictures_by][m],$in[x_pictures_by][y],'end');
$x_files_by = get_timestamp($in[x_files_by][d],$in[x_files_by][m],$in[x_files_by][y],'end');

$n = get_new_ad_n();
dq("insert into $s[pr]ads values ('$n','$in[title]','$in[description]','$in[detail]','$in[keywords]','','$in[address]','$in[youtube_video]','$in[offer_wanted]','$in[price]','$c','$c_path','$a','$a_path','$in[url]','$in[name]','$in[email]','$in[pub_phone1]','$in[pub_phone2]','$in[country]','$in[region]','$in[city]','$in[zip]','$in[latitude]','$in[longitude]','$owner[0]','$created','$in[edited]','$t1','$t2','$status','$en_cats','$in[rewrite_url]','$in[clicks_total]','0','0','$x_bold_by','$x_featured_by','$x_home_page_by','$x_featured_gallery_by','$x_highlight_by','$x_pictures_by','$in[x_pictures_max]','$x_files_by','$in[x_files_max]','$x_paypal_by','$in[x_paypal_email]','$in[x_paypal_currency]','$in[x_paypal_price]','$in[x_paypal_disable]','$in[x_paypal_disabled]')",1);
dq("insert into $s[pr]ads_stat values ('$n','$owner[0]','0','0','0','0','$s[cas]')",1);
if ($in[use_address]) dq("update $s[pr]ads set latitude = '$in[latitude]', longitude = '$in[longitude]', country = '$in[country]', region = '$in[region]', city = '$in[city]', zip = '$in[zip]' where n = '$n'",1);
else get_geo_data($in[address],$n,0);
add_update_user_items($n,0,ad_created_edited_get_usit($in[categories][0],$in));
if (!$s[dont_recount]) recount_ads_cats_areas($c_path,'',$a_path,'');
recount_ads_for_owner($owner[0]);
update_item_index('ad',$n);
if ($in[load_n]) ad_copied_copy_files($in[load_n],$n); else upload_files('a',$n,$in,0,0);
ih();
echo info_line('Classified Ad Created');
show_one_ad($n);
ift();
}

##################################################################################

function ad_copied_copy_files($from,$to) {
global $s;
$q = dq("select * from $s[pr]files where item_n = '$from' and what = 'a' and queue = '0'",1);
while ($file = mysql_fetch_assoc($q))
{ if (!trim($file[filename])) continue;
  $file_name = str_replace($s[site_url],$s[phppath],$file[filename]);
  if ($file[file_type]=='image') $folder = 'images'; elseif ($file[file_type]=='file') $folder = 'download'; elseif ($file[file_type]=='video') $folder = 'video';
  $new_file_name = str_replace("$folder/$from","$folder/$to",$file_name);
  copy($file_name,$new_file_name);
  if ($file[file_type]=='image')
  { $file_name_big = preg_replace("/\/$from-/","/$from-big-",$file_name);
    $new_file_name_big = str_replace("images/$from","images/$to",$file_name_big);
    copy($file_name_big,$new_file_name_big);
  }
  $url = str_replace($s[phppath],$s[site_url],$new_file_name);
  dq("insert into $s[pr]files values(NULL,'a','$to','0','$file[file_n]','$url','$file[description]','$file[file_type]','$file[extension]','$file[size]')",1);
}
}

##################################################################################

function ad_edited($in) {
global $s;
unset($in[ad][0]);
foreach ($in[ad] as $k=>$v)
{ $ad = $v; 
  $ad[n] = $k; ad_edited_process($ad);
}
$s[info] = info_line('Selected ad has been updated');
ad_create_edit($ad[n]);
}

##################################################################################
##################################################################################
##################################################################################

function ad_manage($in) {
global $s;
$ad = get_ad_variables($in[n]);
if ($in[what]) { $status = 'enabled'; $enabled = 'Enabled'; } else { $status = 'disabled'; $enabled = 'Disabled'; }
dq("update $s[pr]ads set status = '$status' where n = '$in[n]'",1);
if (!$s[dont_recount]) recount_ads_cats_areas($ad[c_path],'',$ad[a_path],'');
ih();
echo info_line('<br><br>Selected Ad Has Been '.$enabled);
echo '<br><br><a href="javascript:history.go(-1)">Back</a>';
ift();
}

##################################################################################

function ad_delete($n) {
global $s;
delete_ads_process($n);
ih();
echo info_line('Selected Classified Ad Has Been Deleted');
echo '<br><br><a href="javascript:history.go(-1)">Back</a>';
ift();
}

######################################################################################
######################################################################################
######################################################################################

?>