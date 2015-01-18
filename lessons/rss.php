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
get_messages('rss.php');
$s[time_plus] = $s[cas]-gmmktime();

switch ($_GET[action]) {
case 'new'				: classifieds('new');
case 'popular'			: classifieds('popular');
case 'featured'			: classifieds('featured');
case 'most_comments'	: classifieds('most_comments');
case 'category'			: classifieds($_GET);
}


function classifieds($in) {
global $s,$m;
if ($_GET[action]=='category')
{ $where = get_where_fixed_part(0,$in[category],0,$in[area],$s[cas],$in[offer_wanted]).' order by created desc'; 
  $x = get_category_variables($in[category]);
  $title = str_replace('&','&amp;',$m[classifieds_category].' '.$x[title]);
}
elseif ($in=='new') { $where = get_where_fixed_part(0,'',0,'',$s[cas]).' order by created desc'; $title = $m[ads_new]; }
elseif ($in=='popular') { $where = get_where_fixed_part(0,'',0,'',$s[cas]).' order by clicks_total desc'; $title = $m[ads_popular]; }
elseif ($in=='featured') { $where = get_where_fixed_part(0,'',0,'',$s[cas])." and x_featured_by > '$s[cas]'"; $title = $m[ads_featured]; }
elseif ($in=='most_comments') { $where = get_where_fixed_part(0,'',0,'',$s[cas]).' order by comments desc'; $title = $m[ads_most_comments]; }
else exit;

if (!$a[individual_items])
{ $q = dq("select * from $s[pr]ads where $where limit $s[l_rss_per_page]",1);
  while ($x = mysql_fetch_assoc($q)) { $items[] = $x; $numbers[] = $x[n]; }
  list($images,$files) = get_item_files('a',$numbers,0);
  
  foreach ($items as $k1=>$item)
  { $item[created] = date('D, j M Y H:i:s',$item[created]+$s[time_plus]);
	$item[url] = get_detail_page_url('ad',$item[n],$item[rewrite_url],$item[category]);
	$item[detail] = strip_tags($item[detail]);
    foreach ($images[$item[n]] as $k1=>$v1) { if (!trim($v1[url])) continue; if (!$item[image]) $item[image] = $v1[url]; }
    if ($item[image]) $item[description] = "<![CDATA[ <img src=\"$item[image]\" />  $item[description] ]]>";
    foreach ($item as $k=>$v) $item[$k] = str_replace('&','&amp;',unreplace_once_html($item[$k]));
    $a[individual_items] .= parse_part('rss_one_ad.txt',$item);
  }
}
$a = array_merge((array)$a,(array)$s);
$a[title] = $title;
header('Content-type: text/xml');
echo stripslashes(parse_part('rss.html',$a));
exit;
}
?>
