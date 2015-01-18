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

//RewriteRule user\/(.*) search.php?action=user&user=$1 [NC]

include('./common.php');
$s[selected_menu] = 5;
//$s[search_use_like] = 1;
//$s[short_words] = array('and','AND','or','OR');
get_messages('search.php');

foreach ($_GET as $k=>$v) if ((!is_array($v)) AND (!trim($v))) unset($_GET[$k]);
if (($_GET) AND (!$_GET[action])) $_GET[action] = 'ads_simple';
$_GET = replace_array_text($_GET);
$_GET[phrase] = replace_array_text($_GET[phrase]);


switch ($_GET[action]) {
case 'ads_one_category'		: ads_one_category($_GET);
case 'ads_simple'			: ads_simple($_GET);
case 'ads_advanced'			: ads_advanced($_GET);
}
search_form('');

#############################################################################
#############################################################################
#############################################################################

function ads_one_category($in) {
global $s;
if ($in[search_kind]=='phrase') { $phrases[] = $in[phrase]; }
else $phrases = explode(' ',$in[phrase]);
//foreach ($in as $k=>$v) echo "$k - $v<br>";
ads_searched('ads_one_category',$phrases,$in[search_kind],$in[offer_wanted],$in[category],$in[area_boss],$in[price_mark],$in[price],$in[zip_city],$in[radius],$in[usit],$in[page],$in[perpage],$in[order_by],$in[direction],$in[nolog]);
}

#############################################################################

function ads_simple($in) {
global $s;
if ($in[search_kind]=='phrase') $phrases[0] = $in[phrase];
else $phrases = explode(' ',$in[phrase]); 
//if (!$phrases[0]) search_form($in);
if (!$in[area_boss]) $in[area_boss] = $in[area];
if (!$in[offer_wanted]) $in[offer_wanted] = 'offer_wanted';
ads_searched('ads_simple',$phrases,$in[search_kind],$in[offer_wanted],$in[category_boss],$in[area_boss],'',0,$in[zip_city],$in[radius],'',$in[page],$in[perpage],$in[order_by],$in[direction],$in[nolog]);
}

#############################################################################

function ads_advanced($in) {
global $s;
if (is_array($in[phrase])) $phrases = $in[phrase]; else $phrases[0] = $in[phrase];
if (!$phrases[0]) search_form($in);
if ($in[search_kind]=='and') $original_search_kind = 'and'; else $original_search_kind = 'or';
ads_searched('ads_advanced',$phrases,$in[search_kind],$in[offer_wanted],$in[category],$in[area],$in[price_mark],$in[price],$in[zip_city],$in[radius],'',$in[page],$in[perpage],$in[order_by],$in[direction],$in[nolog]);
}

#############################################################################

function ads_searched($action,$phrases,$original_search_kind,$offer_wanted,$category,$area,$original_price_mark,$price,$zip_city,$radius,$usit,$page,$perpage,$order_by,$direction,$nolog) {
global $s,$m;
$phrases = delete_some_words($phrases);
foreach ($phrases as $k=>$v) $phrases[$k] = delete_special_chars($v);
if (!count($phrases)) search_form();
if ($s[search_highlight]) { $s[highlight] = $phrases; foreach ($usit as $k=>$v) if ($v) $s[highlight_usit][$k] = $v; }
if ((!is_numeric($page)) OR ($perpage<=0)) $page = 1;
if ((!is_numeric($perpage)) OR ($perpage>100) OR ($perpage<=0)) $perpage = $s[per_page];
$from = ($page-1)*$perpage;
update_search_log($phrases,'l',$nolog);
$sortby = find_order_by_ads($order_by,$direction);

//$where[] = get_where_fixed_part(0,0,0,0,$s[cas],$offer_wanted);
$where[] = get_where_fixed_part(0,$category,0,$area,$s[cas],$offer_wanted);

if ((is_numeric($price)) AND ($original_price_mark))
{ if ($original_price_mark=='less_equal') $price_mark = '<=';
  elseif ($original_price_mark=='more_equal') $price_mark = '>=';
  elseif ($original_price_mark=='equal') $price_mark = '=';
  else return false;
  $where[] = "price $price_mark $price";
}
//$regexp = "REGEXP '(^|[^a-zA-Z])$v($|[^a-zA-Z])'";

//foreach ($phrases as $k=>$v) $where_partial[] = "((title like '%$v%' OR (description like '%$v%' OR (detail like '%$v%' OR (keywords like '%$v%')"; 
foreach ($phrases as $k=>$v) 
{ $v = trim($v);
  if (!$v) continue;
  if ($s[search_words]) $regexp = "REGEXP '(^|[^a-zA-Z])$v($|[^a-zA-Z])'";  elseif ($s[search_use_like]) $regexp = "like '%$v%'"; else $regexp = "REGEXP '$v'";
  $where_partial[] = "($s[pr]index.all_text $regexp)";
  if ((count($phrases)==1) AND (is_numeric($v))) $searched_number = $v;
}

$allowed_search_kind = array('and','or','phrase');
if (!in_array($original_search_kind,$allowed_search_kind)) $original_search_kind = 'and';
if ($original_search_kind=='phrase') $search_kind = 'and'; else $search_kind = $original_search_kind;
if ($where_partial)
{ $where[] = implode(" $search_kind ",$where_partial);
  unset($where_partial);
}

//foreach ($_GET as $k=>$v) echo "$k - $v<br>";

if (($s[radius_search]) AND (trim($zip_city)))
{ //foreach ($_GET as $k=>$v) echo "$k - $v<br>";
  if ($zip_city)
  { $q = dq("select * from $s[pr]city_zip where zip = '$zip_city'",1);
    if (!mysql_num_rows($q)) $q = dq("select * from $s[pr]city_zip where city = '$zip_city'",1);
    if (!mysql_num_rows($q)) $q = dq("select * from $s[pr]city_zip where city like '%$zip_city%'",1);
    if ($city_vars = mysql_fetch_assoc($q))
    { //foreach ($city_vars as $k=>$v) echo "$k - $v<br>";
      $radius = round($radius); if (!$radius) $radius = 10;
      //if ($s[km_miles]=='km') $radius = $radius / 0.621371192;
      if ($s[km_miles]=='km') $what_distance = ",( 6371 * acos( cos( radians($city_vars[lat]) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($city_vars[lon]) ) + sin( radians($city_vars[lat]) ) * sin( radians( latitude ) ) ) ) AS distance";
	  else $what_distance = ",( 3959 * acos( cos( radians($city_vars[lat]) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($city_vars[lon]) ) + sin( radians($city_vars[lat]) ) * sin( radians( latitude ) ) ) ) AS distance";
	  $having_distance = "HAVING distance <= $radius";
    }
  }
}
$bigboss = get_bigboss_category($category);
if ($usit)
{ list($usits,$avail_val) = get_category_usit($bigboss);
//foreach ($usit[5] as $k=>$v) echo "$k - $v<br>";
  $usit = replace_array_text($usit);
  foreach ($usits as $k=>$v)
  { $value = $usit[$k];
    if (!trim($value)) continue;
    if ( ($usits[$k][item_type]=='select') or ($usits[$k][item_type]=='radio') ) $where_partial[] = "$s[pr]ads_usit.code$k = '$value'";
    elseif ($usits[$k][item_type]=='multiselect') $where_partial[] = "$s[pr]ads_usit.text$k like '%\_$value\_%'";
    /*elseif ($usits[$k][item_type]=='multiselect')
    { foreach ($usit[$k] as $k1=>$v1) if (trim($v1)) $where_partial1[] = "$s[pr]ads_usit.text$k like '%\_$v1\_%'";
      if ($where_partial1) $where_partial[] = '('.implode(' or ',$where_partial1).')';
    }*/
    elseif ($usits[$k][item_type]=='checkbox') $where_partial[] = "$s[pr]ads_usit.code$k = '$value'";
    else $where_partial[] = "$s[pr]ads_usit.text$k like '%$value%'";
  }
  if ($where_partial[0])
  { $where[] = implode(' and ',$where_partial)." AND $s[pr]ads_usit.queue = '0'";
    $tables_list = "$s[pr]ads,$s[pr]index,$s[pr]ads_usit";
    $usit_standard_where = " AND $s[pr]ads_usit.n = $s[pr]ads.n";
  }
}
if (!$tables_list) { $tables_list = "$s[pr]ads,$s[pr]index"; $usit_standard_where = ''; }

$final_where = '('.implode(") and (",$where).") $usit_standard_where AND $s[pr]ads.n = $s[pr]index.n";
//echo "$final_where<br><br>";

$q = dq("select count($s[pr]ads.n) $what_distance from $tables_list where $final_where $having_distance",1); $x = mysql_fetch_row($q); $a[total] = $x[0];

foreach ($usit as $k=>$v) { $values["usit_value_$k"] = $v; $values['usit_selected_'.$k.'_'.$v] = ' selected'; $values['usit_checked_'.$k.'_'.$v] = ' checked'; }

$a[original_phrase] = $values[original_phrase] = trim(implode(' ',$phrases));

$a["kind_selected_$original_search_kind"] = $values["kind_selected_$original_search_kind"] = ' selected';
$values["offer_wanted_$offer_wanted"] = ' selected';
$values["perpage_selected_$perpage"] = ' selected';
$values["orderby_selected_$order_by"] = ' selected';
$values["direction_selected_$direction"] = ' selected';
$values["price_selected_$original_price_mark"] = ' selected';
$values[price] = $price;
$a[search_form] = get_category_search_form($bigboss,$category,$area,$values);
$bigboss_area = get_bigboss_area($area); $a["area_selected_$bigboss_area"] = ' selected';
$a["cat_selected_$bigboss"] = ' selected';
 
if ($a[total])
{ //echo "select $s[pr]ads.* $what_distance from $tables_list where $final_where $having_distance order by $sortby limit $from,$perpage";
  $q = dq("select $s[pr]ads.* $what_distance from $tables_list where $final_where $having_distance order by $sortby limit $from,$perpage",1);
  while ($x=mysql_fetch_assoc($q))
  { /*  echo "<br><br>$x[distance]<br>";*/
    $item[] = $x; $numbers[] = $x[n];
    if (($searched_number) AND ($x[n]==$searched_number)) { header("Location: ".get_detail_page_url('ad',$x[n],$x[rewrite_url],0)); exit; }
  }
  if ($item) 
  { $a[ads] = get_complete_ads($item,$numbers,'ad_a.txt');
    $a[pages] = search_pages_list($action,$phrases,$original_search_kind,$offer_wanted,$category,$area,$original_price_mark,$price,$zip_city,$_GET[radius],$usit,$a[total],$page,$perpage,$order_by,$direction);
  }
}
else
{ $s[info] = info_line($m[no_result]);
  $a[search_kind] = $original_search_kind;
  $a[area] = $area;
  $a[offer_wanted] = $offer_wanted;
  $a[category] = $category;
  $a[area] = $area;
  $a[price_mark] = $original_price_mark;
  $a[price] = $price;
  $a[zip_city] = $zip_city;
  $a[radius] = $_GET[radius];
  $a[perpage] = $perpage;
  $a[order_by] = $order_by;
  $a[direction] = $direction;
  search_form($a);
}
$a[result_categories] = categories_searched('ad',$phrases,$original_search_kind);
$a[meta_title] = $a[original_phrase];

page_from_template('search_result.html',$a);
}

#############################################################################
#############################################################################
#############################################################################

function search_pages_list($action,$phrases,$original_search_kind,$offer_wanted,$category,$area,$price_mark,$price,$zip_city,$radius,$usit,$total,$page,$perpage,$order_by,$direction) {
global $s,$m;
if ((!$total) OR ($total<1)) return false;
$b = array('action'=>$action,'search_kind'=>$original_search_kind,'offer_wanted'=>$offer_wanted,'category'=>$category,'area'=>$area,'price_mark'=>$price_mark,'price'=>$price,'zip_city'=>$zip_city,'radius'=>$radius,'perpage'=>$perpage,'nolog'=>1);
if ($action=='ads_advanced') { foreach ($phrases as $k=>$v) $b["phrase[$k]"] = $v; } else $b[phrase] = implode(' ',$phrases);
foreach ($usit as $k=>$v) if (trim($v)) $b[usit][$k] = $v;

foreach ($b as $k => $v)
{ if (is_array($v)) foreach ($v as $k1=>$v1) $x[] = $k.'['.$k1.']='.$v1;
  else $x[] = $k.'='.$v;
}
if ($x) $final = implode('&',$x);
$a[pages_list] = search_pages_list_numbers($total,$perpage,$page,"$final&order_by=$order_by&direction=$direction");

$base = "$s[site_url]/search.php?$final&order_by=";
$sorts = explode(',',$s[sort_ads_options]);
if (!$direction) $direction = 'asc';

foreach ($sorts as $k=>$v)
{ if ($order_by==$v) $sort_options[] = $m[$v];
  else $sort_options[] = '<a href="'.$base.$v.'&direction='.$direction.'">'.$m[$v].'</a>';
}
$a[sortby_options] = implode(' - ',$sort_options);
if (!$order_by) $order_by = $s[sortby];
$a[url_asc] = $base.$order_by.'&direction=asc'; $a[url_desc] = $base.$order_by.'&direction=desc';

$a[total] = $total;

if (($total==1) OR ($total<=$perpage)) { $a[hide_pages_begin] = '<!--'; $a[hide_pages_end] = '-->'; }
return parse_part('search_result_pages_list.txt',$a);
}

#############################################################################

function search_pages_list_numbers($total,$perpage,$page,$hidden) {
global $s,$m;
$pages = ceil($total/$perpage); 
if ($pages==1) $pages_list = '';
else
{ $sort = str_replace(' desc','',str_replace('pick desc,','',$sort));
  $url = $s[site_url].'/search.php?'.$hidden.'&page=';
  for ($x=1;$x<=$pages;$x++)
  { if ($x==$page) $y .= " <b>$x</b> "; 
    elseif ((!$s[pages_max_ads]) OR (($x>=($page-$s[pages_max_ads])) AND ($x<=($page+$s[pages_max_ads])))) $y .= ' <a href="'.$url.$x.'">'.$x.'</a> ';
    if ($s[pages_max_ads])
    { if ($page>1) $link_first = ' <a href="'.$url.'1"><<</a> ';
      if ($page<$pages) $link_last = ' <a href="'.$url.$x.'">>></a> ';
    }
    if ($x==($page-1)) $link_down = ' <a href="'.$url.($page-1).'"><</a> ';
    elseif ($x==($page+1)) $link_up = ' <a href="'.$url.($page+1).'">></a> ';
  }
  $pages_list = " $link_first$link_down$y$link_up$link_last";
}
return $pages_list;
}

#####################################################################################
#####################################################################################
#####################################################################################

function delete_some_words($in) {
global $s;
if (!is_array($in)) $in = explode(' ',$in);
foreach ($in as $k => $v)
{ if ((in_array(trim($v),$s[short_words])) OR (strlen($v)<$s[search_min])) unset($in[$k]);
  $in[$k] = str_replace('?','',$in[$k]);
  $in[$k] = str_replace('(','',$in[$k]);
  $in[$k] = str_replace(')','',$in[$k]);
}
return $in;
}

#####################################################################################

function delete_special_chars($x) {
return str_replace("'",' ',str_replace('"',' ',str_replace(chr(92),' ',$x)));
}

#####################################################################################

function update_search_log($phrases,$what,$nolog) {
global $s;
if ($nolog) return false;
if (is_array($phrases)) $x = $phrases; else $x = explode(' ',$phrases);
foreach($x as $k => $v)
{ $v = trim($v); if ((!$v) OR (strstr($v,'http://')) OR (strstr($v,'<')) OR (strlen($v)>25)) continue;
  dq("update $s[pr]log_search set count = count + 1 where what = '$what' AND word = '$v'",0);
  if (mysql_affected_rows()<=0) dq("insert into $s[pr]log_search values('$what','$v','1',NULL)",0);
}
}

#############################################################################
#############################################################################
#############################################################################

function search_form($in) {
global $s,$m;

//foreach ($in as $k=>$v) echo "$k - $v<br>";
if (!$in[original_phrase]) $in[original_phrase] = trim(implode(' ',$in[phrase]));
if (!$in[phrase0]) $in[phrase0] = $in[original_phrase];
$in[categories_select] = categories_selected('c',$in[category],0,1,0,1);
$in[areas_select] = areas_selected($in[area],1,1);
$in[order_by_list] = ads_sortby_selectlist($in[order_by]);
$in[direction_list] = ads_direction_selectlist($in[direction]);
$in[offer_wanted_list] = ads_offer_wanted_selectlist($in[offer_wanted]);
$in[ads_top] = top_tags();
$in["kind_selected_$in[search_kind]"] = ' selected';
if (($in[search_kind]!='and') and ($in[search_kind]!='or')) $in[search_kind] = 'and';
$in["search_kind_checked_$in[search_kind]"] = ' checked';
foreach ($in[phrase] as $k=>$v) $in["phrase$k"] = replace_once_text($v);
$in["price_selected_$in[price_mark]"] = ' selected';
$bigboss_area = get_bigboss_area($in[area]); $in["area_selected_$bigboss_area"] = ' selected';
if (!is_numeric($in[perpage])) $in[perpage] = 20; $in["perpage_checked_$in[perpage]"] = ' checked';
if (!$s[radius_search]) { $a[hide_radius_begin] = '<!--'; $a[hide_radius_end] = '-->'; }
if ($s[km_miles]=='km') $in[km_miles] = $m[Kilometres]; else $in[km_miles] = $m[Miles];

if ( (!$in[search_form]) AND (is_numeric($in[category])) AND ($in[category]) AND ($bigboss=get_bigboss_category($in[category])) )
{ $values[original_phrase] = $in[original_phrase];
  $values["kind_selected_$in[search_kind]"] = ' selected';
  $values["offer_wanted_$in[offer_wanted]"] = ' selected';
  $values["perpage_selected_$in[perpage]"] = ' selected';
  $values["orderby_selected_$in[order_by]"] = ' selected';
  $values["direction_selected_$in[direction]"] = ' selected';
  $values["price_selected_$in[price_mark]"] = ' selected';
  $values[price] = $in[price];
  $in[search_form] = get_category_search_form($bigboss,$in[category],$bigboss_area,$values);
}
$in[info] = $s[info];
page_from_template('search.html',$in);
}

#############################################################################

function ads_sortby_selectlist($in) {
global $s,$m;
$sorts = explode(',',$s[sort_ads_options]);
$in = trim(str_replace(' desc','',str_replace('pick desc,','',$in)));
foreach ($sorts as $k=>$v)
{ if ($in==$v) $x = ' selected'; else $x = '';
  $a .= '<option value="'.$v.'"'.$x.'>'.$m[$v].'</option>';
}
return '<select name="order_by" class="field10">'.$a.'</select>';
}

#############################################################################

function ads_offer_wanted_selectlist($in) {
global $s,$m;
if ($in=='offer') $offer = ' selected'; elseif ($in=='wanted') $wanted = ' selected'; else $offer_wanted = ' selected';
return '<select name="offer_wanted" class="field10">
<option value="offer_wanted"'.$offer_wanted.'>'.$m[offer_wanted].'</option>
<option value="offer"'.$offer.'>'.$m[offer].'</option>
<option value="wanted"'.$wanted.'>'.$m[wanted].'</option>
</select>';
}

#############################################################################

function ads_direction_selectlist($in) {
global $s,$m;
if ($in=='desc') $desc = ' selected'; else $asc = ' selected';
return '<select name="direction" class="field10">
<option value="asc"'.$asc.'>'.$m[ascending].'</option>
<option value="desc"'.$desc.'>'.$m[descending].'</option>
</select>';
}

#############################################################################
#############################################################################
#############################################################################

function top_tags() {
global $s,$m;
$search = array("[\]",'&amp;',"&#039;",'"','(',')','-');
$replace = array('&#92;','&',"'",'','','','');
$q = dq("SELECT DISTINCT `word`,COUNT(`word`) AS num_logs FROM `$s[pr]index_suggest` GROUP BY `word` ORDER BY num_logs DESC LIMIT 100",1);
while ($x = mysql_fetch_assoc($q))
{ $font_size = round(35 - ($pocet/2));
  $x1 = trim(str_replace($search,$replace,unhtmlentities($x[word])));
  if ($x1) { $pocet++; if ($pocet<=50) $words_array[] = '<a style="font-size:'.$font_size.'px"; href="'.$s[site_url].'/search.php?phrase='.urlencode($x1).'">'.$x1.'</a>'; }
}
shuffle($words_array);
$a = implode("\n",$words_array);
return $a;
}

#####################################################################################
#####################################################################################
#####################################################################################

function categories_searched($what,$phrases,$search_kind) {
global $s,$m;
$s[columns] = 2;
foreach ($phrases as $k=>$v) if ($v) $w[] = "(title like '%$v%' OR description like '%$v%')";
if (!$w[0]) return false;
$where = "where visible = 1 AND (".implode(" $search_kind ",$w).')';
$q = dq("select * from $s[pr]cats $where",1);
while ($c=mysql_fetch_assoc($q))
{ $c[which_items] = 'Ads';
  $c[url] = category_url($what,$c[n],$c[alias_of],1,$c[rewrite_url]);  
  if ($c[alias_of]) $c[title] = $s[alias_pref].$c[title].$s[alias_after];
  $c[folder_icon] = folder_icon($c[item_created]);
  $categories[] = parse_part('search_result_category.txt',$c);
  $pocet++;
}
if (!$pocet) return false;
$s[columns] = 2;
$rows = ceil($pocet/$s[columns]);
for ($x=$pocet+1;$x<=($rows*$s[column]);$x++)
{ $categories[] .= "<td><font size=1>&nbsp;</td></tr>\n";
  $pocet++;
}
if ($s[in_sort_rows]==1)
{ for ($x=1;$x<=$rows;$x++)
  { $a[categories] .= '<tr>';
    for ($y=($x-1)*$s[columns];$y<=$x*$s[columns]-1;$y++)
    $a[categories] .= $categories[$y];
    $a[categories] .= '</tr>';
  }
}
else
{ for ($x=1;$x<=$rows;$x++)
  { $a[categories] .= '<tr>';
    for ($y=$x-1;$y<=$pocet-1;$y=$y+$rows)
    $a[categories] .= $categories[$y];
    $a[categories] .= '</tr>';
  }
}
$a[colspan] = $s[columns];
return parse_part('search_result_categories.txt',$a);
}

#####################################################################################
#####################################################################################
#####################################################################################

?>