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

get_messages('ad_create_edit_functions.php');
$s[selected_menu] = 6;
include("$s[phppath]/data/data_forms.php");
if ($s[mult_cats_admin]) $s[max_cats] = $s[max_areas] = 1;
$s[dont_end_increase] = 1;

##################################################################################
##################################################################################
##################################################################################

function ad_create($in) {
global $s,$m;
if ($s[post_ads_who])
{ $_SESSION[GC_ad_create] = $in[c];
  $user = check_logged_user();
  if (($s[post_ads_who]==2) AND (!$user[post_ads])) problem($m[dont_right_add]);
}
elseif ($s[GC_u_n]) $user = check_logged_user();

$free_period = get_cat_free_period(get_bigboss_category($in[c][0]));
if (!$free_period)
{ $user = check_logged_user();
  if (!$user[n]) problem($m[dont_right_add_paid_cat]);
  $in[prices_table] = get_prices_table($in[c][0]);
}

if (!$in[name]) $in[name] = $user[name]; if (!$in[email]) $in[email] = $user[email]; if (!$in[url]) $in[url] = $user[url];
if (!$in[pub_phone1]) $in[pub_phone1] = $user[phone1]; if (!$in[pub_phone2]) $in[pub_phone2] = $user[phone2];

$in[c] = str_replace('_','',$in[c]);
if ((!is_numeric($in[c][0])) AND (is_numeric($in[c1]))) $in[c][0] = $in[c1];
if (!is_numeric($in[c][0]))
{ $in[categories_select] = categories_first_level();
  page_from_template('ad_create_select_category.html',$in);
}

$s[current_action] = 'ad_create';
$in[action] = 'ad_created';
$in[n] = 0;
$in[hide_ad_n_begin] = '<!--'; $in[hide_ad_n_end] = '-->';
$in[form_data] = ad_create_edit_form($in);
if (!$s[GC_u_n]) { if ($s[user_login_captcha]) $a[field_captcha_test] = parse_part('form_captcha_test.txt',''); $in[info] .= parse_part('ad_create_registration_info.txt',$a); }

page_from_template('ad_create.html',$in);
}

##################################################################################

function get_cat_free_period($bigboss) {
global $s;
$q = dq("select count(*) from $s[pr]ads_prices where c = '$bigboss'",1);
$x = mysql_fetch_row($q);
if ($x[0]) $q = dq("select max(days) from $s[pr]ads_prices where c = '$bigboss' and ad = 0.00",1);
else $q = dq("select max(days) from $s[pr]ads_prices where c = '0' and ad = 0.00",1);
$price = mysql_fetch_row($q);
if (!$price[0])
{ $q = dq("select count(*) from $s[pr]ads_prices where c = '0'",1);
  $x = mysql_fetch_row($q);
  if (!$x[0]) { $s[no_prices] = 1; return 30; }
}
return $price[0];
}

##################################################################################

function ad_edit($in) {
global $s,$m;
$ad = check_ad_owner($in[n],0,1);
if ($in[action]=='ad_edit')
{ if (!$ad[n]) problem($m[not_found]);
  $in[action] = 'ad_edit';
  $in = $ad;
}
//else $in = $ad;
$in[action] = 'ad_edited';
$s[current_action] = 'ad_edit';
$category = str_replace('_','',$ad[c]); $in[c1] = get_bigboss_category($category);
clean_item_files('a',$in[n]);
$s[form_data] = ad_create_edit_form($in);

$s = array_merge($s,(array)$in);
page_from_template('ad_edit.html',$s);
}

##################################################################################

function ad_create_edit_form($in) {
global $s,$m;
get_messages('ad_create_edit_functions.php');
if (is_array($in[c])) $in[c] = $in[c][0];
$category = get_category_variables(get_ad_first_category($in[c]));
$in[field_categories] = categories_rows_form('ad',$in,$in[c1]);
$in[field_areas] = areas_rows_form($in);
$y = list_of_categories_for_item('l',0,$in[c],'<br>',1); $in[current_categories] = $y[categories_names];

if ($s[ad_v_title]) { $x[item_name] = $m[title]; if ($s[ad_r_title]) $x[item_name] .= " *"; $x[field_name] = 'title'; $x[field_value] = $in[title]; $x[field_maxlength] = $s[ad_max_title]; $in[field_title] = parse_part('form_field.txt',$x); }
if ($s[ad_v_description]) { $x[item_name] = $m[description]; if ($s[ad_r_description]) $x[item_name] .= " *"; $x[field_name] = 'description'; $x[field_value] = $in[description]; $x[field_maxlength] = $s[ad_max_description]; $in[field_description] = parse_part('form_field.txt',$x); }
if ($s[ad_v_detail])
{ if (!$s[a_details_html_editor]) $in[detail] = str_replace('<br>',"\n",$in[detail]);
  $x[item_name] = $m[long_description]; if ($s[ad_r_detail]) $x[item_name] .= " *"; 
  if ($s[a_details_html_editor]) { $x[html_editor] = get_fckeditor('detail',$in[detail],'PublicToolbar'); $in[field_detail] = parse_part('form_detail_html.txt',$x); }
  else { $x[field_name] = 'detail'; $x[field_value] = $in[detail]; $in[field_detail] = parse_part('form_field_textarea.txt',$x); }
}
if (($s[ad_v_price]) AND ($category[price])) { $x[item_name] = "$m[price] $s[currency]"; if ($s[ad_r_price]) $x[item_name] .= " *"; $x[field_name] = 'price'; $x[field_value] = $in[price]; $x[field_maxlength] = 10; $in[field_price] = parse_part('form_field.txt',$x); }
if ($s[ad_v_url]) { $x[item_name] = $m[url]; if ($s[ad_r_url]) $x[item_name] .= " *"; $x[field_name] = 'url'; $x[field_value] = $in[url]; $x[field_maxlength] = 255; $in[field_url] = parse_part('form_field.txt',$x); }
if ($s[ad_v_name]) { $x[item_name] = $m[name]; if ($s[ad_r_name]) $x[item_name] .= " *"; $x[field_name] = 'name'; $x[field_value] = $in[name]; $x[field_maxlength] = 255; $in[field_name] = parse_part('form_field.txt',$x); }
if ($s[ad_v_email]) { $x[item_name] = $m[email]; if ($s[ad_r_email]) $x[item_name] .= " *"; $x[field_name] = 'email'; $x[field_value] = $in[email]; $x[field_maxlength] = 255; $in[field_email] = parse_part('form_field.txt',$x); }
if ($s[ad_v_pub_phone1]) { $x[item_name] = $m[pub_phone1]; if ($s[ad_r_pub_phone1]) $x[item_name] .= " *"; $x[field_name] = 'pub_phone1'; $x[field_value] = $in[pub_phone1]; $x[field_maxlength] = 255; $in[field_phone1] = parse_part('form_field.txt',$x); }
if ($s[ad_v_pub_phone2]) { $x[item_name] = $m[pub_phone2]; if ($s[ad_r_pub_phone2]) $x[item_name] .= " *"; $x[field_name] = 'pub_phone2'; $x[field_value] = $in[pub_phone2]; $x[field_maxlength] = 255; $in[field_phone2] = parse_part('form_field.txt',$x); }
if ($s[ad_v_address]) { $x[item_name] = $m[address]; if ($s[ad_r_address]) $x[item_name] .= " *"; $x[field_name] = 'address'; $x[field_value] = $in[address]; $x[field_maxlength] = 255; $in[field_address] = parse_part('form_field.txt',$x); }
if ($s[ad_v_youtube_video]) { $x[item_name] = $m[youtube_video]; if ($s[ad_r_youtube_video]) $x[item_name] .= " *"; $x[field_name] = 'youtube_video'; $x[field_value] = $in[youtube_video]; $in[field_youtube_video] = parse_part('form_field.txt',$x); }
if ($s[ad_v_keywords]) { $x[item_name] = "$m[keywords]<br><span class=\"text10\">$m[separated_commas]</span>"; if ($s[ad_r_keywords]) $x[item_name] .= " *"; $x[field_name] = 'keywords'; $x[field_value] = $in[keywords]; $in[field_keywords] = parse_part('form_field.txt',$x); }
if ($category[offer_wanted]) $in[field_offer_wanted] = form_get_offer_wanted($in[offer_wanted]);
if ($in[n]) $ad = get_ad_variables($in[n]);
if (($ad[x_paypal_by]>$s[cas]) AND ($ad[x_paypal_email]) AND ($ad[x_paypal_currency]) AND ($ad[x_paypal_price])) { if ($in[x_paypal_disable]) $in[paypal_disable_checked] = ' checked'; else $in[paypal_disable_checked] = ''; }
else { $in[hide_paypal_disable_begin] = '<!--'; $in[hide_paypal_disable_end] = '-->'; }

$x = usit_rows_form_public($in[c],$in[n],'',1,$s[current_action],$in); $in = array_merge((array)$in,(array)$x);

$in[field_pictures] = images_form_users('a',$in);
list($images,$files,$videos) = get_item_files('a',$in[n],0);

if ($in[n]) $item_n = $in[n]; else $item_n = 0;
for ($y=1;$y<=($s[a_max_files_users]+$in[x_files_max]);$y++)
{ $x[item_name] = "$m[upload_file]$y"; $x[field_name] = 'file_upload['.$item_n.']['.$y.']';
  $x[description_name] = 'file_description['.$item_n.']['.$y.']';
  if ($in[file_description][$item_n][$y]) $x[description_value] = $in[file_description][$item_n][$y];
  else $x[description_value] = $files[$item_n][$y][description];
  $in[field_files] .= parse_part('form_upload.txt',$x);
  if (($item_n) AND ($files[$item_n][$y][url]))
  { $x[current_file] = '<a target="_blank" href="'.$files[$item_n][$y][url].'">'.str_replace("$s[site_url]/uploads/files/",'',$files[$item_n][$y][url]).'</a>';
    $x[file_n] = $y;
    $in[field_files] .= parse_part('form_file_current.txt',$x);
  }
}
for ($y=1;$y<=($s[max_videos]+$in[x_videos_max]);$y++)
{ $x[item_name] = "$m[upload_video]$y"; $x[field_name] = 'video_upload['.$item_n.']['.$y.']';
  $x[description_name] = 'video_description['.$item_n.']['.$y.']';
  if ($in[video_description][$item_n][$y]) $x[description_value] = $in[video_description][$item_n][$y];
  else $x[description_value] = $videos[$item_n][$y][description];
  $x[allowed_extensions] = $m[allowed_extensions].implode(', ',$s[videos_extensions]);
  $in[field_videos] .= parse_part('form_upload.txt',$x);
  if (($item_n) AND ($videos[$item_n][$y][url]))
  { $x[current_video] = video_player($videos[$item_n][$y][description],400,350,'false');
    $x[file_n] = $y;
    $in[field_videos] .= parse_part('form_video_current.txt',$x);
  }
}
if (($s[ad_v_captcha]) AND (!$s[GC_u_n])) $in[field_captcha_test] = parse_part('form_captcha_test.txt',$x);
if (!$s[GC_u_n]) $in[field_terms] = parse_part('form_field_terms.txt',$x);
return parse_part('ad_create_edit_form.txt',$in);
}

########################################################################################

function areas_rows_form($in) {
global $s,$m;
$areas = $in[a];
if (!$in[n]) $in[n] = 0;
if (!is_array($areas)) $areas = explode(' ',str_replace('_','',$areas));
$areas_tree = areas_tree(1);
foreach ($areas as $k=>$v)
{ $areas_tree = str_replace("#%checked_$v%#",' checked',$areas_tree);
  $area = get_area_variables($v);
  $areas_array = array_merge((array)explode(' ',trim(str_replace('_',' ',$area[path_n]))),(array)$areas_array);
}

foreach ($areas_array as $k=>$v) $areas_tree = str_replace("#%expand_$v%#",' style="display:block"',$areas_tree);
$a[areas_tree] = $areas_tree;
$a[max] = $s[max_areas];
return parse_part('form_areas.txt',$a);
}

##################################################################################

function areas_rows_form_old($in) {
global $s,$m;
$areas = $in[a];
if (!$in[n]) $in[n] = 0;
if (!is_array($areas)) $areas = explode(' ',str_replace('_','',$areas));
for ($x=0;$x<=$s[max_areas]-1;$x++)
{ $b[select_boxes] .= '<select class="field10" name="a[]">';
  if ($x) $b[select_boxes] .= '<option value="0">'.$m[none].'</option>';
  $b[select_boxes] .= areas_selected($areas[$x],1,1,0,0).'</select><br>';
}
$b[max] = $s[max_areas];
$a = parse_part('form_areas.txt',$b);
return $a;
}

##################################################################################

function categories_rows_form($what,$in,$bigboss) {
global $s,$m;
$categories = $in[c];
if (!$in[n]) $in[n] = 0;
if (!is_array($categories)) $categories = explode(' ',str_replace('_','',$categories));
$categories_tree = categories_tree($bigboss);
foreach ($categories as $k=>$v)
{ $categories_tree = str_replace("#%checked_$v%#",' checked',$categories_tree);
  $category = get_category_variables($v);
  $categories_array = array_merge((array)explode(' ',trim(str_replace('_',' ',$category[path_n]))),(array)$categories_array);
}

foreach ($categories_array as $k=>$v) $categories_tree = str_replace("#%expand_$v%#",' style="display:block"',$categories_tree);
$a[categories_tree] = $categories_tree;
$a[c1] = $in[c1];
$a[max] = $s[max_cats];
return parse_part('form_categories.txt',$a);
}

##################################################################################

function categories_rows_form_old($what,$in,$bigboss) {
global $s,$m;
$categories = $in[c];
if (!$in[n]) $in[n] = 0;
if (!is_array($categories)) $categories = explode(' ',str_replace('_','',$categories));
if ($bigboss) $only_bigboss_n = get_bigboss_category($bigboss);
elseif ($categories[0]) $only_bigboss_n = get_bigboss_category($categories[0]);
//if ($only_list) $s[max_cats] = 1;
//if ($first_level) { $what = 'ad_first'; $no_info = 1; }

for ($x=0;$x<=$s[max_cats]-1;$x++)
{ $b[select_boxes] .= '<select class="field10" name="c[]">';
  if ($x) $b[select_boxes] .= '<option value="0">'.$m[none].'</option>';
  $b[select_boxes] .= categories_selected($what,$categories[$x],1,1,0,$no_info,$only_bigboss_n).'</select><br>';
}
if ($only_list) return $b[select_boxes];
$y = list_of_categories_for_item('ad',0,$categories,'<br>',1); $b[current_categories] = $y[categories];
$b[c1] = $in[c1];
$b[max] = $s[max_cats];
$a = parse_part('form_categories.txt',$b);
return $a;
}

##################################################################################

function form_get_offer_wanted($offer_wanted) {
global $s;
if ($offer_wanted=='offer') $in[offer] = ' selected'; elseif ($offer_wanted=='wanted') $in[wanted] = ' selected';
return parse_part('form_offer_wanted.txt',$in);
}

##################################################################################

function usit_rows_form_public($category,$ad_n,$fields_name,$only_visible_forms,$action,$in) {
global $s;
$category = str_replace('_','',$category); $bigboss = get_bigboss_category($category);
list($usits,$avail_val) = get_category_usit($bigboss,$only_visible_forms,0);
if ($ad_n)
{ if ($action=='ad_edit') $queue = 0;
  $from_database = usit_get_current_values($ad_n,$queue);
}
foreach ($usits as $k=>$usit)
{ unset($list[value]);
  if ($usit[required]) $usit[description] .= '*';
  if ($usit[item_type]=='text')
  { $template = 'form_user_item_text.txt';
    if (($_POST) AND ($in['user_item_'.$usit[n]])) $usit[value] = $in['user_item_'.$usit[n]]; //new (something entered)
    elseif ($in['user_item_'.$usit[usit_n]]) $usit[value] = $in['user_item_'.$usit[usit_n]]; // edited
    elseif ($ad_n) $usit[value] = $from_database[$usit[n]][value_text]; // edit
    else $usit[value] = $usit[def_value_text]; // new
  }
  elseif ($usit[item_type]=='textarea')
  { $template = 'form_user_item_textarea.txt';
    if (($_POST) AND ($in['user_item_'.$usit[n]])) $usit[value] = $in['user_item_'.$usit[n]]; //new (something entered)
    elseif ($in['user_item_'.$usit[usit_number]]) $usit[value] = $in['user_item_'.$usit[usit_number]]; // new (something entered) or edited
    elseif ($ad_n) $usit[value] = $from_database[$usit[n]][value_text]; // edit
    else $usit[value] = $usit[def_value_text]; // new
  }
  elseif ($usit[item_type]=='htmlarea')
  { $template = 'form_user_item_htmlarea.txt';
    if (($_POST) AND ($in['user_item_'.$usit[n]])) $usit[value] = $in['user_item_'.$usit[n]]; //new (something entered)
    elseif ($in['user_item_'.$usit[usit_number]]) $usit[value] = $in['user_item_'.$usit[usit_number]]; // new (something entered) or edited
    elseif ($ad_n) $usit[value] = $from_database[$usit[n]][value_text]; // edit
    else $usit[value] = $usit[def_value_text]; // new
    $usit[html_editor] = get_fckeditor('user_item_'.$usit[n],refund_html($usit[value]),'PublicToolbar');
  }
  else
  { if (($_POST) AND ($in['user_item_'.$usit[n]])) $value = $in['user_item_'.$usit[n]]; //new (something entered)
    elseif ($in['user_item_'.$usit[usit_number]]) $value = $in['user_item_'.$usit[usit_number]]; // edited
    elseif ($ad_n) $value = $from_database[$usit[n]][value_code]; // edit
    else $value = $usit[def_value_code]; // new
    if ($usit[item_type]=='checkbox')
    { $template = 'form_user_item_checkbox.txt';
      if ($value) $usit[checked] = ' checked'; else $usit[checked] = '';
      $usit_value = '<input type="checkbox" name="user_item_'.$usit[n].'" value="1"'.$usit[checked].'>';
    }
    elseif ($usit[item_type]=='radio')
    { $template = 'form_user_item_radio.txt';
      foreach ($avail_val[$usit[n]] as $k1=>$one_option)
      { if ($value==$one_option[n]) $x = ' checked'; else $x = '';
	    $usit[options] .= '<input type="radio" name="user_item_'.$usit[n].'" value="'.$one_option[n].'"'.$x.'>'.$one_option[description].'<br>';
      }
      $usit_value = $usit[options];
    }
    elseif ($usit[item_type]=='select')
    { $template = 'form_user_item_select.txt';
      foreach ($avail_val[$usit[n]] as $k1=>$one_option)
      { if ($value==$one_option[n]) $x = ' selected'; else $x = '';
        $usit[options] .= '<option value="'.$one_option[n].'"'.$x.'>'.$one_option[description].'</option><br>';
      }
      $usit_value = '<select name="user_item_'.$usit[n].'" class="field10">'.$usit[options].'</select>';
    }   
    elseif ($usit[item_type]=='multiselect')
    { $template = 'form_user_item_multiselect.txt';
      if (($from_database[$usit[n]][value_text]) AND ($_POST[action]!='ad_edited')) $value = explode(' ',trim(str_replace('_',' ',$from_database[$usit[n]][value_text])));
      foreach ($avail_val[$usit[n]] as $k1=>$one_option)
      { if (in_array($one_option[n],$value)) $x = ' selected'; else $x = '';
        $usit[options] .= '<option value="'.$one_option[n].'"'.$x.'>'.$one_option[description].'</option><br>';
      }
      $usit_value = '<select name="user_item_'.$usit[n].'" class="field10">'.$usit[options].'</select>';
    }   
  }
  $display_it[$usit[usit_n]] = $usit[visible_forms];
  
  $b[$usit[usit_n]] = $a['user_item_'.$usit[usit_n]] = parse_part($template,$usit);
  $a['user_item_value_'.$usit[usit_n]] = $usit_value; unset($usit_value);
  $a['user_item_name_'.$usit[usit_n]] = $usit[description];
}
foreach ($b as $k=>$v) if (!$display_it[$k]) unset($b[$k]);
$a[field_user_defined] = implode('',$b);
return $a;
}

######################################################################################

function ad_paypal_enable($in) {
global $s,$m;
if (!is_numeric($in[n])) exit;
$ad = check_ad_owner($in[n]);
dq("update $s[pr]ads set x_paypal_disabled = '0' where n = '$in[n]'",1);
$s[info] =  info_line($m[ad_has_been_enabled]);
user_home_page();
}

##################################################################################
##################################################################################
##################################################################################

function ad_created($in) {
global $s,$m;

if (($s[post_ads_who]) OR ($s[GC_u_n]))
{ $user = check_logged_user();
  if (($s[post_ads_who]==2) AND (!$user[post_ads])) problem($m[dont_right_add]);
}
$data = ad_form_control($in);

$in = $data[1];

if ($data[0])
{ $in[info] = info_line($m[errorsfound],implode('<br>',$data[0]));
  ad_create($in);
}


$category = str_replace('_','',$old[c]); $bigboss = get_bigboss_category($in[c]);
list($usits,$avail_val) = get_category_usit($bigboss,1,0);
$s[free_period] = get_cat_free_period($bigboss);

$n = insert_ad(0,$in);

if (!$s[waiting]) ad_created_edited_send_emails('created',$n);

if ((!$s[GC_u_n]) OR ($s[no_prices]))
{ if ($s[waiting]) $s[info] = info_line($m[ad_created_waiting]);
  elseif ($s[ad_autoapr]) $s[info] = info_line($m[new_islive]);
  else $s[info] = info_line($m[ad_created_queue]);
  $not_include = 1;
  include("$s[phppath]/classified.php");
  if (($s[ad_autoapr]) OR ($s[waiting])) $queue = 0; else $queue = 1;
  show_ad_details($n,$queue);
}

recount_ads_for_owner($s[GC_u_n]);
$in[n] = $n;
//if ($s[ad_autoapr_user]) { 
if ($s[free_period]) $s[info] = info_line($m[ad_created_user]); else $s[info] = info_line($m[ad_created_user1]);
ad_features_edit($in);/* }*/
//else { $s[info] = info_line($m[new_queue]); user_home_page(); }
}

##################################################################################

function ad_edited($in) {
global $s,$m;
check_logged_user();
$data = ad_form_control($in);
$in = $data[1];
if ($data[0])
{ $s[info] = info_line($m[errorsfound],implode('<br>',$data[0]));
  ad_edit($in);
}

$category = str_replace('_','',$old[c]); $bigboss = get_bigboss_category($old[c]);
list($usits,$avail_val) = get_category_usit($bigboss,1,0);
ad_edited_to_database($in);
ad_created_edited_send_emails('edited',$in[n]);
if (!$s[ad_autoapr_user]) { $s[info] = info_line($m[edited_queue]); user_home_page(); }
$s[info] = info_line($m[edited_islive]);
recount_ads_for_owner($s[GC_u_n]);
ad_edit($in);
}


#############################################################################

function ad_created_edited_send_emails($action,$n) {
global $s,$m;

if ($s[GC_u_n]) { if ($s[ad_autoapr_user]) $queue = 0; else $queue = 1; }
else { if ($s[ad_autoapr]) $queue = 0; else $queue = 1; }
$ad = get_ad_variables($n,$queue);

if ($ad[offer_wanted]) $ad[ad_type] = $m[$ad[offer_wanted]]; else $ad[ad_type] = $m[na];
$x = list_of_categories_for_item('ad',0,$ad[c],', ',0); $ad = array_merge((array)$ad,(array)$x);
$x = list_of_areas_for_item($ad[a],', ',0); $ad = array_merge((array)$ad,(array)$x);

$ad[created_edited] = strtoupper($action);
if ($queue)
{ $ad[admin_queue_info] = 'This classified ad is currently in the queue. To accept or reject it, you have to go to the Queue screen in administration.';
  $ad[owner_info] = $m[new_queue];
}
else $ad[owner_info] = $m[new_islive];
if ($s[GC_u_n]) { $ad[owner_n] = $s[GC_u_n]; $ad[owner_username] = $s[GC_u_email]; $ad[owner_email] = $s[GC_u_email]; $ad[owner_name] = $s[GC_u_name]; }
else $ad[owner_n] = $m[na];

if ($s[new_email_admin])
{ $a[to] = $s[mail]; $ad[where_hear] = $_POST[where_hear];
  mail_from_template('ad_created_edited_admin.txt',$ad);
}
if ($s[new_email_owner]) 
{ if ($ad[owner_email]) $ad[to] = $ad[owner_email]; else $ad[to] = $ad[email];
  mail_from_template('ad_created_edited_owner.txt',$ad);
}
}

##################################################################################

function ad_form_control($in) {
global $s,$m;
foreach ($in as $k=>$v) if (!is_array($v)) $in[$k] = html_entity_decode(str_replace('&nbsp;',' ',$v)); 
$in = replace_array_text($in);
if ($in[n])
{ $old = check_ad_owner($in[n]);
  $in[old_c_path] = $old[c_path]; $in[old_a_path] = $old[a_path];
}
elseif (($s[ad_v_captcha]) AND (!$s[GC_u_n])) { $x = check_entered_captcha($in[image_control]); if ($x) $problem[] = $x; }
//if (!$in[n]) { if (!$in[terms]) $problem[] = 'Please read and agree to the Terms and Conditions'; }
$category_vars = get_category_variables(get_ad_first_category($in[c][0]));
foreach ($in as $k=>$v) { $black = try_blacklist($v,'word'); if ($black) $problem[] = $black; }

$in[c] = array_unique($in[c]);
$in[c_path] = ad_edit_get_categories($in[c],1);
unset($x); foreach ($in[c] as $k => $v) if ($v) $x[] = '_'.$v.'_'; $x = array_slice($x,0,$s[max_cats]); $in[c] = implode(' ',$x);
if ($s[category_error]) $problem[] = $s[category_error];

$in[a_path] = ad_edit_get_areas($in[a],1);
unset($x); foreach ($in[a] as $k => $v) if ($v) $x[] = '_'.$v.'_';$x = array_slice($x,0,$s[max_areas]); $in[a] = implode(' ',$x);
if ($s[area_error]) $problem[] = $s[area_error];

$in[en_cats] = has_some_enabled_categories($in[c]);
$in[rewrite_url] = discover_rewrite_url($in[title],0);
$in[zip] = str_replace(' ','',$in[zip]);
/*
if (($in[title]) AND (!$in[n]))
{ if ($s[GC_u_n]) $where = "and owner = '$s[GC_u_n]'"; elseif ($in[email]) $where = "and email = '$in[email]'";
  $q = dq("select n from $s[pr]ads where title = '$in[title]' $where",0);
  if (mysql_num_rows($q)) $problem[] = "You have already submitted this ad";
}
*/
if (($s[ad_r_title]) and (!$in[title])) $problem[] = "$m[m_field] $m[title]";
elseif ($in[title])
{ $x = strlen(utf8_decode($in[title]));
  if (($x<$s[ad_min_title]) OR ($x>$s[ad_max_title])) $problem[] = "$m[title] $m[should_be] $s[ad_min_title]-$s[ad_max_title] $m[characters]";
}
if ($s[convert_title]) $in[title] = ucwords(my_strtolower($in[title]));

if (($s[ad_r_description]) and (!$in[description])) $problem[] = "$m[m_field] $m[description]";
elseif ($in[description])
{ $x = strlen(utf8_decode($in[description]));
  if (($x<$s[ad_min_description]) OR ($x>$s[ad_max_description])) $problem[] = "$m[description] $m[should_be] $s[ad_min_description]-$s[ad_max_description] $m[characters]";
}
if ($s[convert_description]) $in[description] = ucfirst(my_strtolower($in[description]));

if (!$s[a_details_html_editor]) $in[detail] = str_replace("\n",'<br>',strip_tags($in[detail]));
$in[detail] = refund_html($in[detail]);
if (($s[ad_r_detail]) and (!$in[detail])) $problem[] = "$m[m_field] $m[long_description]";
elseif ($in[detail])
{ $x = strlen(utf8_decode($in[detail]));
  if (($x<$s[ad_min_detail]) OR ($x>$s[ad_max_detail])) $problem[] = "$m[long_description] $m[should_be] $s[ad_min_detail]-$s[ad_max_detail] $m[characters]";
}

if (($s[ad_r_keywords]) and (!$in[keywords])) $problem[] = "$m[m_field] $m[keywords]";
elseif ($in[keywords])
{ $x = strlen(utf8_decode($in[keywords]));
  if (($x<$s[ad_min_keywords]) OR ($x>$s[ad_max_keywords])) $problem[] = "$m[keywords] $m[should_be] $s[ad_min_keywords]-$s[ad_max_keywords] $m[characters]";
}

if (($s[ad_r_price]) and ($category_vars[price]) and (!$in[price])) $problem[] = "$m[m_field] $m[price]"; // jen nektere kategorie
if (($s[ad_r_url]) and (!$in[url])) $problem[] = "$m[m_field] $m[url]";
if (($s[ad_r_name]) and (!$in[name])) $problem[] = "$m[m_field] $m[name]";
if (($s[ad_r_email]) and (!$in[email])) $problem[] = "$m[m_field] $m[email]";
elseif (strlen($in[email]) > 255) $problem[] = $m[a_email];
if ((trim($in[email])) AND (!check_email($in[email]))) $problem[] = $m[w_email];
if (($s[ad_r_pub_phone1]) and (!$in[pub_phone1])) $problem[] = "$m[m_field] $m[pub_phone1]";
if (($s[ad_r_pub_phone2]) and (!$in[pub_phone2])) $problem[] = "$m[m_field] $m[pub_phone2]";
if ($in[address]) get_geo_data($in[address],0,0); //if ($s[new_address]) $in[address] = $s[new_address];
//foreach ($s[ll_data] as $k=>$v) echo "$k - $v<br>";
foreach ($s[ll_data] as $k=>$v) $in[$k] = $v;
if ($s[ad_r_address])
{ if (!$in[address]) $problem[] = "$m[m_field] $m[address]";
  elseif (!$s[ll_data][latitude]) $problem[] = "$m[address_unkown]";
}
if (($s[ad_r_youtube_video]) and (!$in[youtube_video])) $problem[] = "$m[m_field] $m[youtube_video]";
if (($in[url]) AND (!strstr($in[url],'http://'))) $in[url] = "http://$in[url]";

list($usits,$usit_values) = get_category_usit(get_bigboss_category($category_vars[n]),1,0);
foreach ($usits as $n=>$usit) if (($usit[item_type]!='checkbox') AND ($usit[required]) AND (!$in['user_item_'.$usit[n]])) $problem[] = "$m[m_field] $usit[description]";

foreach ($_FILES[image_upload][name][$in[n]] as $file_n=>$v)
{ if (!trim($v)) continue;
  $x = check_file_size($_FILES[image_upload][name][$in[n]][$file_n],$_FILES[image_upload][size][$in[n]][$file_n],$s[max_filesize]); if ($x) $problem[] = $x;  
  if (($_FILES[image_upload][name][$in[n]][$file_n]) AND (!$_FILES[image_upload][size][$in[n]][$file_n])) $problem[] = "This file can't be uploaded because it exceeds our server limits: ".$_FILES[image_upload][name][$in[n]][$file_n];
  elseif ($s[img_ext_by_mime]) { if (!in_array($_FILES[image_upload][type][$in[n]][$file_n],$s[images_mime_types])) $problem[] = "$m[not_allowed_format] ".$_FILES[image_upload][name][$in[n]][$file_n]; }
  elseif (!in_array(strtolower(str_replace('.','',strrchr($_FILES[image_upload][name][$in[n]][$file_n],'.'))),$s[images_extensions])) $problem[] = "$m[not_allowed_format] ".$_FILES[image_upload][name][$in[n]][$file_n];
}
foreach ($_FILES[file_upload][name][$in[n]] as $file_n=>$v)
{ if (!trim($v)) continue;
  $x = check_file_size($_FILES[file_upload][name][$in[n]][$file_n],$_FILES[file_upload][size][$in[n]][$file_n],$s[max_filesize]); if ($x) $problem[] = $x;
  if (!$s[allowed_formats]) $s[allowed_formats] = get_file_formats(1);
  if (($_FILES[file_upload][name][$in[n]][$file_n]) AND (!$_FILES[file_upload][size][$in[n]][$file_n])) $problem[] = "This file can't be uploaded because it exceeds our server limits: ".$_FILES[file_upload][name][$in[n]][$file_n];
  elseif ($s[file_ext_by_mime]) { if (!in_array($_FILES[file_upload][type][$in[n]][$file_n],$s[allowed_formats][mime_types])) $problem[] = "$m[not_allowed_format] ".$_FILES[file_upload][name][$in[n]][$file_n]; }
  elseif (!in_array(str_replace('.','',strrchr($_FILES[file_upload][name][$in[n]][$file_n],'.')),$s[allowed_formats][extensions])) $problem[] = "$m[not_allowed_format] ".$_FILES[file_upload][name][$in[n]][$file_n];
}
return array($problem,$in);
}


########################################################################################

function check_file_size($filename,$size,$max) {
global $s,$m;
if ($size>$max) return "$m[max_file_size_1] $filename $m[max_file_size_2] $size $m[max_file_size_3] $max $m[max_file_size_4]";
}

##################################################################################

function ad_edited_to_database($in) {
global $s;
if ($s[GC_u_n]) $autoapprove = $s[ad_autoapr_user]; else $autoapprove = $s[ad_autoapr];
if ($autoapprove)
{ dq("update $s[pr]ads set title = '$in[title]', description = '$in[description]', detail = '$in[detail]', keywords = '$in[keywords]', address = '$in[address]', youtube_video = '$in[youtube_video]', offer_wanted = '$in[offer_wanted]', price = '$in[price]', c = '$in[c]', c_path = '$in[c_path]', a = '$in[a]', a_path = '$in[a_path]', url = '$in[url]', name = '$in[name]', email = '$in[email]', pub_phone1 = '$in[pub_phone1]', pub_phone2 = '$in[pub_phone2]', country = '$in[country]', region = '$in[region]', city = '$in[city]', zip = '$in[zip]', latitude = '$in[latitude]', longitude = '$in[longitude]', rewrite_url = '$in[rewrite_url]', en_cats = '$in[en_cats]', edited = '$s[cas]', x_paypal_disable = '$in[x_paypal_disable]' where n = '$in[n]' and status = 'enabled'",1);
  add_update_user_items($in[n],0,ad_created_edited_get_usit($in[c],$in));
  get_geo_data($in[address],$in[n],0);
  upload_files('a',$in[n],$in,0,1,$in[delete_image],$in[delete_file],$in[delete_video]);
  if (!$s[dont_recount]) recount_ads_cats_areas($in[c_path],$in[old_c_path],$in[a_path],$in[old_a_path]);
  update_item_index('ad',$in[n]);
}
else insert_ad($in[n],$in);
}

##################################################################################

function insert_ad($n,$in) {
global $s;

if ($n) // edited queue
{ $old = get_ad_variables($in[n]);
  dq("delete from $s[pr]ads where n = '$n' and status = 'queue'",1);
  dq("insert into $s[pr]ads values ('$n','$in[title]','$in[description]','$in[detail]','$in[keywords]','','$in[address]','$in[youtube_video]','$in[offer_wanted]','$in[price]','$in[c]','$in[c_path]','$in[a]','$in[a_path]','$in[url]','$in[name]','$in[email]','$in[pub_phone1]','$in[pub_phone2]','$in[country]','$in[region]','$in[city]','$in[zip]','$in[latitude]','$in[longitude]','$s[GC_u_n]','$old[created]','$s[cas]','$old[t1]','$old[t2]','queue','$in[en_cats]','$in[rewrite_url]','$old[clicks_total]','$old[popular]','$old[comments]','$old[x_bold_by]','$old[x_featured_by]','$old[x_home_page_by]','$old[x_featured_gallery_by]','$old[x_highlight_by]','$old[x_pictures_by]','$old[x_pictures_max]','$old[x_files_by]','$old[x_files_max]','$old[x_paypal_by]','$old[x_paypal_email]','$old[x_paypal_currency]','$old[x_paypal_price]','$old[x_paypal_disable]','0')",1);
  //dq("delete from $s[pr]files where file_type = 'image' and what = 'a' and item_n = '$n' and queue = '1'",1);
  //$q = dq("select * from $s[pr]files where item_n = '$n' and queue = '0'",1);
  //while ($old_file = mysql_fetch_assoc($q))
  //dq("insert into $s[pr]files values(NULL,'a','$n','1','$old_file[file_n]','$old_file[filename]','$old_file[description]','$old_file[file_type]','$old_file[extension]','$old_file[size]')",1);
  upload_files('a',$n,$in,1,1,$in[delete_image],$in[delete_file],$in[delete_video]);
  add_update_user_items($n,1,ad_created_edited_get_usit($in[c],$in));
  update_item_index('ad',$n);
  update_item_image1('a',$n);
}
else // new
{ if ($s[GC_u_n]) $autoapprove = $s[ad_autoapr_user]; else $autoapprove = $s[ad_autoapr];
  if ((!$s[GC_u_n]) AND ($s[ad_email_confirm])) { $status = 'waiting'; $s[waiting] = 1; }
  elseif ($autoapprove) { $queue = 0; $status = 'enabled'; }
  else { $queue = 1; $status = 'queue'; }
  $n = get_new_ad_n();
  $t1 = $s[cas]; $t2 = $s[cas] + ($s[free_period]*86400);
  dq("insert into $s[pr]ads values ('$n','$in[title]','$in[description]','$in[detail]','$in[keywords]','','$in[address]','$in[youtube_video]','$in[offer_wanted]','$in[price]','$in[c]','$in[c_path]','$in[a]','$in[a_path]','$in[url]','$in[name]','$in[email]','$in[pub_phone1]','$in[pub_phone2]','$in[country]','$in[region]','$in[city]','$in[zip]','$in[latitude]','$in[longitude]','$s[GC_u_n]','$s[cas]','0','$t1','$t2','$status','$in[en_cats]','$in[rewrite_url]','0','0','0','0','0','0','0','0','0','0','0','0','0','','','','$in[x_paypal_disable]','0')",1);
  dq("insert into $s[pr]ads_stat values ('$n','$s[GC_u_n]','0','0','0','0','$s[cas]')",1);
  upload_files('a',$n,$in,$queue,1);
  add_update_user_items($n,$queue,ad_created_edited_get_usit($in[c],$in));
  if ((!$queue) AND (!$s[dont_recount])) { recount_ads_cats_areas($in[c_path],'',$in[a_path],''); }
  update_item_index('ad',$n);
  //if ($status=='enabled') dq("insert into $s[pr]u_to_email values('a','$n')",1);
  update_item_image1('a',$n);
  if ($status=='waiting')
  { $in[confirmation_link] = "$s[site_url]/user.php?action=ad_confirm&n=$n&x=".md5($s[cas].$n.$s[year]);
	$in[to] = $in[email];
    mail_from_template('ad_confirm.txt',$in);
  }
  return $n;
}
}

##################################################################################

function ad_confirm($in) {
global $s,$m;
if (!is_numeric($in[n])) exit;
$ad = get_ad_variables($in[n]);
$code = md5($ad[created].$ad[n].$s[year]);
if ($code!=$in[x]) problem($m[incorrect_confirm_url]);
if ($s[ad_autoapr]) { dq("update $s[pr]ads set status = 'enabled' where n = '$in[n]'",1); $queue = 0; }
else { dq("update $s[pr]ads set status = 'queue' where n = '$in[n]'",1); $queue = 1; }
dq("update $s[pr]ads_usit set queue = '$queue' where n = '$in[n]'",1);
dq("update $s[pr]files set queue = '$queue' where item_n = '$in[n]' and what = 'a'",1);
$s[info] = info_line($m[ad_confirmed]);

ad_created_edited_send_emails('created',$in[n]);
if ($s[ad_autoapr]) $s[info] = info_line($m[new_islive]);
else $s[info] = info_line($m[ad_created_queue]);
$not_include = 1;
include("$s[phppath]/classified.php");
if ($s[ad_autoapr]) $queue = 0; else $queue = 1;
update_item_index('ad',$in[n]);
show_ad_details($in[n],$queue);
}

##################################################################################
##################################################################################
##################################################################################

function ad_features_edit($in) {
global $s,$m;
if (($in[action]=='ad_created') AND (!$s[ad_autoapr_user])) $queue = 1;
$ad = check_ad_owner($in[n],$queue);
$ad[prices_table] = get_prices_table($ad[c]);
$ad[ad_active] = $m[active_by].' '.datum($ad[t2],0);
foreach ($s[extra_options] as $k=>$v) { if ($ad['x_'.$v.'_by']>$s[cas]) { $ad[$v.'_checked'] = ' checked'; $ad[$v.'_active'] = $m[active_by].' '.datum($ad['x_'.$v.'_by'],0); } }
if ($ad[x_pictures_by]>$s[cas]) { $ad['xtra_pictures_'.$ad[x_pictures_max]] = ' selected'; $ad[pictures_active] = $m[active_by].' '.datum($ad[x_pictures_by],0).' - '.$ad[x_pictures_max].' pictures'; }
if ($ad[x_files_by]>$s[cas]) { $ad['xtra_files_'.$ad[x_files_max]] = ' selected'; $ad[files_active] = $m[active_by].' '.datum($ad[x_files_by],0).' - '.$ad[x_files_max].' pictures'; }
$ad[period_options] = $s[period_options];
$ad[paypal_currencies_select] = pp_currency_select('x_paypal_currency',$ad[x_paypal_currency]);
$ad[info] = $s[info];
page_from_template('ad_edit_features.html',$ad);
}

##################################################################################

function ad_features_edited($in) {
global $s,$m;
$in = replace_array_text($in);
$ad = check_ad_owner($in[n]);
dq("update $s[pr]ads set x_paypal_email = '$in[x_paypal_email]', x_paypal_price = '$in[x_paypal_price]', x_paypal_currency = '$in[x_paypal_currency]' where n = '$in[n]'",1);
$ad[price] = get_ad_price($in[bold],$in[highlight],$in[featured],$in[home_page],$in[featured_gallery],$in[paypal],$in[xtra_pictures],$in[xtra_files],$in[xtra_videos],$in[period],$ad[created],$in[n],$ad[c],0);
if ($ad[price]<0.01) ad_features_edit($in);
$ad[ordered_features] .= $m[basic_listing].'<br>';
foreach ($s[extra_options] as $k=>$v) { if ($in[$v]) { $ad[ordered_features] .= $m['xtra_'.$v].'<br>'; $ad[hidden_fields] .= '<input type="hidden" name="'.$v.'" value="1">'; } }
if ($in[xtra_pictures]) { $ad[ordered_features] .= 'Extra '.$in[xtra_pictures].' pictures<br>'; $ad[hidden_fields] .= '<input type="hidden" name="xtra_pictures" value="'.$in[xtra_pictures].'">'; }
if ($in[xtra_files]) { $ad[ordered_features] .= 'Extra '.$in[xtra_files].' files<br>'; $ad[hidden_fields] .= '<input type="hidden" name="xtra_files" value="'.$in[xtra_files].'">'; }
$ad[period] = $in[period]; $ad[hidden_fields] .= '<input type="hidden" name="period" value="'.$in[period].'">';
page_from_template('ad_edited_features.html',$ad);
}

##################################################################################

function ad_features_edited_confirmed($in) {
global $s,$m;
$in = replace_array_text($in);
$ad = check_ad_owner($in[n]);
list ($ad[price],$payment_n) = get_ad_price($in[bold],$in[highlight],$in[featured],$in[home_page],$in[featured_gallery],$in[paypal],$in[xtra_pictures],$in[xtra_files],$in[xtra_videos],$in[period],$ad[created],$in[n],$ad[c],1);
create_payment_process_record($payment_n);
if (($s[pp_currency]) AND ($s[pp_email])) $ad[payment_links] = get_paylink_paypal($s[GC_u_email],$s[GC_u_password],$payment_n,$ad[price]);
if (($s[co_n]) AND ($s[co_secret_word])) $ad[payment_links] .= get_paylink_2checkout($payment_n,$ad[price]);
if (trim($s[other_payment_com])) $ad[payment_links] .= str_replace('#%order%#',$payment_n,str_replace('#%price%#',$ad[price],$s[other_payment_com]));
page_from_template('ad_payment_page.html',$ad);
}

##################################################################################

function create_payment_process_record($n) {
global $s;
if ($_COOKIE[GC_u_n]) $remember_me = 1;
dq("delete from $s[pr]payment_process where time < ($s[cas]-600) or user = '$s[GC_u_n]'",1);
dq("insert into $s[pr]payment_process values ('$s[ip]','$n','$s[GC_u_n]','$s[cas]','$remember_me')",1);
}

##################################################################################

function get_paylink_paypal($username,$password,$payment_n,$price) {
global $s;
if ($s[pp_test]) $data[paypal_url] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
else $data[paypal_url] = 'https://www.paypal.com/cgi-bin/webscr';
$data[pp_currency] = $s[pp_currency]; $data[pp_email] = $s[pp_email];
$data[n] = $payment_n; $data[price] = $price;
return parse_part('order_form_paypal.txt',$data);
}

##################################################################################

function get_paylink_2checkout($payment_n,$price) {
global $s;
$data[order_id] = $payment_n; $data[price] = $price;
$data[user_id] = $s[co_n]; if ($s[co_test]) $data[test] = '&demo=Y';
return parse_part('order_form_2checkout.txt',$data);
}

##################################################################################

function get_paylink_any($payment_n,$price) {
global $s;
$data[order] = $payment_n; $data[price] = $price;
return parse_part('order_form_any.txt',$data);
}

##################################################################################

function get_ad_price($bold,$highlight,$featured,$home_page,$featured_gallery,$paypal,$xtra_pictures,$xtra_files,$xtra_videos,$days,$created_time,$ad,$c,$insert) {
global $s;
$bigboss = get_bigboss_category($c);
$free_period = get_cat_free_period($bigboss);
$q = dq("select * from $s[pr]ads_prices where c = '$bigboss' and days = '$days'",1);
if (!mysql_num_rows($q)) $q = dq("select * from $s[pr]ads_prices where c = '0' and days = '$days'",1);
$x = mysql_fetch_assoc($q);
if (!$x[days]) die('Not allowed number of days');
if ((($s[cas]-$created_time)/86400) > ($free_period-$days)) $price = $price + $x[ad];
foreach ($s[extra_options] as $k=>$v) if (($v) AND ($$v)) $price = $price + $x[$v];
if ($xtra_pictures) $price = $price + $x['xtra_'.$xtra_pictures.'_pictures'];
if ($xtra_files) $price = $price + $x['xtra_'.$xtra_files.'_files'];
if ($insert)
{ dq("insert into $s[pr]ads_orders values(NULL,'$ad','$s[GC_u_n]','$s[cas]','$price','0','$control','','')",1);
  $order_n = mysql_insert_id();
  dq("insert into $s[pr]ads_orders_parts values('$order_n','$ad','$s[GC_u_n]','$days','1','$bold','$featured','$home_page','$featured_gallery','$highlight','$paypal','$xtra_pictures','$xtra_files')",1);
  return array($price,$order_n);// do not use number_format
}
return number_format($price,2);
}

##################################################################################
##################################################################################
##################################################################################

?>