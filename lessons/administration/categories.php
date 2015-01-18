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
check_admin('categories');

switch ($_GET[action]) {
case 'categories_home'				: categories_home(0);
case 'categories_multiple_create'	: categories_home(0);
case 'categories_tree'				: categories_tree();
case 'category_edit'				: category_edit($_GET);
case 'category_copy'				: category_copy($_GET);
case 'category_delete'				: category_delete($_GET);
case 'categories_import_form'		: categories_import_form();
case 'category_usit_details_edit'	: category_usit_details_edit($_GET);
case 'category_usit_deleted'		: category_usit_deleted($_GET);
}
switch ($_POST[action]) {
case 'category_created'				: category_created($_POST);
case 'category_edited'				: category_edited($_POST);
case 'categories_import_count'		: categories_import_count($_POST);
case 'categories_imported'			: categories_imported($_POST);
case 'category_edited_usit'			: category_edited_usit($_POST);
case 'category_usit_details_edited'	: category_usit_details_edited($_POST);
}

########################################################################################
########################################################################################
########################################################################################

function categories_home($n) {
global $s;
$x[n] = $n;
ih();
echo $s[info];
echo page_title('Categories');
category_create_edit_form('category_created',$x);
echo '<form action="categories.php" method="get" name="form1">'.check_field_create('admin').'
<table border="0" width="750" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Edit & Copy & Delete An Existing Category</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align=center><select class="field10" name="n">'.categories_selected('c',0,1,1,1,0).'</select></td></tr>
<tr><td align=center>Action: 
<input type="radio" name="action" value="category_edit" checked>Edit 
<input type="radio" name="action" value="category_copy">Copy 
<input type="radio" name="action" value="category_delete">Delete
</td></tr>
<tr><td align="center"><input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</table></td></tr></table></form><br>';
echo '<form action="categories.php" method="get" name="form1">'.check_field_create('admin').'
<input type="hidden" name="action" value="categories_multiple_create">
<table border="0" width="750" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Create Multiple Categories</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center">Create <select class="field10" name="n">';
for ($x=1;$x<=100;$x++) echo '<option value="'.$x.'">'.$x.'</option>';
echo '</select> categories at once</span> <input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</table></td></tr></table></form><br>';
ift();
}

########################################################################################

function category_create_edit_form($action,$in) {
global $s;
//foreach ($in as $k=>$v) echo "$k - $v<br>";
if ($in[n])
{ $data = get_category_variables($in[n]);
  if ($data[submit_here]) $checked=' checked';
  $data = stripslashes_array($data);
  foreach ($data as $k=>$v) $data[$k] = str_replace('<','&lt;',str_replace('>','&gt;',$v));
  $q = dq("select count(*) from $s[pr]cats where parent = '$in[n]'",1);
  $has_subcats = mysql_fetch_row($q);
  if ($data[visible])
  { $visible = ' checked';
    if ($has_subcats[0]) $visible_info = '<span class="text10">If you hide this category, all its subcategories will be hidden too.</span>';
  }
  if ($data[alias_of]) $parent = categories_selected('ad',$data[parent],1,1,1,0); else $parent = categories_selected('ad',$data[parent],1,1,0,0);
  $info2 = $s[table_title];
  if (($data[level]==1) AND (!$data[alias_of])) $s[show_usit_form] = 1;
}
else
{ $submit_here = $visible = ' checked';
  $info2 = 'Create A New Category';
  $parent = categories_selected('c',0,1,1,1,0);
}

$sim = explode(' ',str_replace('_','',$data[similar]));
for ($x=0;$x<=$s[ads_max_simcats];$x++) $similar .= '<select class="field10" name="similar['.$x.']"><option value=0>None</option>'.categories_selected('c',$sim[$x],1,1,1,0).'</select><br>';
$tmpl_cat = category_template_select('category.html',$data[tmpl_cat]);
$tmpl_one = category_template_select('ad_a.txt',$data[tmpl_one]);
$tmpl_det = category_template_select('ad_details.html',$data[tmpl_det]);
$alias_of = categories_selected('c',$data[alias_of],1,1,0,0);
for ($x=1;$x<=10;$x++)
{ if ($data[cat_group]==$x) $selected = ' selected'; else $selected = '';
  $cat_group .= '<option value="'.$x.'"'.$selected.'>'.$x.'</option>';
}

if ($_GET[action]=='categories_multiple_create') { $n_cats = $_GET[n]; $label_category_name = 'Names of categories'; }
else { $n_cats = 1; $label_category_name = 'Category name'; }
if ($data[n])
{ $category_url = category_url('a',$data[n],$data[alias_of],1,$data[rewrite_url]);
  $category_url = str_replace('area_n-','0/',$category_url);
  $category_url = str_replace('page_n/','',$category_url);
  $category_url = str_replace('/area_rewrite','',$category_url);
  $category_url = str_replace('-extra_commands','',$category_url);
}
echo '<form ENCTYPE="multipart/form-data" action="categories.php" method="post">'.check_field_create('admin').'
<input type="hidden" name="action" value="'.$action.'">
<input type="hidden" name="n" value="'.$in[n].'">
<table border="0" width="700" cellspacing="0" cellpadding="0" class="common_table">
<tr><td align="center" colspan="2" class="common_table_top_cell">'.$info2.'</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">';
if ($in[n]) echo '<tr>
<td align="left" valign="top" nowrap>Category URL </td>
<td align="left" valign="top"><a target="_blank" href="'.$category_url.'">'.$category_url.'</a></td>
</tr>';
echo '<tr>
<td align="left" valign="top" nowrap>Parent category</td>
<td align="left" valign="top"><select class="field10" name="parent"><option value=0>None</option>'.$parent.'</select></td>
</tr>';
for ($x=1;$x<=$n_cats;$x++)
{ if ($x>1) unset($label_category_name);
  echo '<tr>
  <td align="left" valign="top" nowrap>'.$label_category_name.'&nbsp;</td>
  <td align="left" valign="top"><input class="field10" name="title[]" maxlength=255 value="'.$data[title].'" style="width:550px"></td>
  </tr>';
}
echo '<tr>
<td align="left" valign="top">Group<br><span class="text10">You can group categories on the home page by your needs.</span></td>
<td align="left" valign="top"><select class="field10" name="cat_group"><option value=0>None</option>'.$cat_group.'</select></td>
</tr>';
if (!$in[n])
{ echo '<tr>
  <td align="left" valign="top">Alias of category</td>
  <td align="left" valign="top"><select class="field10" name="alias_of"><option value=0>None</option>'.$alias_of.'</select></td>
  </tr>';
}
elseif ($data[alias_of])
{ echo '<tr>
  <td align="left" valign="top">Alias of category</td>
  <td align="left" valign="top"><select class="field10" name="alias_of">'.$alias_of.'</select></td>
  </tr>';
}
echo '<tr>
<td align="left" valign="top" nowrap>Description</td>
<td align="left" valign="top"><textarea class="field10" name="description" rows="10"  style="width:550px">'.$data[description].'</textarea></td>
</tr>';
if (!$data[alias_of])
{ if (!$in[n]) echo '<tr>
  <td align="center" valign="top" colspan="2" nowrap>All the fields below are not applicable for categories marked as Alias</td>
  </tr>';
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
  echo '<tr>
  <td align="left" valign="top" nowrap>Image on the category page</td>
  <td align="left" valign="top"><input type="file" class="field10" style="width:550px" maxlength=255 name="image1" value="'.$data[image1].'"></td>
  </tr>';
  if ($data[n])
  { if ($data[image1]) $image = '<img border="0" src="'.$data[image1].'">'; else $image = 'None';
    echo '<tr>
    <td align="left" valign="top" nowrap>Current image</td>
    <td align="left" valign="top">'.$image.'</td>
    </tr>';
  }
  echo '<tr>
  <td align="left" valign="top" nowrap>Meta keywords</td>
  <td align="left" valign="top"><textarea class="field10" name="m_keyword" rows="10" style="width:550px">'.$data[m_keyword].'</textarea></td>
  </tr>
  <tr>
  <td align="left" valign="top" nowrap>Meta description</td>
  <td align="left" valign="top"><textarea class="field10" name="m_desc" rows="10" style="width:550px">'.$data[m_desc].'</textarea></td>
  </tr>
  <tr>
  <td align="left" valign="top" nowrap>Category page template</td>
  <td align="left" valign="top"><select class="field10" name="tmpl_cat">'.$tmpl_cat.'</select><br><span class="text10">Template which will be used for the whole category page</span></td>
  </tr>
  <tr>
  <td align="left" valign="top" nowrap>One item template</td>
  <td align="left" valign="top"><select class="field10" name="tmpl_one">'.$tmpl_one.'</select><br><span class="text10">Template which will be used for each classified ad in this category</span></td>
  </tr>
  <tr>
  <td align="left" valign="top" nowrap>Detail page template</td>
  <td align="left" valign="top"><select class="field10" name="tmpl_det">'.$tmpl_det.'</select><br><span class="text10">Template which will be used for all detail pages for classified ads from this category</span></td>
  </tr>';
  echo '<tr>
  <td align="left" valign="top" nowrap>Similar categories</td>
  <td align="left" valign="top">'.$similar.'</td>
  </tr>';
  /*if ((!$in[n]) OR ($data[level]==1)) echo '<tr>
  <td align="left" valign="top" nowrap>Announcements  </td>
  <td align="left" valign="top"><input type="checkbox" name="announcements" value="1"'; if ($data[announcements]) echo ' checked'; echo '></td>
  </tr>';*/
  echo '<tr>
  <td align="left" valign="top" nowrap>Divide to Offer/Wanted</td>
  <td align="left" valign="top"><input type="checkbox" name="offer_wanted" value="1"'; if ($data[offer_wanted]) echo ' checked'; echo '></td>
  </tr>
  <tr>
  <td align="left" valign="top" nowrap>Show "Price" field</td>
  <td align="left" valign="top"><input type="checkbox" name="price" value="1"'; if ($data[price]) echo ' checked'; echo '></td>
  </tr>
  <tr>
  <td align="left" valign="top" nowrap>Allow submissions</td>
  <td align="left" valign="top"><input type="checkbox" name="submit_here" value="1"'; if ($data[submit_here]) echo ' checked'; echo '></td>
  </tr>';
  if (($in[n]) AND (!$data[visible]) AND ($has_subcats[0]))
  echo '
  <script language="javascript">
  function showHide(chkBox) 
  { if(chkBox.checked==true) { document.getElementById("vrstva").style.visibility = \'visible\'; document.getElementById("vrstva1").style.visibility = \'visible\'; }
    else { document.getElementById("vrstva").style.visibility = \'hidden\'; document.getElementById("vrstva1").style.visibility = \'hidden\'; }
  }
  </script>
  <tr>
  <td align="left" valign="top" nowrap>Is visible</td>
  <td align="left" valign="top"><input type="checkbox" name="visible" onClick=\'showHide(this)\' value="1"'.$visible.'></td>
  </tr>
  <tr>
  <td align="left" valign="top"><div id="vrstva" style="visibility:hidden">Enable also all subcategories of this category </div></td>
  <td align="left" valign="top"><div id="vrstva1" style="visibility:hidden"><input type="checkbox" name="visible_subcats" value="1" checked></div></td>
  </tr>';
  else echo '<tr>
  <td align="left" valign="top" nowrap>Is visible</td>
  <td align="left" valign="top"><input type="checkbox" name="visible" value="1"'.$visible.'>'.$visible_info.'</td>
  </tr>';
  echo '<tr>
  <td align="left" valign="top" nowrap>Show in the top menu</td>
  <td align="left" valign="top"><input type="checkbox" name="in_menu" value="1"'; if ($data[in_menu]) echo ' checked'; echo '></td>
  </tr><tr>
  <td align="left" valign="top" nowrap>Rank </td>
  <td align="left" valign="top"><input class="field10" name="rank" value="'.$data[rank].'" style="width:100px"><br><span class="text10">Use this field to set rank of categories in the left column and also on the home page. It shows these categories by this rank value. Categories with lower ranks are displayed higher.</span></td>
  </tr>';
  if ($in[n]) echo '<tr>
  <td align="left" valign="top" nowrap>Category URL</td>
  <td align="left" valign="top"><input class="field10" name="rewrite_url" value="'.$data[rewrite_url].'" style="width:550px"><br><span class="text10">Only English letters, numbers and these characters: - _ /. If you let it blank, the script will generate the URL automatically by the Title field.</span></td>
  </tr>
  ';
}
echo '<tr>
<td align="center" colspan="2"><input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</table></td>
</tr></table></form>
<br><br>';
}

##################################################################################
##################################################################################
##################################################################################

function category_created($in) {
global $s;
foreach ($in[title] as $k=>$v) if (trim($v)) $titles[] = trim($v);
if (!$in[title][0])
{ $s[info] = info_line('Category name is required. Please try again.');
  categories_home($in);
}
if ($in[parent])
{ $q = dq("select * from $s[pr]cats where n = '$in[parent]'",1);
  $parent = mysql_fetch_assoc($q);
  if (!$parent[visible]) $in[visible] = 0;
  if (($parent[alias_of]) AND (!$in[alias_of]))
  { $s[info] = info_line('If a parent category is an alias, also its child category must be an alias. Please try again.');
    categories_home($in);
  }
}
else { $parent[level] = 0; $parent[path_text] = ''; }
$level = $parent[level] + 1;

foreach ($in[similar] as $k=>$v) if ($v) $sim[] = '_'.$v.'_'; $similar = trim(implode(' ',$sim));
if ($in[alias_of])
{ $q = dq("select items,visible from $s[pr]cats where n = '$in[alias_of]'",1);
  $x = mysql_fetch_assoc($q); $items = $x[items]; $in[visible] = $x[visible];
}

$in = replace_array_text($in);
$in[description] = refund_html($in[description]);
foreach ($in as $k=>$v) $in[$k] = str_replace('&lt;','<',str_replace('&gt;','>',$v));

foreach ($titles as $k=>$v)
{ $v = replace_once_text($v);
  dq("insert into $s[pr]cats values (NULL,'$in[parent]','$level','$in[rank]','$in[alias_of]','$v','$in[description]','','','$in[m_keyword]','$in[m_desc]','$items','','','$in[submit_here]','0','$similar','$in[rewrite_url]','$in[tmpl_cat]','$in[tmpl_one]','$in[tmpl_det]','','$in[visible]','$in[cat_group]','$in[in_menu]','$in[announcements]','$in[offer_wanted]','$in[price]','0','0')",1);
  $in[n] = mysql_insert_id();
  update_disabled_categories();
  update_category_area_paths('c',$in[n]);
  $info[] = 'Category created';
}
if ($_FILES[image1][name]) upload_category_area_image('c',$in[n],1,$_FILES[image1][name],$_FILES[image1][tmp_name],$old[image1]);
if ($_FILES[image2][name]) upload_category_area_image('c',$in[n],2,$_FILES[image2][name],$_FILES[image2][tmp_name],$old[image2]);

if (count($info)==1) { $s[info] = info_line($info[0]); category_edit($in); }
else $s[info] = info_line('Multiple categories created',implode('<br>',$info));
categories_home(0);
}

##################################################################################

function category_copy($in) {
global $s;
ih();
echo $s[info];
$s[table_title] = 'Copy Selected Category';
category_create_edit_form('category_created',$in);
ift();
}

##################################################################################

function category_edit($in) {
global $s;
ih();
echo $s[info];
$s[table_title] = 'Edit Selected Category';
category_create_edit_form('category_edited',$in);
if ($s[show_usit_form])
{ category_create_edit_usit($in[n]);
  echo '<br><br><a href="ads_prices.php?action=ads_prices_home&c='.$in[n].'">Edit prices for this category and its subcategories</a>';
}
ift();
}

##################################################################################

function category_create_edit_usit($n) {
global $s;
if ($s[cats_share_usit]) $n = 0;
$q = dq("select * from $s[pr]usit_list where category = '$n'",1);
category_usit_edit_form($n);
echo '</table></td></tr></table>';
}

###################################################################################

function category_usit_edit_form($category) {
global $s;
if ($s[cats_share_usit]) $category = 0;
echo '<a name="usit_form"></a>
<form action="categories.php" method="post">'.check_field_create('admin').'
<table border="0" width="700" cellspacing="0" cellpadding="0" class="common_table">
<tr><td align="center" colspan="2" class="common_table_top_cell">Extra Fields</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center" colspan="10">These fields are valid for this category and all its subcategories</td></tr>
<input type="hidden" name="action" value="category_edited_usit">
<input type="hidden" name="category" value="'.$category.'">
<tr>
<td align="left" valign="top" nowrap><span class="text10">#</span></td>
<td align="center" valign="top" nowrap><span class="text10">Rank</span></td>
<td align="center" valign="top" nowrap><span class="text10">Description</span></td>
<td align="center" valign="top" nowrap><span class="text10">Type</span></td>
<td align="center" valign="top" nowrap><span class="text10">Visible<br>in submit<br>forms</span></td>
<td align="center" valign="top" nowrap><span class="text10">Required</span></td>
<td align="center" valign="top" nowrap><span class="text10">Visible<br>on<br>pages</span></td>
<td align="center" valign="top" nowrap><span class="text10">Visible<br>in search<br>form</span></td>
<td align="center" valign="top" nowrap><span class="text10">&nbsp;</span></td>
<td align="center" valign="top" nowrap><span class="text10">&nbsp;</span></td>
</tr>';
$q = dq("select * from $s[pr]usit_list where category = '$category' order by usit_n",1);
while ($x = mysql_fetch_assoc($q)) $usits[$x[usit_n]] = $x;

for ($item=1;$item<=25;$item++)
{ $rank = get_usit_rank_options();
  if (!$usits[$item][n]) $usits[$item][n] = '&nbsp;';
  echo '<tr>
  <td align="left" nowrap><span class="text10">'.$usits[$item][n].'</span></td>
  <td align="center" nowrap>'.get_usit_rank_options("usit[$item][rank]",$usits[$item][rank]).'</td>
  <td align="center" nowrap><input class="field10" size="35" name="usit['.$item.'][description]" maxlength="100" value="'.$usits[$item][description].'"></td>
  <td align="center" nowrap>'.get_usit_item_type_options("usit[$item][item_type]",$usits[$item][item_type]).'</td>
  <td align="center" nowrap><span class="text10"><input type="checkbox" name="usit['.$item.'][visible_forms]" value="1" '; if ($usits[$item][visible_forms]) echo ' checked'; echo '></span></td>
  <td align="center" nowrap><span class="text10"><input type="checkbox" name="usit['.$item.'][required]" value="1" '; if ($usits[$item][required]) echo ' checked'; echo '></span></td>
  <td align="center" nowrap><span class="text10"><input type="checkbox" name="usit['.$item.'][visible_pages]" value="1" '; if ($usits[$item][visible_pages]) echo ' checked'; echo '></span></td>
  <td align="center" nowrap><span class="text10"><input type="checkbox" name="usit['.$item.'][visible_search]" value="1" '; if ($usits[$item][visible_search]) echo ' checked'; echo '></span></td>
  ';
  if ($usits[$item][n]=='&nbsp;') echo '<td align="center"><span class="text10">&nbsp;</span></td><td align="center"><span class="text10">&nbsp;</span></td>';
  else echo '<td align="center"><span class="text10">[<a class="link10" href="#usit_form" onClick="open_new_window(\'categories.php?action=category_usit_details_edit&n='.$usits[$item][n].'\',700,500,1);">Details</a>]</span></td>
  <td align="center" nowrap><span class="text10">[<a class="link10" href="javascript: go_to_delete(\'Are you sure?\',\'categories.php?action=category_usit_deleted&n='.$usits[$item][n].'\')" title="Delete this user item">X</a>]</span></td>';
  echo '</tr>';
}
echo '<tr><td align="center" nowrap colspan="10"><input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</table></td></tr>
</table></form>';
}

###################################################################################

function get_usit_rank_options($name,$rank) {
global $s;
for ($x=1;$x<=25;$x++)
{ if ($rank==$x) $selected = ' selected'; else $selected = '';
  $rank .= '<option value="'.$x.'"'.$selected.'>'.$x.'</a>';
}
return '<select class="field10" name="'.$name.'">'.$rank.'</select>';
}

###################################################################################

function get_usit_item_type_options($name,$item_type) {
global $s;
$item_types = array('text','textarea','htmlarea','select','multiselect','radio','checkbox');
foreach ($item_types as $k=>$v)
{ if ($item_type==$v) $selected = ' selected'; else $selected = '';
  $a .= '<option value="'.$v.'"'.$selected.'>'.$v.'</a>';
}
return '<select class="field10" name="'.$name.'">'.$a.'</select>';
}

###################################################################################

function category_edited_usit($in) {
global $s;
if ($s[cats_share_usit]) $in[category] = 0;
$q = dq("select * from $s[pr]usit_list where category = '$in[category]'",1);
while ($x = mysql_fetch_assoc($q)) $old[$x[usit_n]] = $x;
$q = dq("delete from $s[pr]usit_list where category = '$in[category]'",1);

foreach ($in[usit] as $k=>$v)
{ if (!$v[description]) continue;
  if (in_array($v[rank],$used_ranks)) $unknown_ranks[] = $k;
  else $used_ranks[$k] = $v[rank];
}

foreach ($unknown_ranks as $k=>$v)
{ for ($x=1;$x<=25;$x++)
  { if (in_array($x,$used_ranks)) continue;
    $in[usit][$v][rank] = $x;
    $used_ranks[$v] = $x;
    break;
  }
}

for ($usit_n=1;$usit_n<=25;$usit_n++)
{ $current = replace_array_text($in[usit][$usit_n]); if (!$current[description]) continue;
  $current_old = $old[$usit_n];
  if (!$current_old) $current_old[n] = 'NULL';
  dq("insert into $s[pr]usit_list values($current_old[n],'$usit_n','$in[category]','$current[item_type]','$current[description]','$current_old[def_value_text]','$current_old[def_value_code]','$current[rank]','$current_old[maxlength]','$current[required]','$current[visible_forms]','$current[visible_pages]','$current[visible_search]')",1);
  $title[$usit_n] = $current[description];
  $visible[$usit_n] = $current[visible_pages];
}
dq("delete from $s[pr]usit_list_short where c = '$in[category]'",1);
dq("insert into $s[pr]usit_list_short values('$in[category]','$title[1]','$visible[1]','$title[2]','$visible[2]','$title[3]','$visible[3]','$title[4]','$visible[4]','$title[5]','$visible[5]','$title[6]','$visible[6]','$title[7]','$visible[7]','$title[8]','$visible[8]','$title[9]','$visible[9]','$title[10]','$visible[10]','$title[11]','$visible[11]','$title[12]','$visible[12]','$title[13]','$visible[13]','$title[14]','$visible[14]','$title[15]','$visible[15]','$title[16]','$visible[16]','$title[17]','$visible[17]','$title[18]','$visible[18]','$title[19]','$visible[19]','$title[20]','$visible[20]','$title[21]','$visible[21]','$title[22]','$visible[22]','$title[23]','$visible[23]','$title[24]','$visible[24]','$title[25]','$visible[25]')",1);
$in[n] = $in[category];
category_edit($in);
}
  
###################################################################################
###################################################################################
###################################################################################

function category_usit_details_edit($in) {
global $s;
$q = dq("select * from $s[pr]usit_list where n = '$in[n]'",1); $usit_list = mysql_fetch_assoc($q);
if ($usit_list[required]) $required = ' checked';
if (($usit_list[item_type]=='checkbox') AND ($usit_list[def_value_code])) $usit_list[def_value_text] = 'checked';
if ((!$usit_list[usit_n]) OR ($usit_list[visible_forms])) $visible_forms = ' checked';
if ((!$usit_list[usit_n]) OR ($usit_list[visible_pages])) $visible_pages = ' checked';
if ((!$usit_list[usit_n]) OR ($usit_list[visible_search])) $visible_search = ' checked';
ih();
echo $s[info];
echo '<form action="categories.php" method="post">'.check_field_create('admin').'
<input type="hidden" name="action" value="category_usit_details_edited">
<input type="hidden" name="n" value="'.$in[n].'">
<table border=0 width=750 cellspacing="0" cellpadding="0" class="common_table">
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr>
<td align="left" nowrap>Description</td>
<td align="left"><input class="field10" name="description" maxlength="255" value="'.$usit_list[description].'" style="width:350px"></td>
</tr>
<tr>
<td align="left" nowrap>Type</td>
<td align="left">'.get_usit_item_type_options('item_type',$usit_list[item_type]).'</td>
</tr>
<tr>
<td align="left" nowrap>Maximum length<br><span class="text10">For text fields only</span></td>
<td align="left"><input class="field10" size="10" name="maxlength" value="'.$usit_list[maxlength].'" style="width:50px"><span class="text10"> Maximum is 255 characters</span></td></tr>
<tr>
<td align="left" valign="top" nowrap>Values<br><span class="text10">If you have selected as type<br><b>Radio</b> or <b>Select Box</b>, enter<br>its values here.</span></td>
<td align="left">'.category_usit_details_edit_options_part($in[n]).'
</td>
</tr>
<tr>
<td align="left" nowrap>Default value</td>
<td align="left"><input class="field10" name="def_value" maxlength="255" value="'.$usit_list[def_value_text].'" style="width:350px"></td>
</tr>
<tr>
<td align="left" nowrap>Rank in form</td>
<td align="left">'.get_usit_rank_options("rank",$usit_list[rank]).'</td>
</tr>
<tr>
<td align="left" nowrap>Visible in submit/edit forms</td>
<td align="left"><input type="checkbox" name="visible_forms" value="1"'.$visible_forms.'></td>
</tr>
<tr>
<td align="left" nowrap>Required</td>
<td align="left"><input type="checkbox" name="required" value="1"'.$required.'></td>
</tr>
<tr>
<td align="left" nowrap>Visible on pages (categories etc.) </td>
<td align="left"><input type="checkbox" name="visible_pages" value="1"'.$visible_pages.'></td>
</tr>
<tr>
<td align="left" nowrap>Visible in search form </td>
<td align="left"><input type="checkbox" name="visible_search" value="1"'.$visible_search.'></td>
</tr>
<tr><td align="center" colspan="2"><input type="submit" name="submit" value="Submit" class="button10"></td>
</tr>
</table></td></tr></table>
</form>
<br><br>';
ift();
}

###################################################################################

function category_usit_details_edit_options_part($n) {
global $s;
$a = '<table border=0 width="100%" cellspacing="0" cellpadding="0"><tr>
<td align="left"><span class="text10">Rank</td>
<td align="center"><span class="text10">Value</td>
</tr>';

$q = dq("select * from $s[pr]usit_list_values where usit_list_n = '$n' order by rank",1);
while ($x = mysql_fetch_assoc($q))
{ $a .= '<tr>
  <td align="left"><input class="field10" name="ranks['.$x[value_code].']" maxlength="100" value="'.$x[rank].'" style="width:40px"></td>
  <td align="center"><input class="field10" name="values['.$x[value_code].']" maxlength="255" value="'.$x[description].'" style="width:300px"></td>
  </tr>';
  if ($biggest_rank<$x[rank]) $biggest_rank = $x[rank];
}
for ($x=1;$x<=10;$x++)
{ $rank = $biggest_rank + $x;
  $a .= '<tr>
  <td align="left"><input class="field10" size="5" name="ranks_new[]" maxlength="100" value="'.$rank.'" style="width:40px"></td>
  <td align="center"><input class="field10" size="55" name="values_new[]" maxlength="255" value="" style="width:300px"></td>
  </tr>';
}
$a .= '</table>';
return $a;
}

###################################################################################

function category_usit_details_edited($in) {
global $s;
$in = category_usit_details_form_control($in);
dq("update $s[pr]usit_list set item_type = '$in[item_type]', description = '$in[description]', rank = '$in[rank]', maxlength = '$in[maxlength]', required = '$in[required]', def_value_code = '0', visible_forms = '$in[visible_forms]', visible_pages = '$in[visible_pages]', visible_search = '$in[visible_search]' where n = '$in[n]'",1);
if (($in[item_type]!='radio') AND ($in[item_type]!='select') AND ($in[item_type]!='multiselect')) dq("delete from $s[pr]usit_list_values where n = '$in[n]'",1);
if (($in[item_type]=='text') OR ($in[item_type]=='textarea') OR ($in[item_type]=='htmlarea')) dq("update $s[pr]usit_list set def_value_text = '$in[def_value]' where n = '$in[n]'",1);
elseif (($in[item_type]=='checkbox') AND ($in[def_value]=='checked')) dq("update $s[pr]usit_list set def_value_code = '1' where n = '$in[n]'",1);
elseif (($in[item_type]=='radio') OR ($in[item_type]=='select') OR ($in[item_type]=='multiselect'))
{ dq("update $s[pr]usit_list set def_value_text = '$in[def_value]' where n = '$in[n]'",1);
  dq("delete from $s[pr]usit_list_values where usit_list_n = '$in[n]'",1);
  $unknown_ranks = 1000;
  foreach ($in[values] as $k=>$v)
  { if (!$v) continue;
    if ($in[ranks][$k]) $rank = $in[ranks][$k]; else { $unknown_ranks++; $rank = $unknown_ranks; }
    dq("insert into $s[pr]usit_list_values values('$in[usit_n]','$in[n]','$v','$rank')",1);
    $dont_delete_a[] = $k;
  }
  //exit;
  foreach ($in[values_new] as $k=>$v)
  { if (!$v) continue;
    if ($in[ranks_new][$k]) $rank = $in[ranks_new][$k]; else { $unknown_ranks++; $rank = $unknown_ranks; }
    dq("insert into $s[pr]usit_list_values values(NULL,'$in[n]','$v','$rank')",1);
  }
  $q = dq("select n from $s[pr]usit_list_values where usit_list_n = '$in[n]' AND description = '$in[def_value]'",1);
  $x = mysql_fetch_row($q);
  dq("update $s[pr]usit_list set def_value_code = '$x[0]' where n = '$in[n]'",1);
}
$s[info] = info_line('Your changes have been saved');
category_usit_details_edit($in);
}

###################################################################################

function category_usit_details_form_control($in) {
global $s;
//foreach ($in as $k=>$v) echo "$k - $v<br>";
$in = replace_array_text($in);
if (!$in[description]) user_item_error($in,'Description is missing');
if (!$in[rank]) user_item_error($in,'Rank is missing');
$q = dq("select rank from $s[pr]usit_list where n = '$in[n]'",1);
$x = mysql_fetch_row($q);
if ($in[rank]!=$x[0])
{ $q = dq("select count(*) from $s[pr]usit_list where rank = '$in[rank]'",1);
  $x = mysql_fetch_row($q); if ($x[0]) user_item_error($in,'Entered rank is already in use');
}
if (($in[item_type]=='radio') OR ($in[item_type]=='select') OR ($in[item_type]=='multiselect'))
{ $in[values] = replace_array_text($in[values]);
  $in[values_new] = replace_array_text($in[values_new]);
  if ((count($in[values_new])+count($in[values]))<2) user_item_error($in,'Number values for field radio or select cannot be lower than 2.');
  if (!$in[def_value]) user_item_error($in,'Default value is missing');
  elseif ((!in_array($in[def_value],$in[values])) AND (!in_array($in[def_value],$in[values_new]))) user_item_error($in,'Default value must be one of the values available in field "Values"');
}
else
{ unset($in[values]);
  if (($in[item_type]=='checkbox') AND ($in[def_value]) AND ($in[def_value]!='checked'))
  user_item_error($in,'The only allowed default value for checkbox is "checked".<br>To have it unchecked let the field blank.');
  if (($in[item_type]=='text') AND (!$in[maxlength])) $in[maxlength] = 255;
}
return $in;
}

###################################################################################

function user_item_error($in,$error) {
global $s;
$s[info] = info_line($error);
$in = strip_slashes_array($in);
category_usit_details_edit($in);
}

###################################################################################
###################################################################################
###################################################################################

function category_usit_deleted($in) {
global $s;
$q = dq("select * from $s[pr]usit_list where n = '$in[n]'",1);
$x = mysql_fetch_assoc($q);
dq("delete from $s[pr]usit_list where n = '$in[n]'",1);
dq("delete from $s[pr]usit_list_values where usit_list_n = '$in[n]'",1);
dq("update $s[pr]usit_list_short set title$x[usit_n] = '', visible$x[usit_n] = '0' where c = '$x[category]'",1);
dq("update $s[pr]ads_usit set n$x[usit_n] = '0', code$x[usit_n] = '0', text$x[usit_n] = '0' where n$x[usit_n] = '$x[n]'",1);
$s[info] = info_line('Selected item has been deleted');
$in[n] = $x[category];
category_edit($in);
}

###################################################################################
###################################################################################
###################################################################################

















#################################################################################

function category_edited($in) {
global $s;
//foreach ($in as $k=>$v) echo "$k - $v<br>";//exit;

$in[title] = $in[title][0];

if (!$in[title])
{ $s[info] = info_line('Category title is required. Please try again.');
  category_edit($in);
}
if ($in[parent])
{ if ($in[n]==$in[parent]) { $s[info] = info_line('Category may not be a self-parent'); category_edit($in); }
  $q = dq("select * from $s[pr]cats where n = '$in[parent]'",1);
  $parent = mysql_fetch_assoc($q);
  $bigboss = find_bigboss_category($in[parent]);
  $path_n = trim($parent[path_n]).$in[n].'_';
  $path_text = "$parent[path_text]%><%$in[title]";
  if (!$parent[visible]) $in[visible] = 0;
}
else { $parent[level] = 0; $path_n = '_'.$in[n].'_'; $path_text = "<%$in[title]"; $bigboss = $in[n]; }

$level = $parent[level] + 1;
$q = dq("select * from $s[pr]cats where n = '$in[n]'",1);
$old = mysql_fetch_assoc($q);
if ($old[title] != $in[title]) repair_path($in[n],$path_n,$path_text);
if ($old[parent] != $in[parent])
{ $q = dq("select count(*) from $s[pr]cats where parent = '$in[n]'",1);
  $x = mysql_fetch_row($q);
  if ($x[0]) 
  { $s[info] = info_line('This category has one or more subcategories. These subcategories must be deleted or moved in order to move this category.');
    category_edit($in);
  }
  $info = 'You moved this category from one parent category to another.';
}
if (!$in[alias_of])
{ if ((!$old[visible]) AND ($in[visible]))
  { manage_subcategories_of_category(1,$in[n]);
    dq("update $s[pr]cats set visible = '1' where alias_of = '$in[n]'",1);
    $info = 'You have enabled at least one category.';
  }
  if (($old[visible]) AND (!$in[visible]))
  { manage_subcategories_of_category(0,$in[n]);
    dq("update $s[pr]cats set visible = '0' where alias_of = '$in[n]'",1);
    $info = 'You have disabled at least one category.';
  }
}

foreach ($in[similar] as $k=>$v) if ($v) $sim[] = '_'.$v.'_'; $similar = trim(implode(' ',$sim));
if ($in[alias_of])
{ $q = dq("select items,visible from $s[pr]cats where n = '$in[alias_of]'",1);
  $x = mysql_fetch_assoc($q); $items = ",items = '$x[items]'"; $in[visible] = $x[visible];
}

$in = replace_array_text($in);
$in[description] = refund_html($in[description]);
foreach ($in as $k=>$v) $in[$k] = str_replace('&lt;','<',str_replace('&gt;','>',$v));

dq("update $s[pr]cats set parent = '$in[parent]', rank = '$in[rank]', alias_of = '$in[alias_of]', title = '$in[title]', description = '$in[description]', m_keyword = '$in[m_keyword]', m_desc = '$in[m_desc]', submit_here = '$in[submit_here]', similar = '$similar', tmpl_cat = '$in[tmpl_cat]', tmpl_one = '$in[tmpl_one]', tmpl_det = '$in[tmpl_det]', rewrite_url = '$in[rewrite_url]', visible = '$in[visible]', cat_group = '$in[cat_group]', in_menu = '$in[in_menu]', announcements = '$in[announcements]', offer_wanted = '$in[offer_wanted]', price = '$in[price]' where n = '$in[n]'",1);
update_category_area_paths('c',$in[n]);
if ($_FILES[image1][name]) upload_category_area_image('c',$in[n],1,$_FILES[image1][name],$_FILES[image1][tmp_name],$old[image1]);
if ($_FILES[image2][name]) upload_category_area_image('c',$in[n],2,$_FILES[image2][name],$_FILES[image2][tmp_name],$old[image2]);
update_disabled_categories();
if (!$in[alias_of]) update_en_cats_in_ads(get_items_in_category($in[n]));
if ($info) $info = '<br>'.$info.' Therefore now you have to go to <a href="rebuild.php?action=reset_rebuild_home"><b>reset/rebuild</b></a> and run functions "Repair paths of classified ads" and  "Recount classified ads in individual categories"';
$s[info] = info_line('Category "'.$in[title].'" has been edited. '.$info);
category_edit($in);
}

#######################################################################################

function get_all_subcategories_of_category($n) {
global $s;
$to_check[] = $n;
while (count($to_check))
{ $current_cat = array_rand($to_check);
  $q = dq("select n,title from $s[pr]cats where parent = '$to_check[$current_cat]'",1);
  while ($x = mysql_fetch_assoc($q)) { $list[$x[n]] = $x[title]; $to_check[] = $x[n]; }
  unset($to_check[$current_cat]);
}
$list = array_unique($list);
return $list;
}

#######################################################################################

function count_ads_only_in_categories($numbers) {
global $s;
if (!is_array($numbers)) $numbers[] = $numbers;
$where = get_where_fixed_part(0,$c,0,$a,$s[cas])." and c = '_".implode("_' or c = '_",$numbers)."_'";
$q = dq("select count(*) from $s[pr]ads where $where",1);
$x = mysql_fetch_row($q);
return $x[0];
}

#######################################################################################

function get_ads_only_in_categories($numbers) {
global $s;
if (!is_array($numbers)) $numbers[] = $numbers;
$where = get_where_fixed_part(0,$c,0,$a,$s[cas])." and c = '_".implode("_' or c = '_",$numbers)."_'";
$q = dq("select n from $s[pr]ads where $where",1);
while ($x = mysql_fetch_row($q)) $a[] = $x[0];
return $a;
}

#######################################################################################

function category_delete($in) {
global $s;
$subcategories = get_all_subcategories_of_category($in[n]);
if ($subcategories)
{ if (!$in[ok])
  { $check_cats = array_keys($subcategories); $check_cats[] = $in[n];
    $ads_in_categories = count_ads_only_in_categories($check_cats);
    if ($ads_in_categories) $ads_info = ", as well as $ads_in_categories classifieds that are listed only in these categories";
    ih();
    echo '
    <tr><td align="center">'.info_line('Selected category has one or more subcategories. These subcategories'.$ads_info.' will be deleted as well. Are you sure?','    <form action="categories.php" method="get" name="form1">'.check_field_create('admin').'
    <input type="hidden" name="action" value="category_delete">
    <input type="hidden" name="ok" value="1">
    <input type="hidden" name="n" value="'.$in[n].'"><br><input type="submit" name="submit" value="Yes, delete it" class="button10"></form><br>
    ');
    echo '<br><br><a href="javascript: history.go(-1)">Cancel it and go back</a>';
    ift();
  }
  else
  { $delete_cats = array_keys($subcategories); $delete_cats[] = $in[n];
    delete_categories_process($delete_cats);
  }
}
elseif (!$in[ok])
{ $check_cats[] = $in[n];
  $ads_in_categories = count_ads_only_in_categories($check_cats);
  if ($ads_in_categories)
  { ih();
    echo '
    <tr><td align="center">'.info_line('Total of '.$ads_in_categories.' classified ads are listed in this category only. If you delete the category, these classifieds will be deleted as well. Are you sure?','<form action="categories.php" method="get" name="form1">'.check_field_create('admin').'
    <input type="hidden" name="action" value="category_delete">
    <input type="hidden" name="ok" value="1">
    <input type="hidden" name="n" value="'.$in[n].'">
    <br><input type="submit" name="submit" value="Yes, delete it" class="button10"></form><br>
    ');
    echo '<br><br><a href="javascript: history.go(-1)">Cancel it and go back</a>';
    ift();
  }
  else delete_categories_process($check_cats);
}
else
{ $delete_cats[] = $in[n];
  delete_categories_process($delete_cats);
}

update_en_cats_in_ads($affected_ads);
$s[info] = info_line('Selected category has been deleted');
if ($in[backto]=='categories_tree') categories_tree($what);
else categories_home($what,0);
}

#######################################################################################

function delete_categories_process($n) {
global $s;
if (is_array($n)) $categories = $n; else $categories[0] = $n;
if ((!count($categories)) OR ((count($categories)==1) AND (!$categories[0]))) return false;
delete_ads_process(get_ads_only_in_categories($categories));
$query = my_implode('n','or',$categories);
$query1 = my_implode('alias_of','or',$categories);
$query2 = my_implode('category','or',$categories);
dq("delete from $s[pr]cats where $query",1);
dq("delete from $s[pr]cats where $query1",1);
dq("delete from $s[pr]cats_areas_n where $query2",1);
dq("delete from $s[pr]cats_disabled where $query",1);
dq("delete from $s[pr]cats_home where $query",1);
dq("delete from $s[pr]cats_search_forms where $query",1);
foreach ($categories as $k=>$v)
{ $c = '_'.$v.'_';
  $q = dq("select n,c from $s[pr]ads where c like '%\_$v\_%'",1);
  while ($x = mysql_fetch_assoc($q))
  { $c_new = str_replace($c,'',$x[c]);
    dq("update $s[pr]ads set c = '$c_new' where n = '$x[n]'",1);
  }
}
}

#######################################################################################
#######################################################################################
#######################################################################################

function manage_subcategories_of_category($action,$n) {
global $s;
dq("update $s[pr]cats set visible = '$action' where n = '$n'",1);
$numbers[0] = $list[] = $n;
while (count($numbers))
{ $k = array_rand($numbers);
  $q = dq("select n from $s[pr]cats where parent = '$numbers[$k]'",1);
  while ($x = mysql_fetch_row($q))
  { dq("update $s[pr]cats set visible = '$action' where n = '$x[0]'",1);
    $numbers[] = $list[] = $x[0];
  }
  unset($numbers[$k]);
}
update_disabled_categories(); // must be here because update_en_cats_in_ads() needs it
$list = array_unique($list);
foreach ($list as $k=>$v) $kkk[] = get_items_in_category($v);
foreach ($kkk as $k=>$v) if (is_array($v)) foreach ($v as $k1=>$v1) $items[] = $v1;
update_en_cats_in_ads($items);
}

#######################################################################################

function update_disabled_categories() {
global $s;
dq("delete from $s[pr]cats_disabled",1);
$q = dq("select n from $s[pr]cats where visible = '0'",1);
while ($x = mysql_fetch_row($q)) dq("insert into $s[pr]cats_disabled values ('$x[0]')",1);
}

#######################################################################################
#######################################################################################
#######################################################################################

//repair_path(42,'<%1. hlavni'); exit;
function repair_path($n,$parent_path,$parent_path_text) {
global $s;
$q = dq("select * from $s[pr]cats where parent = '$n'",1);
while ($x=mysql_fetch_assoc($q))
{ $path_text = "$parent_path_text%><%$x[title]";
  $path_n = $parent_path.$x[n].'_';
  dq("update $s[pr]cats set path_text = '$path_text', path_n = '$path_n' where n = '$x[n]'",1);
  repair_path($x[n],$path_n,$path_text);
}
}

###################################################################################

function find_bigboss_category($category) {
global $s;
while ($category)
{ $old_category = $category;
  $q = dq("select parent,level from $s[pr]cats where n = '$old_category'",1);
  $y = mysql_fetch_row($q);
  $category = $y[0]; $level = $y[1];
}
return $old_category;
}

#######################################################################################
#######################################################################################
#######################################################################################

function categories_tree($what) {
global $s;
$q = dq("select * from $s[pr]cats order by path_text",1);
while ($a=mysql_fetch_assoc($q))
{ set_time_limit(300);
  if (time()>($time1+10)) { $time1=time(); echo str_repeat (' ',4000); flush(); }
  if (!$a[visible]) $hidden = ' <b><font color="red">i</font></b>'; else $hidden = '';
  $mo = ''; for ($i=1;$i<$a[level];$i++) $mo .= '-&nbsp;';
  $a[path_text] = eregi_replace("<%.+%>", "", $a[path_text]);
  $a[path_text] = eregi_replace("<%.+$",$a[title],$a[path_text]);
  if (!$a[path_text]) $a[path_text] = $a[title];
  if ($a[alias_of]) $a[path_text] = $s[alias_pref].$a[path_text].$s[alias_after];
  $categories .= "$mo <a href=\"categories.php?action=category_edit&n=$a[n]\" title=\"Click to edit/view details\" class=\"link10\"><b>$a[path_text]</b></a> 
  <font color=\"blue\">#$a[n]</font> ";
  //$categories .= "<font color=\"green\">xxx $a[items]</font>";
  $categories .= "$hidden - 
  <a class=\"link10\" href=\"categories.php?action=category_copy&n=$a[n]\" title=\"Copy this category to another place\">Copy</a> - ";
  $categories .= "<a class=\"link10\" href=\"ads_list.php?action=ads_searched&c=$a[n]\">Classifieds</a>";
  if ($a[level]==1) $categories .= " - <a class=\"link10\" href=\"ads_list.php?action=ads_searched&bigboss=$a[n]\">Classifieds incl. subcategories</a>";
  $categories .= " - <a class=\"link10\" href=\"categories.php?action=category_delete&n=$a[n]&backto=categories_tree\" title=\"Delete this category\">x</a>";
  $categories .= '<br>';
}
ih();
echo $s[info];
echo '<table border=0 width=98% cellspacing=0 cellpadding=2 class="common_table">
<tr><td class="common_table_top_cell">All Categories</td></tr>
<tr><td align="center">
<table border=0 width=100% cellspacing=2 cellpadding=0>
<tr><td align=left><span class="text10">'.
stripslashes($categories)
.'</span></td></tr></table></td></tr></table><br>
<b><font color="red">i</font></b> - Invisible category<br>
<font color="blue">#25</font> - Category number<br>
x - Delete category<br>';
ift();
}

#############################################################################
#############################################################################
#############################################################################

function categories_import_form() {
global $s;
$pocet = 20;
ih();
?>
<form ENCTYPE="multipart/form-data" action="categories.php" method="post" name="form1"><?PHP echo check_field_create('admin') ?>
<input type="hidden" name="action" value="categories_import_count">
<table border="0" width="500" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Import Categories</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center" colspan="4">Structure of your file<br>
<span class="text10">Make sure to read Instructions below<br>
</span></td></tr>
<?PHP
for ($x=1;$x<=$pocet;$x++)
{ if ($x%2) echo '<tr>';
  ?>
  <td align="left" nowrap>Rank #<?PHP echo $x ?></td>
  <td align="left"><select name="rank[<?PHP echo $x ?>]" class="field10">
  <option value="0">None</option>
  <option value="title">Title</option>
  <option value="parent">Parent category (number)</option>
  <option value="description">Description</option>
  <option value="image2">Icon URL</option>
  <option value="image1">Image URL</option>
  <option value="cat_group">Category group (numbers from 0 to 10)</option>
  <option value="alias_of">Alias of category (requires number)</option>
  <option value="similar">Similar categories (numbers divided by commas, no spaces)</option>
  <option value="offer_wanted">Divide to offer/wanted (can be 1 if yes, 0 if no)</option>
  <option value="price">Show price field (can be 1 if yes, 0 if no)</option>
  <option value="submit_here">Allow submissions (can be 1 if yes, 0 if no)</option>
  <option value="visible">Is visible (can be 1 if yes, 0 if no)</option>
  <option value="m_keyword">Meta keywords</option>
  <option value="m_desc">Meta description</option>
  <option value="rewrite_url">Rewrite URL (optional, the system creates it if needed)</option>
  <option value="tmpl_cat">Template name to use for the category</option>
  <option value="tmpl_one">Template name to use for each ad</option>
  <option value="tmpl_det">Template name to use for detail pages</option>
  <?PHP
  foreach ($all_user_items_list as $k=>$v) echo '<option value="user_item_'.$k.'">'.$v.'</option>';
  echo '</select></td>';
  if (!$x%2) echo '</tr>';
}
?>
<tr>
<td align="left" colspan="4" nowrap>Separator (it separates individual values on the same line) 
<select name="separator" class="field10">
<option value="|">| (recommended)</option><option value=",">,</option><option value=";">;</option>
</select><br>
<span class="text10">The character which will be used as separator can't be used in any value </span>
</td></tr>
<tr>
<td align="left" colspan="4" nowrap>Each value is enclosed by 
<select name="enclosed_by" class="field10">
<option value="0">None (recommended)</option><option value="&quot;">&quot;</option><option value="&#039;">'</option>
</select></td></tr>
<tr>
<td align="left" colspan="4" nowrap>File 
<input type="file" name="datafile" class="field10" style="width:550px">
</td></tr>
<tr>
<td align="center" colspan="4" nowrap>
<input type="submit" name="submit" value="Submit" class="button10">
</td></tr>
</table></td></tr>
</table></form>
<br>
<table border="0" width="700" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Info & Instructions</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="left">
All the data of each link must be placed on the same line.<br>
One blank line is required at the end of the file.<br>
Required items: Title, Parent category.<br>
Parent category - enter number of a parent category or number 0 if it's a first-level category<br>
Create the text file very carefully.<br>The script grabs the categories and places them to the database without any kind of control.<br>
</td></tr>
<tr><td colspan="2" class="common_table_top_cell">Example</td></tr>
<tr><td align="left">
If you have a file with the following structure:<br>
<span class="text10">
Title|Parent_category|Description|Image_URL<br>
Title|Parent_category|Description|Image_URL<br>
Title|Parent_category|Description|Image_URL<br>
Title|Parent_category|Description|Image_URL<br>
.....<br></span>
Select these values:<br>
Rank&nbsp;#1&nbsp;Title&nbsp;&nbsp; 
Rank&nbsp;#2&nbsp;Parent&nbsp;category&nbsp;&nbsp; 
Rank&nbsp;#3&nbsp;Description&nbsp;&nbsp; 
Rank&nbsp;#4&nbsp;Image&nbsp;URL&nbsp;&nbsp; 
<br>
Separator <b>|</b><br>
Each value is enclosed by <b>None</b><br>
</td></tr>
<tr><td colspan="2" class="common_table_top_cell">Problems?</td></tr>
<tr><td align="center">
If you cannot save your current data to the required format, we are able to do it for you. Please <a href="mailto:mail@abscripts.com">email us</a> for more info.
</td></tr>
</table>
</td></tr>
</table>

<?PHP
ift();
}

#################################################################################

function categories_import_count($data) {
global $s;
$filename = $s[phppath].'/data/imported_categories';
if (file_exists($filename)) unlink ($filename);
move_uploaded_file($_FILES[datafile][tmp_name],$filename);
if (file_exists($filename)) chmod($filename,0644);

foreach ($data[rank] as $k=>$v) if (!$v) unset ($data[rank][$k]);
$data[enclosed_by] = stripslashes(htmlspecialchars($data[enclosed_by],ENT_QUOTES));

ih();
$f = fopen ($filename,"r") or problem("Unable to read file<br>'$filename'");
while (!feof($f))
{ $line = fgets($f,100000);
  if (!trim($line)) continue;
  $pocet++;
}
echo info_line('The file you uploaded has '.$pocet.' valid lines - categories');
if ($pocet<100) 
{ echo '<form method="post" action="categories.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="categories_imported">
  <input type="hidden" name="separator" value="'.$data[separator].'">
  <input type="hidden" name="enclosed_by" value="'.$data[enclosed_by].'">
  <input type="hidden" name="total_categories" value="'.$pocet.'">';
  foreach ($data[rank] as $k=>$v) echo '<input type="hidden" name="rank['.$k.']" value="'.$v.'">';
  echo '<input type="submit" name="submit" value="Continue" class="button10"></form>';
}
else
{ echo 'If your PHP is running in a safe mode, you can\'t add all these categories at once. This is because scripts in safe mode can\'t run longer that the time which is set in the PHP configuration (default is 30 sec. but sometimes it is only 5 - 10 sec.). 
  If you are sure that PHP IS NOT running in a safe mode click the button <b>Import all categories</b> and be patient, it may take 15 minutes or more to add a bigger database.<br><br>
  If PHP is running is a safe mode or if you are not sure, click the button <b>Import 100 categories</b>.
  <table border=0 width=350 cellspacing=10 cellpadding=0>
  <form method="post" action="categories.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="categories_imported">
  <input type="hidden" name="total_categories" value="'.$pocet.'">
  <input type="hidden" name="separator" value="'.$data[separator].'">
  <input type="hidden" name="enclosed_by" value="'.$data[enclosed_by].'">';
  foreach ($data[rank] as $k=>$v) echo '<input type="hidden" name="rank['.$k.']" value="'.$v.'">';
  echo '<td align="center"><input type="submit" name="submit" value="Import all categories" class="button10"></td>
  </form>
  <form method="post" action="categories.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="categories_imported">
  <input type="hidden" name="total_categories" value="'.$pocet.'">
  <input type="hidden" name="separator" value="'.$data[separator].'">
  <input type="hidden" name="enclosed_by" value="'.$data[enclosed_by].'">
  <input type="hidden" name="from" value="1">
  <input type="hidden" name="step" value="100">';
  foreach ($data[rank] as $k=>$v) echo '<input type="hidden" name="rank['.$k.']" value="'.$v.'">';
  echo '<td align="center"><input type="submit" name="submit" value="Import 100 categories" class="button10"></td>
  </form>
  </tr></table>
  ';
}
echo info_line('<br><br>Important note:','Never hit your browser\'s "Reload" or "Back" button, otherwise some categories may be added more than once.');
ift();
}

#############################################################################

function categories_imported($data) {
global $s;
//foreach ($data[rank] as $k=>$v) echo "$k - $v<br>";//exit;
if ($data[enclosed_by])
{ // dalsi 2 radky jsou tady proto, ze se to porad prevadelo samo z &#039; na \', ale nemuzu se na to asi spolehnout
  $data[enclosed_by] = str_replace(chr(92),'',$data[enclosed_by]);
  if ($data[enclosed_by]=="'") $data[enclosed_by] = '&#039;'; elseif ($data[enclosed_by]=='"') $data[enclosed_by] = '&quot;';
  if ($data[enclosed_by]=='&#039;') $enclosed_by = "'"; elseif ($data[enclosed_by]=='&quot;') $enclosed_by = '"';
}


/*
  <option value="visible">Is visible (can be 1 if yes, 0 if no)</option>
*/
ih();
echo '<br>';
$time0 = time(); $pocet = 0;
$f = fopen("$s[phppath]/data/imported_categories",'r');
while (!feof($f))
{ $pocet ++; if ($pocet<$data[from]) { $line = fgets($f,10000); continue; }
  set_time_limit(600);
  if (time()>$time0+30) { header('X-pmaPing: Pong'); $time0 = time(); }
  //if (time()>($time1+10)) { $time1=time(); echo ' Working ... '.str_repeat (' ',4000); flush(); }
  $line = fgets($f,100000); if (!trim($line)) continue;
  $line = str_replace(chr(92),'',$line);
  //echo $line;
  $pole = explode($data[separator],trim($line));
  if ($enclosed_by)
  { foreach ($pole as $k=>$v) $pole[$k] = ereg_replace("^".$enclosed_by,'',ereg_replace($enclosed_by."$",'',$v)); }
  $pole = replace_array_text($pole);
  foreach ($data[rank] as $k=>$v) { $x = $k - 1; $$v = $pole[$x]; }
  unset ($user_items); $arr = get_defined_vars();
  foreach ($arr as $k=>$v) 
  { if (strstr($k,'user_item_'))
    { $x = str_replace('user_item_','',$k); $user_items[$x] = $v; }
  }
  if ($parent)
  { $parent = get_category_variables($parent);
    if ($parent[n])
    { $path_text = $parent[path_text].'%><%'.$title;
      $parent_path = $parent[path_n];
      $level = $parent[level]+1;
      if (!isset($visible)) $visible = $parent[visible];
      if ($parent[level]>1) $bigboss = $parent[bigboss]; else $bigboss = $parent[n];
      $parent = $parent[n];
    }
    else { $parent = $bigboss = 0; $level = 1; if (!isset($visible)) $visible = 1; $path_text = '<%'.$title; }
  }
  else { $parent = $bigboss = 0; $level = $visible = 1; $path_text = '<%'.$title; }
  if (!$rewrite_url) $rewrite_url = discover_rewrite_url(str_replace('<%','',str_replace('%><%','/',$path_text)),1);
  if (!$tmpl_cat) $tmpl_cat = 'category.html';
  if (!$tmpl_one) $tmpl_one = 'ad_a.txt';
  if (!$tmpl_det) $tmpl_det = 'ad_details.html';
  if (trim($similar)) $similar = '_'.implode('_ _',$similar).'_';
  if (!isset($visible)) $visible = 1;
  dq("insert into $s[pr]cats values (NULL,'$parent','','$rank','$alias_of','$title','$description','$image1','$image2','$m_keyword','$m_desc','0','','','','','$similar','',' $tmpl_cat','$tmpl_one','$tmpl_det','','$visible','$cat_group','0','$announcements','$offer_wanted','$price','0','0')",1);
  $n = mysql_insert_id();
  update_category_area_paths('c',$n);
  unset($tmpl_cat,$tmpl_one,$tmpl_det,$similar,$alias_of,$visible);
  if (($data[step]) AND ($pocet>=($data[from]+$data[step]-1))) break;
}
$to = $data[from] + $data[step] - 1; $from = $to+1;
if (($data[step]) AND ($data[total_categories]>$to))
{ echo 'Categories '.$data[from].' to '.$to.' added<br>
  <form method="post" action="categories.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="categories_imported">
  <input type="hidden" name="total_categories" value="'.$data[total_categories].'">
  <input type="hidden" name="separator" value="'.$data[separator].'">
  <input type="hidden" name="enclosed_by" value="'.$data[enclosed_by].'">
  <input type="hidden" name="from" value="'.$from.'">
  <input type="hidden" name="step" value="'.$data[step].'">';
  foreach ($data[rank] as $k=>$v) echo '<input type="hidden" name="rank['.$k.']" value="'.$v.'">';
  echo '<td align="center"><input type="submit" name="submit" value="Import next 100 categories" class="button10"></td></form>';
}
else echo info_line('Total of '.($pocet-1).' categories imported.');
exit;
}


##################################################################################
##################################################################################
##################################################################################

?>