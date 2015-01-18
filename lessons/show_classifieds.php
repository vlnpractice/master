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

switch ($_GET[action]) {
case 'new'				: classifieds('new');
case 'popular'			: classifieds('popular');
case 'featured'			: classifieds('featured');
case 'most_comments'	: classifieds('most_comments');
case 'category'			: classifieds($_GET);
}

##################################################################################
##################################################################################
##################################################################################

function classifieds($in) {
global $s,$m;
if ($_GET[action]=='category')
{ $where = get_where_fixed_part(0,$in[n],0,$in[area],$s[cas],$in[offer_wanted]).' order by created desc'; 
  $x = get_category_variables($in[n]);
  $title = str_replace('&','&amp;',$m[classifieds_category].' '.$x[title]);
}
elseif ($in=='new')
{ $query = get_new_items($s[l_rss_per_page]);
  $q = dq("select * from $s[pr]ads where $query",1);
  while ($x = mysql_fetch_assoc($q)) { if ($x[created]>$x[t1]) $items["$x[created]-$x[n]"] = $x; else $items["$x[t1]-$x[n]"] = $x; }
  ksort($items); $items = array_reverse($items);
  foreach ($items as $k=>$v)
  { $item = $v;
	$item[url] = get_detail_page_url('ad',$item[n],$item[rewrite_url],$item[category]);
    foreach ($item as $k=>$v) $item[$k] = str_replace('&','&amp;',unreplace_once_html($item[$k]));
    $a[individual_items] .= parse_part('javascript_ad.txt',$item);
  }
  $title = $m[ads_new]; 
}
elseif ($in=='popular') { $where = get_where_fixed_part(0,'',0,'',$s[cas]).' order by clicks_total desc'; $title = $m[ads_popular]; }
elseif ($in=='featured') { $where = get_where_fixed_part(0,'',0,'',$s[cas])." and x_featured_by > '$s[cas]'"; $title = $m[ads_featured]; }
elseif ($in=='most_comments') { $where = get_where_fixed_part(0,'',0,'',$s[cas]).' order by comments desc'; $title = $m[ads_most_comments]; }
else exit;

if (!$a[individual_items])
{ $q = dq("select * from $s[pr]ads where $where limit $s[l_rss_per_page]",1);//echo "select * from $s[pr]ads where $where limit $s[l_rss_per_page]";
  while ($item = mysql_fetch_assoc($q))
  { $item[url] = get_detail_page_url('ad',$item[n],$item[rewrite_url],$item[category]);
    foreach ($item as $k=>$v) $item[$k] = str_replace('&','&amp;',unreplace_once_html($item[$k]));
    $a[individual_items] .= parse_part('javascript_ad.txt',$item);
  }
}
$data = stripslashes('<table border="0" cellpadding="2" cellspacing="0">'.$a[individual_items].'</table>');

$a = explode("\n",$data);
echo "<!--\n";
foreach ($a as $k=>$v) echo "document.write('".addslashes(trim($v))."')\n";;
echo "-->";
exit;
}

##################################################################################
##################################################################################
##################################################################################

?>