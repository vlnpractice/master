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
$s[selected_menu] = 3;
get_messages('category.php');
if (!$s[new_page]) $s[new_page] = 25;

$_GET = replace_array_text($_GET);
if (!$_GET[action]) $_GET[action] = $_GET[vars];
else $_GET[action] = str_replace('.html','',$_GET[action]);

switch ($_GET[action]) {
case 'new'			: new_ads();
case 'popular'		: popular_ads();
case 'most_comments': most_commented_ads();
case 'featured'		: featured_ads();
case 'galleries'	: galleries();
}

#############################################################################
#############################################################################
#############################################################################

function most_commented_ads() {
global $s,$m;
$where = get_where_fixed_part(0,0,0,0,$s[cas],'');
$q = dq("select * from $s[pr]ads where $where and comments > 0 order by comments desc limit $s[new_page]",0);
while ($x = mysql_fetch_assoc($q)) { $item[] = $x; $numbers[] = $x[n]; }
if ($numbers) $a[ads] = get_complete_ads($item,$numbers,'ad_a.txt');
$a[meta_title] = $a[title] = $m[most_commented_ads];
page_from_template('category_extra.html',$a);
}

#############################################################################

function new_ads() {
global $s,$m;
if ($query = get_new_items($s[new_page]))
{ $q = dq("select * from $s[pr]ads where $query",1);
  while ($x = mysql_fetch_assoc($q))
  { if ($x[created]>$x[t1]) $ads["$x[created]-$x[n]"] = $x;
    else $ads["$x[t1]-$x[n]"] = $x;
    $numbers[] = $x[n];
  }
  ksort($ads); $ads = array_reverse($ads);
  $a[ads] = get_complete_ads($ads,$numbers,'ad_a.txt');
}
$a[meta_title] = $a[title] = $m[new_ads];
page_from_template('category_extra.html',$a);
}

#############################################################################

function popular_ads() {
global $s,$m;
if (is_numeric($_GET[area])) $area = $_GET[area]; else $area = 0;
$where = get_where_fixed_part(0,0,0,$area,$s[cas],'');
$q = dq("select * from $s[pr]ads where $where order by clicks_total desc limit $s[new_page]",0);
while ($x = mysql_fetch_assoc($q)) { $item[] = $x; $numbers[] = $x[n]; }
if ($item) $a[ads] = get_complete_ads($item,$numbers,'ad_a.txt');
$a[meta_title] = $a[title] = $m[popular_ads];
page_from_template('category_extra.html',$a);
}

#############################################################################

function featured_ads() {
global $s,$m;
$where = get_where_fixed_part(0,0,0,0,$s[cas],'');
$q = dq("select *,MD5(RAND()) AS m from $s[pr]ads where $where and x_featured_by > '$s[cas]' order by m desc limit $s[new_page]",0);
while ($x = mysql_fetch_assoc($q)) { $item[] = $x; $numbers[] = $x[n]; }
if ($item) $a[ads] = get_complete_ads($item,$numbers,'ad_a.txt');
$a[meta_title] = $a[title] = $m[featured_ads];
page_from_template('category_extra.html',$a);
}

#############################################################################

function galleries() {
global $s,$m;
$where = get_where_fixed_part(0,0,0,0,$s[cas],'');
$q = dq("select *,MD5(RAND()) AS m from $s[pr]ads where $where and x_featured_gallery_by > '$s[cas]' order by m desc limit $s[new_page]",0);
while ($x = mysql_fetch_assoc($q)) { $item[] = $x; $numbers[] = $x[n]; }
if ($item) $a[ads] = get_complete_ads_simple($item,$numbers,'ad_simple.txt');
$a[meta_title] = $a[title] = $m[featured_galleries];
page_from_template('category_extra.html',$a);
}

#############################################################################
#############################################################################
#############################################################################

?>