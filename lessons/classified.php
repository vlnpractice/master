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
if (!$not_include)
{ include('./common.php');
  include("$s[phppath]/data/data_forms.php");
  if (substr($_GET[vars],0,11)=='ad_details-') $n = str_replace('ad_details-','',$_GET[vars]);
  elseif ($_GET[n]) $n = $_GET[n];
  else $n = $ad_n;
  if (!is_numeric($n))
  { $where = get_where_fixed_part(0,0,0,0,$s[cas]);
    $q = dq("select n,MD5(RAND()) AS m from $s[pr]ads where $where order by m limit 1",1);
	$x = mysql_fetch_row($q); $n = $x[0];
  }
  show_ad_details($n);
}
else unset($s[A_option]);

##################################################################################

function show_ad_details($ad_n,$queue) {
global $s,$m;
if (($s[A_option]=='rewrite') AND (!$_GET[vars])) { $url = get_detail_page_url('',$_GET[n],'',0); if (!$url) $url = "$s[site_url]/"; header("HTTP/1.1 301 Moved Permanently"); header ("Location: $url"); exit; }
get_messages('classified.php');
if (!$queue) $queue = 0;
if (!is_numeric($ad_n)) header("Location: $s[site_url]");
$a = get_ad_variables($ad_n,$queue); if ((!$a[n]) AND ($queue)) $a = get_ad_variables($ad_n,0);
if (!$a[n]) problem($m[ad_no_exists]);
$c = get_bigboss_category($a[c]);
$usit = usit_display($c,$a[n],'user_item_listing.txt',0,1,$queue);
if (!$usit) { $a[hide_usit_begin] = '<!--'; $a[hide_usit_end] = '-->'; }
$a[user_defined] = $usit[$a[n]]; 

foreach ($usit['individual_'.$a[n]] as $k1=>$v1) $a[$k1] = $v1;
list($images,$files,$videos) = get_item_files('a',$a[n],$queue);
if ($a[x_pictures_by]<$s[cas]) $allowed_pictures = $s[a_max_pictures]; else $allowed_pictures = 1000; if (count($images[$a[n]]>$allowed_pictures)) $images[$a[n]] = array_splice($images[$a[n]],0,$allowed_pictures);
if ($a[x_files_by]<$s[cas]) $allowed_files = $s[max_files]; else $allowed_files = 1000;
$images = detail_page_images($images[$a[n]],$a[n],0);
if ($images[full_size_image]) $a[pictures_gallery] = $images[full_size_image];
if ($images[pictures_gallery]) { $a[pictures_gallery] = $images[pictures_gallery]; $a[previews_width] = $images[previews_width]; }

foreach ($files[$a[n]] as $k=>$v)
{ if (!$a[file_1]) $a[file_1] = parse_part('ad_one_file.txt',$v);
  $v[icon] = $s[file_icons][$v[extension]]; if ($v[icon]) $v[icon] = '<img border="0" src="'.$s[site_url].'/images/file_icons/'.$v[icon].'">';
  $a[files]++;
  if ($a[files]>$allowed_files) { $a[files] = $a[files] - 1; break; }
  $a[all_files] .= parse_part('ad_one_file.txt',$v);
}
/*foreach ($videos[$a[n]] as $k=>$v)
{ $video_player = video_player($v[url],400,350,'false'); if (!$video_player) continue;
  $a[videos]++;
  if ($a[videos]==1) $a[all_videos] = '<div id="video_div_'.$a[videos].'" style="display:block;">'.$video_player.'</div>';
  else $a[all_videos] .= '<div id="video_div_'.$a[videos].'" style="display:none;">'.$video_player.'</div>';
  $a[list_videos] .= '<a onclick="#%x%# show_hide_div(1,document.getElementById(\'video_div_'.$a[videos].'\'));" href="#video">'.$v[description].'</a><br>';
  $hide_all .= 'show_hide_div(0,document.getElementById(\'video_div_'.$a[videos].'\'));';
}*/

if ($a[youtube_video])
{ $a[youtube_video] = get_youtube_video_code($a[youtube_video]);
  $a[videos]++;
  $a[all_videos] = $a[youtube_video];
  /*
  if ($a[videos]==1) $a[all_videos] = '<div id="video_div_'.$a[videos].'" style="display:block;">'.$video_player.'</div>';
  else $a[all_videos] .= '<div id="video_div_'.$a[videos].'" style="display:none;">'.$video_player.'</div>';
  $a[list_videos] .= '<a onclick="#%x%# show_hide_div(1,document.getElementById(\'video_div_'.$a[videos].'\'));" href="#video">'.$m[movie_from_youtube].'</a><br>';
  $hide_all .= 'show_hide_div(0,document.getElementById(\'video_div_'.$a[videos].'\'));';
  */
}
if ($a[videos]==1) unset($a[list_videos]); else $a[list_videos] = str_replace('#%x%#',$hide_all,$a[list_videos]);




if (!$a[files]) { $a[hide_files_begin] = '<!--'; $a[hide_files_end] = '-->'; }
if (!$a[videos]) { $a[hide_videos_begin] = '<!--'; $a[hide_videos_end] = '-->'; }
if (!$a[url]) { $a[hide_url_begin] = '<!--'; $a[hide_url_end] = '-->'; }

if ($a[offer_wanted]) $a[ad_type] = $m[$a[offer_wanted]]; else { $a[hide_ad_type_begin] = '<!--'; $a[hide_ad_type_end] = '-->'; }
if ($a[price]<0.01) { $a[hide_price_begin] = '<!--'; $a[hide_price_end] = '-->'; }
if (($a[x_paypal_by]>$s[cas]) AND ($a[x_paypal_email]) AND ($a[x_paypal_currency]) AND ($a[x_paypal_price])) $a[paypal_button] = parse_part('ad_paypal_button.txt',$a); else { $a[hide_paypal_button_begin] = '<!--'; $a[hide_paypal_button_end] = '-->'; }

if ($a[t1]>$a[created]) $a[created] = datum($a[t1],1); else $a[created] = datum($a[created],1);
if ($a[updated]) $a[updated] = datum($a[updated],1); else { $a[hide_updated_begin] = '<!--'; $a[hide_updated_end] = '-->'; }
$a[detail] = str_replace('&#039;',"'",$a[detail]);

if ((($a[latitude]==-1) AND ($a[longitude]==-1)) OR (($a[latitude]==0) AND ($a[longitude]==0))) { $a[hide_map_begin] = '<!--'; $a[hide_map_end] = '-->'; }
else
{ if ( (($a[latitude]==0.0000000) OR ($a[longitude]==0.0000000)) AND (trim($a[address])) ) $new_ll = get_geo_data($a[address],$a[n],0);
  if ((($new_ll[latitude]==-1) AND ($new_ll[longitude]==-1)) OR (($new_ll[latitude]==0) AND ($new_ll[longitude]==0))) { $a[hide_map_begin] = '<!--'; $a[hide_map_end] = '-->'; }
  else $a[map_n] = $a[n];
}

$a[report_box] = report_box($a[n],0,1);
$a[tell_friend_box] = tell_friend_box($a[n],0,1);
$a[enter_comment_box] = enter_comment_box($a[n]);
$a[contact_box] = contact_box('a',$a[n],0,1);

if (trim($a[title])) $a[tags] = tags_for_item('ad',0,"$a[keywords] $a[title]"); else { $a[hide_tags_begin] = '<!--'; $a[hide_tags_end] = '-->'; }

$x = list_of_categories_for_item('ad',0,$a[c],'<br>',0); $a = array_merge((array)$a,(array)$x);
$x = list_of_areas_for_item($a[a],'<br>',0); $a = array_merge((array)$a,(array)$x);
$a[show_comments] = comments_get($a[n]);
$a[owner_items] = more_ads_of_user($a[n],$a[owner],$a[email]);
//$x = previous_next_links($_COOKIE[GC_category],$_COOKIE[GC_area],$a[c],$a[a],$_COOKIE[GC_offer_wanted],$a[n]); $a = array_merge((array)$a,(array)$x);
$statistic = get_ads_statistic($a[n]); if ($statistic[$a[n]][i_detail]) $a[clicks] = $statistic[$a[n]][i_detail]; else $a[clicks] = 0;

if ($s[message_owner_captcha]) $a[field_captcha_test] = parse_part('form_captcha_test.txt','');

if ($s[GC_u_n])
{ $bookmarks = get_favorites_status('ad',$a[n]); $a[add_delete_favorites] = get_favorite_line('ad',$a[n],$bookmarks[$a[n]]);
  if ($notes[$a[n]]) { $a[notes] = $notes[$a[n]]; $a[notes_style_display] = 'block'; } else $a[notes_style_display] = 'none';
  $s[current_notes] = $a[notes]; $a[notes_edit_box] = notes_edit_box('ad',$a[n],'');
}

if (check_admin_rights('ads')) $a[edit_link] = '<a target="_blank" href="'.$s[site_url].'/administration/ad_details.php?action=ad_edit&n='.$a[n].'">Edit this classified</a>';
$a[owner_url] = get_user_url($a[owner]); if (!$a[owner_url]) { $a[hide_owner_begin] = '<!--'; $a[hide_owner_end] = '-->'; }
if ($s[owner_vars][picture]) $a[user_picture] = $s[owner_vars][picture]; else $a[user_picture] = "$s[site_url]/images/no_picture.png";

if ($a[pub_phone1]) $phones[] = $a[pub_phone1]; if ($a[pub_phone2]) $phones[] = $a[pub_phone2]; $a[phones] = implode(', ',$phones); if (!$a[phones]) { $a[hide_phones_begin] = '<!--'; $a[hide_phones_end] = '-->'; }
$a[icons] = get_icons_for_item($a,$bookmarks[$a[n]]); if (!$a[icons]) { $a[hide_icons_begin] = '<!--'; $a[hide_icons_end] = '-->'; }
$a[this_url] = get_detail_page_url('ad',$a[n],$a[rewrite_url],'');
$template = get_detail_template_name('ad',$a[c]);
$a[info] = $s[info];
count_detail_click($a);

$a[meta_description] = $a[description];
$a[meta_keywords] = $a[keywords];
$a[meta_title] = $a[title];
//$a[meta_title] = "$a[category_path] >> $a[title]";

page_from_template($template,$a);
}

###################################################################################

function check_admin_rights($action) {
global $s;
if (($_SESSION[GC_admin_user]) AND ($_SESSION[GC_admin_password]))
{ $username = $_SESSION[GC_admin_user]; $password = $_SESSION[GC_admin_password]; }
else { $username = $_COOKIE[GC_admin_user]; $password = $_COOKIE[GC_admin_password]; }
$username = str_replace("'",'',$username); $password = str_replace("'",'',$password);
if ($action) $q = dq("select count(*) from $s[pr]admins,$s[pr]admins_rights where $s[pr]admins.username = '$username' and $s[pr]admins.password = '$password' and $s[pr]admins_rights.admin = $s[pr]admins.n and $s[pr]admins_rights.action = '$action'",1);
else $q = dq("select count(*) from $s[pr]admins where username = '$username' and password = '$password'",1);
$data = mysql_fetch_row($q);
if ($data[0]) return 1;
return 0;
}

###################################################################################

function more_ads_of_user($n,$owner,$email) {
global $s,$m;
$query = get_where_fixed_part(0,'',0,'',$s[cas],'');
if ($owner) $x = "owner = '$owner'"; else $x = "email = '$email'";
$q = dq("select * from $s[pr]ads where $query AND $x and n != '$n' order by created desc limit 15",1);
while ($x = mysql_fetch_assoc($q)) { $item[] = $x; $numbers[] = $x[n]; }
return get_complete_ads_simple($item,$numbers,'ad_simple.txt');
}

##################################################################################

function count_detail_click($ad_vars) {
global $s;
list($s[y],$s[m],$s[d]) = explode('-',date('Y-n-j',$s[cas]));
if ((!is_numeric($ad_vars[n])) OR (!$ad_vars[n])) return false;
if ($s[one_click_ip_day]) 
{ $q = dq("SELECT COUNT(*) FROM $s[pr]ads_stat_ip WHERE n = '$ad_vars[n]' AND ip = '$s[ip]'",0);
  $x = mysql_fetch_row($q);
  if ($x[0]) $not_count = 1;
  else dq("INSERT INTO $s[pr]ads_stat_ip VALUES ('$ad_vars[n]','$s[ip]')",0);
}
if (!$not_count)
{ dq("UPDATE $s[pr]ads set clicks_total = clicks_total + 1 WHERE n = '$ad_vars[n]'",0);
  dq("UPDATE $s[pr]ads_stat set i_detail = i_detail + 1, reset_i_detail = reset_i_detail + 1 WHERE n = '$ad_vars[n]'",0);
  if (mysql_affected_rows()<=0) dq("insert into $s[pr]ads_stat values ('$ad_vars[n]','$ad_vars[owner]','1','1','1','1','$s[cas]')",1);
  dq("UPDATE $s[pr]ads_stat_days set i_detail = i_detail + 1 WHERE n = '$ad_vars[n]' and y = '$s[y]' and m = '$s[m]' and d = '$s[d]'",0);
  if (mysql_affected_rows()<=0) dq("insert into $s[pr]ads_stat_days values ('$ad_vars[n]','$ad_vars[owner]','1','$s[y]','$s[m]','$s[d]')",1);
}
}

##################################################################################

function previous_next_links($category,$area,$all_c,$all_a,$offer_wanted,$n) {
global $s,$m;
if (!is_numeric($category)) { $x = explode(' ',str_replace('_','',$all_c)); if (($x[0]) AND (!$x[1])) $category = $x[0]; }
if (!is_numeric($area)) { $x = explode(' ',str_replace('_','',$all_a)); if (($x[0]) AND (!$x[1])) $area = $x[0]; }
if ($category) $category_vars = get_category_variables($category);
if ($area) $area_vars = get_area_variables($area);
if (($category) OR ($area))
{ if (($category) AND ($area)) $text = $m[next_in_cat].' '.$category_vars[title].$m[next_in_area1].' '.$area_vars[title];
  elseif ($category) $text = $m[next_in_cat].' '.$category_vars[title].$m[next_in_area1].' '.$area_vars[title];
  elseif ($area) $text = $m[next_in_area].' '.$area_vars[title];

  $where = get_where_fixed_part('',$category,'',$area,$s[cas],$offer_wanted);
  $q = dq("select n,rewrite_url from $s[pr]ads where $where and n > '$n' order by n limit 1",1);
  $ad = mysql_fetch_assoc($q);
  if ($a[n])
  { $a[next_category] = '<a class="link10" href="'.get_detail_page_url('ad',$a[n],$a[rewrite_url],$category).'">'.$m['Next'].' '.$text.'</a>';
    $have_cat_area = 1;
  }
  else { $a[hide_next_cat_begin] = '<!--'; $a[hide_next_cat_end] = '-->'; }
  $q = dq("select n,rewrite_url from $s[pr]ads where $where and n < '$n' order by n desc limit 1",1);
  $ad = mysql_fetch_assoc($q);
  if ($a[n])
  { $a[previous_category] = '<a class="link10" href="'.get_detail_page_url('ad',$a[n],$a[rewrite_url],$category).'">'.$m['Previous'].' '.$text.'</a>';
    $have_cat_area = 1;
  }
  else { $a[hide_previous_cat_begin] = '<!--'; $a[hide_previous_cat_end] = '-->'; }
}
else { $a[hide_next_cat_begin] = '<!--'; $a[hide_next_cat_end] = '-->'; $a[hide_previous_cat_begin] = '<!--'; $a[hide_previous_cat_end] = '-->'; }

/*if (!$have_cat_area)
{ unset($a[hide_next_cat_begin],$a[hide_next_cat_end],$a[hide_previous_cat_begin],$a[hide_previous_cat_end]);
  $a[hide_previous_next_cat_begin] = '<!--'; $a[hide_previous_next_cat_end] = '-->';
}*/
$where = get_where_fixed_part('','','','',$s[cas],'');
$q = dq("select n,rewrite_url from $s[pr]ads where $where and n > '$n' order by n limit 1",1);
$ad = mysql_fetch_assoc($q);
if ($a[n])
{ $url = get_detail_page_url('ad',$a[n],$a[rewrite_url],$category);
  $a['next'] = '<a class="link10" href="'.get_detail_page_url('ad',$a[n],$a[rewrite_url],0).'">'.$m[next_in_any].'</a>';
  $have_anything = 1;
}
else { $a[hide_next_begin] = '<!--'; $a[hide_next_end] = '-->'; }
$q = dq("select n,rewrite_url from $s[pr]ads where $where and n < '$n' order by n desc limit 1",1);
$ad = mysql_fetch_assoc($q);
if ($a[n])
{ $url = get_detail_page_url('ad',$a[n],$a[rewrite_url],$category);
  $a[previous] = '<a class="link10" href="'.get_detail_page_url('ad',$a[n],$a[rewrite_url],0).'">'.$m[previous_in_any].'</a>';
  $have_anything = 1;
}
else { $a[hide_previous_begin] = '<!--'; $a[hide_previous_end] = '-->'; }
/*if (!$have_anything)
{ unset($a[hide_next_begin],$a[hide_next_end],$a[hide_previous_begin],$a[hide_previous_end]);
  $a[hide_previous_next_all_begin] = '<!--'; $a[hide_previous_next_all_end] = '-->';
}*/
/*if ((!$have_cat_area) AND (!$have_anything))
{ unset($a);
  $a[hide_previous_next_begin] = '<!--'; $a[hide_previous_next_end] = '-->';
}*/
return $a;
}

##################################################################################
##################################################################################
##################################################################################

?>