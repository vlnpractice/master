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
include('./data/data.php');
include_once($s[phppath].'/administration/functions.php');
redirect_www();
get_messages('common.php');
$s[pages_public] = 1;
$s[ip] = getenv('REMOTE_ADDR');
if (!preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" . "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/",$s[ip])) $s[ip] = 'UNKNOWN';
try_ip_blacklist($s[ip]);


$_GET[vars] = str_replace('.html','',$_GET[vars]);
$_GET[vars] = str_replace("$s[ARfold_l_cat]-",'categories-',$_GET[vars]);
//echo $_GET[vars];

//foreach ($_GET as $k=>$v) echo "$k - $v<br>";

if (preg_match("/$s[ARfold_l_detail]-[0-9]+/i",$_GET[vars],$x)) $ad_n = str_replace("$s[ARfold_l_detail]-",'',$x[0]);
else
{ //if (preg_match("/\/offer_wanted-[a-z]+/i",$_GET[vars],$x)) { $_GET[vars] = str_replace($x[0],'',$_GET[vars]); $s[this_offer_wanted] = str_replace('/offer_wanted-','',$x[0]); }
  if (($_GET[vars]=='offer') OR ($_GET[vars]=='wanted') OR ($_GET[vars]=='all')) $_SESSION[this_offer_wanted] = $s[this_offer_wanted] = $_GET[vars]; 
  elseif (preg_match("/-offer|-wanted|-all/i",$_GET[vars],$x)) $_SESSION[this_offer_wanted] = $s[this_offer_wanted] = str_replace('-','',$x[0]); 
  elseif (($_SESSION[this_offer_wanted]=='offer') OR ($_SESSION[this_offer_wanted]=='wanted') OR ($_SESSION[this_offer_wanted]=='all')) $s[this_offer_wanted] = $_SESSION[this_offer_wanted];
  if (preg_match("/-sort-[a-z_]+/i",$_GET[vars],$x)) { $_GET[vars] = str_replace($x[0],'',$_GET[vars]); $_SESSION[this_sort] = $s[this_sort] = str_replace('-sort-','',$x[0]); } elseif (preg_match("/[a-z_]+/i",$_SESSION[this_sort])) $s[this_sort] = $_SESSION[this_sort];
  if (preg_match("/-direction-[a-z]+/i",$_GET[vars],$x)) { $_GET[vars] = str_replace($x[0],'',$_GET[vars]); $_SESSION[this_direction] = $s[this_direction] = str_replace('-direction-','',$x[0]); } elseif (preg_match("/[a-z_]+/i",$_SESSION[this_direction])) $s[this_direction] = $_SESSION[this_direction];
  if (preg_match("/\/categories-[0-9]+-[0-9]+-[0-9]+/i",$_GET[vars],$x)) { $_GET[vars] = str_replace($x[0],'',$_GET[vars]); $x = explode('-',str_replace("/categories-",'',$x[0])); $s[this_cat] = $x[0]; if (!$s[this_area]) $s[this_area] = $x[1]; if (!$s[this_page]) $s[this_page] = $x[2]; }
  elseif (preg_match("/\/categories-[0-9]+-[0-9]+/i",$_GET[vars],$x)) { $_GET[vars] = str_replace($x[0],'',$_GET[vars]); $x = explode('-',str_replace("/categories-",'',$x[0])); $s[this_cat] = $x[0]; if (!$s[this_area]) $s[this_area] = $x[1]; }
  if (substr($_GET[vars],0,1)=='/') $s[this_cat_rewrite] = substr($_GET[vars],1,1000); else $s[this_cat_rewrite] = $_GET[vars];
  if (strstr($s[this_cat_rewrite],'ad_details')) unset($s[this_cat_rewrite]);
}
if ($s[this_area])
{ $q = dq("select bigboss,rewrite_url,title,level,latitude,longitude from $s[pr]areas where n = '$s[this_area]'",1); $x = mysql_fetch_assoc($q);
  $s[this_area_url] = $x[rewrite_url];
  $s[this_area_title] = $x[title];
  $s[this_area_vars] = $x;
}
//foreach ($_GET as $k=>$v) echo "$k - $v<br>";

//echo "$s[this_cat] --- $s[this_area] --- $s[this_sort] --- $s[this_direction] --- $s[this_offer_wanted]";

if ($_COOKIE[GC_u_email])
{ $s[GC_u_password] = $_COOKIE[GC_u_password];
  $s[GC_u_n] = $_COOKIE[GC_u_n];
  $s[GC_u_style] = $_COOKIE[GC_u_style];
  $s[GC_u_name] = $_COOKIE[GC_u_name];
  $s[GC_u_email] = $_COOKIE[GC_u_email];
}
elseif ($_SESSION[GC_u_email])
{ $s[GC_u_password] = $_SESSION[GC_u_password];
  $s[GC_u_n] = $_SESSION[GC_u_n];
  $s[GC_u_style] = $_SESSION[GC_u_style];
  $s[GC_u_name] = $_SESSION[GC_u_name];
  $s[GC_u_email] = $_SESSION[GC_u_email];
}
if ($s[GC_u_n])
{ $user_vars = get_user_variables($s[GC_u_n]);
  if ((!$user_vars[n]) OR ($s[GC_u_password]!=$user_vars[password]))
  { unset($s[GC_u_n],$s[GC_u_password],$s[GC_u_style],$s[GC_u_name],$s[GC_u_email]);
    unset($_SESSION[GC_u_email],$_SESSION[GC_u_password],$_SESSION[GC_u_name],$_SESSION[GC_u_style]);
    setcookie(GC_u_email,false);
    setcookie(GC_u_password,false);
    setcookie(GC_u_name,false);
    setcookie(GC_u_style,false);
  }
}
if ($s[GC_u_email]) $s[GC_style] = $s[GC_u_style];
else $s[GC_style] = $_SESSION[GC_style];
if (!$s[GC_style]) $s[GC_style] = $s[def_style];

$a = getenv('HTTP_USER_AGENT');
if ( ($s[GC_style]!='Mobile') AND (!$_SESSION[nomobile]) AND ((strpos($a,'BlackBerry')!= false) OR (strpos($a,'Windows Phone')!= false) OR (strpos($a,'iPhone')!=false) OR (strpos($a,'Android')!=false) OR (strpos($a,'Opera Mini')!=false)) ) { $_SESSION[GC_style] = 'Mobile'; header("Location: $s[site_url]/styles.php?style=Mobile"); exit; }

##################################################################################
##################################################################################
##################################################################################

function redirect_www() {
global $s;
$http_host = getenv('HTTP_HOST');
if (!$http_host) return false;
if ((strstr($http_host,'www.')) AND (!strstr($s[site_url],'//www.'))) $new_host = str_replace('www.','',$http_host);
elseif ((!strstr($http_host,'www.')) AND (strstr($s[site_url],'//www.'))) $new_host = "www.$http_host";
if (!$new_host) return false;
$request = getenv('REQUEST_URI');
if (!$request) $request = '/';
header("Location: http://$new_host$request");
exit;
}

##################################################################################

function add_this_area($a) {
global $s;
if (!$s[this_area]) return $a;
return str_replace('area_n',$s[this_area],str_replace('area_rewrite',$s[this_area_url],$a));
}

##################################################################################

function get_prices_table($c) {
global $s,$m;
$c = get_bigboss_category($c);
$q = dq("select * from $s[pr]ads_prices where c = '$c' order by days",1);
if (!mysql_num_rows($q)) $q = dq("select * from $s[pr]ads_prices where c = '0' order by days",1);
while ($b=mysql_fetch_assoc($q)) { $a[lines] .= parse_part('ad_prices_one_line.txt',$b); $s[period_options] .= '<option value="'.$b[days].'">'.$b[days].'</option>'; }
return parse_part('ad_prices.txt',$a);
}

##################################################################################
##################################################################################
##################################################################################

function info_window($info) {
global $s;
$a[info] = $info;
if ($s[GC_style]) $a[style] = $s[GC_style]; else $a[style] = $s[def_style];
$a[charset] = $s[charset];
echo stripslashes(parse_part('info_window.html',$a));
exit;
}

##################################################################################
##################################################################################
##################################################################################

function check_email($email) {
if (eregi("^[a-z0-9_.-]+@[a-z0-9_-]+\.[a-z0-9.]+$",$email)) return 1;
return 0;
}

##################################################################################
##################################################################################
##################################################################################

function who_is_online() {
global $s;
if (!$s[ip]) return 'This function is not supported by this server.';
$t = $s[cas]-600;
dq("delete from $s[pr]online where time < '$t'",0);
dq("insert into $s[pr]online values ('$s[cas]','$s[ip]')",0);
$q = dq("select count(*) from $s[pr]online",0); $x = mysql_fetch_row($q);
return $x[0];
}

##################################################################################
##################################################################################
##################################################################################

function get_detail_template_name($what,$categories) {
global $s;
$query = my_implode('n','OR',explode(' ',str_replace('_','',$categories)));
$q = dq("select tmpl_det from $s[pr]cats where visible = '1' AND $query limit 1",0);
$y = mysql_fetch_row($q);
if (!$y[0]) if ($what=='ad') $y[0] = 'ad_details.html';
return $y[0];
}

##################################################################################
##################################################################################
##################################################################################

function categories_first_level($c) {
global $s,$m;
$q = dq("select * from $s[pr]cats where level = 1 order by title",1);
while ($b=mysql_fetch_assoc($q)) $a .= "<input type=\"radio\" name=\"c1\" value=\"$b[n]\">$b[title]<br>";
return $a;
}

########################################################################################

function categories_tree($bigboss) {
global $s,$m;
if ($bigboss) $where .= " AND (bigboss = '$bigboss' or n = '$bigboss')";
$q = dq("select * from $s[pr]cats where alias_of = 0 $where order by level desc,path_text",1);
while ($b=mysql_fetch_assoc($q))
{ set_time_limit(30);
  if (time()>($time1+10)) { $time1=time(); echo str_repeat (' ',4000); flush(); }
  if ($b[submit_here]) $checkbox = "&nbsp;<input type=\"checkbox\" name=\"c[]\" value=\"$b[n]\" #%checked_$b[n]%#>"; else $checkbox = '';
  $categories_array[$b[level]][$b[parent]] .= "<li><a href=\"#categories_tree_top\" id=\"node_$b[n]\">$b[title]</a>$checkbox#%sub_$b[n]%#</li>\n";
  if (!$max_level) $max_level = $b[level];
}
foreach ($categories_array as $level=>$level_array)
{ if ($level==$max_level) continue;
  foreach ($level_array as $parent=>$categories_list)
  { foreach ($categories_array[($level+1)] as $k=>$v) if (strstr($categories_list,"#%sub_$k%#")) $categories_list = str_replace("#%sub_$k%#","\n\t<ul#%expand_$k%#>$v</ul>\n",$categories_list);
    $categories_array[$level][$parent] = $categories_list;
  }
}
return $categories_array[1][0];
}


#################################################################################
#################################################################################
#################################################################################

function images_form_users($what,$in) {
global $s,$m;
if ($what=='u') { $max = $s[u_max_pictures_users]; }
else
{ $ad_vars = get_ad_variables($in[n],0);
  $max = $s[a_max_pictures_users] + $ad_vars[x_pictures_max];
  if ($in[n]) $ad_n = $in[n]; else $ad_n = 0;
}
list($images) = get_item_files($what,$in[n],$queue);
for ($y=1;$y<=$max;$y++)
{ $x[item_name] = "$m[upload_picture]$y"; 
  $x[field_name] = 'image_upload['.$ad_n.']['.$y.']';
  $x[description_name] = 'image_description['.$ad_n.']['.$y.']';
  if ($in[image_description][$ad_n][$y]) $x[description_value] = $in[image_description][$ad_n][$y];
  else $x[description_value] = $images[$in[n]][$y][description];
  $a .= parse_part('form_upload.txt',$x);
  if (($in[n]) AND ($images[$in[n]][$y][url]))
  { $x[current_picture] = image_preview_code($images[$in[n]][$y][n],$images[$in[n]][$y][url],preg_replace("/\/$in[n]-/","/$in[n]-big-",$images[$in[n]][$y][url]));
    $x[image_n] = $y;
    $a .= parse_part('form_picture_current.txt',$x);
  }
}
return $a;
}

##################################################################################
##################################################################################
##################################################################################

function get_complete_ads($ads,$numbers,$template) {
global $s,$m;
$width = floor(100/$s[l_columns]);
if ($s[this_cat_n]) $usit = usit_display($s[this_cat_n],$numbers,'user_item_listing.txt',0,1,0,1);
else { /* search result - musi u kazdeho adu vzit usit */ }
//foreach ($usit as $k=>$v) echo "$k - $v<br>";
//list($images,$files,$videos) = get_item_files('a',$numbers,0);
$statistic = get_ads_statistic($numbers);
count_impressions($numbers);
if ($s[GC_u_n])
{ $notes = get_private_notes_for_items('ad',$numbers);
  $bookmarks = get_favorites_status('ad',$numbers);
}
foreach ($ads as $k => $a)
{ if (!$usit) { $a[hide_usit_begin] = '<!--'; $a[hide_usit_end] = '-->'; }
  $a[user_defined] = $usit[$a[n]]; 
  foreach ($usit['individual_'.$a[n]] as $k1=>$v1) $a[$k1] = $v1;
  if ($a[picture])
  { $a[picture_big] = preg_replace("/\/$a[n]-/","/$a[n]-big-",$a[picture]);
    if (!file_exists(str_replace("$s[site_url]/","$s[phppath]/",$a[picture_big]))) $a[picture_big] = $a[picture];
  }
  else $a[picture] = $a[picture_big] = "$s[site_url]/images/no_picture.png";
  if (!$a[url]) { $a[hide_url_begin] = '<!--'; $a[hide_url_end] = '-->'; }
  $a[icons] = get_icons_for_item($a,$bookmarks[$a[n]]); if (!$a[icons]) { $a[hide_icons_begin] = '<!--'; $a[hide_icons_end] = '-->'; }
  if ($a[offer_wanted]) $a[ad_type] = $m[$a[offer_wanted]]; else { $a[hide_ad_type_begin] = '<!--'; $a[hide_ad_type_end] = '-->'; }
  if ($a[price]>0.01)
  { $a[price] = "$s[currency]$a[price]"; 
    //$a[price] = "$a[price]"; 
    //$a[price] = str_replace('USD','$',$a[price]);
    //$a[price] = str_replace('BOB','Bs',$a[price]);
  }
  else { $a[hide_price_begin] = '<!--'; $a[hide_price_end] = '-->'; }
  if (($a[x_paypal_by]>$s[cas]) AND ($a[x_paypal_email]) AND ($a[x_paypal_currency]) AND ($a[x_paypal_price])) $a[paypal_button] = parse_part('ad_paypal_button.txt',$a); else { $a[hide_paypal_button_begin] = '<!--'; $a[hide_paypal_button_end] = '-->'; }
  if (($a[x_highlight_by]>$s[cas]) AND ($a[x_bold_by]>$s[cas])) $a[table_style] = 'table_item_bold_highlight';
  elseif ($a[x_highlight_by]>$s[cas]) $a[table_style] = 'table_item_highlight';
  elseif ($a[x_bold_by]>$s[cas]) $a[table_style] = 'table_item_bold';
  else $a[table_style] = 'table_item';
  if ($statistic[$a[n]][i_detail]) $a[clicks] = $statistic[$a[n]][i_detail]; else $a[clicks] = 0;
  $a[item_details_url] = get_detail_page_url('ad',$a[n],$a[rewrite_url],$a[category]);
  if ($a[t1]>$a[created]) { $a[date_created] = datum($a[t1],0); $a[created] = datum($a[t1],1); }
  else { $a[date_created] = datum($a[created],0); $a[created] = datum($a[created],1); }
  if ($a[updated]) $a[updated] = datum($a[updated],1); else { $a[hide_updated_begin] = '<!--'; $a[hide_updated_end] = '-->'; }
  $a[detail] = strip_tags(str_replace('&#039;',"'",$a[detail]),'<img><br><p><a>');
  if (trim($a[title])) $a[tags] = tags_for_item('ad',0,$a[keywords],$a[title]); else { $a[hide_tags_begin] = '<!--'; $a[hide_tags_end] = '-->'; }
  if (($s[search_highlight]) AND ($s[highlight])) { $a[title] = highlight_words('',$a[title]); $a[description] = highlight_words('',$a[description]); $a[detail] = highlight_words('',strip_tags($a[detail])); }
  $a[report_box] = report_box($a[n],0,0);
  $a[tell_friend_box] = tell_friend_box($a[n],0,1);
  $a[enter_comment_box] = enter_comment_box($a[n]);
  
  $x = list_of_categories_for_item('ad',0,$a[c],'&nbsp; &nbsp;',0); $a[categories] = $x[categories]; $a[categories_incl] = $x[categories_incl]; $a[categories_names] = $x[categories_names];
  $x = list_of_areas_for_item($a[a],'&nbsp; &nbsp;'); $a[areas] = $x[areas]; $a[areas_incl] = $x[areas_incl]; $a[areas_names] = $x[areas_names];
  //$a[keywords_search] = keywords_search_for_item($a[keywords],'&nbsp; &nbsp;');
  if ($s[GC_u_n])
  { $a[add_delete_favorites] = get_favorite_line('ad',$a[n],$bookmarks[$a[n]]);
    if ($notes[$a[n]]) { $a[notes] = $notes[$a[n]]; $a[notes_style_display] = 'block'; } else $a[notes_style_display] = 'none';
    $s[current_notes] = $a[notes]; $a[notes_edit_box] = notes_edit_box('ad',$a[n],'');
  }

  if ($a[pub_phone1]) $phones[] = $a[pub_phone1]; if ($a[pub_phone2]) $phones[] = $a[pub_phone2]; $a[phones] = implode(', ',$phones); if (!$a[phones]) { $a[hide_phones_begin] = '<!--'; $a[hide_phones_end] = '-->'; }
  $complete_array[] = parse_part($template,$a);
  //$complete_array[] = '<td valign="top" width="'.$width.'%">'.parse_part($template,$a).'</td>';
  $pocet++;
}
return implode("\n\n\n",$complete_array);
$rows = ceil($pocet/$s[l_columns]);
for ($x=$pocet+1;$x<=($rows*$s[l_columns]);$x++)
{ $complete_array[] = '<td width="'.$width.'%">&nbsp;</td>';
  $pocet++;
}
for ($x=1;$x<=$rows;$x++)
{ $complete .= '<tr>';
  for ($y=($x-1)*$s[l_columns];$y<=$x*$s[l_columns]-1;$y++)
  $complete .= $complete_array[$y];
  $complete .= '</tr>';
}
return $complete;
}

##################################################################################

function get_complete_ads_simple($ads,$numbers,$template) {
global $s,$m;
if ($s[GC_style]!='Mobile') $s[l_columns] = 4;
$width = floor(100/$s[l_columns]);
//list($images,$files,$videos) = get_item_files('a',$numbers,0);
count_impressions($numbers);
foreach ($ads as $k => $a)
{ if (!$a[picture]) $a[picture] = "$s[site_url]/images/no_picture.png";
  if ($a[offer_wanted]) $a[ad_type] = $m[$a[offer_wanted]]; else { $a[hide_ad_type_begin] = '<!--'; $a[hide_ad_type_end] = '-->'; }
  if ($a[price]>0.01) $a[price] = "$s[currency]$a[price]"; else { $a[hide_price_begin] = '<!--'; $a[hide_price_end] = '-->'; }
  $a[item_details_url] = get_detail_page_url('ad',$a[n],$a[rewrite_url],$a[category]);
  $a[created] = datum($a[t1],1);
  $complete_array[] = parse_part($template,$a);
  $pocet++;
}
$rows = ceil($pocet/$s[l_columns]);
for ($x=$pocet+1;$x<=($rows*$s[l_columns]);$x++)
{ $complete_array[] = '<td width="'.$width.'%">&nbsp;</td>';
  $pocet++;
}
for ($x=1;$x<=$rows;$x++)
{ $complete .= '<tr>';
  for ($y=($x-1)*$s[l_columns];$y<=$x*$s[l_columns]-1;$y++)
  $complete .= $complete_array[$y];
  $complete .= '</tr>';
}
return $complete;
}

##################################################################################

function tags_for_item($what,$c,$keywords,$title) {
global $s;
if (is_array($c)) $x = $c; else $x = explode(' ',str_replace('_','',$c));
//if ($x[0]) $categories = get_category_data($what,$x,0);
foreach ($categories as $k=>$v) { $name = trim($v[name]); if (!$name) continue; $tags_array[] = $name; }
$title = str_replace(',',' ',$title); $title_array = explode(" ",$title);
foreach ($title_array as $k=>$v) { $v = trim($v); if (!$v) continue; $tags_array[] = $v; }
$keywords_array = explode(",",$keywords);
foreach ($keywords_array as $k=>$v) { $v = trim($v); if (!$v) continue; $tags_array[] = $v; }
foreach ($tags_array as $k=>$v) $tags_a[] = '<img border="0" src="'.$s[site_url].'/images/icon_tag.gif">&nbsp;<a href="'.$s[site_url].'/search.php?phrase='.urlencode($v).'">'.str_replace(' ','&nbsp;',$v).'</a>';
return '&nbsp;'.implode(' &nbsp; ',$tags_a);
}

##################################################################################

function get_ads_statistic($numbers) {
global $s;
if (is_array($numbers)) $query = my_implode('n','or',$numbers);
else $query = "n = '$numbers'";
$q = dq("select * from $s[pr]ads_stat where $query",1);
while ($x = mysql_fetch_assoc($q)) $a[$x[n]] = $x;
return $a;

}
function count_impressions($numbers) {
global $s;
if (is_array($numbers)) $query = my_implode('n','or',$numbers);
else $query = "n = '$numbers'";
dq("UPDATE $s[pr]ads_stat set i = i + 1, reset_i = reset_i + 1 WHERE $query",1);
}

##################################################################################

function highlight_words($word,$in) {
global $s;
if ($word) $highlight[] = trim($word); else $highlight = $s[highlight];
foreach ($highlight as $k=>$v) { $highlight[] = ucfirst($v); $highligh[] = strtolower($v); }
foreach ($highlight as $k=>$v) $in = str_replace($v,'<span class="text_highlight">'.$v.'</span>',$in);
return $in;
}

##################################################################################
##################################################################################
##################################################################################

function unreplace_once_html($x) {
// na html po vytazeni z databaze
if (!$x) return $x;
$x = ereg_replace("''","'",str_replace(chr(92),'',$x));
return ereg_replace('&#039;',"'",ereg_replace("--BACKSLASH--",'\\\\',ereg_replace('&#92;','\\',$x)));
}

##################################################################################
##################################################################################
##################################################################################

function get_messages($script) {
global $s,$m;
if ($s[GC_style]) $st = $_SESSION[GC_style]; else $st = $s[def_style];
if (file_exists($s[phppath].'/styles/'.$st.'/messages/'.$script))
$x = $st; else $x = '_common';
include($s[phppath].'/styles/'.$x.'/messages/'.$script);
//$vl[site_url] = $s[site_url]; $vl[site_name] = $s[site_name];
//foreach ($vl as $k=>$v) foreach ($m as $k1=>$v1) $m[$k1] = str_replace('#_'.$k.'_#',$v,$v1);
}

##################################################################################

function page_from_template($t,$vl) {
global $s,$m;
//$t1 = getmicrotime();
//<FILE>http://localhost/ZKUSEBNI/gold_classifieds/01.php?kk=khjgsdj</FILE>

if (!is_array($vl)) $vl = array();

if ((!$s[GC_style]) OR (!is_dir("$s[phppath]/styles/$s[GC_style]"))) $s[GC_style] = $s[def_style];
$vl[charset] = $s[charset]; $vl[site_url] = $s[site_url]; $vl[site_name] = $s[site_name]; $vl[currency] = $s[currency]; $vl[google_search_id] = $s[google_search_id];
$vl[logo_url] = $s[logo_url]; $vl[banner_code] = $s[banner_code]; $vl[css_style] = $s[GC_style];

$vl[online] = who_is_online(); if ($s[selected_menu]) $vl[selected_menu] = $s[selected_menu]; else $vl[selected_menu] = 0;
include("$s[phppath]/data/stats.php");
$vl[t_cats] = $s[t_cats]; $vl[t_areas] = $s[t_areas]; $vl[active_ads] = $s[active_ads]; 
$vl[menu] = str_replace('#%hide_div_no_user%#','style="display:none;"',implode('',file("$s[lug_phppath]/data/menu.txt")));

$vl[tell_friend_site_box] = tell_friend_box(0);
$vl[contact_site_box] = contact_box(0);

$vl[lug_phppath] = $s[lug_phppath];
$vl[lug_site_url] = $s[lug_site_url];
if (!$vl[meta_title]) $vl[meta_title] = $s[site_name];
if ($vl[meta_description]) $vl[meta_description] = substr(str_replace("\r",'',str_replace("\n",' ',str_replace('&#039;',"'",strip_tags($vl[meta_description])))),0,200); else $vl[meta_description] = $s[site_description];
if ($vl[meta_keywords]) $vl[meta_keywords] = substr(str_replace("\r",'',str_replace("\n",' ',str_replace('&#039;',"'",strip_tags($vl[meta_keywords])))),0,200); else $vl[meta_keywords] = $s[site_keywords];
$vl[first_areas_select] = select_list_first_areas('_common',1); 

$vl[user_n] = $s[GC_u_n];

if (($s[show_qr]) AND ($qrimage=show_qrcode($vl[this_url]))) $vl[qrimage] = '<img border="0" src="'.$qrimage.'">';
//echo $vl[qrimage];

/*if ($st=='Greek')
{ $phrase  = "You should eat fruits, vegetables, and fiber every day.";
  $array1 = array ('Search for Classifieds','Search for','Search type','Exact phrase','Wanted<','Offer<','Offer & Wanted','In category','Area','Price <','Per page','Order by','"Search"');
  $array2 = array ('Search for Classifieds','Search for','Search type','Exact phrase','Wanted<','Offer<','Offer & Wanted','In category','Area','Price <','Per page','Order by','"Search"');
  $a[search_form] = str_replace($healthy,$yummy,$a[search_form]);
}*/

/*
dq("update $s[pr]visits_today set old = 1 where time < ($s[cas]-($s[visits_today_unique]*60))",1);
$q = dq("select count(*) from $s[pr]visits_today where ip = '$s[ip]' and old = 0",1); $pocet = mysql_fetch_row($q);
if (!$pocet[0]) dq("insert into $s[pr]visits_today values ('$s[ip]',$s[cas],0)",1);
$q = dq("select count(*) from $s[pr]visits_today",1); $pocet = mysql_fetch_row($q); $vl[todays_visits] = $pocet[0];
*/
if (!$vl[share_it]) $vl[share_it] = parse_part('share_it.txt',$a);
if (!$vl[bookmark_title]) $vl[bookmark_title] = $s[site_name];

$vl[categories] = get_categories_list();
if ($vl[categories]) 
{ if ($s[this_cat_n]) $vl[ALL_CATEGORIES] = $m[ALL_CATEGORIES]; else $vl[ALL_CATEGORIES] = "<b>$m[ALL_CATEGORIES]</b>";
  if ($s[this_area]) $vl[url_all_categories] = add_this_area(category_url('ad',0));
  else $vl[url_all_categories] = "$s[site_url]/";
}
else { $vl[hide_categories_begin] = '<!--';  $vl[hide_categories_end] = '-->'; }

$vl[areas] = get_areas_list();
if ($vl[areas]) 
{ if ($s[this_area]) $vl[ALL_AREAS] = $m[ALL_AREAS]; else $vl[ALL_AREAS] = "<b>$m[ALL_AREAS]</b>";
  if (!$s[this_cat_n]) $vl[url_all_areas] = "$s[site_url]/";
  else
  { $category_vars = get_category_variables($s[this_cat_n]); $s[this_cat_rewrite] = $category_vars[rewrite_url];
	$vl[url_all_areas] = category_url('ad',$category_vars[n],$category_vars[alias_of],1,$category_vars[rewrite_url]);
  }
}
else { $vl[hide_areas_begin] = '<!--';  $vl[hide_areas_end] = '-->'; }

list($vl[area_title],$vl[area_arrow]) = get_arrow_areas();
if ((!$vl[area_title]) AND (!$vl[area_arrow]))
{ $vl[hide_area_info_begin] = '<!--';  $vl[hide_area_info_end] = '-->';
  if (!$vl[hide_areas_begin]) { $vl[hide_all_areas_begin] = '<!--';  $vl[hide_all_areas_end] = '-->'; }
}

if ($vl[area_title]) $vl[area_title_left] = $vl[area_title]; else $vl[area_title_left] = $m[areas];
if ($vl[category_title]) $vl[category_title_left] = $vl[category_title]; else $vl[category_title_left] = $m[categories];
if (!$s[this_offer_wanted]) $s[this_offer_wanted] = 'all';
include("$s[phppath]/data/info.php");
if ($s[show_left_offer_wanted])
{ unset($vl[offer_wanted]);
  if ($s[this_offer_wanted]=='offer') { $s[ads_type] = $m[offer]; $vl[offer_wanted] .= "<b>$m[offer]</b> "; } else { $vl[offer_wanted] .= '<a rel="nofollow" class="link10" href="'.str_replace('extra_commands','offer',str_replace($s[site_url].'/extra_commands',$s[site_url].'/index_offer',$s[offer_wanted_base])).'">'.$m[offer].'</a> '; }
  if ($s[this_offer_wanted]=='wanted') { $s[ads_type] = $m[wanted]; $vl[offer_wanted] .= "<b>$m[wanted]</b> "; } else { $vl[offer_wanted] .= '<a rel="nofollow" class="link10" href="'.str_replace('extra_commands','wanted',str_replace($s[site_url].'/extra_commands',$s[site_url].'/index_wanted',$s[offer_wanted_base])).'">'.$m[wanted].'</a> '; }
  if ($s[this_offer_wanted]=='all') { $s[ads_type] = $m[offer_wanted]; $vl[offer_wanted] .= "<b>$m[offer_wanted]</b> "; } else { $vl[offer_wanted] .= '<a rel="nofollow" class="link10" href="'.str_replace('extra_commands','all',str_replace($s[site_url].'/extra_commands',$s[site_url].'/index_all',$s[offer_wanted_base])).'">'.$m[offer_wanted].'</a><br>'; }
}
else { $vl[hide_offer_wanted_begin] = '<!--'; $vl[hide_offer_wanted_end] = '-->'; }

if (!$s[area_title]) $s[area_title] = $m[all_areas];
$vl[category_info] = "$s[category_title] - $s[area_title]";
if ($s[show_left_offer_wanted]) $vl[category_info] .= " - $s[ads_type]";
if ($s[show_rss_category])
{ $vl[rss_category_info] = "$s[category_title]<br>$s[area_title]<br>$s[ads_type]<br>";
  $vl[rss_category_url] = "$s[site_url]/rss.php?action=category&category=$s[this_cat_n]&area=$s[this_area]";
}
else { $vl[hide_rss_category_begin] = '<!--';  $vl[hide_rss_category_end] = '-->'; }
if (!$s[user_login_captcha]) { $vl[hide_user_login_captcha_begin] = '<!--';  $vl[hide_user_login_captcha_end] = '-->'; }

$q = dq("select * from $s[pr]static where (style = '$s[GC_style]' or style = '0') and page = '0'",1);
while ($x = mysql_fetch_assoc($q)) $vl[$x[mark]] = $x[html];
include("$s[phppath]/data/info.php");

if ($s[GC_u_email])
{ $vl[hide_for_user_begin] = '<!--'; $vl[hide_for_user_end] = '-->';
  $vl[GC_u_email] = $s[GC_u_email];
  $vl[hide_user_login_captcha_begin] = $vl[hide_user_login_captcha_end] = '';
  $check_field = check_field_create("$s[GC_u_email]$s[GC_u_password]$s[GC_u_n]");

}
else {   $vl[user_login_form_lug] = parse_part('user_login_form_lug.txt',$x);
$vl[hide_for_no_user_begin] = '<!--'; $vl[hide_for_no_user_end] = '-->'; $vl[hide_for_no_user_begin1] = '<!--'; $vl[hide_for_no_user_end1] = '-->'; }

$vl[styles_options] = get_styles_options($s[styles],$s[GC_style]);
$styles = explode(',',$s[styles]);
foreach ($styles as $k=>$v)
{ $style_n++;
  if ($v==$s[GC_style]) $style_image = 'style_current.png'; else $style_image = "style_$v.png";
  if ($v==$s[GC_style]) $vl[menu_styles] .= "<a href=\"$s[site_url]/styles.php?style=$v\" style=\"TEXT-TRANSFORM: uppercase;\"> :".str_replace('_',' ',$v).": </a>";
  else $vl[menu_styles] .= "<a href=\"$s[site_url]/styles.php?style=$v\"> ".str_replace('_',' ',$v)." </a>";
}
//foreach ($vl as $k=>$v) echo "$k - $v<br>";
for ($x=1;$x<=$s[in_templates];$x++)
{ $vl["in$x"] = str_replace('&#039;',"'",parse_variables($vl["in$x"],$vl));
  if ($_GET[bigboss]) $vl["in$x"] = str_replace('value="'.$_GET[bigboss].'"','value="'.$_GET[bigboss].'" selected',$vl["in$x"]); // searched
  if ($_GET[search_kind]) $vl["in$x"] = str_replace('value="'.$_GET[search_kind].'"','value="'.$_GET[search_kind].'" selected',$vl["in$x"]); // searched
  //if ($_GET[search_kind]) $vl["in$x"] = str_replace('value="'.$_GET[search_kind].'"','value="'.$_GET[search_kind].'" selected',$vl["in$x"]); // searched
}
if ($vl[original_phrase]) $words = explode(' ',$vl[original_phrase]);
elseif ($vl[current_title]) $words = explode(' ',$vl[current_title]);
elseif ($vl[title]) $words = explode(' ',$vl[title]);
if ((!$words) OR (!$words[0])) $words = explode(' ',$s[site_name]);
foreach ($words as $k=>$v) $words1[] = '"'.$v.'"';
$vl[current_words] = implode(',',$words1);

$vl[head] = parse_variables_in_template(template_select('_head2.txt',0,$style),$vl);
$line = parse_variables_in_template(template_select('_head1.txt',0,$style),$vl);
$line .= parse_variables_in_template(template_select($t,0,$style),$vl);
$line .= str_replace('</body',$info.'</body',parse_variables_in_template(template_select('_footer.txt',0,$style),$vl));
$line = str_replace('</form>',$check_field.'</form>',$line);

$line = ereg_replace('--BACKSLASH--','\\',$line);
$line = str_replace('-extra_commands','',$line);
$line = str_replace('-page_n','',$line);
$line = str_replace('area_rewrite/','',$line);
$line = str_replace('-area_n','-0',$line);
if ($s[A_option]=='rewrite') $line = str_replace("$s[site_url]/index.php","$s[site_url]/index.html",$line);
else
{ $line = str_replace("$s[site_url]/index_offer.html","$s[site_url]/index.php?vars=offer",$line);
  $line = str_replace("$s[site_url]/index_wanted.html","$s[site_url]/index.php?vars=wanted",$line);
  $line = str_replace("$s[site_url]/index_all.html","$s[site_url]/index.php?vars=all",$line);
  $line = str_replace("$s[site_url]/$s[ARfold_l_detail]-","$s[site_url]/classified.php?vars=",$line);
  $line = str_replace("$s[site_url]/$s[ARfold_l_cat]-","$s[site_url]/index.php?vars=/$s[ARfold_l_cat]-",$line);
  $line = str_replace("$s[site_url]/extra_category/","$s[site_url]/category.php?action=",$line);
}

echo str_replace('&#039;',"'",$line);
exit;
}

##################################################################################

function page_from_template_no_headers($t,$vl) {
global $s,$m;
if (!is_array($vl)) $vl = array();
$vl[charset] = $s[charset]; $vl[site_url] = $s[site_url]; $vl[currency] = $s[currency];
$vl[selected_menu] = 0;
$vl[meta_title] = $s[site_name];

$x = $s[phppath].'/styles/'.$s[GC_style].'/templates/';
if (file_exists($x.$t)) $template = $x.$t;
if (file_exists($x.'_head1.txt')) $head1 = $x.'_head1.txt';

$x = $s[phppath].'/styles/_common/templates/';
if (!$template) $template = $x.$t;
if (!$head1) $head1 = $x.'_head1.txt';
$head1 = parse_variables_in_template($head1,$vl);

$line = implode('',file($template));
foreach ($vl as $k=>$v) $line = str_replace("#%$k%#",$v,$line);
$line = eregi_replace("#%[a-z0-9_]*%#",'',stripslashes($line));
$line = ereg_replace('--BACKSLASH--','\\',$line);

echo eregi_replace('</head>','<LINK href="'.$s[site_url].'/styles/'.$s[GC_style].'/styles.css" rel="StyleSheet"></head>',$line);
exit;
}

##################################################################################

function get_categories_list() {
global $s;

if (!$s[this_area]) $s[this_area] = 0;
$this_url = preg_replace("/page-[0-9]+\//",'',$s[this_url]);
if ($s[this_cat_n]) $where = "(parent = '$s[this_cat_n]' or n = '$s[this_cat_n]')"; 
else $where = "level = '1'";
$q = dq("select * from $s[pr]cats where $where and visible = 1 order by level,rank,title",1);
while ($x=mysql_fetch_assoc($q)) { $a[$x[n]] = $x; $numbers[] = $x[n]; }
$this_category = $a[$s[this_cat_n]];
$categories = get_category_tree($this_category,$this_url);

if (($s[this_cat_n]) and (count($numbers)==1)) // last level
{ unset($a);
  $q = dq("select * from $s[pr]cats where parent = '$this_category[parent]' order by rank,title",1);
  while ($x=mysql_fetch_assoc($q)) { $a[] = $x; $numbers[] = $x[n]; }
  if ($s[show_left_items]) $items = get_item_numbers_cats($s[this_area],$numbers,$s[this_offer_wanted]);
  foreach ($a as $k=>$category)
  { if ($s[show_left_items]) $left_items = ' ('.$items[$category[n]][items].')';
    $pomlcek = $category[level] - 1; $pomlcek = str_repeat ('-',$pomlcek);
    $folder_icon = folder_icon(0,$category[image2]);
    if ($category[alias_of]) $category[title] = $s[alias_pref].$category[title].$s[alias_after];
    if ($category[n]==$s[this_cat_n]) $categories .= '<p><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a href="'.add_this_area(category_url('ad',$category[n],$category[alias_of],1,$category[rewrite_url])).'"><b>'.$category[title].$left_items.'</b></a></p>';
    else $categories .= '<p><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a href="'.add_this_area(category_url('ad',$category[n],$category[alias_of],1,$category[rewrite_url])).'">'.$category[title].$left_items.'</a></p>';
  }
}
else
{ if ($s[show_left_items]) $items = get_item_numbers_cats($s[this_area],$numbers,$s[this_offer_wanted]);
  foreach ($a as $k=>$category)
  { if ($s[show_left_items]) $left_items = ' ('.$items[$category[n]][items].')';
    $folder_icon = folder_icon(0,$category[image2]);
    if ($category[alias_of]) $category[title] = $s[alias_pref].$category[title].$s[alias_after];
    if ($s[this_cat_n])
    { $pomlcek = $category[level] - 1; $pomlcek = str_repeat ('-',$pomlcek);
      if ($category[n]==$s[this_cat_n]) $categories .= '<p><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a href="'.add_this_area(category_url('ad',$category[n],$category[alias_of],1,$category[rewrite_url])).'"><b>'.$category[title].$left_items.'</b></a></p>';
      else $categories .= '<p><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a href="'.add_this_area(category_url('ad',$category[n],$category[alias_of],1,$category[rewrite_url])).'">'.$category[title].$left_items.'</a></p>';
    }
    else // first level
    $categories .= '<p><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a href="'.add_this_area(category_url('ad',$category[n],$category[alias_of],1,$category[rewrite_url])).'">'.$category[title].$left_items.'</a></p>';
  }
}

return $categories;
}

##################################################################################

function get_category_tree($category_array,$this_url) {
global $s;
$path_n = explode('_',$category_array[path_n]); foreach ($path_n as $k=>$v) if ((!$v) OR ($v==$s[this_cat_n])) unset($path_n[$k]);
$query = my_implode('n','or',$path_n);
$q = dq("select * from $s[pr]cats where $query order by level",1);
while ($x=mysql_fetch_assoc($q)) { $a[] = $x; $numbers[] = $x[n]; }
if ($s[show_left_items]) $items = get_item_numbers_cats($s[this_area],$numbers,$s[this_offer_wanted]);
foreach ($a as $k=>$category)
{ if ($s[show_left_items]) $left_items = ' ('.$items[$category[n]][items].')';
  $pomlcek = $category[level] - 1; $pomlcek = str_repeat ('-',$pomlcek);
  $folder_icon = folder_icon(0,$category[image2]);
  if ($category[alias_of]) $category[title] = $s[alias_pref].$category[title].$s[alias_after];
  if ($category[n]==$s[this_category]) $categories .= '<p><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a class="link10" href="'.add_this_area(category_url('ad',$category[n],$category[alias_of],1,$category[rewrite_url])).'"><b>'.$category[title].$left_items.'</b></a></p>';
  else $categories .= '<p><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a class="link10" href="'.add_this_area(category_url('ad',$category[n],$category[alias_of],1,$category[rewrite_url])).'">'.$category[title].$left_items.'</a></p>';
}
return $categories;
}

##################################################################################

function get_areas_list() {
global $s;
if ($s[this_cat_n]) { $category = get_category_variables($s[this_cat_n]); $url = category_url('ad',$category[n],$category[alias_of],1,$category[rewrite_url]); /*$s[this_cat_rewrite] = $category_vars[rewrite_url];*/ }
else $url = "$s[site_url]/$s[ARfold_l_cat]-0-area_n/area_rewrite";

if ($s[this_area]) $where = "(parent = '$s[this_area]' or n = '$s[this_area]')"; 
else $where = "level = '1'";
$q = dq("select * from $s[pr]areas where $where order by level,rank,title",1);
while ($x=mysql_fetch_assoc($q)) { $a[$x[n]] = $x; $numbers[] = $x[n]; }
$this_area = $a[$s[this_area]]; $s[area_title] = $this_area[title];
$areas = get_area_tree($this_area,$url);

if (($s[this_area]) and (count($numbers)==1)) // last level
{ unset($a);
  $q = dq("select * from $s[pr]areas where parent = '$this_area[parent]' order by rank,title",1);
  while ($x=mysql_fetch_assoc($q)) { $a[] = $x; $numbers[] = $x[n]; }
  if ($s[show_left_items]) $items = get_item_numbers_areas($numbers,$s[this_cat_n],$s[this_offer_wanted]);
  foreach ($a as $k=>$area)
  { if ($s[show_left_items]) $left_items = ' ('.$items[$area[n]][items].')';
    $pomlcek = $area[level] - 1; $pomlcek = str_repeat ('-',$pomlcek);
    $folder_icon = folder_icon(0,$area[image2]);
    if ($area[alias_of]) $area[title] = $s[alias_pref].$area[title].$s[alias_after];
    if (strstr($area[rewrite_url],'http://')) $this_url = $area[rewrite_url]; else $this_url = str_replace('area_rewrite',"$area[rewrite_url]",str_replace('-area_n',"-$area[n]",$url));
    if ($area[n]==$s[this_area]) $areas .= '<p><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a href="'.$this_url.'"><b>'.$area[title].$left_items.'</b></a></p>';
    else $areas .= '<p><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a href="'.$this_url.'">'.$area[title].$left_items.'</a></p>';
  }
}
else
{ if ($s[show_left_items]) $items = get_item_numbers_areas($numbers,$s[this_cat_n],$s[this_offer_wanted]);
  foreach ($a as $k=>$area)
  { if ($s[show_left_items]) $left_items = ' ('.$items[$area[n]][items].')';
    $folder_icon = folder_icon(0,$area[image2]);
    if ($area[alias_of]) $area[title] = $s[alias_pref].$area[title].$s[alias_after];
    if (strstr($area[rewrite_url],'http://')) $this_url = $area[rewrite_url]; else $this_url = str_replace('area_rewrite',"$area[rewrite_url]",str_replace('-area_n',"-$area[n]",$url));
    if ($s[this_area])
    { $pomlcek = $area[level] - 1; $pomlcek = str_repeat ('-',$pomlcek);
      if ($area[n]==$s[this_area]) $areas .= '<p><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a href="'.$this_url.'"><b>'.$area[title].$left_items.'</b></a></p>';
      else $areas .= '<p><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a href="'.$this_url.'">'.$area[title].$left_items.'</a></p>';
    }
    else //echo $url;// first level
    $areas .= '<p><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a href="'.$this_url.'">'.$area[title].$left_items.'</a></p>';
  }
}
return $areas;
}

##################################################################################

function get_area_tree($area_array,$this_url) {
global $s;

$path_n = explode('_',$area_array[path_n]); foreach ($path_n as $k=>$v) if ((!$v) OR ($v==$s[this_area])) unset($path_n[$k]);
$query = my_implode('n','or',$path_n);
$q = dq("select * from $s[pr]areas where $query order by level",1);
while ($x=mysql_fetch_assoc($q)) { $a[] = $x; $numbers[] = $x[n]; }

if ($s[show_left_items]) $items = get_item_numbers_areas($numbers,$s[this_cat_n],$s[this_offer_wanted]);

foreach ($a as $k=>$area)
{ if ($s[show_left_items]) $left_items = ' ('.$items[$area[n]][items].')';
  $pomlcek = $area[level] - 1; $pomlcek = str_repeat ('-',$pomlcek);
  $folder_icon = folder_icon(0,$area[image2]);
  if ($area[alias_of]) $area[title] = $s[alias_pref].$area[title].$s[alias_after];
  if (strstr($area[rewrite_url],'http://')) $this_url = $area[rewrite_url]; else $this_url = str_replace('area_rewrite',"$area[rewrite_url]",str_replace('-area_n',"-$area[n]",$this_url));
  if ($area[n]==$s[this_area]) $areas .= '<tr><td align="left" class="cell_embosed1"><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a class="link10" href="'.$this_url.'"><b> '.$area[title].$left_items.'</b></a></p>';
  else $areas .= '<tr><td align="left" class="cell_embosed1"><img alt="" src="'.$folder_icon.'">&nbsp;'.$pomlcek.'<a class="link10" href="'.$this_url.'">'.$area[title].' ('.$area[items].')</a></p>';
}
return $areas;
}

##################################################################################

function get_arrow_areas() {
global $s,$m;
if (!$s[this_area]) return false;
$url = category_url('ad',$s[this_cat_n],0,1,$s[this_cat_rewrite]);
$x = get_area_variables($s[this_area]);
$x = explode(' ',trim(str_replace('_',' ',$x[path_n])));
foreach ($x as $k=>$v)
{ $area_vars = get_area_variables($v);
  if ($area_vars[n]!=$s[this_area]) $areas .= '<a href="'.str_replace('area_n',$area_vars[n],str_replace('area_rewrite',$area_vars[rewrite_url],$url)).'">'.$area_vars[title].'</a> >> ';
  else $area_title = $area_vars[title];
}
if ($s[this_cat]) $home_url = category_url('ad',$s[this_cat_n],0,1,$s[this_cat_rewrite]);
else $home_url = "$s[site_url]/";
$areas = '<a href="'.$home_url.'">'.$m[all_areas].'</a> >> '.$areas;
return array($area_title,$areas);
}

##################################################################################

function get_styles_options($all,$selected) {
global $s;
$styles = explode(',',$all);
foreach ($styles as $k=>$v)
{ if ($v==$selected) $x = ' selected'; else $x = '';
  $a .= '<option value="'.$v.'"'.$x.'>'.str_replace('_',' ',$v).'</option>';
}
return $a;
}

##################################################################################

function parsejava($template,$vl) {
global $s,$m;
$vl[site_url] = $s[site_url]; $vl[currency] = $s[currency];
$template = template_select($template);
$fh = fopen($template,'r') or die("Unable to read template $t");
while (!feof($fh))
{ $line = trim(fgets($fh,4096));
  $line = unreplace_once_html($line);
  $line = ereg_replace('"','\"',$line);
  $lines .= "document.write(\"$line\");\n";
}
fclose ($fh);
while (list($key,$val) = each($vl)) $lines = str_replace("#%$key%#",$val,$lines);
reset ($vl);
$lines = eregi_replace("#%[a-z0-9_]*%#",'', $lines);
echo "\n$lines";
}

##################################################################################

function template_select($t) {
global $s;
if ($s[GC_style]) $st = $s[GC_style]; else $st = $s[def_style];
if (file_exists($s[phppath].'/styles/'.$st.'/templates/'.$t))
return $s[phppath].'/styles/'.$st.'/templates/'.$t;
return $s[phppath].'/styles/_common/templates/'.$t;
}

##################################################################################
##################################################################################
##################################################################################

function getmicrotime() { 
list($usec,$sec) = explode(" ",microtime()); 
return ((float) $usec + (float)$sec); 
} 

##################################################################################
##################################################################################
##################################################################################

function problem($error) {
global $s;
$a[info] = info_line($error);
page_from_template('error.html',$a);
}

##################################################################################

function my_strtolower($a) {
$a = strtolower($a);
$a = strtr($a,'ABCDEFGHIJKLMNOPQRSTUVWXYZ&#204;&#352;&#200;&#216;&#381;&#221;&#193;&#205;&#201;&#218;A&#194;A&#196;&#199;&#201;&#203;&#205;&#206;&#211;&#212;&#214;&#218;&#220;&#221;','abcdefghijklmnopqrstuvwxyz&#236;&#353;&#232;&#248;&#382;&#253;&#225;&#237;&#233;&#250;a&#226;a&#228;&#231;&#233;&#235;&#237;&#238;&#243;&#244;&#246;&#250;&#252;&#253;');
return $a;
}

##################################################################################
##################################################################################
##################################################################################

function try_blacklist($phrase,$what) {
global $s,$m;
$q = dq("select phrase from $s[pr]blacklist where what like '$what'",1);
while ($pole = mysql_fetch_row($q))
{ /*if ($what=='word') { if (preg_match("/\b$pole[0]\b/i",$phrase)) return "$m[black1] $pole[0] $m[black2]"; }
  else*/if (strstr ($phrase, $pole[0])) return "$m[black1] $pole[0] $m[black2]";
}
}

##################################################################################

function try_ip_blacklist($ip) {
global $s,$m;
$q = dq("select phrase from $s[pr]blacklist where what = 'ip' AND phrase = '$ip'",1);
$x = mysql_fetch_row($q);
if ($x[0]) problem($m[ban_ip]);
}

##################################################################################
##################################################################################
##################################################################################

function get_ipnum() {
global $s;
if ($s[have_ip]) $x = explode('.',$s[have_ip]);
else $x = explode('.',trim(getenv('REMOTE_ADDR')));
//$x = explode('.','61.88.255.255'); // AU
$s[ipnum] = 16777216*$x[0] + 65536*$x[1] + 256*$x[2] + $x[3];
}

##################################################################################

function log_country() {
global $s;
if ($_SESSION[log_country]) return false;
get_ipnum();
if (!$s[ipnum]) return '';
dq("delete from $s[pr]ip_country_temp where time < ($s[cas]-900)",1);
$q = dq("select cc from $s[pr]ip_country_temp where n = '$s[ipnum]'",1);
$x = mysql_fetch_row($q); if ($x[0]) $country = $x[0];
if (!$country)
{ $q = dq("select cc from $s[pr]ip_country where start <= '$s[ipnum]' and end >= '$s[ipnum]'",1);
  $x = mysql_fetch_row($q);
  if ($x[0])
  { dq("insert into $s[pr]ip_country_temp values ('$s[ipnum]','$x[0]','$s[cas]')",1);
    $country = $x[0];
  }
}
if ($country) $q = dq("update $s[pr]countries set i = i + 1 where code = '$country'",1);
$_SESSION[log_country] = $country;
}

##################################################################################
##################################################################################
##################################################################################

function usit_display($cat,$n,$template,$email,$filter,$queue,$striptags) {
global $s,$m;
if ($s[cats_share_usit]) $bigboss = $cat = 0;
$bigboss = get_bigboss_category($cat);
if (!$s[filter_usit]) $filter = 0;
if (!$queue) $queue = 0;
if (is_array($n)) $ad_numbers = $n; else $ad_numbers[0] = $n;
if (!$usit_list)
{ $q = dq("select * from $s[pr]usit_list_short where c = '$bigboss'",1);
  $usit_list_short = mysql_fetch_assoc($q);
}

$q = dq("select * from $s[pr]usit_list where category = '$cat' order by rank",1);
while ($x=mysql_fetch_assoc($q)) $usit_ranks[$x[rank]] = $x[usit_n];

$query = 'where '.my_implode('n','OR',$ad_numbers);
$q = dq("select * from $s[pr]ads_usit $query and queue = '$queue'",1);
while ($y = mysql_fetch_assoc($q))
{ for ($x=1;$x<=25;$x++)
  { if (!$y["n$x"]) continue;
    if (($filter) AND ((!$y["text$x"]) OR (!trim(str_replace('_','',$y["text$x"])))) AND (!$y["code$x"])) continue;
    if (strstr($y["text$x"],"\n\n\n\n\n")) { $x1 = explode("\n\n\n\n\n",$y["text$x"]); $data[value] = $x1[1]; } // multiselect
    else $data[value] = $y["text$x"];
    if ($striptags) $data[value] = strip_tags($data[value],'<br><p>');
    $data[name] = $usit_list_short["title$x"];
    if ($email)
    { $usit_array[$x] = parse_part_of_email($template,$data);
    //$a[$y[n]] .= parse_part_of_email($template,$data);
      $a['individual_'.$y[n]]['user_item_'.$y["n$x"]] = parse_part_of_email($template,$data);
    }
    else
    { if (($s[search_highlight]) AND ($s[highlight])) $data[value] = highlight_words('',$data[value]);
      if ($s[highlight_usit]) $data[value] = highlight_words($s[highlight_usit],$data[value]);
      if (($y["code$x"]==0) AND (!$data[value])) continue; // checkbox unchecked
      elseif (($y["code$x"]==1) AND (!$data[value]) AND ($template=='user_item_listing.txt')) $parsed = parse_part('user_item_listing_checkbox.txt',$data);
      else $parsed = parse_part($template,$data);
      if (substr(strip_tags($parsed),-1,1)==':') $parsed = substr_replace(strip_tags($parsed),'',-1,1);
      if ($usit_list_short["visible$x"]) $usit_array[$x] = $parsed;
      $a['individual_'.$y[n]]['user_item_'.$y["n$x"]] = $parsed;
      if ($data[value]) $a['individual_'.$y[n]]['user_item_value_'.$y["n$x"]] = $data[value];
      else $a['individual_'.$y[n]]['user_item_value_'.$y["n$x"]] = $parsed;
      $a['individual_'.$y[n]]['user_item_name_'.$y["n$x"]] = $data[name];
    }
  }
  foreach ($usit_ranks as $k=>$v) $a[$y[n]] .= $usit_array[$v];
  unset($usit_array);
}
return $a;
}

##################################################################################

function get_random_password($a) {
list($usec,$sec) = explode(' ',microtime());
$x = $sec+($usec*1000000);
return substr(md5($a.$x.$s[cas]),5,15);
}

##################################################################################
##################################################################################
##################################################################################

function find_order_by_ads($sort_by,$direction) {
global $s;
$y = explode(',',$s[sort_ads_options]); foreach ($y as $k => $v) $allowed_sortby[] = $v;
$allowed_sortby[] = 'pick';
foreach ($allowed_sortby as $k=>$v) if (!$v) unset($allowed_sortby[$k]);
if(in_array($sort_by,$allowed_sortby))
{ if ($direction=='desc') $a = "x_featured_by desc,$sort_by desc";
  else $a = $a = "x_featured_by desc,$sort_by asc";
}
else $a = "x_featured_by desc,$s[sortby] $s[sortby_direct]";
return $a;
}

##################################################################################
##################################################################################
##################################################################################

function categories_selected($what,$vybrana,$incl_invisible,$incl_disabled_submissions,$incl_aliases,$no_info,$only_bigboss_n) {
global $s,$m;
if (!$incl_invisible) $where = 'AND visible = 1';
if (!$incl_disabled_submissions) $where .= ' AND submit_here = 1';
if (!$incl_aliases) $where .= ' AND alias_of = 0';
if (!$s[cats_share_usit]) { if ($only_bigboss_n) $where .= " AND bigboss = '$only_bigboss_n'"; }
if ($what=='ad_first') $where .= " AND level = '1'";
$q = dq("select * from $s[pr]cats where 1 $where order by path_text",1);
while ($a=mysql_fetch_assoc($q))
{ set_time_limit(30);
  if (!$no_info)
  { unset($i,$info);
    if (!$a[visible]) $i[] = $m[invisible]; if (!$a[submit_here]) $i[] = $m[disabled];
    if ($i) $info = '('.implode(', ',$i).')';
  }
  $mo = ''; for ($i=1;$i<$a[level];$i++) $mo .= '- ';
  $a[path_text] = eregi_replace("<%.+%>",'',$a[path_text]);
  $a[path_text] = eregi_replace("<%.+$",$a[title],$a[path_text]);
  if ($a[alias_of]) $a[path_text] = $s[alias_pref].$a[path_text].$s[alias_after];
  $a[path_text] = stripslashes($a[path_text]);
  if ($a[n]==$vybrana) $selected = ' selected'; else $selected = '';
  $x .= "<option value=\"$a[n]\"$selected>$mo $a[path_text]$info</option>\n";
}
return stripslashes($x);
}

##################################################################################

function get_category_search_form($bigboss,$c,$a,$vl) {
global $s;
if ((!$bigboss) AND ($c)) $bigboss = get_bigboss_category($c);
if (!$bigboss) return false;
$q = dq("select * from $s[pr]cats_search_forms where n = '$bigboss'",1);
$x = mysql_fetch_assoc($q);

//echo $x[form];exit;

$vl["category_selected_$c"] = ' selected';

$a = get_bigboss_area($a);
$vl["area_selected_$a"] = ' selected';

foreach ($vl as $k=>$v) $x[form] = str_replace("#%$k%#",$v,$x[form]);
return $x[form];
}

################################################################################
################################################################################
################################################################################

function auto_payment_done($n) {
global $s,$m;
$q = dq("select * from $s[pr]payment_process where ip = '$s[ip]' and time >= ($s[cas]-600) and order_n = '$n'",1);
$payment_process = mysql_fetch_assoc($q);
if ($payment_process[user]) $user = get_user_variables($payment_process[user]);
dq("delete from $s[pr]payment_process where time < ($s[cas]-600) or user = '$payment_process[user]'",1);

if ($_COOKIE[GC_u_email]) $user = get_user_variables($_COOKIE[GC_u_n]);
elseif ($user[n])
{ if ($payment_process[remember_me])
  { setcookie(GC_u_password,$user[password],$s[cas]+31536000); 
    setcookie(GC_u_name,$user[name],$s[cas]+31536000); 
    setcookie(GC_u_email,$user[email],$s[cas]+31536000); 
    setcookie(GC_u_n,$user[n],$s[cas]+31536000);
    setcookie(GC_u_style,$user[style],$s[cas]+31536000);
  }
  $_SESSION[GC_u_password] = $user[password];
  $_SESSION[GC_u_name] = $user[name];
  $_SESSION[GC_u_email] = $user[email];
  $_SESSION[GC_u_n] = $user[n];
  $_SESSION[GC_u_style] = $user[style];
}
else user_login_form();

$s[GC_u_password] = $user[password];
$s[GC_u_name] = $user[name];
$s[GC_u_email] = $user[email];
$s[GC_u_n] = $user[n];
$s[GC_u_style] = $s[GC_style] = $user[style];
$q = dq("select * from $s[pr]ads_orders where user = '$s[GC_u_n]' AND n = '$payment_process[order_n]'",1);
$order = mysql_fetch_assoc($q);
if ($order[info]) $s[info] = info_line($order[info]);
user_home_page();
}

#########################################################################

function user_login_form($in) {
global $s;
if ($_SERVER[HTTP_REFERER]) $in[back] = $_SERVER[HTTP_REFERER];
else $in[back] = $s[site_url];
$in[info] = $s[info];
if ($s[user_login_captcha]) $in[field_captcha_test] = parse_part('form_captcha_test.txt','');
page_from_template('user_login.html',$in);
}

#########################################################################

function user_home_page() {
global $s,$m;
$user_vars = check_logged_user();
$user_vars[info] = $s[info];
$user_vars[user_site_url] = get_user_url($user_vars[n]);
page_from_template('user_home.html',$user_vars);
}

################################################################################

function get_user_url($n) {
global $s;
$n = round($n);
if (!$n) return false;
$user_vars = get_user_variables($n);
if (!$user_vars[n]) return false;
$s[owner_vars] = $user_vars;
if ($s[A_option]=='rewrite') return "$s[site_url]/user-$user_vars[n]/".str_replace(' ','-',$user_vars[nick]).".html";
return "$s[site_url]/users.php?n=$user_vars[n]";
}

################################################################################

function show_classifieds() {
global $s,$m;
check_logged_user();
$q = dq("select * from $s[pr]ads where owner = '$s[GC_u_n]' order by n desc",1);
while ($ad=mysql_fetch_assoc($q))
{ $ad[details_url] = get_detail_page_url('ad',$ad[n],$ad[rewrite_url],0);
  foreach ($s[extra_options] as $k=>$v) if ($ad['x_'.$v.'_by']>$s[cas]) $extra[] = $m['xtra_'.$v];
  if ($ad[x_pictures_by]>$s[cas]) $extra[] = "$m[xtra_pictures] $ad[x_pictures_max]";;
  if ($ad[x_files_by]>$s[cas]) $extra[] = "$m[xtra_files] $ad[x_files_max]";;
  if (!$extra) $ad[extra_features] = 'N/A'; else $ad[extra_features] = implode(', ',$extra);
  unset($extra);
  $ad[created] = datum($ad[created],1);
  $ad[valid_by] = datum($ad[t2],1);
  $ad[hide_enable_paypal_begin] = '<!--'; $ad[hide_enable_paypal_end] = '-->';
  if (ad_is_active($ad[t1],$ad[t2],$ad[status],$ad[n]))
  { $ad[active] = $m[active];
    if (($ad[x_paypal_disable]) AND ($ad[x_paypal_disabled])) unset($ad[hide_enable_paypal_begin],$ad[hide_enable_paypal_end]);
    //foreach ($ad as $k=>$v) echo "$k - $v<br>";
  }
  elseif ($ad[status]=='queue') { $ad[active] = $m[queued]; $ad[hide_ad_links_begin] = '<!--'; $ad[hide_ad_links_end] = '-->'; }
  else $ad[active] = $m[inactive]; 
  $a[ads] .= parse_part('user_classified.txt',$ad);
}
$a[info] = $s[info];
page_from_template('user_classifieds.html',$a);
}

###################################################################################

function detail_page_images($images,$n,$html,$item_vars) {
global $s;
if ($item_vars[picture])
{ $test_path = str_replace($s[site_url],$s[phppath],$item_vars[picture]);
  if (file_exists($test_path)) $picture1 = 1;
  else dq("update $s[pr]ads set picture = '' where n = '$n'",1);
}
if ($html) $function = 'A_parse_part'; else $function = 'parse_part';
foreach ($images as $k=>$v)
{ $pictures++;
  $full_size_image = '<br><img border="0" src="'.preg_replace("/\/$n-/","/$n-big-",$v[url]).'" alt="'.$v[description].'"><br>'.$v[description];
  $a[all_images] .= '<a href="javascript:show_gallery(\'image-'.$pictures.'\');"><img border="0" src="'.$v[url].'"></a> ';
  if (!$picture1)
  { dq("update $s[pr]ads set picture = '$v[url]' where n = '$n'",1);
    $picture1 = 1;
  }
  $filename = str_replace("$s[site_url]/uploads/images/",'',$v[url]);
  $big_filename = preg_replace("/^$n-/","$n-big-",$filename);
  if (file_exists("$s[phppath]/uploads/images/$big_filename")) $big_url = "$s[site_url]/uploads/images/$big_filename";
  else $big_url = $v[url];
  $a[pictures] .= '<div id="image-'.$pictures.'" style="display:none;text-align:center;padding:5px;"><img border="0" src="'.$big_url.'"><br>'.$v[description].'</div>';
}
if (!$pictures) return false;
if ($pictures==1) return array('full_size_image'=>$full_size_image);
$a[previews_width] = $pictures*85; if ($a[previews_width]>705) $a[previews_width] = 705;
return array('pictures_gallery'=>$function('gallery.txt',$a));
}

################################################################################

function check_logged_user() {
global $s;
if ($s[GC_u_n]) { $n = $s[GC_u_n]; $user = get_user_variables($n); $password = $user[password]; }
elseif ($_COOKIE[GC_u_n]) { $n = $_COOKIE[GC_u_n]; $password = $_COOKIE[GC_u_password]; }
elseif ($_SESSION[GC_u_n]) { $n = $_SESSION[GC_u_n]; $password = $_SESSION[GC_u_password]; }
$n = round($n); if (!$n) user_login_form();
if (!$user) $user = get_user_variables($n);
if ((!$user[n]) OR ($password!=$user[password])) user_login_form();
$s[GC_u_password] = $user[password];
$s[GC_u_name] = $user[name];
$s[GC_u_email] = $user[email];
$s[GC_u_n] = $user[n];
$s[GC_u_style] = $s[GC_style] = $user[style];
if (($_POST) AND (!$s['no_test'])) check_field("$user[email]$user[password]$user[n]");
return $user;
}

#################################################################################

function check_entered_captcha($entered_code) {
global $s,$m;
include("image_control.php");
$image_control = new image_control();
$image_control->get_both_codes($entered_code);
$valid_code = $image_control->valid_code;
$entered_code = $image_control->entered_code;
if ((!trim($entered_code)) OR ($valid_code!=$entered_code)) $problem = $m[w_code1].'<br>'.$m[w_code2].' <b>'.$valid_code.'</b>'.$m[w_code3].'<b> '.$entered_code.'</b>.';
if ($problem) return $problem;
}

################################################################################
################################################################################
################################################################################

function get_favorite_line($what,$n,$bookmark) {
global $s,$m;
if ($what=='ad') $what_word = $m[bookmark_it]; else $what_word = $m[bookmark_this_category];
if ($bookmark) return '<a href="'.$s[site_url].'/favorites.php?action=remove&amp;what='.$what.'&amp;n='.$n.'">'.$m[remove_from_bookmarks].'</a>';
else return '<a href="'.$s[site_url].'/favorites.php?action=add&amp;what='.$what.'&amp;n='.$n.'">'.$what_word.'</a>';
}

################################################################################

function get_favorites_status($what,$in_n) {
global $s,$m;
if (is_array($in_n)) $n = $in_n; else $n[0] = $in_n;
$query = my_implode('n','or',$n);
if (!$query) return false;
$q = dq("select * from $s[pr]u_favorites where user = '$s[GC_u_n]' AND what = '$what' AND $query",1);
while ($x=mysql_fetch_assoc($q)) $bookmarks[$x[n]] = 1;
return $bookmarks;
}

################################################################################

function get_private_notes_for_items($what,$in_n) {
global $s,$m;
if (is_array($in_n)) $n = $in_n; else $n[0] = $in_n;
$query = my_implode('n','or',$n);
if (!$query) return false;
$q = dq("select * from $s[pr]u_private_notes where user = '$s[GC_u_n]' AND what = '$what' AND $query",1);
while ($x=mysql_fetch_assoc($q)) $notes[$x[n]] = nl2br($x[notes]);
return $notes;
}

################################################################################
################################################################################
################################################################################

function get_icons_for_item($in,$bookmark,$separator) {
global $s;
if (!$separator) $separator = '&nbsp;';
$marknew = $s[marknew_time];
if ((($in[edited]+$marknew) > $s[cas]) AND ($s[pref_upd]) AND ($s[upd_img])) $icons[] = $s[upd_img];
elseif ((($in[created]+$marknew) > $s[cas]) AND ($s[new_img])) $icons[] = $s[new_img];
elseif ((($in[edited]+$marknew) > $s[cas]) AND ($s[upd_img])) $icons[] = $s[upd_img];
if (($in[pick]) AND ($s[pick_img])) $icons[] = $s[pick_img];
if (($in[popular]) AND ($s[pop_img])) $icons[] = $s[pop_img];
if (($in[x_featured_by]>$s[cas]) AND (trim($s[featured_img]))) $icons[] = $s[featured_img];
if ($bookmark) $icons[] = $s[bookmark_img];
if (count($icons)) return implode($separator,$icons);
}

################################################################################

function folder_icon($cas,$icon) {
global $s;
if (trim($icon)) return $icon;
for ($x=1;$x<=4;$x++) { $icon_cas = $s[cas] - ($s["icon_folder_t$x"] * 86400); if ($cas>$icon_cas) return  $s[site_url].'/images/icon_folder_'.$x.'.gif'; }
return  $s[site_url].'/images/icon_folder_5.gif';
}


##################################################################################
##################################################################################
##################################################################################

function category_pages_list($what,$category_n,$category_rewrite,$area_n,$total,$page) {
global $s,$m;
if ($total<=1) return false;
$url = category_url('ad',$category_n,0,1,$category_rewrite);
$sorts = explode(',',$s[sort_ads_options]);
if (!$s[this_sort]) { $s[this_sort] = $s[sortby]; $s[this_direction] = $s[sortby_direct]; }

foreach ($sorts as $k=>$v)
{ $sort = "sort-$v";
  if ($s[this_sort]==$v) $sort_options[] = $m[$v];
  elseif ($s[category_use_ajax]) $sort_options[] = "<a href=\"#content_top\" onclick=\"show_waiting('content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category&n=$s[this_cat]&area=$s[this_area]&sort=$v&direction=$s[this_direction]&page=1','content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$v&direction=$s[this_direction]&page=1&total=$total','pages_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$v&direction=$s[this_direction]&page=1&total=$total','pages_div_box1');\">$m[$v]</a>";
  else $sort_options[] = '<a rel="nofollow" href="'.str_replace('extra_commands',$sort,$url).'">'.$m[$v].'</a>';
}
$a[sortby_options] = implode(' - ',$sort_options);

if ($s[category_use_ajax])
{ $a[link_asc] = "href=\"#content_top\" onclick=\"show_waiting('content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=asc&page=1','content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=asc&page=1&total=$total','pages_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=asc&page=1&total=$total','pages_div_box1');\"";
  $a[link_desc] = "href=\"#content_top\" onclick=\"show_waiting('content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=desc&page=1','content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=desc&page=1&total=$total','pages_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=desc&page=1&total=$total','pages_div_box1');\"";
}
else
{ $a[link_asc] = 'href="'.str_replace('extra_commands','direction-asc',$url).'"';
  $a[link_desc] = 'href="'.str_replace('extra_commands','direction-desc',$url).'"';
}

$numbers = category_pages_list_numbers($url,$total,$page);
if ($numbers) $a[pages_list] = $numbers; else { $a[hide_pages_begin] = '<!--'; $a[hide_pages_end] = '-->'; }

$a[total] = $total;
return add_this_area(parse_part('category_pages_list.txt',$a));
}


###################################################################################

function category_pages_list_numbers($url,$total,$page) {
global $s,$m;

//echo "$s[this_cat] --- $s[this_area] --- $s[this_sort] --- $s[this_direction] --- $s[this_offer_wanted]";

if ((!$page) OR ($page<1) OR (!is_numeric($page))) $page = 1;
$pages = ceil($total/$s[per_page]);
if ($pages==1) $pages_list = '';
else
{ for ($x=1;$x<=$pages;$x++)
  { if ($s[category_use_ajax])
    { if ($x==$page) $pages_numbers .= " <b>$x</b> "; 
      elseif ((!$s[pages_max_ads]) OR (($x>=($page-$s[pages_max_ads])) AND ($x<=($page+$s[pages_max_ads])))) $pages_numbers .= "&nbsp;<a href=\"#content_top\" onclick=\"show_waiting('content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=$x','content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=$x&total=$total','pages_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=$x&total=$total','pages_div_box1');\">".$x."</a> ";
      if ($s[pages_max_ads])
      { if ($page>1) $link_first = "&nbsp;<a href=\"#content_top\" onclick=\"show_waiting('content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=1','content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=1&total=$total','pages_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=1&total=$total','pages_div_box1');\"><<</a> ";
        if ($page<$pages) $link_last = "&nbsp;<a href=\"#content_top\" onclick=\"show_waiting('content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=$x','content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=$x&total=$total','pages_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=$x&total=$total','pages_div_box1');\">>></a> ";
      }
      if ($x==($page-1)) $link_down = "&nbsp;<a href=\"#content_top\" onclick=\"show_waiting('content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=".($page-1)."','content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=".($page-1)."&total=$total','pages_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=".($page-1)."&total=$total','pages_div_box1');\"><</a> ";
      elseif ($x==($page+1)) $link_up = "&nbsp;<a href=\"#content_top\" onclick=\"show_waiting('content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$s[this_direction]&page=".($page+1)."','content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$direction&page=".($page+1)."&total=$total','pages_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&n=$s[this_cat]&area=$s[this_area]&sort=$s[this_sort]&direction=$direction&page=".($page+1)."&total=$total','pages_div_box1');\">></a> ";
    }
    else
	{ if ($x==$page) $pages_numbers .= " <b>$x</b> "; 
      elseif ((!$s[pages_max_ads]) OR (($x>=($page-$s[pages_max_ads])) AND ($x<=($page+$s[pages_max_ads]))))
      { if ($x==1) $pages_numbers .= '&nbsp;<a href="'.str_replace('-page_n',"",$url).'">'.$x.'</a> ';
        else $pages_numbers .= '&nbsp;<a href="'.str_replace('-page_n',"-$x",$url).'">'.$x.'</a> ';
      }
      if ($s[pages_max_ads])
      { if ($page>1) $link_first = '&nbsp;<a href="'.str_replace('-page_n','',$url).'"><<</a> ';
        if ($page<$pages) $link_last = '&nbsp;<a href="'.str_replace('-page_n',"-$x",$url).'">>></a> ';
      }
      if ($x==($page-1)) $link_down = '&nbsp;<a href="'.str_replace('-page_n',"-".($page-1),$url).'"><</a> ';
      elseif ($x==($page+1)) $link_up = '&nbsp;<a href="'.str_replace('-page_n',"-".($page+1),$url).'">></a> ';
    }
  }
  $pages_list = " $link_first$link_down$pages_numbers$link_up$link_last";
}
return $pages_list;
}

###################################################################################
###################################################################################
###################################################################################

function notes_edit_box($what,$n,$info) {
global $s,$m;
include_once("$s[phppath]/data/data_forms.php");
if (isset($s[current_notes])) $a[notes] = $s[current_notes];
else
{ $q = dq("select * from $s[pr]u_private_notes where user = '$s[GC_u_n]' AND what = '$what' AND n = '$n'",1);
  $a = mysql_fetch_assoc($q);
}
$a[what] = $what; $a[n] = $n;
if (trim($info)) $a[info] = $info;
return parse_part('notes_form.txt',$a);
}

########################################################################################

function report_box($n,$error,$hide_cancel) {
global $s,$m;
include_once("$s[phppath]/data/data_forms.php");
if ($_POST[hide_cancel]) $hide_cancel = 1;
$a = replace_array_text($_POST);
$a[what] = $what; $a[n] = $n;
if ($s[error_report_captcha]) $a[field_captcha_test] = parse_part('form_captcha_test.txt',$a);
if (trim($error)) $a[info] = $error;
if ($hide_cancel) { $a[hide_cancel] = '1'; $a[hide_cancel_begin] = '<!--'; $a[hide_cancel_end] = '-->'; }
return parse_part('abuse_report.txt',$a);
}

########################################################################################

function tell_friend_box($n,$error,$hide_cancel) {
global $s,$m;
include_once("$s[phppath]/data/data_forms.php");
if ($_POST[hide_cancel]) $hide_cancel = 1;
$in = replace_array_text($_POST);
if (is_numeric($n))
{ if ($what=='c')
  { $a = get_category_variables($n);
    $a[title] = "$s[site_name] - $a[title]";
    $a[url] = category_url($a[use_for],$in[category],$a[alias_of],$a[name],1,$a[pagename],$a[rewrite_url],'','');
  }
  elseif (is_numeric($n)) { $a = get_ad_variables($n); $a[url] = get_detail_page_url('ad',$a[n],$a[rewrite_url],$a[category]); }
  /*{ if ($what=='l') $a = get_link_variables($n);
    elseif ($what=='a') $a = get_article_variables($n);
    elseif ($what=='n') $a = get_new_variables($n);
    elseif ($what=='v') $a = get_video_variables($n);
    if ($a[n]) $a[url] = get_detail_page_url($what,$a[n],$a[rewrite_url],'',1);
  }*/
}
if (!$a[n]) { $a[title] = $s[site_name]; $a[url] = $s[site_url]; }
$a[name] = $in[name]; $a[email] = $in[email]; $a[friend_email] = $in[friend_email]; $a[message] = $_POST[message];
$a[what] = $what; $a[n] = $n;
if ($s[tell_friend_captcha]) $a[field_captcha_test] = parse_part('form_captcha_test.txt',$a);
if (trim($error)) $a[info] = $error;
if ($hide_cancel) { $a[hide_cancel] = '1'; $a[hide_cancel_begin] = '<!--'; $a[hide_cancel_end] = '-->'; }
return parse_part('tell_friend.txt',$a);
}

########################################################################################

function enter_comment_box($n,$error) {
global $s,$m;
include_once("$s[phppath]/data/data_forms.php");
$a = replace_array_text($_POST);
$a[n] = $n;
$in[name] = $a[name]; $in[email] = $a[email];
if ($s[comm_v_name]) { $x[item_name] = $m[name]; $x[field_name] = 'name'; $x[field_value] = $in[name]; $x[field_maxlength] = 255; $a[field_name] = parse_part('form_field.txt',$x); }
if ($s[comm_v_email]) { $x[item_name] = $m[email]; $x[field_name] = 'email'; $x[field_value] = $in[email]; $x[field_maxlength] = 255; $a[field_email] = parse_part('form_field.txt',$x); }
if ($s[comm_v_captcha]) $a[field_captcha_test] = parse_part('form_captcha_test.txt',$a);
if (trim($error)) $a[info] = $error;
return parse_part('comment_form.txt',$a);
}

########################################################################################

function contact_box($what,$n,$error,$hide_cancel) {
global $s,$m;
include_once("$s[phppath]/data/data_forms.php");
if ($_POST[hide_cancel]) $hide_cancel = 1;
foreach ($_POST as $k=>$v) $_POST[$k] = iconv('windows-1256','UTF-8',$v);
$in = replace_array_text($_POST);
if (is_numeric($n))
{ if ($what=='a') $a = get_ad_variables($n,0);
  elseif ($what=='u') $a = get_user_variables($n);
  $need_captcha = $s[message_owner_captcha];
}
if (!$a[n]) { $a[title] = $s[site_name]; $a[url] = $s[site_url]; $need_captcha = $s[message_to_us_captcha]; }
else { $need_captcha = $s[message_owner_captcha]; }
$a[name] = $in[name]; $a[email] = $in[email]; $a[message] = $_POST[message];
$a[what] = $what; $a[n] = $n;
if ($need_captcha) $a[field_captcha_test] = parse_part('form_captcha_test.txt',$a);
if (trim($error)) $a[info] = $error;
if ($hide_cancel) { $a[hide_cancel] = '1'; $a[hide_cancel_begin] = '<!--'; $a[hide_cancel_end] = '-->'; }
return parse_part('contact_form.txt',$a);
}

########################################################################################
########################################################################################
########################################################################################

?>