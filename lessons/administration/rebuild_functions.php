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

$s[where_fixed_part] = get_where_fixed_part('','','','',$s[cas],'');
$s[styles_list] = get_styles_list(0,1);
include("$s[phppath]/data/data_forms.php");

function daily_job($showresult) {
global $s;
set_time_limit(600);
new_time('times_d');
delete_old($showresult);
update_popular($showresult);
if ($s[daily_recount]) recount_all_items(0);
count_stats($showresult);
update_index_suggest($showresult);
rebuild_index_categories($showresult);
rebuild_index_categories_groups($showresult);
create_in_files($showresult,0);
update_search_form_for_categories($showresult,0);
send_expired_emails($showresult,0);
if ($s[sitemap_location]) create_sitemap($showresult);
}

################################################################################
################################################################################
################################################################################

function update_index_suggest($showresult) {
global $s;
$search = array('&amp;',"&#039;",'"','\(','\)','-');
$replace = array('&',"'",'','','');
$x = explode(',',$s[ignored_tags]);
foreach ($x as $k=>$v) { $v = trim($v); if ($v) $search_replace1[] = $v; }
$q = dq("SELECT DISTINCT `word`,COUNT(`word`) AS num_logs FROM `$s[pr]index_suggest` GROUP BY `word` ORDER BY num_logs DESC LIMIT 1000",1);
while ($x = mysql_fetch_assoc($q))
{ $x1 = unhtmlentities($x[word]);
  $x1 = str_replace("[\]",'&#92;',$x1);
  foreach ($search as $k=>$v) $x1 = preg_replace("/$v/i",$replace[$k],$x1);
  $x1 = trim($x1);
  foreach ($search_replace1 as $k=>$v) if (!strcasecmp ($x1,$v)) /*($x1==$v)*/ continue 2;
  if (strlen($x1)<=2) continue;
  if ($x1) { /*if ($pocet<=100) */$top_tags_for_search[] = '"'.str_replace("'",'',$x1).'"'; /*$words[] = '"'.$x1.'"';*/ $pocet++; if ($pocet<=25) $s[top_tags_words][] = $x1; }
}
$info = 'List of top searches updated.<br />';
if ($showresult) echo $info; else $s[info] .= $info;
}

###################################################################################

function create_index($showresult) {
global $s;
dq("delete from $s[pr]index",1);
dq("delete from $s[pr]index_suggest",1);
{ $q = dq("select n from $s[pr]ads where $s[where_fixed_part]",1);foreach ($x as $k=>$v) echo "$k - $v<br>";
  while ($x = mysql_fetch_assoc($q)) { update_item_index($what,$x[n]); increase_print_time(2,1); }
}
increase_print_time(2,'end');
$info = 'Index for searching created<br />';
$s[info] .= $info;
}

################################################################################

function delete_statistic_days($in) {
global $s;
dq("delete from $s[pr]ads_stat_days where y < '$in[delete_days_year]'",1);
dq("delete from $s[pr]ads_stat_days where y = '$in[delete_days_year]' AND m <= '$in[delete_days_month]'",1);
$s[info] = info_line('Selected records have been deleted');
reset_rebuild_home();
}

################################################################################

function delete_old($showresult) {
global $s;
$cas = $s[cas] - ($s[user_unconfirmed_delete_after]*86400); $cas1 = $s[cas] - 604800;
dq("delete from $s[pr]users where confirmed = '0' AND joined < '$cas'",1);
dq("delete from $s[pr]ads_stat_ip",1);
//dq("delete from $s[pr]visits_today",1);
$q = dq("select time from $s[pr]board order by time desc",0);
if (mysql_data_seek($q,$s[board]))
{ $r = mysql_fetch_row($q);
  dq("delete from $s[pr]board where time <= '$r[0]'",1);
}
if ($s[delete_expired_items]) { $s[dont_recount] = 1; delete_expired_items($showresult); }

if ($fp = opendir("$s[phppath]/uploads"))
{ while (false !== ($file = readdir($fp)))
  { if (($file != ".") AND ($file != "..") AND ($file != 'index.html') AND (!is_dir("$s[phppath]/uploads/$file")) )
    unlink("$s[phppath]/uploads/$file");
  }
  closedir($handle);
}

$info .= 'Unconfirmed users joined more than 24 hours ago deleted<br>
IP records deleted<br>
In the message board table is now no more than '.$s[board].' newest messages<br>';
if ($showresult) echo $info; else $s[info] .= $info;
}

################################################################################
################################################################################
################################################################################

function delete_expired_items($showresult) {
global $s;

$q = dq("select n from $s[pr]ads where t2 > '0' AND t2 < '$s[cas]'",1);
while ($x = mysql_fetch_row($q)) $numbers[] = $x[0];
if ($numbers) delete_ads_process($numbers);
$info = 'Expired classified ads deleted<br>';
unset($numbers);

if (($s[ad_email_confirm]) AND ($s[ad_unconfirm_delete]))
{ $q = dq("select n from $s[pr]ads where status = 'waiting' and created < ($s[cas]-(86400*$s[ad_unconfirm_delete]))",1);
  while ($x = mysql_fetch_row($q)) $numbers[] = $x[0];
  if ($numbers) delete_ads_process($numbers);
}

if ($showresult) echo $info; else $s[info] .= $info;
}

################################################################################
################################################################################
################################################################################

function update_popular($showresult) {
global $s;
dq("update $s[pr]ads set popular = 0",1);
$q = dq("select n from $s[pr]ads order by clicks_total desc limit $s[popular]",1);
while ($x = mysql_fetch_assoc($q)) dq("update $s[pr]ads set popular = '1' where n = '$x[n]'",1);
dq("update $s[pr]ads set x_featured_by = '0' where x_featured_by < '$s[cas]'",1);
$info = 'Popular classified ads updated<br>';
if ($showresult) echo $info; else $s[info] .= $info;
}

################################################################################
################################################################################
################################################################################

function count_stats($showresult) {
global $s;
$q = dq("select count(*) as total from $s[pr]cats",0);
$cats = mysql_fetch_assoc($q);
$q = dq("select count(*) as total from $s[pr]areas",0);
$areas = mysql_fetch_assoc($q);
$q = dq("select count(*) as total,sum(clicks_total) as clicks_total,sum(comments) as comments from $s[pr]ads",0);
$total = mysql_fetch_assoc($q);
if (!$total[clicks_total]) $total[clicks_total] = 0;
if (!$total[comments]) $total[comments] = 0;
$q = dq("select count(*) as total from $s[pr]ads where $s[where_fixed_part]",1);
$active = mysql_fetch_assoc($q);
$q = dq("select count(*) as total from $s[pr]ads where status = 'queue'",0);
$queue = mysql_fetch_assoc($q); if (!$queue[total]) $queue[total] = 0;
$q = dq("select count(*) as total from $s[pr]ads_abuse_reports",0);
$abuse = mysql_fetch_assoc($q); if (!$abuse[total]) $abuse[total] = 0;
$q = dq("select count(*) as total from $s[pr]users",0);
$users = mysql_fetch_assoc($q); if (!$users[total]) $users[total] = 0;
$q = dq("select count(*) as posts from $s[pr]board",0);
$board = mysql_fetch_assoc($q); if (!$board[posts]) $board[posts] = 0;
$q = dq("select count(*) as records from $s[pr]log_search",0);
$search = mysql_fetch_assoc($q);
$data = "<?PHP\n
\$s[t_cats] = $cats[total];
\$s[t_areas] = $areas[total];
\$s[t_ads] = $total[total];
\$s[active_ads] = $active[total];
\$s[t_queue] = $queue[total];
\$s[t_clicks_total] = $total[clicks_total];
\$s[t_comments] = $total[comments];
\$s[t_abuse_reports] = $abuse[total];
\$s[t_users] = $users[total];
\$s[t_board] = $board[posts];
\$s[search_records] = $search[records];

\n?>";
$fp = fopen ("$s[phppath]/data/stats.php",'w') or problem ("Unable to write to file $s[phppath]/data/stats.php");
fwrite ($fp,$data); fclose($fp);
chmod ("$s[phppath]/data/stats.php",0666);
$info = 'Numbers of links and other statistic data updated<br>';
if ($showresult) echo $info; else $s[info] .= $info;
}

################################################################################
################################################################################
################################################################################

function rebuild_index_categories($showresult) {
global $s;
dq("delete from $s[pr]cats_home",1);
$list_of_items = array('ad');
foreach ($list_of_items as $k=>$what)
{ $q = dq("select * from $s[pr]cats where level = '1' AND visible = '1' order by title",1);
  while ($c = mysql_fetch_assoc($q))
  { if ($c[alias_of]) { $c[title] = $s[alias_pref].$c[title].$s[alias_after]; $n = $c[alias_of]; } else $n = $c[n];
    dq("insert into $s[pr]cats_home values ('$n','0','ads','$c[rank]','$c[title]','$c[description]','$c[rewrite_url]')",1);
    if ($s[index_max_subc])
    { $r = dq("select * from $s[pr]cats where level = '2' AND visible = '1' AND parent = '$c[n]' order by title limit $s[index_max_subc]",1);
      while ($x = mysql_fetch_assoc($r))
      { if ($x[alias_of]) $x[title] = $s[alias_pref].$x[title].$s[alias_after];
        if ($x[alias_of]) { $x[title] = $s[alias_pref].$x[title].$s[alias_after]; $n = $x[alias_of]; } else $n = $x[n];
        dq("insert into $s[pr]cats_home values ('$n','$c[n]','ads','$x[rank]','$x[title]','$x[description]','$x[rewrite_url]')",1);
      }
    }
  }
}

$info = 'List of categories on home page updated<br>';
if ($showresult) echo $info; else $s[info] .= $info;
}

################################################################################

function rebuild_index_categories_groups($showresult) {
global $s;
for ($group=1;$group<=10;$group++)
{ $q = dq("select * from $s[pr]cats where cat_group = '$group' and visible = '1' order by title",1);
  while ($c = mysql_fetch_assoc($q))
  { if ($c[alias_of]) { $c[title] = $s[alias_pref].$c[title].$s[alias_after]; $n = $c[alias_of]; } else $n = $c[n];
    dq("insert into $s[pr]cats_home values ('$n','$c[parent]','$group','$c[rank]','$c[title]','$c[description]','$c[rewrite_url]')",1);
  } 
}
$info = 'Groups of categories on home page updated<br>';
if ($showresult) echo $info; else $s[info] .= $info;
}

################################################################################
################################################################################
################################################################################

function create_in_files($showresult,$html) {
global $s,$m;

$data[ads_new] = create_in_files_get_new_items('ads');
$q = dq("select n,url,title,rewrite_url from $s[pr]ads where $s[where_fixed_part] order by clicks_total desc limit $s[ads_r_n]",1);
$data[ads_popular] = create_in_files_one_item('ads',$q);
$q = dq("select n,url,title,rewrite_url from $s[pr]ads where $s[where_fixed_part] order by comments desc limit $s[ads_r_n]",1);
$data[ads_most_comments] = create_in_files_one_item('ads',$q);

$q = dq("select word,count from $s[pr]log_search order by count desc limit 25",1);
while ($x=mysql_fetch_assoc($q))
{ $font_size = round(20 - ($pocet/2));
  $words_array[] = '<a style="font-size:'.$font_size.'px"; href="'.$s[site_url].'/search.php?phrase='.urlencode($x[word]).'">'.$x[word].'</a>';
  $pocet++;
}
shuffle($words_array);
$b[topsearch] = implode("\n",$words_array);

unset($pocet,$words_array);
foreach ($s[top_tags_words] as $k=>$word) 
{ $font_size = round(20 - ($pocet/2));
  $words_array[] = '<a style="font-size:'.$font_size.'px"; href="'.$s[site_url].'/search.php?phrase='.urlencode($word).'">'.$word.'</a>';
  $pocet++;
}
shuffle($words_array);
$b[top_tags] = implode("\n",$words_array);

$b[a_first_cats_select] = categories_selected('a_first',0,0,1,1,1);
$b[first_cats_select] = select_list_first_cats('_common',1);
$b[first_areas_select] = select_list_first_areas('_common',1); 
$b[offer_wanted_select] = "<option value=\"offer_wanted\">$m[offer_wanted]</option><option value=\"offer\">$m[offer]</option><option value=\"wanted\">$m[wanted]</option>";
$b[last_rebuild] = datum($s[times_d],1);

foreach ($data as $what=>$array)
{ foreach ($array as $mark=>$items_array)
  { foreach ($items_array as $k=>$item_array)
    { if ($html)
      { if (strstr($what,'_cats')) $out[$what] .= A_parse_part('_in_one_category.txt',$data[$what][$k]);
        else $out[$what] .= A_parse_part('_in_one_item.txt',$data[$what][$k]);
      }
      else
  	  { foreach ($s[styles_list] as $stlk=>$st)
	    { if ($mark=='first_cats') $out[$st][$what.'_first_cats'] .= php_rebuild_parse_part('_in_one_category.txt',$st,$item_array);
          else $out[$st][$what.'_'.$mark] .= php_rebuild_parse_part('_in_one_item.txt',$st,$item_array);
          if ($mark=='new_items') 
          { //foreach ($item_array as $k5=>$v5) echo "$k5 - $v5<br>";
            //$new_items[$what][$st][] = php_rebuild_parse_part('index_new_item.txt',$st,$item_array); // two columns of news items on home page
            $static[home][$st][$what][new_items] .= php_rebuild_parse_part('index_new_item.txt',$st,$item_array);
          }
        }
      }
    }
  }
}



//if (!$s[message_to_us_captcha]) { $b[hide_captcha_test_begin] = '<!--'; $b[hide_captcha_test_end] = '-->'; }

foreach ($data as $what=>$val)
{ foreach ($data[$what] as $k=>$v)
  { foreach ($s[styles_list] as $stlk=>$st) $out[$st][$what] .= php_rebuild_parse_part('_in_one_item.txt',$st,$data[$what][$k]); }
}


foreach ($s[styles_list] as $stlk=>$st) 
{ $a = array_merge((array)$out[$st],(array)$b);
  for ($x=1;$x<=$s[in_templates];$x++)
  { if (!file_exists("$s[phppath]/styles/_common/templates/_in$x.txt")) continue;
    $static[0][$st][x]["in$x"] = php_rebuild_parse_part('_in'.$path.$x.'.txt',$st,$a);
  }
}


$q = dq("select * from $s[pr]cats where in_menu = '1' order by title",1);
while ($c = mysql_fetch_assoc($q))
{ $number++;
  $static[0][0][x][menu_categories] .= "<a href=\"".category_url('ad',$c[n],$c[alias_of],1,$c[rewrite_url])."\">$c[title] </a>";
}
$q = dq("select * from $s[pr]areas where in_menu = '1' order by title",1);
while ($c = mysql_fetch_assoc($q))
{ $number++;
  $url = "$s[site_url]/$s[ARfold_l_cat]-0-$c[n]/$c[rewrite_url].html";
  $static[0][0][x][menu_areas] .= "<a href=\"$url\">$c[title] </a>";
}

dq("truncate table $s[pr]static",1);
foreach ($static as $page=>$array1)
{ foreach ($array1 as $style=>$array2) 
  { foreach ($array2 as $what=>$array3)
    { foreach ($array3 as $mark=>$content)
      { $content = replace_once_html($content);
        dq("insert into $s[pr]static values ('$page','$style','$what','$mark','$content')",1);
      }
    }
  }
}

/*
$where = get_where_fixed_part(0,0,0,0,$s[cas],'');
$q = dq("select *,MD5(RAND()) AS m from $s[pr]ads where $where and x_featured_by > '$s[cas]' order by m desc",1);
while ($x = mysql_fetch_assoc($q))
{ $x[url] = get_detail_page_url('ad',$x[n],$x[rewrite_url],1);
  $java .= php_rebuild_parse_part('ad_javascript.txt','_common',$x);
}
$fp = fopen("$s[phppath]/include/featured.txt",'w'); fwrite ($fp,$java); fclose($fp); chmod ("$s[phppath]/include/featured.txt",0666);
*/
$info = 'Included files updated<br>';
if ($showresult) echo $info; else $s[info] .= $info;
}

#############################################################################

function send_expired_emails($showresult,$html) {
global $s;
/*
$cas = $s[cas] + 259200; $cas1 = $s[cas] + 345600;
$q = dq("select n,title,email from $s[pr]ads where t2 > '$cas' and t2 < '$cas1'",1);
*/
$cas = $s[cas] - 86400;
$q = dq("select n,title,email from $s[pr]ads where t2 > '$cas' and t2 < '$s[cas]'",1);
while ($ad=mysql_fetch_assoc($q))
{ $ad[to] = $ad[email];
  $ad[increase_link] = "$s[site_url]/user.php?action=increase&n=$ad[n]&x=".md5($ad[email]).'-'.md5($user[title]);
  mail_from_template('ad_expired.txt',$ad);
  set_time_limit(60);
}
/*
$cas = $s[cas] + 518400;
$cas1 = $s[cas] + 604800;
$q = dq("select * from $s[pr]ads where (x_bold_by > '$cas' and x_bold_by < '$cas1') OR (x_featured_by > '$cas' and x_featured_by < '$cas1') OR (x_home_page_by > '$cas' and x_home_page_by < '$cas1') OR (x_featured_gallery_by > '$cas' and x_featured_gallery_by < '$cas1') OR (x_highlight_by > '$cas' and x_highlight_by < '$cas1') OR (x_pictures_by > '$cas' and x_pictures_by < '$cas1') OR (x_files_by > '$cas' and x_files_by < '$cas1') OR (x_paypal_by > '$cas' and x_paypal_by < '$cas1')",1);
while ($ad=mysql_fetch_assoc($q))
{ $ad[to] = $ad[email];
  foreach ($s[extra_options] as $k=>$v) { if (($ad['x_'.$v.'_by']>$cas) AND ($ad['x_'.$v.'_by']<$cas1)) $ad[options] .= $m['xtra_'.$v].", valid until: ".datum($ad['x_'.$v.'_by'])."\n"; }
  if (($ad['x_pictures_by']>$cas) AND ($ad['x_pictures_by']<$cas1)) $ad[options] .= "$m[xtra_pictures]: $ad[x_pictures_max], valid until: ".datum($ad['x_pictures_by'])."\n";
  if (($ad['x_files_by']>$cas) AND ($ad['x_files_by']<$cas1)) $ad[options] .= "$m[xtra_files]: $ad[x_files_max], valid until: ".datum($ad['x_files_by'])."\n";
  mail_from_template('ad_expire_features.txt',$ad);
  set_time_limit(60);
}
Subject: Extra options will expire
Temat: Wygas³ numer Twojego og³oszenia, Twojej reklamy: #%n%#
Tytu³: #%title%#
Options to expire: #%options%#
*/
foreach ($s[extra_options] as $k => $v)
{ $column = 'x_'.$v.'_by';
  dq("update $s[pr]ads set $column = 0 where $column < $s[cas] and $column > 0",1);
}

$info = 'Sent emails to owners of ads that expire in the last 24 hours.<br>';
if ($showresult) echo $info; else $s[info] .= $info;
}

#############################################################################

function update_search_form_for_categories() {
global $s;
include($s[phppath].'/styles/_common/messages/common.php');

$sorts = explode(',',$s[sort_ads_options]);
foreach ($sorts as $k=>$v) $order_by_options .= '<option value="'.$v.'"#_orderby_selected_'.$v.'_#>'.$m[$v].'</option>';

dq("delete from $s[pr]cats_search_forms",1);
$q = dq("select * from $s[pr]cats where level = 1",1);
while ($cat = mysql_fetch_assoc($q))
{ list($usits,$avail_val) = get_category_usit($cat[n],0,0,1);
  foreach ($usits as $k=>$usit)
  { if (($usit[item_type]=='text') OR ($usit[item_type]=='textarea') OR ($usit[item_type]=='htmlarea'))
    { $b[content] = $usit[description].'<br>';
      $b[content] .= '<input name="usit['.$usit[usit_n].']" size=20 maxlength=50 class="field10" value="#_usit_value_'.$usit[usit_n].'_#">';
      $a[search_user_fields] .= php_rebuild_parse_part('search_form_usit.txt','_common',$b);
    }
    if ($usit[item_type]=='checkbox')
    { if ($entered_usit[$usit[usit_n]]) $checked = ' checked'; else $checked = '';
      $b[content] = $usit[description].' <input type="checkbox" name="usit['.$usit[usit_n].']" value="1"#_usit_checked_'.$usit[usit_n].'_1_#>';
      $a[search_user_fields] .= php_rebuild_parse_part('search_form_usit.txt','_common',$b);
    }
    elseif ($usit[item_type]=='radio')
    { $b[content] = $usit[description].'<br><input type="radio" name="usit['.$usit[usit_n].']" value="0"#_usit_checked_'.$usit[usit_n].'_0_#>'.$m[any].'<br>';
      foreach ($avail_val[$usit[n]] as $k1=>$one_option)
      $b[content] .= '<input type="radio" name="usit['.$usit[usit_n].']" value="'.$one_option[n].'"#_usit_checked_'.$usit[usit_n].'_'.$one_option[n].'_#>'.$one_option[description].'<br>';
      $a[search_user_fields] .= php_rebuild_parse_part('search_form_usit.txt','_common',$b);
    }
    elseif (($usit[item_type]=='select') OR ($usit[item_type]=='multiselect'))
    { $b[content] = $usit[description].'<br>';
      $b[content] .= '<select name="usit['.$usit[usit_n].']" class="field10"><option value="0"#_usit_selected_'.$usit[usit_n].'_0_#>'.$m[any].'</option>';
      foreach ($avail_val[$usit[n]] as $k1=>$one_option)
      $b[content] .= '<option value="'.$one_option[n].'"#_usit_selected_'.$usit[usit_n].'_'.$one_option[n].'_#>'.$one_option[description].'</option>';
      $b[content] .= '</select>';
      $a[search_user_fields] .= php_rebuild_parse_part('search_form_usit.txt','_common',$b);
    }
  }
  $a[search_user_fields] = str_replace('#%','#_',str_replace('%#','_#',$a[search_user_fields]));
  $a[search_categories_select] = categories_selected('ad',0,0,1,0,1,$cat[n],1);
  $a[first_areas_select] = select_list_first_areas('_common',1); 
  $a[order_by_options] = $order_by_options;
  if (!$cat[price]) { $a[hide_price_begin] = '<!--'; $a[hide_price_end] = '-->'; }
  if (!$cat[offer_wanted]) { $a[hide_offer_wanted_begin] = '<!--'; $a[hide_offer_wanted_end] = '-->'; }
  $form = addslashes(php_rebuild_parse_part('search_form.txt','_common',$a));
  dq("insert into $s[pr]cats_search_forms values ('$cat[n]','$form')",1);
  unset($a,$form);
}


echo $c;
}

################################################################################

function create_in_files_get_new_items($what) {
global $s;
$count = $s[ads_r_n];
if ($query = get_new_items($count))
{ unset($x,$numbers,$items);
  $q = dq("select * from $s[pr]ads where $query",1);
  while ($x = mysql_fetch_assoc($q))
  {	if ($x[created]>$x[t1]) $items["$x[created]-$x[n]"] = $x;
    else $items["$x[t1]-$x[n]"] = $x;
    $numbers[] = $x[n];
  }
  ksort($items); $items = array_reverse($items);
  return create_in_files_one_item($what,$items);
}
}

################################################################################

function create_in_files_one_item($what,$q) {
global $s;
if (is_array($q)) // new items
{ foreach ($q as $k => $v)
  { $cislo++;
    if ($what=='ads')
    { $data[$cislo][url] = get_detail_page_url('ad',$v[n],$v[rewrite_url],1);
      $data[$cislo][title] = $v[title];
    }
  }
}
else  // mysql result
{ while ($x = mysql_fetch_assoc($q))
  { $cislo++;
    if ($what=='ads')
    { $data[$cislo][url] = get_detail_page_url('ad',$x[n],$x[rewrite_url],1);
      $data[$cislo][title] = $x[title];
    }
    elseif ($what=='search_ads')
    { $data[$cislo][url] = "$s[site_url]/search.php?action=ads_advanced&boolean=and&phrase%5B0%5D=".urlencode($x[word]);
      $data[$cislo][title] = $x[word];
    }
  }
}
return $data;
}

################################################################################
################################################################################
################################################################################

function new_time($what) {
global $s;
load_times();
$s[$what] = $s[cas];
save_times();
}

###################################################################################

function php_rebuild_parse_part($t,$style,$vl) {
global $s;
if (file_exists($s[phppath].'/styles/'.$style.'/templates/'.$t))
$t = $s[phppath].'/styles/'.$style.'/templates/'.$t;
else $t = $s[phppath].'/styles/_common/templates/'.$t;
if (!is_array($vl)) $vl = array();
$vl[charset] = $s[charset]; $vl[site_url] = $s[site_url]; $vl[site_name] = $s[site_name]; $vl[currency] = $s[currency]; $vl[google_search_id] = $s[google_search_id];
$vl[logo_url] = $s[logo_url]; $vl[banner_code] = $s[banner_code]; $vl[css_style] = $s[GC_style];
$fh = fopen ($t,'r') or problem ("Unable to read file $t");
while (!feof($fh)) $line .= fgets ($fh,4096); fclose($fh);
foreach ($vl as $k=>$v) $line = str_replace("#%$k%#",$v,$line);
$line = eregi_replace("#%[a-z0-9_]*%#",'',stripslashes($line));
$line = str_replace('#_','#%',str_replace('_#','%#',$line));
return $line;
}

###################################################################################
###################################################################################
###################################################################################

function recount_all_items($return_result) {
global $s;
$s[dont_end_increase] = 1;
//$start = time();
//for ($xx=1;$xx<=100;$xx++)
{ $q = dq("select * from $s[pr]areas",1); while ($x = mysql_fetch_assoc($q)) $areas[] = $x[n];

if ($_GET[do_recount_all_items_category]) $category = "where bigboss = '$_GET[do_recount_all_items_category]' or n = '$_GET[do_recount_all_items_category]'";
$q = dq("select * from $s[pr]cats $category",1);
while ($cat = mysql_fetch_assoc($q))
{ foreach ($areas as $k1=>$a) { recount_ads_cat_area($cat[n],$a);/*echo "($cat[n],$a)<br>";*/ increase_print_time(2,1); }
  recount_ads_cat_area($cat[n],0);
  
  //$pocet++; if ($pocet==100) exit;
}
foreach ($areas as $k1=>$a) { recount_ads_cat_area(0,$a); increase_print_time(2,1); }
recount_ads_cat_area(0,0);
  
//  recount_ads_cats_areas( implode(' ',$cats),'',implode(' ',$areas),'');
  $info .= '</span><span class="text13a_bold">Classified ads have been recounted'.$xx.'</span><br>';
}
//echo time() - $start;
increase_print_time(2,'end');
if ($return_result) return $info;
}

########################################################################################

function repair_path_ads($return_result) {
global $s;

$q = dq("select * from $s[pr]areas order by level",1);
while ($x = mysql_fetch_assoc($q)) update_category_area_paths('a',$x[n]);

$q = dq("select * from $s[pr]cats order by level",1);
while ($x = mysql_fetch_assoc($q)) update_category_area_paths('c',$x[n]);

$q = dq("select n,path_n from $s[pr]cats",1); while ($x = mysql_fetch_assoc($q)) $cats[$x[n]] = $x[path_n];
$q = dq("select n,path_n from $s[pr]areas",1); while ($x = mysql_fetch_assoc($q)) $areas[$x[n]] = $x[path_n];

$q = dq("select * from $s[pr]ads",1);
while ($x = mysql_fetch_assoc($q))
{ increase_print_time(2,1);
  unset($c_paths,$a_paths);
  $c = explode(' ',str_replace('_','',$x[c]));
  $a = explode(' ',str_replace('_','',$x[a]));
  foreach ($c as $k=>$v) { if (!is_numeric($v)) continue; $c_paths[] = $cats[$v]; } 
  foreach ($a as $k=>$v) { if (!is_numeric($v)) continue; $a_paths[] = $areas[$v]; } 
  $c_path = implode(' ',$c_paths); $a_path = implode(' ',$a_paths);
  if (!$x[rewrite_url]) $x[rewrite_url] = discover_rewrite_url($x[title],0);
  dq("update $s[pr]ads set c_path = '$c_path', a_path = '$a_path', rewrite_url = '$x[rewrite_url]' where n = '$x[n]'",1);
}
increase_print_time(2,'end');
$info .= '</span><span class="text13a_bold">Paths of classified ads repaired</span><br>';
if ($return_result) return $info;
}

########################################################################################
########################################################################################
########################################################################################

function create_sitemap($showresult) {
global $s;
if ($s[sitemap_cats])
{ $q = dq("select * from $s[pr]cats where visible = '1' AND alias_of = '0' order by path_text",1);
  while ($a=mysql_fetch_assoc($q))
  { $a[path_text] = eregi_replace("<%.+%>",'',$a[path_text]);
    $a[path_text] = eregi_replace("<%.+$",$a[title],$a[path_text]);
    $url = sitemap_replace(category_url('ad',$a[n],$a[alias_of],1,$a[rewrite_url]));
    $x .= "<a href=\"$url\" class=\"link10\">$a[path_text]</a><br>\n";
  }
  $data[categories] = stripslashes($x); unset($x);
}
if ($s[sitemap_ads])
{ $x = '<span class="text10">';
  $q = dq("select title,url,description,n from $s[pr]ads where $s[where_fixed_part] order by n",1);
  while ($a = mysql_fetch_assoc($q))
  { if (!$s[sitemap_description]) unset($a[description]);
    $x .= "<a href=\"".get_detail_page_url('ad',$a[n],$a[rewrite_url],1)."\" class=\"link10\">$a[title]</a> $a[description]<br>\n";
  }
  $data[ads] = stripslashes($x); unset($x);
}
/*
if ($s[sitemap_search])
{ $q = dq("select word from $s[pr]log_search",1);
  while ($a = mysql_fetch_assoc($q))
  { $a[url] = $s[site_url].'/search.php?action=ads_simple&amp;phrase='.urlencode($a[word]).'&amp;area_boss=0&amp;search_kind=and';
    $x .= "<a href=\"$s[site_url]/search.php?action=ads_simple&amp;phrase=".urlencode($a[word])."&amp;area_boss=0&amp;search_kind=and\" class=\"link10\">$a[word]</a><br>\n";
  }
  $data[search] = stripslashes($x); unset($x);
}*/
$page = php_rebuild_parse_part('sitemap.html','_common',$data);
create_write_file($s[sitemap_location],$page,0666,1);
}

###################################################################################

function sitemap_replace($a) {
global $s;
$a = str_replace('-extra_commands','',$a);
$a = str_replace('-page_n','',$a);
$a = str_replace('area_rewrite/','',$a);
$a = str_replace('-area_n','-0',$a);
if ($s[A_option]!='rewrite')
{ //$a = str_replace("$s[site_url]/index_offer.html","$s[site_url]/index.php?vars=offer",$a);
  //$a = str_replace("$s[site_url]/index_wanted.html","$s[site_url]/index.php?vars=wanted",$a);
  //$a = str_replace("$s[site_url]/index_all.html","$s[site_url]/index.php?vars=all",$a);
  $a = str_replace("$s[site_url]/$s[ARfold_l_detail]-","$s[site_url]/classified.php?vars=",$a);
  $a = str_replace("$s[site_url]/$s[ARfold_l_cat]-","$s[site_url]/index.php?vars=/$s[ARfold_l_cat]-",$a);
  $a = str_replace("$s[site_url]/extra_category/","$s[site_url]/category.php?action=",$a);
}
$a = str_replace('&','&amp;',str_replace('&amp;','&',$a));
return $a;
}

###################################################################################

function create_google_sitemap($showresult) {
global $s;
$file = fopen($s[g_sitemap_location],'w');
$line = '<?xml version="1.0" encoding="UTF-8" ?> 
<urlset xmlns="http://www.google.com/schemas/sitemap/0.84">'."\n";
fwrite($file,$line);
if ($s[g_sitemap_cats])
{ $q = dq("select * from $s[pr]cats where visible = '1' AND alias_of = '0' order by path_text",1);
  while ($a=mysql_fetch_assoc($q))
  { set_time_limit(300);
    if (time()>($time1+10)) { $time1=time(); echo str_repeat (' ',4000); flush(); }
    $a[url] = sitemap_replace(category_url('ad',$a[n],$a[alias_of],1,$a[rewrite_url]));
    $a[date] = str_replace('---','T',date("Y-m-d---H:i:s+00:00",$s[cas]));
    fwrite($file,php_rebuild_parse_part('google_sitemap.txt','_common',$a)."\n");
  }
}
if ($s[g_sitemap_ads])
{ $q = dq("select * from $s[pr]ads where $s[where_fixed_part] order by n",1);
  while ($a = mysql_fetch_assoc($q))
  { $a[url] = get_detail_page_url('ad',$a[n],$a[rewrite_url],1);
    if ($a[edited]) $date = $a[edited]; else $date = $a[created];
    $a[date] = str_replace('---','T',date("Y-m-d---H:i:s+00:00",$date));
    fwrite($file,php_rebuild_parse_part('google_sitemap.txt','_common',$a)."\n");
  }
}
if ($s[g_sitemap_search])
{ $q = dq("select word from $s[pr]log_search",1);
  while ($a = mysql_fetch_assoc($q))
  { $a[url] = $s[site_url].'/search.php?phrase='.urlencode($a[word]);
    $a[date] = str_replace('---','T',date("Y-m-d---H:i:s+00:00",$s[cas]));
    fwrite($file,php_rebuild_parse_part('google_sitemap.txt','_common',$a)."\n");
  }
}
fwrite ($file,'</urlset>');
fclose($file); chmod ($s[g_sitemap_location],0777);
$info = 'Google sitemap created<br>';
if ($showresult) echo info_line($info); else $s[info] .= $info;
}

###################################################################################

function create_yahoo_sitemap($showresult) {
global $s;
$file = fopen($s[y_sitemap_location],'w');

if ($s[y_sitemap_cats])
{ $q = dq("select * from $s[pr]cats where visible = '1' AND alias_of = '0' order by path_text",1);
  while ($a=mysql_fetch_assoc($q)) $list .= sitemap_replace(category_url('ad',$a[n],$a[alias_of],1,$a[rewrite_url]))."\n";
  fwrite($file,$list); unset($list);
}
if ($s[y_sitemap_ads])
{ $q = dq("select * from $s[pr]ads where $s[where_fixed_part] order by n",1);
  while ($a=mysql_fetch_assoc($q)) $list .= get_detail_page_url('ad',$a[n],$a[rewrite_url],1)."\n";
  fwrite($file,$list); unset($list);
}
if ($s[y_sitemap_search])
{ $q = dq("select word from $s[pr]log_search order by n desc",1);
  while ($a=mysql_fetch_assoc($q))
  { $url = $s[site_url].'/search.php?phrase='.urlencode($a[word]);
    $list .= "$url\n";
  }
  fwrite($file,$list); unset($list);
}
fclose($file); chmod ($s[y_sitemap_location],0666);
$info = 'Yahoo URL list created<br />';
if ($showresult) echo info_line($info); else $s[info] .= $info;
}

###################################################################################
###################################################################################
###################################################################################

function reset_all_question($ok) {
global $s;
ih();
?>
<br><table border=0 width=500 cellspacing=10 cellpadding=0 class="common_table">
<tr><td align="center"><table border=0 cellspacing=2 cellpadding=0>
<form method="post" action="rebuild.php" name="form1"><?PHP echo check_field_create('admin') ?>
<input type="hidden" name="action" value="reset_all">
<tr><td align="center" colspan=2><span class="text13a_bold"><b>It resets numbers clicks of all classified ads to zero. Are you sure?</b></span>
<br><br><input type="submit" name="submit" value="Yes, reset it" class="button10"></td></tr></form></table>
<?PHP
ift();
}

###################################################################################

function reset_all() {
global $s;
dq("update $s[pr]ads set clicks_total = 0",1);
dq("update $s[pr]ads_stat set i = 0, i_detail = 0, reset_i = 0, reset_i_detail = 0, reset_time = '$s[cas]'",1);
dq("delete from $s[pr]ads_stat_days",1);
$s[info] = 'Statistic has been reseted<br>';
reset_rebuild_home();
}

###################################################################################
###################################################################################
###################################################################################

?>