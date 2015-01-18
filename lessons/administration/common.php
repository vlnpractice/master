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

error_reporting (E_ERROR | E_PARSE);
if (!$no_data_now) include('../data/data.php');
include_once($s[phppath].'/administration/functions.php');
if (($_POST) AND (!$s['no_test'])) check_field('admin');

if (($_SESSION[GC_admin_user]) AND ($_SESSION[GC_admin_password]))
{ $s[GC_admin_username] = $_SESSION[GC_admin_user]; $s[GC_admin_password] = $_SESSION[GC_admin_password]; }
else // dava se to aji do session protoze cookies nefakci na public pages
{ $s[GC_admin_username] = $_SESSION[GC_admin_user] = $_COOKIE[GC_admin_user];
  $s[GC_admin_password] = $_SESSION[GC_admin_password] = $_COOKIE[GC_admin_password];
}

###################################################################################
###################################################################################
###################################################################################

function ift() {
include($s[phppath].'/administration/_footer.txt');
exit;
}

function ih() {
global $s;
$x = stripslashes(implode('',file($s[phppath].'/administration/_head.txt')));
echo str_replace('#%charset%#',$s[charset],$x);
}

function page_title($info1,$info2) {
global $s;
if (!$info1) return '';
if (!$info2) return '<h1 style="margin-bottom:20px;margin-top:20px;">'.$info1.'</h1>';
return '<h1 style="padding-bottom:0px;margin-bottom:0px;margin-top:20px;">'.$info1.'</h1><div style="width:725px;text-align:left;padding-bottom:20px;">'.$info2.'</div>';
}

function ahref($link,$text) {
return '<a href="'.$link.'">'.$text.'</a>';
}

function no_result($what) {
echo info_line('No one approved item found');
ift();
}

#####################################################################################
#####################################################################################
#####################################################################################

function unreplace_once_html($x) {
// na html po vytazeni z databaze
if (!$x) return $x;
$x = ereg_replace("''","'",str_replace(chr(92),'',$x));
return ereg_replace('&#039;',"'",ereg_replace("--BACKSLASH--",'\\',ereg_replace('&#92;','\\',$x)));
}

###################################################################################
###################################################################################
###################################################################################

function check_admin($action) {
global $s;
if (($_SESSION[GC_admin_user]) AND ($_SESSION[GC_admin_password]))
{ $username = $_SESSION[GC_admin_user]; $password = $_SESSION[GC_admin_password]; }
else { $username = $_COOKIE[GC_admin_user]; $password = $_COOKIE[GC_admin_password]; }
$username = str_replace("'",'',$username); $password = str_replace("'",'',$password);
if ($action) $q = dq("select count(*) from $s[pr]admins,$s[pr]admins_rights where $s[pr]admins.username = '$username' and $s[pr]admins.password = '$password' and $s[pr]admins_rights.admin = $s[pr]admins.n and $s[pr]admins_rights.action = '$action'",1);
else $q = dq("select count(*) from $s[pr]admins where username = '$username' and password = '$password'",1);
$data = mysql_fetch_row($q);
if (!$data[0]) problem('You don\'t have permission for this action.');
}

#####################################################################################
#####################################################################################
#####################################################################################

function make_time($date) {
list ($x[m], $x[d], $x[y]) = split ('/', $date);
$cas = mktime (0,0,0,$x[m],$x[d],$x[y]);
return $cas;
}

###################################################################################
###################################################################################
###################################################################################

function get_dates_ads_text($data) {
global $s;
if (!$data[t1]) $a[t1] = 'N/A'; else $a[t1] = datum($data[t1],0);
if (!$data[t2]) $a[t2] = 'N/A'; else $a[t2] = datum($data[t2],0);
return $a;
}

#####################################################################################
#####################################################################################
#####################################################################################

function problem($error) {
global $s;
ih();
echo '<br><br><font color="FF0000" size=3 face="Verdana,arial"><b>ERROR</b></font><br><br>
<span class="text13a_bold"><b>'.$error.'</b></span><br><br>';
ift();
}

########################################################################################
########################################################################################
########################################################################################

function user_defined_items_show($what,$in) {
global $s;
foreach ($s[all_user_items_list] as $k=>$v)
{ if ($v[kind]=='checkbox')
  { if ($in['user_item_'.$k][code]) $in['user_item_'.$k][text] = 'Yes';
    else $in['user_item_'.$k][text] = 'No';
  }
  elseif (!$in['user_item_'.$k][text]) $in['user_item_'.$k][text] = 'No value';
  $user_items .= '<tr>
  <td align="left" valign="top">'.$v[description].'<br></td>
  <td align="left" valign="top">'.$in['user_item_'.$k][text].'<br></td>
  </tr>';
}
return $user_items;
}

########################################################################################
########################################################################################
########################################################################################

function categories_rows_form($what,$in) {
global $s;
$categories = $in[c];
if (!is_array($categories)) $categories = explode(' ',str_replace('_','',$categories));
if ($categories[0]) $only_bigboss_n = get_bigboss_category($categories[0]);
if (!$in[n]) $in[n] = 0;
for ($x=0;$x<=$s[max_cats]-1;$x++)
{ if (!$x) $b[categories] = 'Categories'; else $b[categories] = '&nbsp;';
  $select_box = '<select class="field10" name="ad['.$in[n].'][categories][]">';
  if ($x) $select_box .= '<option value="0">None</option>';
  $select_box .= categories_selected($what,$categories[$x],1,1,0,0,$only_bigboss_n).'</select>';
  $a .= '<tr>
  <td nowrap align="left" valign="top">'.$b[categories].' </td>
  <td align="left">'.$select_box.'</td></tr>';
}
return $a; }

########################################################################################

function areas_rows_form($in) {
global $s;
$areas = $in[a];
if (!$in[n]) $in[n] = 0;
if (!is_array($areas)) $areas = explode(' ',str_replace('_','',$areas));
for ($x=0;$x<=$s[max_areas]-1;$x++)
{ if (!$x) $b[areas] = 'Areas'; else $b[areas] = '&nbsp;';
  $select_box = '<select class="field10" name="ad['.$in[n].'][areas][]">';
  if ($x) $select_box .= '<option value="0">None</option>';
  $select_box .= areas_selected($areas[$x],1,1,0,0).'</select>';
  $a .= '<tr>
  <td nowrap align="left" valign="top">'.$b[areas].' </td>
  <td align="left">'.$select_box.'</td></tr>';
} 
return $a;
}

########################################################################################
########################################################################################
########################################################################################

function list_of_categories_for_item_admin($what,$c) {
global $s;
$x = explode(' ',str_replace('_','',$c));
$categories = get_categories_data($x,1);
foreach ($categories as $k=>$v)
{ if (!$v) continue;
  if (!$v[visible]) $info = ' (invisible)'; else $info = '';
  if ($what=='ad') $a .= '<a target="_self" href="ads_list.php?action=ads_searched&category='.$k.'">'.$v[title].$info.'</a><br>';
}
return $a;
}

########################################################################################

function list_of_areas_for_item_admin($areas) {
global $s;
$x = explode(' ',str_replace('_','',$areas));
$areas = get_areas_data($x,1);
foreach ($areas as $k=>$v)
{ if (!$v) continue;
  $a .= $v[title].'<br>';
}
return $a;
}

########################################################################################
########################################################################################
########################################################################################

function category_template_select($kind,$selected) {
global $s;
if (!$selected) $selected = $kind;
$x = explode('.',$kind);
$dr = opendir("$s[phppath]/styles/_common/templates");
rewinddir($dr);
while ($q = readdir($dr))
{ if (($q != ".") AND ($q != "..") AND (is_file("$s[phppath]/styles/_common/templates/$q")))
  if (eregi("^$x[0][a-z0-9_]*\.$x[1]$",$q)) $pole[] = $q;
}
sort($pole);
foreach ($pole as $k => $v)
{ if ($v == $selected) $z = ' selected'; else $z = '';
  $y .= "<option value=\"$v\"$z>$v</option>";
}
return $y;
}

##################################################################################

function categories_selected($what,$vybrana,$incl_invisible,$incl_disabled_submissions,$incl_aliases,$no_info,$only_bigboss_n,$selected_vars) {
global $s;
$m[invisible] = 'invisible'; $m[disabled] = 'disabled submissions'; // jen pro admina
if (!$incl_invisible) $where = 'AND visible = 1';
//if (!$incl_disabled_submissions) $where .= ' AND submit_here = 1';
if (!$incl_aliases) $where .= " AND alias_of = '0'";
if (!$s[cats_share_usit]) { if ($only_bigboss_n) $where .= " AND bigboss = '$only_bigboss_n'"; }
if ($what=='ad_first') $where .= " AND level = '1'";
$q = dq("select * from $s[pr]cats where 1 $where order by path_text",1);
while ($a=mysql_fetch_assoc($q))
{ set_time_limit(30);
  if (time()>($time1+10)) { $time1=time(); echo str_repeat (' ',4000); flush(); }
  if (!$no_info)
  { unset($i,$info);
    if (!$a[visible]) $i[] = $m[invisible]; //if (!$a[submit_here]) $i[] = $m[disabled];
    if ($i) $info = '('.implode(', ',$i).')';
  }
  $mo = ''; for ($i=1;$i<$a[level];$i++) $mo .= '- ';
  $a[path_text] = eregi_replace("<%.+%>", "",$a[path_text]);
  $a[path_text] = eregi_replace("<%.+$",$a[title],$a[path_text]);
  if ($a[alias_of]) $a[path_text] = $s[alias_pref].$a[path_text].$s[alias_after];
  $a[path_text] = stripslashes($a[path_text]);
  if ($a[n]==$vybrana) $selected = ' selected'; else $selected = '';
  if ($selected_vars) $y = "#_category_selected_$a[n]_#";
  $x .= "<option value=\"$a[n]\"$selected$y>$mo $a[path_text]$info</option>\n";
}
return stripslashes($x); }

#######################################################################################

function select_list_first_categories($vybrana) {
global $s;
$q = dq("select n,title from $s[pr]cats where level = '1' order by path_text",1);
while ($a = mysql_fetch_assoc($q))
{ $a[title] = stripslashes($a[title]);
  if ($a[n]==$vybrana) $selected = ' selected'; else unset($selected);
  $x .= '<option value="'.$a[n].'"'.$selected.'>'.$a[title].'</option>';
}
return $x;
}

#######################################################################################
#######################################################################################
#######################################################################################

function get_items_in_category($n) {
global $s;
$q = dq("select n from $s[pr]ads where c like '%\_$n\_%'",1);
while ($x = mysql_fetch_row($q)) $a[] = $x[0];
return $a;
}

########################################################################################

function update_en_cats_in_ads($list) {
global $s;
// opravi seznam kategorii pro ads ktere jsou v $list
// $list is a list of links - array of numbers
if (!count($list)) return false;
$query = my_implode('n','or',array_unique($list));
$q = dq("select n,c from $s[pr]ads where $query",1);
while ($x = mysql_fetch_row($q)) $new_list[$x[0]] = $x[1];
foreach ($new_list as $k=>$v)
{ set_time_limit(60);
  $en_cats = has_some_enabled_categories($v);
  dq("update $s[pr]ads set en_cats = '$en_cats' where n = '$k'",1);
}
}

###################################################################################

function usit_rows_form_admin($category,$ad_n,$fields_name,$only_visible_forms,$action) {
global $s;

$category = str_replace('_','',$category); $bigboss = get_bigboss_category($category);
list($usits,$avail_val) = get_category_usit($bigboss,$only_visible_forms,0);
if ($ad_n)
{ if ($s[queue]) $queue = 1;
  elseif ($action=='ad_edit') $queue = 0;
  $from_database = usit_get_current_values($ad_n,$queue);
}

foreach ($usits as $k=>$usit)
{ unset($field);
  if ($usit[item_type]=='text')
  { if ($ad_n) $field = $from_database[$usit[n]][value_text]; // edit or queue
    elseif (($in[url]) OR ($in[title])) $field = $in['user_item_'.$usit[n]]; // new but already something entered
    else $field = $usit[def_value_text]; // new
    if ($in=='search_form') $field = '<input maxlength="255" name="user_item['.$usit[n].']" value="'.$field.'" class="field10" style="width:550px">';
    else $field = '<input maxlength="255" name="'.$fields_name.'[user_item_'.$usit[n].']" value="'.$field.'" class="field10" style="width:550px">';
  }
  elseif ($usit[item_type]=='textarea')
  { if ($ad_n) $field = $from_database[$usit[n]][value_text]; // edit or queue
    elseif (($in[url]) OR ($in[title])) $field = $in['user_item_'.$usit[n]]; // new but already something entered
    else $field = $usit[def_value_text]; // new
    if ($in=='search_form') $field = '<input maxlength="255" name="user_item['.$usit[n].']" value="'.$field.'" class="field10" style="width:550px">';
    else $field = '<textarea rows="10" cols="70" name="'.$fields_name.'[user_item_'.$usit[n].']" class="field10" style="width:550px">'.$field.'</textarea>';
  }
  elseif ($usit[item_type]=='htmlarea')
  { if ($ad_n) $field = $from_database[$usit[n]][value_text]; // edit or queue
    elseif (($in[url]) OR ($in[title])) $field = $in['user_item_'.$usit[n]]; // new but already something entered
    else $field = $usit[def_value_text]; // new
    if ($in=='search_form') $field = '<input maxlength="255" name="user_item['.$usit[n].']" value="'.$field.'" class="field10" style="width:550px">';
    else $field = get_fckeditor($fields_name.'[user_item_'.$usit[n].']',$field,'AdminToolbar');
    //'<textarea rows="10" cols="70" name="'.$fields_name.'[user_item_'.$usit[n].']" class="field10" style="width:550px">'.$field.'</textarea>';
  }
  else
  { if ($ad_n) $value = $from_database[$usit[n]][value_code]; // edit or queue
    elseif (($in[url]) OR ($in[title])) $value = $in['user_item_'.$usit[n]]; // new but already something entered
    else $value = $usit[def_value_code]; // new
    if ($usit[item_type]=='checkbox')
    { if ($in=='search_form')
      $field = '
      <input type="radio" name="user_item['.$usit[n].']" value="0" checked>Any&nbsp;
      <input type="radio" name="user_item['.$usit[n].']" value="checked">Checked&nbsp;
      <input type="radio" name="user_item['.$usit[n].']" value="unchecked">Unchecked';
	  else
	  { if ($value) $field = ' checked'; else $field = '';
        $field = '<input type="checkbox" name="'.$fields_name.'[user_item_'.$usit[n].']" value="1" '.$field.'>';
      }
    }
    elseif ($usit[item_type]=='radio')
    { if ($in=='search_form') $field = '<input type="radio" name="user_item['.$usit[n].']" value="0" checked>Any<br>';
	  foreach ($avail_val[$usit[n]] as $k=>$v)
      { if ($value==$k) $x = ' checked'; else $x = '';
        if ($in=='search_form') $field .= '<input type="radio" name="user_item['.$usit[n].']" value="'.$k.'"'.$x.'>'.$v[description].'<br>';
	    else $field .= '<input type="radio" name="'.$fields_name.'[user_item_'.$usit[n].']" value="'.$k.'"'.$x.'>'.$v[description].'<br>';
      }
    }
    elseif ($usit[item_type]=='select')
    { if ($in=='search_form') $field = '<option value="0" selected>Any</option>';
	  foreach ($avail_val[$usit[n]] as $k=>$v)
      { if ($value==$k) $x = ' selected'; else $x = '';
	    $field .= '<option value="'.$k.'"'.$x.'>'.$v[description].'</option>';
      }
      if ($in=='search_form') $field = '<select name="user_item['.$usit[n].']" class="field10">'.$field.'</select>';
      else $field = '<select name="'.$fields_name.'[user_item_'.$usit[n].']" class="field10">'.$field.'</select>';
    }
    elseif ($usit[item_type]=='multiselect')
    { $x1 = explode(' ',trim(str_replace('_',' ',$from_database[$usit[n]][value_text])));
      if ($in=='search_form') $field = '<option value="0" selected>Any</option>';
	  foreach ($avail_val[$usit[n]] as $k=>$v)
      { //if ($value==$k) $x = ' selected'; else $x = '';
        if (in_array($k,$x1)) $x = ' selected'; else $x = '';
	    $field .= '<option value="'.$k.'"'.$x.'>'.$v[description].'</option>';
      }
      if ($in=='search_form') $field = '<select name="user_item['.$usit[n].']" class="field10">'.$field.'</select>';
      else $field = '<select name="'.$fields_name.'[user_item_'.$usit[n].'][]" class="field10" size="5" multiple>'.$field.'</select>';
    }
  }
  if ($usit[item_type]=='htmlarea') $a .= '<tr>
  <td nowrap align="left" valign="top" colspan="2">'.$usit[description].' </td>
  </tr>
  <tr>
  <td nowrap align="left" valign="top" colspan="2">'.$field.'</td>
  </tr>';
  else $a .= '<tr>
  <td nowrap align="left" valign="top">'.$usit[description].' </td>
  <td nowrap align="left" valign="top">'.$field.'</td>
  </tr>';
}
return $a;
}

#################################################################################

function get_username($n) {
$user = get_user_variables($n);
return $user[username];
}

##################################################################################

function upload_category_area_image($what,$n,$file_n,$original_name,$tmp_name,$old_file) {
global $s,$m;
if ($what=='a') { $folder_name = 'areas'; $table = "$s[pr]areas"; }
elseif ($what=='c') { $folder_name = 'categories'; $table = "$s[pr]cats"; }
$extension = str_replace('.','',strrchr($original_name,'.'));
$working_name = "$s[phppath]/images/$folder_name/".md5(microtime()).'.'.$extension;
if (!is_uploaded_file($tmp_name)) return array('','','','Unable to upload file '.$original_name);
if (file_exists($working_name)) unlink($working_name);
move_uploaded_file($tmp_name,$working_name);

if (is_array($n)) $numbers = $n; else $numbers[0] = $n;
foreach ($numbers as $k=>$n)
{ $file_name = "$n-$file_n-$s[cas].$extension";
  $file_path = "$s[phppath]/images/$folder_name/$file_name";
  if (trim($old_file)) unlink(str_replace($s[site_url],$s[phppath],$old_file));
  copy($working_name,$file_path); $file_url = "$s[site_url]/images/$folder_name/$file_name";
  if (file_exists($file_path)) 
  { chmod($file_path,0644);
    dq("update $table set image$file_n = '$file_url' where n = '$n'",1);
  }
}
unlink($working_name);
}

##################################################################################

function update_category_area_paths($what,$n) {
global $s;
if ($what=='a') { $vars = get_area_variables($n); $table = "$s[pr]areas"; if ($vars[parent]) $parent = get_area_variables($vars[parent]); }
elseif ($what=='c') { $vars = get_category_variables($n); $table = "$s[pr]cats"; if ($vars[parent]) $parent = get_category_variables($vars[parent]); }
if ($parent[n]) { $path_text = "$parent[path_text]%><%$vars[title]"; $path_n = $parent[path_n].$n.'_'; if ($parent[bigboss]) $bigboss = $parent[bigboss]; else $bigboss = $parent[n]; }
else { $path_text = "<%$vars[title]"; $path_n = '_'.$n.'_'; $bigboss = $n; }
$level = $parent[level] + 1;
if ($vars[rewrite_url]) $rewrite_url = $vars[rewrite_url]; else $rewrite_url = discover_rewrite_url(str_replace('<%','',str_replace('%><%','/',$path_text)),1);
//echo "update $table set path_text = '$path_text', path_n = '$path_n', level = '$level', bigboss = '$bigboss', rewrite_url = '$rewrite_url' where n = '$n'";
dq("update $table set path_text = '$path_text', path_n = '$path_n', level = '$level', bigboss = '$bigboss', rewrite_url = '$rewrite_url' where n = '$n'",1);
}


######################################################################################

function delete_file($in) {
global $s;
ih();
if ($in[ad]) { $what = 'a'; $item_n = $in[ad]; }
elseif ($in[user]) { $what = 'u'; $item_n = $in[user]; }
else exit;
delete_file_process($what,$in[file_type],$item_n,$in['file'],$in[queue]);
echo '<table border=0 width=100% height="100% cellspacing=0 cellpadding=2 class="common_table"><tr><td nowrap align="center" valign="middle">';
echo '<div class="info_line" style="width:250px;height:100px;"><br><br>File deleted</div>';
echo '</td></tr></table>';
ift();
}

#################################################################################
#################################################################################
#################################################################################

function images_form_admin($what,$in,$queue) {
global $s;
if (!$in[n]) $in[n] = 0;
if ($what=='u') { $max = $s[u_max_pictures]; $script = 'users.php'; $field_name = 'user'; }
else
{ $ad_vars = get_ad_variables($in[n],$queue);
  $max = $s[a_max_pictures] + $ad_vars[x_pictures_max];
  $script = 'ad_details.php';
  $field_name = 'ad';
}
if ((!$in[n]) AND (strstr($_GET[action],'_copy'))) echo '<tr><td nowrap align="center" valign="top" colspan="2">Fields to upload images will be available when the item has been copied.</td></tr>';
else
{ list($images) = get_item_files($what,$in[n],$queue);
//foreach ($images[221][1] as $k=>$v) echo "$max -$k - $v<br>";
  for ($x=1;$x<=$max;$x++)
  { echo '<tr>
    <td nowrap align="left" valign="top">Upload an image'; if ($max>1) echo ' #'.$x; echo '</td>
    <td nowrap align="left" valign="top"><input type="file" maxlength="255" style="width:550px" name="image_upload['.$in[n].']['.$x.']" class="field10">';    
    if ($images[$in[n]][$x][url])
    { echo '<br />'.image_preview_code($images[$in[n]][$x][n],$images[$in[n]][$x][url],preg_replace("/\/$in[n]-/","/$in[n]-big-",$images[$in[n]][$x][url]));
      if ($in[n]) echo '<input type="checkbox" name="'.$field_name.'['.$in[n].'][delete_image][]" value="'.$x.'"> Delete this image';
    }
    echo '</td>
    </tr>';
    if ($what!='u') echo '<tr>
    <td nowrap align="left" valign="top">Image description</td>
    <td nowrap align="left" valign="top"><input maxlength="255" style="width:550px" name="image_description['.$in[n].']['.$x.']" value="'.$images[$in[n]][$x][description].'" class="field10"></td>
    </tr>';	
  }
}
}

#############################################################################

function images_show_admin($what,$in,$queue) {
global $s;
$images = get_item_files($what,$in[n],$queue);
foreach ($images[image_url][$in[n]] as $x=>$url)
{ echo '<tr>
  <td nowrap align="left" valign="top">Image '; if ($what!='u') echo '#'.$x; echo '</td>
  <td nowrap align="left" valign="top">'.image_preview_code($images[image_n][$in[n]][$x],$url,preg_replace("/\/$in[n]-/","/$in[n]-big-",$url));
  echo '</td>
  </tr>';
  if ($what!='u') echo '<tr>
  <td nowrap align="left" valign="top">Image description</td>
  <td nowrap align="left" valign="top">'.$images[image_description][$in[n]][$x].'&nbsp;</td>
  </tr>';	
}
}

#############################################################################


?>