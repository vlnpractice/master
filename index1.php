<?PHP

#################################################
##                                             ##
##               Link Up Gold                  ##
##       http://www.phpwebscripts.com/         ##
##       e-mail: info@phpwebscripts.com        ##
##                                             ##
##                                             ##
##               version:  8.0                 ##
##            copyright (c) 2012               ##
##                                             ##
##  This script is not freeware nor shareware  ##
##    Please do no distribute it by any way    ##
##                                             ##
#################################################

include('./common.php');

get_messages('index.php');
set_time_limit(600);

$q = dq("select * from $s[pr]static where (style = '$s[LUG_style]' or style = '0') and page = 'home'",1);
while ($x = mysql_fetch_assoc($q)) $a[$x[what].'_'.$x[mark]] = $x[html];

foreach ($s[item_types_short] as $k=>$what)
{ $a["s_categories_$what"] = $s["s_categories_$what"];
  $a["s_hits_$what"] = $s["s_hits_$what"];
  $a["s_hits_m_$what"] = $s["s_hits_m_$what"];
  $a["s_rating_$what"] = $s["s_rating_$what"];
}
for ($x=1;$x<=5;$x++) $a["icon_folder_t$x"] = $s["icon_folder_t$x"];
$a[categories_colspan] = $s[ind_column];
$a[site_news] = index_site_news(0); if (!$a[site_news]) { $a[hide_site_news_begin] = '<!--'; $a[hide_site_news_end] = '-->'; }
if ($s[rss_home_page_url]) $a[rss_content] = show_rss_content('c',0,$s[rss_home_page_url],$s[rss_home_page_items]);
if (!trim($a[rss_content])) { $a[hide_rss_content_begin] = '<!--'; $a[hide_rss_content_end] = '-->'; }
$a[hide_home_begin] = '<!--'; $a[hide_home_end] = '-->';
$a = array_merge((array)$m,(array)$a);

$a[this_url] = "$s[site_url]/";
$a[title] = $s[site_name];

foreach ($s[items_types_words] as $what=>$word) if ($s["section_$what"]) $a[search_options] .= '<option value="'.$what.'_0">'.$m[$word].'</option>';

$s[where_fixed_part] = get_where_fixed_part('',0,0,$s[cas]);
$q = dq("select * from $s[pr]videos WHERE $s[where_fixed_part] order by pick desc,created desc limit 50",1);
while ($x = mysql_fetch_assoc($q)) { $item[] = $x; $numbers[] = $x[n]; }
if ($numbers)
{ $a[featured_videos] = get_complete_videos($item,$numbers,'video_a.txt');
}

unset($item,$numbers);
$q = dq("select * from $s[pr]news WHERE $s[where_fixed_part] and pick > 0 order by pick desc,created desc limit 10",1);
while ($x = mysql_fetch_assoc($q)) { $item[] = $x; $numbers[] = $x[n]; }
if ($numbers)
{ $a[featured_news] = get_complete_news($item,$numbers,'new_a.txt');
}
$a[display_a] = $a[display_b] = $a[display_l] = $a[display_v] = $a[display_n] = 'none';
$a["display_$_GET[what]"] = 'block';

if ($s[visit]) { $a[show_simple] = 'none'; $a[show_complete] = 'block'; }
else { $a[show_complete] = 'none'; $a[show_simple] = 'block'; }
$a[home_link] = "javascript:show_top_submenu('1');";
page_from_template('index1.html',$a);

#############################################################################

?>