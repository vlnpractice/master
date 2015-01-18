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
check_admin('areas');

switch ($_GET[action]) {
case 'areas_home'				: areas_home(0);
case 'areas_multiple_create'	: areas_home(0);
case 'areas_tree_admin'			: areas_tree_admin();
case 'area_edit'				: area_edit($_GET);
case 'area_copy'				: area_copy($_GET);
case 'area_delete'				: area_delete($_GET);
case 'areas_import_form'		: areas_import_form();
case 'show_country_areas'		: show_country_areas($_GET[country]);
}
switch ($_POST[action]) {
case 'area_created'				: area_created($_POST);
case 'area_edited'				: area_edited($_POST);
case 'areas_import_count'		: areas_import_count($_POST);
case 'areas_imported'			: areas_imported($_POST);
}

########################################################################################
########################################################################################
########################################################################################

function areas_home($n) {
global $s;
$x[n] = $n;
ih();
echo $s[info];
echo page_title('Areas');
area_create_edit_form('area_created',$x);
echo '<form action="areas.php" method="get" name="form1">'.check_field_create('admin').'
<table border="0" width="750" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Edit & Copy & Delete An Existing Area</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align=center><select class="field10" name="n">'.areas_selected(0,1,1,1,0).'</select></td></tr>
<tr><td align=center>Action: 
<input type="radio" name="action" value="area_edit" checked>Edit 
<input type="radio" name="action" value="area_copy">Copy 
<input type="radio" name="action" value="area_delete">Delete
</td></tr>
<tr><td align="center"><input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</table></td></tr></table></form><br>';
echo '<form action="areas.php" method="get" name="form1">'.check_field_create('admin').'
<input type="hidden" name="action" value="areas_multiple_create">
<table border="0" width="750" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Create Multiple Areas</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center">Create <select class="field10" name="n">';
for ($x=1;$x<=100;$x++) echo '<option value="'.$x.'">'.$x.'</option>';
echo '</select> areas at once <input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</table></td></tr></table></form><br>';
ift();
}

########################################################################################

function area_create_edit_form($action,$in) {
global $s;
//foreach ($in as $k=>$v) echo "$k - $v<br>";
if ($in[n])
{ $q = dq("select * from $s[pr]areas where n = '$in[n]'",1);
  $data = mysql_fetch_assoc($q);
  if ($data[submit_here]) $checked=' checked';
  if (!$data[map_address]) $data[map_address] = trim(str_replace('<%',' ',str_replace('%>',' ',$data[path_text])));
  $data = stripslashes_array($data);
  foreach ($data as $k=>$v) $data[$k] = str_replace('<','&lt;',str_replace('>','&gt;',$v));
  if ($data[submit_here]) $submit_here = ' checked';
  if ($data[recip]) $recip = ' checked';
  $q = dq("select count(*) from $s[pr]areas where parent = '$in[n]'",1);
  $has_subcats = mysql_fetch_row($q);
  if ($data[visible])
  { $visible = ' checked';
    if ($has_subcats[0]) $visible_info = '<span class="text10">If you hide this area, all its subareas will be hidden too.</span>';
  }
  $parent = areas_selected($data[parent],1,1,0,0);
  $info2 = $s[table_title];
}
else
{ $submit_here = $visible = ' checked';
  $info2 = 'Create A New Area';
  $parent = areas_selected(0,1,1,1,0);
}

if ($_GET[action]=='areas_multiple_create') { $n_cats = $_GET[n]; $label_area_name = 'Names of areas'; }
else { $n_cats = 1; $label_area_name = 'Area name'; }

echo '<form ENCTYPE="multipart/form-data" action="areas.php" method="post">'.check_field_create('admin').'
<input type="hidden" name="action" value="'.$action.'">
<input type="hidden" name="n" value="'.$in[n].'">
<table border="0" width="750" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">'.$info2.'</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr>
<td align="left" valign="top" nowrap>Parent area</td>
<td align="left" valign="top"><select class="field10" name="parent"><option value=0>None</option>'.$parent.'</select></td>
</tr>';
for ($x=1;$x<=$n_cats;$x++)
{ if ($x>1) unset($label_area_name);
  echo '<tr>
  <td align="left" valign="top" nowrap>'.$label_area_name.'&nbsp;</td>
  <td align="left" valign="top"><input class="field10" style="width:550px" name="title[]" maxlength=255 value="'.$data[title].'"></td>
  </tr>';
}
echo '<tr>
<td align="left" valign="top" nowrap>Icon </td>
<td align="left" valign="top"><input type="file" class="field10" style="width:550px" maxlength=255 name="image2" value="'.$data[image2].'"></td>
</tr>';
if ($data[n])
{ if ($data[image2]) $image = '<img border="0" src="'.$data[image2].'">'; else $image = 'Icon not defined. The default icon is used.';
  echo '<tr>
  <td align="left" valign="top" nowrap>Current image</td>
  <td align="left" valign="top">'.$image.'</td>
  </tr>';
}

if ($data[level]<=2)
{ echo '<tr>
  <td align="left" valign="top" nowrap>Address to show in the map </td>
  <td align="left" valign="top"><input class="field10" style="width:550px" name="map_address" maxlength=255 value="'.$data[map_address].'"></td>
  </tr>
  <tr>
  <td align="left" valign="top" nowrap>Map co-ordinates </td>
  <td align="left" valign="top">Latitude: <input class="field10" style="width:150px" name="latitude" maxlength=255 value="'.$data[latitude].'"> Longitude: <input class="field10" style="width:150px" name="longitude" maxlength=255 value="'.$data[longitude].'"><br><span class="text10">Keep these fields empty to automatically count these values for the address entered above<br></span></td>
  </tr>';
}
if ($data[level]==1)
{ echo '<tr>
  <td align="left" valign="top" nowrap>Map zoom </td>
  <td align="left" valign="top"><select class="field10" name="map_zoom">';
  unset($selected);
  for ($x=1;$x<=18;$x++) { if ($data[map_zoom]==$x) $selected = ' selected'; else $selected = ''; echo '<option value="'.$x.'"'.$selected.'>'.$x.'</option>'; }
  echo '</select></td>
  </tr>';
}

echo '<tr>
<td align="left" valign="top" nowrap>Allow submissions</td>
<td align="left" valign="top"><input type="checkbox" name="submit_here" value="1"'.$submit_here.'></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Show in the top menu</td>
<td align="left" valign="top"><input type="checkbox" name="in_menu" value="1"'; if ($data[in_menu]) echo ' checked'; echo '></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Rank </td>
<td align="left" valign="top"><input class="field10" name="rank" value="'.$data[rank].'" style="width:100px"><br><span class="text10">Use this field to set rank of areas in the left column. It shows these areas by this rank value. Areas with lower ranks are displayed higher.</span></td>
</tr>
';
if ($in[n]) echo '<tr>
<td align="left" valign="top" nowrap>Area URL</td>
<td align="left" valign="top"><input class="field10" name="rewrite_url" value="'.$data[rewrite_url].'" style="width:550px"><br><span class="text10">Only English letters, numbers and these characters: - _ /. If you let it blank, the script will generate the URL automatically by the Title field.</span></td>
</tr>';
echo '<tr>
<td align="center" colspan="2"><input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</table></td>
</tr></table></form>
<br>';
}

##################################################################################
##################################################################################
##################################################################################

function area_created($in) {
global $s;

//foreach ($in as $k=>$v) echo "$k - $v<br>";
//exit;
foreach ($in[title] as $k=>$v) if (trim($v)) $titles[] = trim($v);
if (!$in[title][0])
{ $s[info] = info_line('Area title is required. Please try again.');
  areas_home($in);
}

if ($in[parent])
{ $q = dq("select * from $s[pr]areas where n = '$in[parent]'",1);
  $parent = mysql_fetch_assoc($q);
  if (!$parent[visible]) $in[visible] = 0;
  //foreach ($parent as $k=>$v) echo "$k - $v<br>";
}
else $parent[level] = 0;
$level = $parent[level] + 1;

$in = replace_array_text($in);
$in[description] = refund_html($in[description]);
foreach ($in as $k=>$v) $in[$k] = str_replace('&lt;','<',str_replace('&gt;','>',$v));

foreach ($titles as $k=>$v)
{ $v = replace_once_text($v);
  if (!$in[map_address]) $in[map_address] = trim(str_replace('<%',' ',str_replace('%>',' ',$parent[path_text])))." $v";
  dq("insert into $s[pr]areas values (NULL,'$v','$in[parent]','0','$level','$in[rank]','','','','','$in[submit_here]','$similar','$in[in_menu]','$in[country]','','0','0','0','$in[map_address]','','','7','','','','')",1);
  $n = mysql_insert_id();
  get_geo_data($in[map_address],0,$n);
  update_category_area_paths('a',$n);
  $info[] = 'Area created';
  if ($_FILES[image1][name]) upload_category_area_image('a',$n,1,$_FILES[image1][name],$_FILES[image1][tmp_name],$old[image1]);
  if ($_FILES[image2][name]) upload_category_area_image('a',$n,2,$_FILES[image2][name],$_FILES[image2][tmp_name],$old[image2]);
}

if (count($info)==1) { $s[info] = info_line($info[0]); $in[n] = $n; area_edit($in); }
else $s[info] = info_line('Multiple areas created',implode('<br>',$info));
areas_home(0);
}

##################################################################################

function area_copy($in) {
global $s;
ih();
echo $s[info];
$s[table_title] = 'Copy Selected area';
area_create_edit_form('area_created',$in);
ift();
}

##################################################################################

function area_edit($in) {
global $s;
ih();
echo $s[info];
$s[table_title] = 'Edit Selected Area';
area_create_edit_form('area_edited',$in);
echo '<a href="areas.php?action=areas_home">Back to areas home</a>';
ift();
}

###################################################################################
###################################################################################
###################################################################################

















#################################################################################

function area_edited($in) {
global $s;
//foreach ($in as $k=>$v) echo "$k - $v<br>";//exit;

$in[title] = $in[title][0];

if (!$in[title])
{ $s[info] = info_line('Area title is required. Please try again.');
  area_edit($in);
}
if ($in[parent])
{ if ($in[n]==$in[parent]) { $s[info] = info_line('Area may not be a self-parent'); area_edit($in); }
  $q = dq("select * from $s[pr]areas where n = '$in[parent]'",1);
  $parent = mysql_fetch_assoc($q);
  //foreach ($parent as $k=>$v) echo "$k - $v<br>";
  $bigboss = find_bigboss_area($in[parent]);
  $path_n = trim($parent[path_n])."$in[n]_";
  $path_text = "$parent[path_text]%><%$in[title]";
  if (!$parent[visible]) $in[visible] = 0;
}
else { $parent[level] = 0; $path_n = "_$in[n]_"; $path_text = "<%$in[title]"; $bigboss = $in[n]; }
$level = $parent[level] + 1;
$old = get_area_variables($in[n]);
if ($old[parent] != $in[parent])
{ $q = dq("select count(*) from $s[pr]areas where parent = '$in[n]'",1);
  $x = mysql_fetch_row($q);
  if ($x[0]) 
  { $s[info] = info_line('This area has one or more subareas. These subareas must be deleted or moved in order to move this area.');
    area_edit($in);
  }
  $info = 'You moved this area from one parent area to another.';
}
if (!$in[alias_of])
{ if ((!$old[visible]) AND ($in[visible]))
  { manage_subareas_of_area(1,$in[n]);
    dq("update $s[pr]areas set visible = '1' where alias_of = '$in[n]'",1);
    $info = 'You have enabled at least one area.';
  }
  if (($old[visible]) AND (!$in[visible]))
  { manage_subareas_of_area(0,$in[n]);
    dq("update $s[pr]areas set visible = '0' where alias_of = '$in[n]'",1);
    $info = 'You have disabled at least one area.';
  }
}

$in = replace_array_text($in);
$in[description] = refund_html($in[description]);
foreach ($in as $k=>$v) $in[$k] = str_replace('&lt;','<',str_replace('&gt;','>',$v));
dq("update $s[pr]areas set parent = '$in[parent]', rank = '$in[rank]', title = '$in[title]', map_address = '$in[map_address]', map_zoom = '$in[map_zoom]', submit_here = '$in[submit_here]', in_menu = '$in[in_menu]', similar = '$similar', rewrite_url = '$in[rewrite_url]' where n = '$in[n]'",1);
update_category_area_paths('a',$in[n]);
get_geo_data($in[map_address],0,$in[n]);
//area_repair_path($in[n],$path_text);
if ($_FILES[image1][name]) upload_category_area_image('a',$in[n],1,$_FILES[image1][name],$_FILES[image1][tmp_name],$old[image1]);
if ($_FILES[image2][name]) upload_category_area_image('a',$in[n],2,$_FILES[image2][name],$_FILES[image2][tmp_name],$old[image2]);
if ($info) $info = '<br>'.$info.' Therefore now you have to go to <a href="rebuild.php?action=reset_rebuild_home"><b>reset/rebuild</b></a> and run functions "Repair paths of classified ads" and  "Recount classified ads in individual categories"';
$s[info] = info_line('Area "'.$in[title].'" has been edited. '.$info);
area_edit($in);
}

#######################################################################################

function area_delete($in) {
global $s;
//echo "select count(*) from $s[pr]areas where parent = '$in[n]'";
$q = dq("select count(*) from $s[pr]areas where parent = '$in[n]'",1);
$x = mysql_fetch_row($q);
if ($x[0]) 
{ ih(); echo info_line('Selected area has one or more subareas. These subareas must be deleted or moved in order to delete this area.');
  echo '<a href="javascript: history.go(-1)">Back</a>'; ift();
}

$q = dq("select parent from $s[pr]areas where n = '$in[n]'",1);
$parent = mysql_fetch_row($q);

dq("delete from $s[pr]areas where n = '$in[n]'",1);
dq("delete from $s[pr]ads where a = '_$in[n]_'",1);
if ($parent[0])
{ $parent = get_area_variables($parent[0]);
  $parents = explode('_',$parent[path_n]);
  foreach ($parents as $k=>$v) if (trim($v)) recount_ads_all_cats_areas(0,$v);
}
$s[info] = info_line('Selected area has been deleted');
if ($in[backto]=='areas_tree_admin') areas_tree_admin($what);
else areas_home($what,0);
}

#######################################################################################
#######################################################################################
#######################################################################################

function manage_subareas_of_area($action,$n) {
global $s;
dq("update $s[pr]areas set visible = '$action' where n = '$n'",1);
$numbers[0] = $list[] = $n;
while (count($numbers))
{ $k = array_rand($numbers);
  $q = dq("select n from $s[pr]areas where parent = '$numbers[$k]'",1);
  while ($x = mysql_fetch_row($q))
  { dq("update $s[pr]areas set visible = '$action' where n = '$x[0]'",1);
    $numbers[] = $list[] = $x[0];
  }
  unset($numbers[$k]);
}
$list = array_unique($list);
foreach ($list as $k=>$v) $kkk[] = get_items_in_area($v);
foreach ($kkk as $k=>$v) if (is_array($v)) foreach ($v as $k1=>$v1) $items[] = $v1;
update_en_cats_in_items($items);
}

#######################################################################################
#######################################################################################
#######################################################################################

function area_repair_path($n,$parent_path_text) {
global $s;
$parent = get_area_variables($n);
$q = dq("select n,title,path_n,path_text from $s[pr]areas where parent = '$n'",1);
while ($x=mysql_fetch_assoc($q))
{ $path_text = "$parent[path_text]%><%$x[title]";
  $path_n = $parent[path_n].$x[n].'_';
  dq("update $s[pr]areas set path_text = '$path_text', path_n = '$path_n' where n = '$x[n]'",1);
  area_repair_path($x[n],$path_text);
}
}

#######################################################################################
#######################################################################################
#######################################################################################

function areas_tree_admin($what) {
global $s;
$q = dq("select * from $s[pr]areas order by path_text,path_n",1);
while ($a=mysql_fetch_assoc($q))
{ set_time_limit(300);
  if (time()>($time1+10)) { $time1=time(); echo str_repeat (' ',4000); flush(); }
  if (!$a[visible]) $hidden = ' <b><font color="red">i</font></b>'; else $hidden = '';
  $mo = ''; for ($i=1;$i<$a[level];$i++) $mo .= '-&nbsp;';
  $a[path_text] = eregi_replace("<%.+%>", "", $a[path_text]);
  $a[path_text] = eregi_replace("<%.+$",$a[title],$a[path_text]);
  if (!$a[path_text]) $a[path_text] = $a[title];
  if ($a[alias_of]) $a[path_text] = $s[alias_pref].$a[path_text].$s[alias_after];
  $areas .= "$mo <a href=\"areas.php?action=area_edit&n=$a[n]\" title=\"Click to edit/view details\" class=\"link10\"><b>$a[path_text]</b></a> 
  <font color=\"blue\">#$a[n]</font> ";
  //$areas .= "<font color=\"green\">xxx $a[items]</font>";
  $areas .= "<a class=\"link10\" href=\"areas.php?action=area_copy&n=$a[n]\" title=\"Copy this area to another place\">Copy</a> - ";
  $areas .= "<a class=\"link10\" href=\"ads_list.php?action=ads_searched&area=$a[n]\">Classified ads</a>";
  if ($a[level]==1) $areas .= " - <a class=\"link10\" href=\"ads_list.php?action=ads_searched&bigboss=$a[n]\">Classified ads incl. subareas</a>";
  $areas .= " - <a class=\"link10\" href=\"areas.php?action=area_delete&n=$a[n]&backto=areas_tree_admin\" title=\"Delete this area\">x</a>";
  $areas .= '<br>';
}
ih();
echo $s[info];
echo '<table border=0 width=98% cellspacing=0 cellpadding=2 class="common_table">
<tr><td class="common_table_top_cell">All Areas</td></tr>
<tr><td align="center">
<table border=0 width=100% cellspacing=2 cellpadding=0>
<tr><td align=left><span class="text10">'.
stripslashes($areas)
.'</span></td></tr></table></td></tr></table><br>
<font color="blue">#25</font> - area number<br>
x - Delete area<br>';
ift();
}

##################################################################################
##################################################################################
##################################################################################

function show_country_areas($in) {
global $s;
$q = dq("select * from $s[pr]countries_regions where country = '$in' order by name",1);
while ($a=mysql_fetch_assoc($q)) $list[] = stripslashes($a[name]);
if ($list) echo implode('<br>',$list);
else echo 'There is not available any region to import for this country';
exit;
}

##################################################################################

function areas_imported($in) {
global $s;
if (is_array($in[import_country]))
{ $query = my_implode('code','or',$in[import_country]);
  $q = dq("select * from $s[pr]countries where $query",1);
  while ($country = mysql_fetch_assoc($q)) import_country($country);
}
if (is_array($in[import_areas]))
{ $query = my_implode('code','or',$in[import_areas]);
  $q = dq("select * from $s[pr]countries where $query",1); while ($country = mysql_fetch_assoc($q)) $countries[$country[code]] = $country;
  $query = my_implode('country','or',$in[import_areas]);
  $q = dq("select * from $s[pr]areas where $query",1); while ($area = mysql_fetch_assoc($q)) $areas[$area[country]] = $area;
  $query = my_implode('country','or',$in[import_areas]);
  $q = dq("select * from $s[pr]countries_regions where $query",1);
  while ($region = mysql_fetch_assoc($q))
  { if (!$areas[$region[country]]) { $country_n = import_country($countries[$region[country]]); $pocet++; }
    else $country_n = $areas[$region[country]][n];
    $parent_vars = get_area_variables($country_n);
    $map_address = trim(str_replace('<%',' ',str_replace('%>',' ',$parent_vars[path_text])))." $region[name]";
	dq("insert into $s[pr]areas values (NULL,'$region[name]','$country_n','0','2','0','','','','','1','','0','','','0','0','0','$map_address','','','7','','','','')",1);
    $n = mysql_insert_id();
    get_geo_data($map_address,0,$n);
    update_category_area_paths('a',$n);
    $s[info] .= "$region[name]<br>";
    $pocet++;
  }
}
if ($s[info]) $s[info] = info_line('Areas created:',$s[info]);

if (!$pocet)
{ $q = dq("select * from $s[pr]areas where latitude = 0",1);
  while ($x = mysql_fetch_assoc($q))
  { $map_address = trim(str_replace('<%',' ',str_replace('%>',' ',$x[path_text])));
    get_geo_data($map_address,0,$x[n]);
  }
}
areas_import_form();
}

##################################################################################

function import_country($country) {
global $s;
$q = dq("select * from $s[pr]areas where country = '$country[code]'",1); $a = mysql_fetch_assoc($q); if ($a[n]) return $a[n];
dq("insert into $s[pr]areas values (NULL,'$country[name]','0','0','1','0','','','','','0','','0','','$country[code]','0','0','0','$country[name]','','','7','','','','')",1);
$n = mysql_insert_id();
get_geo_data($country[name],0,$n);
update_category_area_paths('a',$n);
$s[info] .= "$country[name]<br>";
copy("$s[phppath]/images/flags/small/$country[flag]","$s[phppath]/images/areas/$n-2-$s[cas].png");
$file_url = "$s[site_url]/images/areas/$n-2-$s[cas].png";
chmod("$s[phppath]/images/areas/$n-2-$s[cas].png",0644);
dq("update $s[pr]areas set image2 = '$file_url' where n = '$n'",1);
return $n;
}

##################################################################################

function areas_import_form() {
global $s;
ih();
echo $s[info];
echo page_title('Areas');
echo '<form method="POST" action="areas.php">'.check_field_create('admin').'
<input type="hidden" name="action" value="areas_imported">
<table border="0" width="750" cellspacing="0" cellpadding="0" class="common_table">
<tr><td class="common_table_top_cell">Automatically Create Areas</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="inside_table">
<tr><td align="left" colspan="5">
It can import countries and regions as areas to your site. Select those countries which should be imported. If you select that it should import regions in a country which was not yet imported, it also imports this country. Countries are imported as first level areas, regions are imported as second level areas.
<br></td></tr>
<tr>
<td align="left">Name</span></td>
<td align="center">Flag</span></td>
<td align="center">Import this country</span></td>
<td align="center" colspan="2">Regions, counties, states in this country</span></td>
</tr>';
$q = dq("select * from $s[pr]countries order by name",1);
while ($country=mysql_fetch_assoc($q))
{ if ($country[flag]) $flag = '<img border="0" src="'.$s[site_url].'/images/flags/small/'.$country[flag].'">'; else $flag = '';
  $x++;
  $country[name] = strip_replace_once($country[name]);
  echo '<tr>
  <td align="left" nowrap>'.$country[name].'</td>
  <td align="center" nowrap>'.$flag.'</td>
  <td align="center"><input type="checkbox" name="import_country['.$x.']" id="import_country_'.$x.'" value="'.$country[code].'"></td>
  <td align="center" nowrap><a href="javascript:parse_ajax_request(document.getElementById(\'\'),\''.$s[site_url].'/administration/areas.php?action=show_country_areas&country='.$country[code].'\',\'show_areas_div_'.$x.'\');check_show_hide_div(\'show_areas_'.$x.'\')">Show them</td>
  <td align="center" nowrap>Import them <input type="checkbox" name="import_areas['.$x.']" value="'.$country[code].'"></td>
  </tr>
  <tr style="display:none;" id="show_areas_'.$x.'">
  <td align="left">&nbsp;</td>
  <td align="left" colspan="4" nowrap><div id="show_areas_div_'.$x.'">'.$country[name].'</div></td>
  </tr>
  ';
}
echo '<tr><td align="center" colspan="5"><input type="submit" name="A1" value="Import selected countries as areas" class="button10"></td></tr>
</table></td></tr></table></form><br><br>';
ift();
}

########################################################################################


?>