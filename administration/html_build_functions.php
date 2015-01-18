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

include("$s[phppath]/styles/_common/messages/common.php");
include("$s[phppath]/styles/_common/messages/index.php");
include("$s[phppath]/styles/_common/messages/links.php");
include("$s[phppath]/styles/_common/messages/articles.php");
include("$s[phppath]/styles/_common/messages/blogs.php");
include("$s[phppath]/styles/_common/messages/videos.php");
include("$s[phppath]/styles/_common/messages/news.php");

foreach ($s[item_types_short] as $k=>$what)
{ $s[vl]["s_categories_$what"] = $s["s_categories_$what"];
  $s[vl]["s_active_$what"] = $s["s_active_$what"];
  $s[vl]["s_hits_$what"] = $s["s_hits_$what"];
  $s[vl]["s_hits_m_$what"] = $s["s_hits_m_$what"];
  $s[vl]["s_rating_$what"] = $s["s_rating_$what"];
}
$s[where_fixed_part] = get_where_fixed_part('',0,0,$s[cas]);

function A_daily_job($showresult) {
global $s;
if ($showresult) $cas1 = time();
increase_print_time(2,1);
new_time('times_d');
count_stats($showresult);
delete_old($showresult);
update_popular($showresult);
if ($s[daily_recount]) { recount_all_links(0); recount_all_articles(0); recount_all_blogs(0); recount_all_videos(0); recount_all_news(0); }
rebuild_static_files($showresult,0);
if ($showresult) echo '<br />Let\'s create pages, please wait ...</b></span><br /><span class="text10">'; else echo '<span class="text10">';
A_read_common_files();
A_rebuild_index($showresult);

if ($s[A_one_item_l]) A_rebuild_detail_pages('l',$showresult);
A_rebuild_categories('l',$showresult);
A_category_special('l','top_rated',$showresult);
A_category_special('l','popular',$showresult);
A_category_special_new_items('l',$showresult);
A_category_special('l','pick',$showresult);

if ($s[A_one_item_a]) A_rebuild_detail_pages('a',$showresult);
A_rebuild_categories('a',$showresult);
A_category_special('a','top_rated',$showresult);
A_category_special('a','popular',$showresult);
A_category_special_new_items('a',$showresult);
A_category_special('a','pick',$showresult);

if ($s[A_one_item_b]) A_rebuild_detail_pages('b',$showresult);
A_rebuild_categories('b',$showresult);
A_category_special('b','top_rated',$showresult);
A_category_special('b','popular',$showresult);
A_category_special_new_items('b',$showresult);
A_category_special('b','pick',$showresult);

if ($s[A_one_item_n]) A_rebuild_detail_pages('v',$showresult);
A_rebuild_categories('v',$showresult);
A_category_special('v','top_rated',$showresult);
A_category_special('v','popular',$showresult);
A_category_special_new_items('v',$showresult);
A_category_special('v','pick',$showresult);

if ($s[A_one_item_v]) A_rebuild_detail_pages('n',$showresult);
A_rebuild_categories('n',$showresult);
A_category_special('n','top_rated',$showresult);
A_category_special('n','popular',$showresult);
A_category_special_new_items('n',$showresult);
A_category_special('n','pick',$showresult);

if ($s[sitemap_location]) create_sitemap($showresult);

if ($showresult)
{ $cas = time() - $cas1;
  echo '</span>'.info_line('All pages have been rebuild<br /><br />All done. Total time: '.$cas.' sec.<br />');
  ift();
}
echo '</span>';
$s[info] = 'Daily rebuild complete<br><br><span class="text10">'.$s[info].'</span>';
}

#####################################################################################
#####################################################################################
#####################################################################################

function A_rebuild_index($showresult) {
global $s,$m;

$q = dq("select * from $s[pr]static where (style = '$s[def_style]' or style = '0') and page = 'home'",1);
while ($x = mysql_fetch_assoc($q)) $a[$x[what].'_'.$x[mark]] = $x[html];

$a[categories_colspan] = $s[ind_column];
for ($x=1;$x<=5;$x++) $a["icon_folder_t$x"] = $s["icon_folder_t$x"];
$a[site_news] = index_site_news(1); if (!$a[site_news]) { $a[hide_site_news_begin] = '<!--'; $a[hide_site_news_end] = '-->'; }
if ($s[rss_home_page_url]) $a[rss_content] = show_rss_content('c',0,$s[rss_home_page_url],$s[rss_home_page_items],1);
if (!trim($a[rss_content])) { $a[hide_rss_content_begin] = '<!--'; $a[hide_rss_content_end] = '-->'; }
$a[show_simple] = 'none'; $a[show_complete] = 'block';
$a[hide_country_begin] = '<!--'; $a[hide_country_end] = '-->';
$a = array_merge((array)$m,(array)$a);

$hotovo = A_page_from_template('index.html',$a);
$file = fopen("$s[phppath]/$s[Aindexhtml]",'w'); fwrite($file,$hotovo); fclose($file);
chmod("$s[phppath]/$s[Aindexhtml]",0666);
$info = 'Home page updated<br />';
if ($showresult) echo $info; else $s[info] .= $info;
}

#####################################################################################
#####################################################################################
#####################################################################################

function A_rebuild_categories($what,$showresult) {
global $s,$m;
$perpage = $s[$what.'_per_page'];
$table = $s[item_types_tables][$what];
$s_sort_pick = $s[$what.'_sort_pick']; $sortby = $s[$what.'_sortby']; $sortby_direct = $s[$what.'_sortby_direct'];
$words = $s[items_types_words][$what]; $word = $s[item_types_words][$what];

$moreover_pages = 0; $moreover_items = $moreover_pages * $perpage;
$main = dq("select * from $s[pr]cats where visible = 1 and use_for = '$what' AND visible = '1' and alias_of = '0'",1);
while ($a = mysql_fetch_assoc($main))
{ unset($sim);
  increase_print_time(2,1);
  $category = $a[n];
  if (($what=='n') AND (($a[last_import]<($s[cas]-($s[n_load_interval_minutes]*60))))) rss_news_import($a,50);
  elseif (($what=='v') AND (($a[last_import]<($s[cas]-($s[v_load_interval_minutes]*60))))) youtube_import($a);

  if (!$a[tmpl_cat]) $a[tmpl_cat] = 'category.html';
  if (!$a[tmpl_one]) $a[tmpl_one] = $word.'_a.txt';
  if ($a[image1]) $a[image] = '<img border="0" src="'.$a[image1].'" alt="'.$a[name].'">';
  else { $a[image] = ''; $a[hide_image_begin] = '<!--'; $a[hide_image_end] = '-->'; }
  $a[similar] = get_more_categories('similar','',$a); if (!$a[similar]) { $a[hide_similar_begin] = '<!--'; $a[hide_similar_end] = '-->'; }
  if (!$a[description]) { $a[hide_description_begin] = '<!--'; $a[hide_description_end] = '-->'; }
  $a[arrow] = category_get_arrow($what,$a[level],$a[parent]);
  $a[subcategories] = get_more_categories('subcategories',$what,$a[n],'',''); if (!$a[subcategories]) { $a[hide_subcategories_begin] = '<!--'; $a[hide_subcategories_end] = '-->'; }
  $x = preparse_ads_in_category($a); $a = array_merge((array)$a,(array)$x);
  if (($what=='n') OR (!$a[rss_url])) $a[rss_content] = ''; else $a[rss_content] = show_rss_content('c',$a[n],$a[rss_url],$a[rss_items],1);
  if (!trim($a[rss_content])) { $a[hide_rss_content_begin] = '<!--'; $a[hide_rss_content_end] = '-->'; }
  $a[adlinks] = get_adlinks($a[n],'',1);
  if (($a[latitude]!=0.0000000) AND ($a[longitude]!=0.0000000)) $a[div_display_map] = 'block'; 
  else $a[div_display_map] = 'none'; 
  if ($s[suggest_category]) 
  { $a[div_display_suggest] = 'block';
    $a[category_suggest_box] = suggest_category_box($a[n],'');
  }
  else $a[div_display_suggest] = 'none';

  $usit = A_user_defined_items_display('c_'.$what,$all_user_items_list,$all_user_items_values,$a[n],'user_item_listing.txt',0,1,0,1);
  $a[user_defined] = $usit[$a[n]];
  foreach ($usit['individual_'.$a[n]] as $k1=>$v1) $a[$k1] = $v1;
  if (!$a[user_defined]) { $a[hide_user_defined_begin] = '<!--'; $a[hide_user_defined_end] = '-->'; }
  if ($a[submithere]) { $a[item_submit_url] = $s[site_url].'/'.$word.'_create.php?c='.$a[n]; $a[item_submit_text] = $m["item_submit_here_$what"]; }
  else { $a[hide_submit_here_begin] = '<!--'; $a[hide_submit_here_end] = '-->'; }
  $a['rss_'.$words.'_category_url'] = "$s[site_url]/rss.php?c=$a[n]";

  $a[title_path] = str_replace('<%','',str_replace('%>',' ',$a[path_text]));
  $a[this_url] = category_url($a[use_for],$a[n],$a[alias_of],$a[name],1,$a[pagename],$a[rewrite_url],'','');
  $a[current_title] = $a[title] = $a[name];
  $a[meta_title] = $a[name];
  $a[meta_description] = $a[m_desc];
  $a[meta_keywords] = $a[m_keyword];
  $a[items_title] = $m[$s[items_types_words][l]];
  //$a[name1] = str_replace(' ','',$a[name]); $a[name2] = str_replace(' ','+',$a[name]);

  $where = 'where '.get_where_fixed_part('',$a[n],'',$s[cas]);
  
  $q = dq("select count(*) from $table $where",1);
  $total = mysql_fetch_row($q); $total = $total[0];
  $pages = ceil($total/$perpage)+$moreover_pages; if ($pages<=1) $pages = 1;
  if ($s_sort_pick) $sort_by = "pick desc,$sortby $sortby_direct"; else $sort_by = "$sortby $sortby_direct";
  if ($what=='l') $sort_by = "sponsored desc,$sort_by";
  for ($page=1;$page<=$pages;$page++)
  { increase_print_time(2,1);
    unset($item,$numbers);
    $from = $perpage * ($page-1);   
    $q = dq("select * from $table $where order by $sort_by limit $from,$perpage",1);
    while ($x = mysql_fetch_assoc($q)) { $item[] = $x; $numbers[] = $x[n]; }
    if ($item) $a[items] = A_get_complete_items($what,$item,$numbers,$a[tmpl_one]); else $a[items] = '';
    if ($pages>1) $a[pages] = A_category_pages_list($what,$category,$total+$moreover_items,$page,$a[name],$a[pagename]);
    $a[total] = $total;
    $hotovo = A_page_from_template($a[tmpl_cat],$a);
    $filename = A_get_category_file_location($what,$category,$a[name],$a[pagename],$page);
    $file = fopen($filename,'w'); fwrite ($file,$hotovo); fclose($file); chmod($filename,0666);
    if ($showresult) echo "$filename<br />".str_repeat(' ',500);flush();
  }
}
$info = "Categories of $words have been updated.<br />";
if ($showresult) echo $info; else $s[info] .= $info;
}

#####################################################################################

function A_category_pages_list($what,$category,$total,$page,$name,$pagename) {
global $s,$m;
$a[items_displayed] = $m[$s[items_types_words][$what].'_in_cat'];
if ($total<=1)
{ if (!$total) $total = 0;
  $a[total] = $total;
  $a[hide_pages_list_begin] = '<!--'; $a[hide_pages_list_end] = '-->';
  $a[hide_sortby_begin] = '<!--'; $a[hide_sortby_end] = '-->';
  return A_parse_part('pages_list.txt',$a);
}
$a[pages_list] = category_pages_list_numbers($what,$category,$name,$pagename,$total,$page,'','','');
if (!$a[pages_list]) { $a[hide_pages_list_begin] = '<!--'; $a[hide_pages_list_end] = '-->'; }

if (!$sort) $sort = $s[$what.'_sortby'];
$sorts = explode(',',$s[$what.'_sort']);
foreach ($sorts as $k=>$v)
{ if ($sort==$v) $sort_options[] = "<span class=\"text10\"><b>$m[$v]</b></span>";
  elseif ($s[category_use_ajax]) $sort_options[] = "<a class=\"link10\" href=\"#content_top\" onclick=\"show_waiting('content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category&what=$what&n=$category&sort=$v&direction=$direction&page=1','content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&what=$what&n=$category&sort=$v&direction=$direction&page=1&total=$total&rewrite=$rewrite_url','pages_div_box');\">$m[$v]</a>";
  else $sort_options[] = '<a class="link10" href="'.category_url($what,$category,0,'',1,'','',$v,$direction).'">'.$m[$v].'</a>';
}
$a[sortby_options] = implode(' - ',$sort_options);

if ($s[category_use_ajax])
{ $a[link_asc] = "href=\"#content_top\" onclick=\"show_waiting('content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category&what=$what&n=$category&sort=$sort&direction=asc&page=1','content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&what=$what&n=$category&sort=$sort&direction=asc&page=1&total=$total&rewrite=$rewrite_url','pages_div_box');\"";
  $a[link_desc] = "href=\"#content_top\" onclick=\"show_waiting('content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category&what=$what&n=$category&sort=$sort&direction=desc&page=1','content_div_box');javascript:parse_ajax_request('','$s[site_url]/ajax.php?action=category_pages_list&what=$what&n=$category&sort=$sort&direction=desc&page=1&total=$total&rewrite=$rewrite_url','pages_div_box');\"";
}
else
{ $a[link_asc] = 'href="'.category_url($what,$category,0,'',1,'','',$sort,'asc').'"';
  $a[link_desc] = 'href="'.category_url($what,$category,0,'',1,'','',$sort,'desc').'"';
}

$a[total] = $total;
return A_parse_part('pages_list.txt',$a);
}

#####################################################################################
#####################################################################################
#####################################################################################

function A_category_special($what,$category_type,$showresult) {
global $s,$m;
$perpage = $s[$what.'_new_page'];
$table = $s[item_types_tables][$what];
$words = $s[items_types_words][$what]; $word = $s[item_types_words][$what];

if ($category_type=='top_rated') { $order_by = 'rating desc,votes desc'; $category_type1 = 'toprated'; }
elseif ($category_type=='popular') { $order_by = 'hits_m desc'; $category_type1 = 'popular'; }
elseif ($category_type=='pick') { $where_special = ' AND pick > 0'; $order_by = 'pick desc'; $category_type1 = 'pick'; }
$a[title] = $a[meta_title] = $m[$category_type.'_'.$words];

$q = dq("select * from $table where $s[where_fixed_part] $where_special order by $order_by limit $perpage",1);
while ($x = mysql_fetch_assoc($q)) { $items[] = $x; $numbers[] = $x[n]; }
if ($numbers) $a[items] = A_get_complete_items($what,$items,$numbers,$word.'_a.txt');
$hotovo = A_page_from_template('category_special.html',$a);
$file_location = A_get_category_file_location($what,$category_type1,'','',1);
$file = fopen($file_location,'w'); fwrite ($file,$hotovo); fclose($file); chmod($file_location,0666);
if ($showresult) echo "$file_location<br />".str_repeat(' ',500);flush();
}

#####################################################################################

function A_category_special_new_items($what,$showresult) {
global $s,$m;
$perpage = $s[$what.'_new_page'];
$table = $s[item_types_tables][$what];
$words = $s[items_types_words][$what]; $word = $s[item_types_words][$what];

if ($query = get_new_items($what,$perpage))
{ $q = dq("select * from $table where $query",1);
  while ($x = mysql_fetch_assoc($q))
  { if ($x[created]>$x[t1]) $items["$x[created]-$x[n]"] = $x;
    else $items["$x[t1]-$x[n]"] = $x;
    $numbers[] = $x[n];
  }
  ksort($items); $items = array_reverse($items);
  $a[items] = A_get_complete_items($what,$items,$numbers,$word.'_a.txt');
}
$a[title] = $a[meta_title] = $m['new_'.$words];
$hotovo = A_page_from_template('category_special.html',$a);
$file_location = A_get_category_file_location($what,'new','','',1);
$file = fopen($file_location,'w'); fwrite ($file,$hotovo); fclose($file); chmod($file_location,0666);
if ($showresult) echo "$file_location<br />".str_repeat(' ',500);flush();
}

#####################################################################################
#####################################################################################
#####################################################################################

function A_get_category_filename($what,$n,$name,$pagename,$page) {
global $s;
// $n = cislo kategorie nebo 'popular' nebo 'new' nebo 'toprated' nebo 'picks'
if (is_numeric($n))
{ if ($page>1) $page = "-$page"; else $page = '';
  if ((!$pagename) OR (!$name))
  { $q = dq("select pagename,name from $s[pr]cats where use_for = '$what' AND n = '$n'",1);
    $r = mysql_fetch_row($q); $pagename = $r[0]; $name = $r[1];
  }
  if (trim($pagename)) $x = trim($pagename);
  else $x = preg_replace('/\s\s+/','',$name);
}
else { $x = $n; $special = 1; } // special categories
$x = $s['Apr_'.$what.'_cat'].$x;
if ($s['Afolder_'.$what.'_cat']) $x = $s['Afolder_'.$what.'_cat'].'/'.$x;
if ($special) return "$x.$s[Ahtml_ex]";
return "$x$page.$s[Ahtml_ex]";
}

#####################################################################################

function A_category_url($what,$n,$name,$pagename,$page) {
global $s;
$x = A_get_category_filename($what,$n,$name,$pagename,$page);
return "$s[site_url]/$x";
}

#####################################################################################

function A_get_category_file_location($what,$n,$name,$pagename,$page) {
global $s;
$x = A_get_category_filename($what,$n,$name,$pagename,$page);
return "$s[phppath]/$x";
}

#####################################################################################
#####################################################################################
#####################################################################################

function A_rebuild_detail_pages($what,$showresult,$n) {
global $s;
$table = $s[item_types_tables][$what];
$word = $s[item_types_words][$what];
$words = $s[items_types_words][$what];
include("$s[phppath]/styles/_common/messages/$word.php");
$q = dq("select n,tmpl_det from $s[pr]cats where use_for = '$what' AND visible = '1' order by n",1);
while ($x = mysql_fetch_assoc($q)) if ($x[tmpl_det]) $templates[$x[n]] = $x[tmpl_det];

if ($n) $s[where_fixed_part] = "n = '$n'";
$q = dq("select * from $table where $s[where_fixed_part] and en_cats = '1 order by n'",1);
while ($a = mysql_fetch_assoc($q))
{ increase_print_time(2,1);
  $usit = A_user_defined_items_display($what,$all_user_items_list,$all_user_items_values,$a[n],'user_item_listing.txt',0,1,0,1);
  $a[user_defined] = $usit[$a[n]]; if (!$a[user_defined]) { $a[hide_usit_begin] = '<!--'; $a[hide_usit_end] = '-->'; }
  foreach ($usit['individual_'.$a[n]] as $k1=>$v1) $a[$k1] = $v1;
  
  list($images,$files) = pictures_files_display_public($what,$a[n],0);
  $images = detail_page_images($what,$images[$a[n]],$a[n],0,$a);
  if ($images[full_size_image]) $a[pictures_gallery] = $images[full_size_image];
  if ($images[pictures_gallery]) { $a[pictures_gallery] = $images[pictures_gallery]; $a[previews_width] = $images[previews_width]; }

  $a[title_no_tag] = strip_tags($a[title]);
  $a[this_url] = get_detail_page_url($what,$a[n],$a[rewrite_url],$a[category],1);
  $a[share_it] = parse_part('share_it.txt',$a);
  $a[icons] = get_icons_for_item($what,$a,$bookmarks[$a[n]],'&nbsp;');
  $a[created] = datum($a[created],0);
  if ($a[updated]) $a[updated] = datum($a[updated],0); else { $a[hide_updated_begin] = '<!--'; $a[hide_updated_end] = '-->'; }
  $a[rateicon] = get_rateicon($a[rating]);
  $x = list_of_categories_for_item($what,0,$a[c],'<br />',0); $a = array_merge((array)$a,(array)$x);
  $a[tags] = tags_for_item($what,$a[c],$a[keywords]); if (!$a[tags]) { $a[hide_tags_begin] = '<!--'; $a[hide_tags_end] = '-->'; }
  $a[show_comments] = comments_get($what,$a[n],0);// if (!$a[show_comments]) { $a[hide_comments_begin] = '<!--'; $a[hide_comments_end] = '-->'; }
  if ((!$a[email]) OR ($a[email]==$s[mail])) { $a[hide_contact_form_begin] = '<!--'; $a[hide_contact_form_end] = '-->'; }
  if ($s[message_owner_captcha]) $a[contact_form_field_captcha] = parse_part('form_captcha_test.txt',$b);
  $x = previous_next_links($what,$in[c],$a[c],$a[n]); $a = array_merge((array)$a,(array)$x);
  if ($s[det_br]) $a[detail] = str_replace("\n",'<br />',$a[detail]);
  $a[detail] = str_replace('&#039;',"'",$a[detail]);
  if (strstr($a[map],'_gmok_')) $a[div_display_map] = 'block'; else $a[div_display_map] = 'none'; 
  if ($a[rss_url]) $a[rss_content] = show_rss_content($what,$a[n],$a[rss_url],10); if (!trim($a[rss_content])) { $a[hide_rss_content_begin] = '<!--'; $a[hide_rss_content_end] = '-->'; }
  if (($s[allow_claim_l]) AND ($what=='l') AND (!$a[owner])) $a[claim_listing_box] = claim_listing_box($what,$a[n]);
  else { $a[hide_claim_listing_begin] = '<!--'; $a[hide_claim_listing_end] = '-->'; }
  
  $a[tell_friend_box] = tell_friend_box($what,$a[n],'',1);
  $a[enter_comment_box] = enter_comment_box($what,$a[n]);
  $a[notes_edit_box] = notes_edit_box($what,$a[n]);
  $a[contact_box] = contact_box($what,$a[n],'',1);
  $s[search_display] = $what;

  if ($what=='l')
  { if ($s[l_thumbnail_url]) { $x = parse_url($a[url]); $a[thumbnail] = '<a target="_blank" href="'.$a[url].'"  OnClick="track_image_'.$a[n].'.src=\''.$s[site_url].'/track_click.php?free_link='.$a[n].'\';"><img border="0" src="'.str_replace('#%domain%#',$x[host],$s[l_thumbnail_url]).'" alt="'.$a[title_no_tag].'" style="float: left; margin: 0px 5px 0px 0px;"></a>'; }
    if (strstr($a[detail],'<complete_link>')) $a[link] = $a[detail];
    elseif ($a[url]) $a[link] = '<a target="'.$s[rl_open_window].'" href="'.$a[url].'"  OnClick="track_image_'.$a[n].'.src=\''.$s[site_url].'/track_click.php?free_link='.$a[n].'\';"><h2>'.$a[title].'</h2></a>';
    else $a[link] = '<h2>'.$a[title].'</h2>';
    $a[report_box] = report_box($what,$a[n],'',1);
  }
  
  $a[video_code] = youtube_player($a[youtube_id],$a[video_code]);
  $a[more_items] = more_items_of_owner($what,$a[email],0); if (!$a[more_items]) { $a[hide_more_items_begin] = '<!--'; $a[hide_more_items_end] = '-->'; }
  if (!trim($a[rss_content])) { $a[hide_rss_content_begin] = '<!--'; $a[hide_rss_content_end] = '-->'; }
  $a[hide_favorites_begin] = '<!--'; $a[hide_favorites_end] = '-->'; $a[hide_edit_notes_begin] = '<!--'; $a[hide_edit_notes_end] = '-->'; $a[hide_notes_begin] = '<!--'; $a[hide_notes_end] = '-->';
  $a[hide_notes_begin] = '<!--'; $a[hide_notes_end] = '-->';
  if ($in[category_name]) { $a[category_name] = $in[category_name]; $a[category] = $in[c]; }
  $a[meta_title] = $a[title]; $a[meta_description] = $a[description]; $a[meta_keywords] = str_replace("\n",', ',$a[keywords]);
  $template = A_get_detail_template_name($what,$templates,$a[c]);
  //echo $template;exit;
  
  if ($what=='l')
  { if ($a[sponsored])
    { $x = get_link_adv_variables($a[n]);
      $is_advertising = get_link_advertising_status($x);
      if (($x[c_dynamic_now]) AND ($x[c_dynamic_price]!=0)) $a[price] = $x[c_dynamic_price];
    }
    if (!$a[price]) { $a[hide_click_price_begin] = '<!--'; $a[hide_click_price_end] = '-->'; }
    if ($is_advertising) $template = 'link_details_advertising.html';
  }
  
  if (($a[email]) AND ($a[email]!=$s[mail])) $a[more_items] = more_items_of_owner($what,$a[email],0); if (!$a[more_items]) { $a[hide_more_items_begin] = '<!--'; $a[hide_more_items_end] = '-->'; $a[hide_more_items1_begin] = '<!--'; $a[hide_more_items1_end] = '-->'; }
  if ($a[owner])
  { $user_vars = get_user_variables($a[owner]);
    $files_pictures = get_item_files_pictures('u',$user_vars[n],0);
    if ($files_pictures[image_url][$user_vars[n]][1]) 
    { $big_file = preg_replace("/\/$user_vars[n]-/","/$user_vars[n]-big-",$files_pictures[image_url][$user_vars[n]][1]);
      $a[owner_image] = image_preview_code(1,$files_pictures[image_url][$user_vars[n]][1],$big_file);
    }
    else $a[owner_image] = '<img border="0" src="'.$s[site_url].'/images/user_image.png">';
    $a[owner_nick] = $user_vars[nick];
    if ($user_vars[url]) { if (!$user_vars[site_title]) $user_vars[site_title] = $user_vars[url]; $a[owner_website] = '<a target="_blank" href="'.$user_vars[url].'">'.$user_vars[site_title].'</a>'; }
    else $a[owner_website] = $m[none];
    $a[owner_link] = get_detail_page_url('u',$user_vars[n],$user_vars[nick]);
  }
  if (!$user_vars[n]) { $a[hide_owner_begin] = '<!--'; $a[hide_owner_end] = '-->'; unset($a[hide_more_items1_begin],$a[hide_more_items1_end]); }
  $x = explode(' ',str_replace('_','',$a[c])); $b = get_category_variables($x[0]); $x = preparse_ads_in_category($b); $a = array_merge((array)$a,(array)$x);

  if (($what=='a') OR ($what=='b') OR ($what=='n'))
  { $t = explode('<new_page>',$a[text]);
    foreach ($t as $k=>$v)
    { $a[text] = $v;
      if ((count($t))>1)
      { unset($pages_list);
        for ($x=1;$x<=count($t);$x++)
        { if ($x==$k+1) $pages_list .= "&nbsp;$x"; 
          else $pages_list .= '&nbsp;<a href="'.A_get_detail_url($what,$a[n],$a[rewrite_url],$x).'">'.$x.'</a>';
        }
        $a[pages_list] = "<br /><b>$m[pages_list]</b>&nbsp;$pages_list<br />";
      }
      $hotovo = A_page_from_template($template,$a);
      $file_location = A_get_detail_file_location($what,$a[n],$a[rewrite_url],$k+1);
      $file = fopen($file_location,'w');
      fwrite ($file,$hotovo); fclose($file);
      chmod($file_location,0666);
      if ($showresult) echo "$file_location<br />".str_repeat(' ',500);flush();
    }
  }
  else
  { $hotovo = A_page_from_template($template,$a);
    $file_location = A_get_detail_file_location($what,$a[n],$a[rewrite_url],$k+1);
    $file = fopen($file_location,'w');
    fwrite ($file,$hotovo); fclose($file);
    chmod($file_location,0666);
    if ($showresult) echo "$file_location<br />".str_repeat(' ',500);flush();
    //echo "$file_location<br />".str_repeat(' ',500);flush();exit;
  }
}
if ($n) { ih(); echo info_line('Selected page has been updated.<br><br>','<a href="javascript:history.back(-1)">Back</a>'); ift(); }
$info = "Pages of $words have been updated.<br />";
if ($showresult) echo $info; else $s[info] .= $info;
}

#####################################################################################

function A_get_detail_template_name($what,$templates,$c) {
global $s;
$cats = explode(' ',str_replace('_','',$c));
foreach ($cats as $k=>$v) if ($templates[$v]) return $templates[$v];
return $s[item_types_words][$what].'_details.html';
}

#####################################################################################
#####################################################################################
#####################################################################################

function A_get_detail_filename($what,$n,$title,$page) {
global $s;
if (!$rewrite_url)
{ $table = $s[item_types_tables][$what];
  $q = dq("select rewrite_url from $table where n = '$n'",1);
  $x = mysql_fetch_assoc($q); $rewrite_url = $x[rewrite_url];
}
$a = "$rewrite_url-$n";
if ($page>1) $a .= "-$page"; // articles more than one page
$a = $s['Apr_'.$what.'_detail'].$a;
if ($s['Afolder_'.$what.'_detail']) $a = $s['Afolder_'.$what.'_detail'].'/'.$a;
return "$a.$s[Ahtml_ex]";
}

#####################################################################################

function A_get_detail_url($what,$n,$title,$page) {
global $s;
$x = A_get_detail_filename($what,$n,$title,$page);
return "$s[site_url]/$x";
}

#####################################################################################

function A_get_detail_file_location($what,$n,$title,$page) {
global $s;
$x = A_get_detail_filename($what,$n,$title,$page);
return "$s[phppath]/$x";
}

#####################################################################################
#####################################################################################
#####################################################################################

function A_page_from_template($template,$vl) {
global $s,$m;

if (!is_array($vl)) $vl = array();
$vl = array_merge($vl,get_common_variables());
$vl[css_style] = $s[LUG_style] = $s[def_style];
foreach ($s[item_types_short] as $k=>$what) $vl["s_active_$what"] = $s["s_active_$what"];
$vl[hide_div_user] = ' style="display:none"';
$vl[hide_div_static] = ' style="display:none"';

$vl[selected_menu] = 0;
$vl[tell_friend_site_box] = tell_friend_box('',0);
$vl[contact_site_box] = contact_box('',0);
$vl[user_login_form] = user_login_form();
$vl[hide_for_no_user_begin] = '<!--'; $vl[hide_for_no_user_end] = '-->';
$vl[hide_div_no_user] = ' style="display:none;"';
$vl[home_link] = "$s[site_url]/";

if (!$vl[meta_title]) $vl[meta_title] = $s[site_name];
if ($vl[meta_description]) $vl[meta_description] = substr(str_replace("\r",'',str_replace("\n",' ',str_replace('&#039;',"'",strip_tags($vl[meta_description])))),0,200); else $vl[meta_description] = $s[site_description];
if ($vl[meta_keywords]) $vl[meta_keywords] = substr(str_replace("\r",'',str_replace("\n",' ',str_replace('&#039;',"'",strip_tags($vl[meta_keywords])))),0,200); else $vl[meta_keywords] = $s[site_keywords];

if ($s[head_pagination]) $vl[head_pagination] = $s[head_pagination];
if ($s[show_qr])
{ if ($vl[this_url]) $qrurl = $vl[this_url]; else $qrurl = "$s[site_url]/";
  $vl[qrimage] = '<img border="0" src="'.$s[site_url].'/qrimage.php?url='.urlencode($qrurl).'">';
}

foreach ($s[items_types_words] as $k=>$v)
{ if (!$vl['rss_'.$v.'_category_url']) { $vl['hide_rss_'.$v.'_category_begin'] = '<!--'; $vl['hide_rss_'.$v.'_category_end'] = '-->'; }
  $vl["search_display_$k"] = 'none';
}
$vl["search_display_all"] = $vl["search_display_google"] = 'none';
if (!$s[search_display]) $s[search_display] = 'all';
$vl["search_display_$s[search_display]"] = 'block';

$template = $s[phppath].'/styles/_common/templates/'.$template;
$vl[head] = A_parse_variables_in_template($s[vl][head2],$vl);
for ($x=1;$x<=$s[in_templates];$x++) $vl["in$x"] = A_parse_variables_in_template($s[vl]["in$x"],str_replace('#_','#%',str_replace('_#','%#',$vl)));

$line = $s[vl][head1].implode('',file($template)).$s[vl][footer];
$line = A_parse_variables_in_template($line,$vl);
$line = str_replace("$s[site_url]/index.php","$s[site_url]/$s[Aindexhtml]",$line);
return $line;
}

#####################################################################################

function A_parse_variables_in_template($line,$vl) {
$line = A_replace_dynamic_urls($line);
foreach ($s[item_types_short] as $k=>$v) if (!$s["section_$v"]) $line = preg_replace('/#%begin_'.$v.'%#(.*)#%end_'.$v.'%#/eisU','',$line);
return parse_variables($line,$vl);
}

#####################################################################################

function A_parse_part($template,$vl) {
global $s,$m;
return parse_part($template,$vl);
}

#####################################################################################
#####################################################################################
#####################################################################################

function A_get_complete_items($what,$items,$numbers,$in_template) {
global $s;
$width = floor(100/$s[$what.'_columns']);
$usit = A_user_defined_items_display($what,$all_user_items_list,$all_user_items_values,$numbers,'user_item_listing.txt',0,1,0,1);
list($images,$files) = pictures_files_display_public($what,$numbers,0);
foreach ($items as $k => $a)
{ $a[user_defined] = $usit[$a[n]];
  foreach ($usit['individual_'.$a[n]] as $k1=>$v1) $a[$k1] = $v1;
  $a[title_no_tag] = strip_tags($a[title]);
  
  if ($a[youtube_thumbnail]) { $a[pictures] = 1; $a[image_1] = $a[image_1_big] = $a[youtube_thumbnail]; }
  elseif ($a[picture]) 
  { $picture1_path = str_replace($s[site_url],$s[phppath],$a[picture]);
	if (file_exists($picture1_path))
	{ $a[image_1] = $a[picture];
	  if (file_exists(preg_replace("/\/$a[n]-/","/$a[n]-big-",$picture1_path))) $a[image_1_big] = preg_replace("/\/$a[n]-/","/$a[n]-big-",$a[picture]);
      else $a[image_1_big] = $a[picture];
      $a[pictures]++;
    }
  }
  
  if ((!$a[pictures]) AND ($s[l_thumbnail_url])) { $x = parse_url($a[url]); $a[pictures] = 1; $a[image_1] = $a[image_1_big] = str_replace('#%domain%#',$x[host],$s[l_thumbnail_url]); }
  if (!$a[pictures]) { $a[hide_pictures_begin] = '<!--'; $a[hide_pictures_end] = '-->'; }
  $a[video_code] = youtube_player($a[youtube_id],$a[video_code]);

  $a[icons] = get_icons_for_item($what,$a,$bookmarks[$a[n]]);
  $a[item_details_url] = get_detail_page_url($what,$a[n],$a[rewrite_url],$a[category],1);
  if ($a[t1]>$a[created]) $a[created] = datum($a[t1],0); else $a[created] = datum($a[created],0);
  if ($a[updated]) $a[updated] = datum($a[updated],0); else { $a[hide_updated_begin] = '<!--'; $a[hide_updated_end] = '-->'; }
  $a[rateicon] = get_rateicon($a[rating]);
  if (!$a[description]) { $a[description] = strip_tags($a[text],'<img>'); $a[text] = ''; }
  if ($s[det_br]) $a[detail] = str_replace("\n",'<br />',$a[detail]);
  if (($s[search_highlight]) AND ($s[highlight])) { $a[title] = highlight_words('',$a[title]); $a[description] = highlight_words('',$a[description]); $a[detail] = highlight_words('',$a[detail]); $a[display_url] = highlight_words('',$a[url]); }
  //$x = list_of_categories_for_item($what,0,$a[c],'<br />',0); $a[categories] = $x[categories]; $a[categories_incl] = $x[categories_incl]; $a[categories_names] = $x[categories_names];
  if (trim($a[keywords])) $a[tags] = tags_for_item($what,0,$a[keywords]); else { $a[hide_tags_begin] = '<!--'; $a[hide_tags_end] = '-->'; }
  $a[report_box] = report_box($what,$a[n]);
  $a[tell_friend_box] = tell_friend_box($what,$a[n]);
  $a[enter_comment_box] = enter_comment_box($what,$a[n]);
  $a[notes_style_display] = 'none';
  if ($what=='l')
  { if ((!$a[pictures]) AND ($s[l_thumbnail_url])) { $x = parse_url($a[url]); $a[pictures] = 1; $a[image_1] = str_replace('#%domain%#',$x[host],$s[l_thumbnail_url]); }
    if (strstr($a[detail],'<complete_link>')) $a[link] = $a[detail];
    elseif ($a[url]) $a[link] = '<a target="'.$s[rl_open_window].'" href="'.$a[url].'" OnClick="track_image_'.$a[n].'.src=\''.$s[site_url].'/track_click.php?free_link='.$a[n].'\';"><h2>'.$a[title].'</h2></a>';
    else $a[link] = '<h2>'.$a[title].'</h2>';
    unset ($is_advertising);
    if ($a[sponsored])
    { $link_adv = get_link_adv_variables($a[n]);
      $is_advertising = get_link_advertising_status($link_adv);
      if (($link_adv[c_dynamic_now]) AND ($link_adv[c_dynamic_price]!=0)) $a[price] = $link_adv[c_dynamic_price];
    }
    if (!$a[price]) { $a[hide_click_price_begin] = '<!--'; $a[hide_click_price_end] = '-->'; }
    if (($is_advertising) AND ($in_template!='link_c.txt')) $template = 'link_advertising.txt';
  }
  $template = $in_template;
  if (($is_advertising) AND ($in_template!='link_c.txt')) $template = 'link_advertising.txt';
  $complete_array[] = '<td valign="top" width="'.$width.'%">'.A_parse_part($template,$a).'</td>';
  $pocet++;
}
$rows = ceil($pocet/$s[$what.'_columns']);
for ($x=$pocet+1;$x<=($rows*$s[$what.'_columns']);$x++)
{ $complete_array[] = '<td>&nbsp;</td>';
  $pocet++;
}
for ($x=1;$x<=$rows;$x++)
{ $complete .= '<tr>';
  for ($y=($x-1)*$s[$what.'_columns'];$y<=$x*$s[$what.'_columns']-1;$y++)
  $complete .= $complete_array[$y];
  $complete .= '</tr>';
}
return $complete;
}

#####################################################################################
#####################################################################################
#####################################################################################

function A_read_common_files() {
global $s;
include("$s[phppath]/data/info.php");
$q = dq("select * from $s[pr]users order by n desc limit 1",1);
$last_user = mysql_fetch_assoc($q);
if ($last_user[nick]) $vl[last_user_name] = $last_user[nick]; else $vl[last_user_name] = $last_user[name]; 
$vl[last_user_url] = get_detail_page_url('u',$last_user[n],$last_user[nick]);
$vl[total_users] = $s[s_users];
$q = dq("select * from $s[pr]static where (style = '$s[def_style]' or style = '0') and page = '0'",1);
while ($x = mysql_fetch_assoc($q)) $s[vl][$x[mark]] = $x[html];
$s[vl][head1] = implode('',file("$s[phppath]/styles/_common/templates/_head1.txt"));
$s[vl][head2] = implode('',file("$s[phppath]/styles/_common/templates/_head2.txt"));
$s[vl][footer] = str_replace('</body',$info.'</body',implode('',file("$s[phppath]/styles/_common/templates/_footer.txt")));
foreach ($s[vl] as $file_var => $line) { foreach ($vl as $k=>$v) $line = str_replace("#%$k%#",$v,$line); $s[vl][$file_var] = $line; }
}

#####################################################################################

function A_replace_dynamic_urls($line) {
global $s;
unset($s[items_types_words][u]);
if (!$s[replace_urls][from]) foreach ($s[items_types_words] as $what=>$v)
{ $s[replace_urls][from][] = "#%site_url%#/$v.php?action=popular"; $s[replace_urls][to][] = A_category_url($what,'popular','','',1);
  $s[replace_urls][from][] = "#%site_url%#/$v.php?action=new"; $s[replace_urls][to][] = A_category_url($what,'new','','',1);
  $s[replace_urls][from][] = "#%site_url%#/$v.php?action=pick"; $s[replace_urls][to][] = A_category_url($what,'pick','','',1);
  $s[replace_urls][from][] = "#%site_url%#/$v.php?action=top_rated"; $s[replace_urls][to][] = A_category_url($what,'toprated','','',1);
}
foreach ($s[replace_urls][from] as $k=>$v) $line = str_replace($v,$s[replace_urls][to][$k],$line);
return $line;
}

##################################################################################

function A_user_defined_items_display($use_for,$all_user_items_list,$all_user_items_values,$n,$template,$email,$only_with_value,$only_forms,$only_pages) {
global $s,$m;
if (($use_for=='l') OR ($use_for=='l_q') OR ($use_for=='l_w') OR ($use_for=='l_a')) $use_for1 = 'l';
elseif (($use_for=='a') OR ($use_for=='a_q') OR ($use_for=='a_w')) $use_for1 = 'a';
elseif (($use_for=='b') OR ($use_for=='b_q') OR ($use_for=='b_w')) $use_for1 = 'b';
elseif (($use_for=='n') OR ($use_for=='n_q') OR ($use_for=='n_w')) $use_for1 = 'n';
elseif (($use_for=='v') OR ($use_for=='v_q') OR ($use_for=='v_w')) $use_for1 = 'v';
elseif (strstr($use_for,'c_')) $use_for1 = $use_for;
else return false;
if (!$s[filter_usit]) $only_with_value = 0;
if (is_array($n)) $numbers = $n; else $numbers[0] = $n;
if (!$all_user_items_list)
{ $q = dq("select * from $s[pr]usit_list where use_for = '$use_for1' order by rank",1);
  while ($x = mysql_fetch_assoc($q)) $all_user_items_list[] = $x;
}
if (!$all_user_items_values) $all_user_items_values = get_all_user_items_values($use_for1);

$query = 'AND '.my_implode('n','OR',$numbers);
$q = dq("select * from $s[pr]usit_values where use_for = '$use_for' $query",1);
while ($x = mysql_fetch_assoc($q))
{ $b[$x[n]][$x[item_n]][code] = $x[value_code];
  $b[$x[n]][$x[item_n]][text] = $x[value_text];
}
foreach ($numbers as $k1=>$v1)
{ foreach ($all_user_items_list as $k=>$v)
  { $filter_now = $only_with_value;
	if ($v[kind]=='checkbox')
    { if ($only_forms) { if ($b[$v1][$v[item_n]][code]) $data[value] = $m[yes]; else $data[value] = $m[no]; }
      else
	  { if ($b[$v1][$v[item_n]][code]) $data[value] = $v[description]; else $data[value] = '';
        $v[description] = '';
        $filter_now = 1;
      }
    }
    else $data[value] = $b[$v1][$v[item_n]][text];
    if ((!$data[value]) AND (!is_numeric($data[value]))) { if ($filter_now) continue; else $data[value] = $m[na]; }
    $data[name] = $v[description];
    if ($email)
    { $a[$v1] .= parse_part($template,$data,1);
      $a['individual_'.$v1]['user_item_'.$v[item_n]] = parse_part($template,$data,1);
    }
    else
    { if (($s[search_highlight]) AND ($s[highlight])) $data[value] = highlight_words('',$data[value]);
      if ($s[highlight_usit][$v[item_n]]) $data[value] = highlight_words($s[highlight_usit][$v[item_n]],$data[value]);
      $c[$v[item_n]][$v1] = A_parse_part($template,$data);
      $a['individual_'.$v1]['user_item_'.$v[item_n]] = A_parse_part($template,$data);
      $a['individual_'.$v1]['user_item_value_'.$v[item_n]] = $data[value];
      $a['individual_'.$v1]['user_item_name_'.$v[item_n]] = $data[name];
    }
  }
}
foreach ($all_user_items_list as $k=>$v)
{ if (($only_forms) AND (!$v[visible_forms])) unset($c[$v[item_n]]);
  if (($only_pages) AND (!$v[visible_pages])) unset($c[$v[item_n]]);
}
foreach ($c as $k=>$v) foreach ($v as $k1=>$v1) $a[$k1] .= $v1;
return $a;
}

#####################################################################################
#####################################################################################
#####################################################################################

function A_delete_static_files($directory) {
global $s;
$pocet = 0;
if ($directory=='main') $directory = $s[phppath];
else $directory = $s[phppath].'/'.$directory;
$dr = opendir($directory);
rewinddir($dr);
while ($q = readdir($dr)) { if (strstr($q,".$s[Ahtml_ex]")) { unlink("$directory/$q"); $pocet++; } }
closedir ($dr);
return 'Files deleted: '.$pocet;
}

#####################################################################################
#####################################################################################
#####################################################################################

?>