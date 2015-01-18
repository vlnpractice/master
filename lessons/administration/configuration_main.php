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
check_admin('configuration');

switch ($_POST[action]) {
case 'configuration_edited_main'		: configuration_edited_main($_POST);
}
configuration_edit_main();
	
#################################################################################
#################################################################################
#################################################################################

function configuration_edit_main() {
global $info;
include("../data/data.php");

$s = stripslashes_array($s);
foreach ($s as $k=>$v) $s[$k] = htmlspecialchars($v);
$x = explode(',',$s[a_tags]); foreach ($x as $k => $v) { if ($v) $x[$k] = "<$v>"; $s[a_tags] = implode(',',$x); }
$x = explode(',',$s[sort_ads_options]); foreach ($x as $k => $v) { $sort_ads_options[$k] = $v; }
$x = explode(',',$s[sort_a]); foreach ($x as $k => $v) { $sort_a[$k] = $v; }

if (!$s[site_url])
{ $s[site_url] = str_replace('/administration/home.php?action=left_frame','',getenv('HTTP_REFERER'));
  $s[new_img] = htmlspecialchars('<img border="0" src="'.$s[site_url].'/images/button_new.png">');
  $s[upd_img] = htmlspecialchars('<img border="0" src="'.$s[site_url].'/images/button_updated.png">');
  $s[pick_img] = htmlspecialchars('<img border="0" src="'.$s[site_url].'/images/button_pick.png">');
  $s[pop_img] = htmlspecialchars('<img border="0" src="'.$s[site_url].'/images/button_popular.png">');
  $s[featured_img] = htmlspecialchars('<img border="0" src="'.$s[site_url].'/images/button_featured.png">');
  $s[bookmark_img] = htmlspecialchars('<img border="0" src="'.$s[site_url].'/images/button_bookmark.png">');
  $s[sitemap_location] = "$s[phppath]/sitemap.html";
  $s[g_sitemap_location] = "$s[phppath]/sitemap.xml";
  $s[y_sitemap_location] = "$s[phppath]/sitemap.txt";
  $s[logo_url] = "$s[site_url]/images/logo.png";
  $s[ARfold_l_cat] = 'category'; $s[ARfold_l_detail] = 'ads';
}
if (!$s[in_templates]) $s[in_templates] = 3;
if (!$s[p_domain]) { $x = parse_url($s[site_url]); $s[p_domain] = str_replace('www.','',$x[host]); }
ih();
echo $info;
$entered_info = '<br /><span class="text10">You already entered it.</span>';
$entered_info1 = '***************';
if ($s[p_user]) { $i[p_user] = $entered_info; $i1[p_user] = $entered_info1; }
if ($s[p_pass]) { $i[p_pass] = $entered_info; $i1[p_pass] = $entered_info1; }
if ($s[dbusername]) { $i[dbusername] = $entered_info; $i1[dbusername] = $entered_info1; }
if ($s[dbpassword]) { $i[dbpassword] = $entered_info; $i1[dbpassword] = $entered_info1; }
?>
<form method="POST" action="configuration_main.php"><?PHP echo check_field_create('admin') ?>
<input type="hidden" name="action" value="configuration_edited_main">
<INPUT type="hidden" name="pr" value="<?PHP echo $s[pr]; ?>">
<table border="0" width="99%" cellspacing="0" cellpadding="0" class="common_table">
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td colspan="2" class="common_table_top_cell">Configuration</td></tr>
<tr>
<td colspan="2" align="center">Never use backslash (\) in any of your values<br>Fields marked by * are required. If you leave some of them blank, the system will not work properly.</span></td>
</tr>
<tr><td colspan="2" class="common_table_top_cell">Your License</td></tr>
<tr><td colspan="2" align="center">If you don't remember this info, <a target="_blank" href="http://www.abscripts.com/scripts/owner.php">click here</a> to find it out</td></tr>
<tr>
<td align="left" valign="top">Your username at AbScripts.com users area *</td>
<td align="left" valign="top"><input class="field10" maxlength=15 size=15 name="p_user" value="<?PHP echo $i1[p_user] ?>"><?PHP echo $i[p_user] ?></td>
</tr>
<tr>
<td align="left" valign="top">Your password at AbScripts.com users area *</td>
<td align="left" valign="top"><input class="field10" maxlength=15 size=15 name="p_pass" type="password" value="<?PHP echo $i1[p_pass] ?>"><?PHP echo $i[p_pass] ?></td>
</tr>
<tr>
<td align="left" valign="top">Name of the domain you purchased the license for<br><span class="text10">Leave it blank if you have an unlimited license</span></td>
<td align="left" valign="top"><INPUT class="field10" maxLength=255 style="width:550px" name="p_domain" value="<?PHP echo $s[p_domain]; ?>"><br>
<span class="text10">Correct: mydomain.com<br>Wrong: www.mydomain.com, http://mydomain.com/</td>
</tr>



<tr><td colspan="2" class="common_table_top_cell">Mysql Database Data</td></tr>
<tr>
<tr>
<td align="left" valign="top">Mysql database host *</td>
<td align="left" valign="top"><INPUT class="field10" style="width:550px" name="dbhost" value="<?PHP echo $s[dbhost]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Name of your mysql database *</td>
<td align="left" valign="top"><INPUT class="field10" style="width:550px" name="dbname" value="<?PHP echo $s[dbname]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Your mysql database username</td>
<td align="left" valign="top"><input class="field10" style="width:550px" name="dbusername" value="<?PHP echo $i1[dbusername] ?>"><?PHP echo $i[dbusername] ?></td>
</tr>
<tr>
<td align="left" valign="top">Mysql database password</td>
<td align="left" valign="top"><input class="field10" style="width:550px" name="dbpassword" value="<?PHP echo $i1[dbpassword] ?>"><?PHP echo $i[dbpassword] ?></td>
</tr>
<tr>
<td align="left" valign="top">Prefix of all tables *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength="10" style="width:100px" name="pr" value="<?PHP echo $s[pr]; ?>" disabled></td>
</tr>



<tr><td colspan="2" class="common_table_top_cell">Main Variables</td></tr>
<tr>
<td align="left" valign="top">Failed login attempts<br><span class="text10">You can set up that after given number of failed attempts to log in, the system locks the account and also the IP address that sends such requests. This option is valid for accounts of users (publishers, advertisers) and also for accounts of admins. Let these fields empty to disable this option.</span><br></td>
<td align="left" valign="top">Lock given account and IP address for <input class="field10" maxlength=5 size=5 name="log_fail_hours" value="<?PHP echo $s[log_fail_hours] ?>"> hours<br>after <input class="field10" maxlength=5 size=5 name="log_fail_max" value="<?PHP echo $s[log_fail_max] ?>"> failed attempts to log in<br></td>
</tr>
<tr>
<td align="left" valign="top">Email admin when someone tried to log in with incorrect data<br><span class="text10">It requires the option above to be enabled.<br></span></td>
<td align="left" valign="top"><input type="checkbox" name="log_fail_email" value="1"<?PHP if ($s[log_fail_email]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Secret word *<br><span class="text10">It is a "password" for your script 'rebuild.php'. It may contain letters and numbers.</span></td>
<td align="left" valign="top"><INPUT class="field10" maxLength=30 size=30 name="secretword" value="<?PHP echo $s[secretword]; ?>"></td>
</tr>
<tr><td align="left" valign="top">Run Daily Job automatically<br><span class="text10">Not recommended - read Manual for more info.</span></td>
<td align="left" valign="top"><input type="checkbox" name="rebuild_auto" value="1"<?PHP if ($s[rebuild_auto]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Record numbers of ads in individual categories/areas and their combinations<br><span class="text10">This field should be checked if you want to use any of the two options below it.<br>It saves system resources when it's unchecked.</td>
<td align="left" valign="top"><input type="checkbox" name="record_numbers" value="1"<?PHP if ($s[record_numbers]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Recount classified ads in all categories and areas each time when the Daily Job runs<br><span class="text10">Recommended. If you uncheck this field, run the recount at least once a week manually. This option is in on the Reset/rebuild screen</td>
<td align="left" valign="top"><input type="checkbox" name="daily_recount" value="1"<?PHP if ($s[daily_recount]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Show numbers of ads in categories and areas in the left column </td>
<td align="left" valign="top"><input type="checkbox" name="show_left_items" value="1"<?PHP if ($s[show_left_items]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Automatically delete classified ads which are no more valid<br><span class="text10">You can set an expiration date for each classified ad. Expired classified ads are not visible on public pages. If you check this field, these classified ad will be removed from database by the Daily Job.</td>
<td align="left" valign="top"><input type="checkbox" name="delete_expired_items" value="1"<?PHP if ($s[delete_expired_items]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Default style </td>
<td align="left" valign="top"><select class="field10" name="def_style" value="<?PHP echo $s[def_style]; ?>">
<?PHP
$styles_list = get_styles_list(0,1);
foreach ($styles_list as $k=>$v)
{ if ($v==$s[def_style]) $selected = ' selected'; else $selected = '';
  echo '<option value="'.$v.'"'.$selected.'>'.str_replace('_',' ',$v).'</option>';
}
?>
</select>
</td></tr>



<tr>
<td align="left" valign="top">Full path to the folder where your scripts reside *<br><span class="text10">No trailing slash</span></td>
<td align="left" valign="top"><INPUT class="field10" style="width:550px" name="phppath" value="<?PHP echo $s[phppath]; ?>"><br><span class="text10">Example for Linux: /htdocs/sites/user/html/gold_classifieds<br>Example for Windows: C:/somefolder/domain.com/gold_classifieds</span></td>
</tr>
<tr>
<td align="left" valign="top">URL of the folder where your scripts reside *<br><span class="text10">No trailing slash</span></td>
<td align="left" valign="top"><INPUT class="field10" style="width:550px" name="site_url" value="<?PHP echo $s[site_url] ?>"></td>
</tr>


<tr>
<td align="left" valign="top">Link Up Gold installation path </span></td>
<td align="left" valign="top"><INPUT class="field10" style="width:550px" name="lug_phppath" value="<?PHP echo $s[lug_phppath]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Link Up Gold URL </td>
<td align="left" valign="top"><INPUT class="field10" style="width:550px" name="lug_site_url" value="<?PHP echo $s[lug_site_url] ?>"></td>
</tr>








<tr>
<td align="left" valign="top">Number of _inX.txt templates which can be used<br /><span class="text10">These templates can be used to show content shared by multiple pages.<br>Use as low number of these templates as possible. The default value is 3.</span></td>
<td align="left" valign="top"><input class="field10" style="width:50px" name="in_templates" value="<?PHP echo $s[in_templates]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Date format</td>
<td align="left" valign="top">
<select class="field10" name="date_form_1">
<option value="d"<?PHP if ($s[date_form_1]=='d') echo ' selected' ?>>dd</option>
<option value="m"<?PHP if ($s[date_form_1]=='m') echo ' selected' ?>>mm</option>
<option value="y"<?PHP if ($s[date_form_1]=='y') echo ' selected' ?>>yyyy</option>
</select>
<select class="field10" name="date_form_1a">
<option value="Space"<?PHP if ($s[date_form_1a]=='Space') echo ' selected' ?>>Space</option>
<option value="Nothing"<?PHP if ($s[date_form_1a]=='Nothing') echo ' selected' ?>>Nothing</option>
<option value="-"<?PHP if ($s[date_form_1a]=='-') echo ' selected' ?>>-</option>
<option value="/"<?PHP if ($s[date_form_1a]=='/') echo ' selected' ?>>/</option>
<option value="."<?PHP if ($s[date_form_1a]=='.') echo ' selected' ?>>.</option>
</select>
<select class="field10" name="date_form_2">
<option value="d"<?PHP if ($s[date_form_2]=='d') echo ' selected' ?>>dd</option>
<option value="m"<?PHP if ($s[date_form_2]=='m') echo ' selected' ?>>mm</option>
<option value="y"<?PHP if ($s[date_form_2]=='y') echo ' selected' ?>>yyyy</option>
</select>
<select class="field10" name="date_form_2a">
<option value="Space"<?PHP if ($s[date_form_2a]=='Space') echo ' selected' ?>>Space</option>
<option value="Nothing"<?PHP if ($s[date_form_2a]=='Nothing') echo ' selected' ?>>Nothing</option>
<option value="-"<?PHP if ($s[date_form_2a]=='-') echo ' selected' ?>>-</option>
<option value="/"<?PHP if ($s[date_form_2a]=='/') echo ' selected' ?>>/</option>
<option value="."<?PHP if ($s[date_form_2a]=='.') echo ' selected' ?>>.</option>
</select>
<select class="field10" name="date_form_3">
<option value="d"<?PHP if ($s[date_form_3]=='d') echo ' selected' ?>>dd</option>
<option value="m"<?PHP if ($s[date_form_3]=='m') echo ' selected' ?>>mm</option>
<option value="y"<?PHP if ($s[date_form_3]=='y') echo ' selected' ?>>yyyy</option>
</select>
<select class="field10" name="date_form_3a">
<option value="Space"<?PHP if ($s[date_form_3a]=='Space') echo ' selected' ?>>Space</option>
<option value="Nothing"<?PHP if ($s[date_form_3a]=='Nothing') echo ' selected' ?>>Nothing</option>
<option value="-"<?PHP if ($s[date_form_3a]=='-') echo ' selected' ?>>-</option>
<option value="/"<?PHP if ($s[date_form_3a]=='/') echo ' selected' ?>>/</option>
<option value="."<?PHP if ($s[date_form_3a]=='.') echo ' selected' ?>>.</option>
</select>
</td>
</tr>
<tr>
<td align="left" valign="top">Time format</td>
<td align="left" valign="top"><select class="field10" name="time_form">
<OPTION value="12"<?PHP if ($s[time_form]=='12') echo ' selected' ?>>12 hours (3:25 pm)</option>
<OPTION value="24"<?PHP if ($s[time_form]=='24') echo ' selected' ?>>24 hours (15:25)</option>
</select></td>
</tr>
<tr>
<td align="left" valign="top">Difference (if exists) between time on the server and your local time. Only hours.</td>
<td align="left" valign="top"><input class="field10" maxlength=5 style="width:50px" name="timeplus" value="<?PHP echo $s[timeplus]/3600; ?>"><span class="text10"><br />Example: Time on server is 8:00 but your local time is 10:00, you will write number 2, time on server is 10:00 but your local time is 8:00, you will write number -2</span></td>
</tr>
<tr>
<td align="left" valign="top">Admin email *</td>
<td align="left" valign="top"><input class="field10" style="width:550px" name="mail" value="<?PHP echo $s[mail]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Title of your site *</td>
<td align="left" valign="top"><input class="field10" style="width:550px" name="site_name" value="<?PHP echo $s[site_name]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Meta keywords<span class="text10"><br><br>These keywords are used by default. Each category and detail page can have its own unique keywords.<br></span></td>
<td align="left" valign="top"><textarea class="field10" name="site_keywords" rows=5 style="width:550px"><?PHP echo $s[site_keywords]; ?></textarea></td>
</tr>
<tr>
<td align="left" valign="top">Meta description<span class="text10"><br><br>This description is used by default. Each category and detail page can have its own unique description.<br></span></td>
<td align="left" valign="top"><textarea class="field10" name="site_description" rows=5 style="width:550px"><?PHP echo $s[site_description]; ?></textarea></td>
</tr>
<tr>
<td align="left" valign="top">URL of your logo *</td>
<td align="left" valign="top"><input class="field10" style="width:550px" name="logo_url" value="<?PHP echo $s[logo_url]; ?>"><br><span class="text10">It's displayed at the top of all pages.<br></span></td>
</tr>
<tr>
<td align="left" valign="top">Banner HTML code<br><br><span class="text10">It's displayed at the top of all pages.<br></span></td>
<td align="left" valign="top"><textarea class="field10" name="banner_code" rows=5 style="width:550px"><?PHP echo $s[banner_code]; ?></textarea></td>
</tr>
<tr>
<td align="left" valign="top">Character set to use for pages and emails *</td>
<td align="left" valign="top"><input class="field10" style="width:100px" name="charset" value="<?PHP echo $s[charset]; ?>"><br /><span class="text10">Example: ISO-8859-1</span></td>
</tr>
<tr>
<td align="left" valign="top">Icons of folders<br><span class="text10">You can mark folders with different icons by the time when there has been added the last classified ad.</span></td>
<td align="left" valign="top" nowrap>
<?PHP
for ($x=1;$x<=4;$x++)
echo '<img border="0" src="../images/icon_folder_'.$x.'.gif">&nbsp;Folders with items less than <INPUT class="field10" size=5 name="icon_folder_t'.$x.'" value="'.$s["icon_folder_t$x"].'"> days ago<br>';
echo '<img border="0" src="../images/icon_folder_5.gif">&nbsp;All other folders<br>';
?>
</td>
</tr>
<tr>
<td align="left" valign="top">Display maximum of </td>
<td align="left" valign="top"><INPUT class="field10" size=5 name="pages_max_ads" value="<?PHP echo $s[pages_max_ads] ?>"> links to previous/next pages</span><br><span class="text10">Let it blank to display links to all pages<br></span></td>
</tr>
<tr>
<td align="left" valign="top">Use Ajax to load individual pages in categories </td>
<td align="left" valign="top"><input type="checkbox" name="category_use_ajax" value="1"<?PHP if ($s[category_use_ajax]) echo ' checked'; ?>><br /><span class="text10">If checked, pages in categories load faster however some search engines may be able to index only the first page in each category.<br />This option needs the charset UTF-8 to be used for your site.<br /></span></td>
</tr>
<tr>
<td align="left" valign="top">Show QR codes </td>
<td align="left" valign="top"><input type="checkbox" name="show_qr" value="1"<?PHP if ($s[show_qr]) echo ' checked'; ?>><br /><span class="text10">This feature may slow public pages down.<br /></span></td>
</tr>
<tr>
<td align="left" valign="top">Allow radius search </td>
<td align="left" valign="top"><input type="checkbox" name="radius_search" value="1"<?PHP if ($s[radius_search]) echo ' checked'; ?>><br /><span class="text10">Detailed info about this feature is available <a href="latitudes.php">here</a>.<br /></span></td>
</tr>
<tr>
<td align="left" valign="top">Radius search uses </td>
<td align="left" valign="top" nowrap>
<select class="field10" name="km_miles">
<?PHP
$$s[km_miles] = ' selected';
echo "
<option value=\"km\"$km>Kilometres</option>
<option value=\"miles\"$miles>Miles</option>
";
?>
</select>
</td></tr>

<tr>
<td align="left" valign="top">Only words with </td>
<td align="left" valign="top"><INPUT class="field10" size=5 name="search_min" value="<?PHP echo $s[search_min] ?>"> or more characters can be searched</td>
</tr>
<tr>
<td align="left" valign="top">Highlight searched words/phrases in search results</td>
<td align="left" valign="top"><input type="checkbox" name="search_highlight" value="1"<?PHP if ($s[search_highlight]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Send all emails generated by scripts in HTML format</td>
<td align="left" valign="top"><input type="checkbox" name="htmlmail" value="1"<?PHP if ($s[htmlmail]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">SMTP server<br><span class="text10">Optional feature. If your server is unable to properly send emails, you can use a smtp server to send emails.</span></td>
<td align="left" valign="top">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td align="left">Server </td>
<td align="left"><INPUT class="field10" style="width:400px" name="smtp_server" value="<?PHP echo $s[smtp_server] ?>"></td>
</tr>
<tr>
<td align="left">Username </td>
<td align="left"><INPUT class="field10" style="width:400px" name="smtp_username" value="<?PHP echo $s[smtp_username] ?>"></td>
</tr>
<tr>
<td align="left">Password </td>
<td align="left"><INPUT class="field10" style="width:400px" name="smtp_password" value="<?PHP echo $s[smtp_password] ?>"></td>
</tr>
</table>
</td>
</tr>
<tr>
<td align="left" valign="top">Mark aliases of categories by <br><span class="text10">Alias is not a real category but a shortcut which leads to another category. Search engines usually use @ to mark such "categories".</span></td>
<td align="left" valign="top">Before name <INPUT class="field10" size=5 name="alias_pref" value="<?PHP echo $s[alias_pref] ?>"> After name <INPUT class="field10" size=5 name="alias_after" value="<?PHP echo $s[alias_after] ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Ignored tags <span class="text10"><br><br>Top words included in ads are listed in the left column of all pages and also are used for suggestions in the simple search form. Enter words which should never be used for these purposes (prepositions etc.), separated by comma.<br></span></td>
<td align="left" valign="top"><textarea class="field10" name="ignored_tags" rows=15 style="width:550px"><?PHP echo $s[ignored_tags]; ?></textarea></td>
</tr>
<tr>
<td align="left">Watermark text to use on uploaded images </td>
<td align="left"><INPUT class="field10" style="width:550px" name="watermark_text" value="<?PHP echo $s[watermark_text] ?>"></td>
</tr>

<!--
<tr>
<td align="left" valign="top">Mark Visitors Today unique after  </td>
<td align="left" valign="top"><INPUT class="field10" size=5 name="visits_today_unique" value="<?PHP echo $s[visits_today_unique] ?>"> minutes</td>
</tr>
-->
<!--
CREATE TABLE gc_visits_today (
  ip varchar(50) NOT NULL default '',
  time int(10) unsigned NOT NULL default '0',
  old tinyint(1) NOT NULL default '0',
  KEY time (time),
  KEY ip (ip)
) ENGINE=MyISAM;
-->


<tr><td colspan="2" class="common_table_top_cell">Home Page</td></tr>
<tr>
<td align="left" valign="top">Number of columns of tables with categories *</td>
<td align="left" valign="top"><INPUT class="field10" size=5 name="index_column_cats" value="<?PHP echo $s[index_column_cats] ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Maximum number of subcategories to list under each main category</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=5 size=5 name="index_max_subc" value="<?PHP echo $s[index_max_subc] ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Character(s) to separate subcategories </td>
<td align="left" valign="top"><INPUT class="field10" maxLength=10 size=5 name="ind_sep_subc" value="<?PHP echo $s[ind_sep_subc] ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Organize categories on the home page alphabetically in *<br><span class="text10">This option is valid for normal listing of categories as well as for categories in groups</span></td>
<td align="left" valign="top">
<INPUT type="radio" name="in_sort_rows" value="1"<?PHP if ($s[in_sort_rows]) echo ' checked'; ?>> rows &nbsp;&nbsp;
<INPUT type="radio" name="in_sort_rows" value="0"<?PHP if (!$s[in_sort_rows]) echo ' checked'; ?>> columns
</td></tr>
<tr>
<td align="left" valign="top">Address to show in the center of map on the home page </td>
<td align="left" valign="top"><INPUT class="field10" style="width:550px" name="home_map_address" value="<?PHP echo $s[home_map_address]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Co-ordinates to show in the center of map on the home page </td>
<td align="left" valign="top">Latitude: <INPUT class="field10" style="width:150px" name="home_map_lat" value="<?PHP echo $s[home_map_lat]; ?>"> Longitude: <INPUT class="field10" style="width:150px" name="home_map_lon" value="<?PHP echo $s[home_map_lon]; ?>"><br><span class="text10">Keep these fields empty to automatically count these values for the address entered above<br></span></td>
</tr>
<tr>
<td align="left" valign="top">Map zoom </td>
<td align="left" valign="top"><select class="field10" name="home_map_zoom">
<?PHP
unset($selected);
for ($x=1;$x<=18;$x++) { if ($s[home_map_zoom]==$x) $selected = ' selected'; else $selected = ''; echo '<option value="'.$x.'"'.$selected.'>'.$x.'</option>'; }
?>
</select></td>
</tr>




<tr><td colspan="2" class="common_table_top_cell">Classified Ads</td></tr>
<tr>
<td align="left" valign="top">Classified ads per page in categories *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="per_page" value="<?PHP echo $s[per_page]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Number of featured classifieds on the home page *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="per_page_index" value="<?PHP echo $s[per_page_index]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Classified ads to display in special categories *<br><span class="text10">It includes: New Classified Ads, Popular Classified Ads<br></span></td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="new_page" value="<?PHP echo $s[new_page]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Classified ads per page on RSS pages *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="l_rss_per_page" value="<?PHP echo $s[l_rss_per_page]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Number of columns of listing in categories and search results *</td>
<td align="left" valign="top"><INPUT class="field10" size=5 name="l_columns" value="<?PHP echo $s[l_columns]; ?>"></td>
</tr>
<?PHP if ($s[marknew_time]) $s[marknew_time] = $s[marknew_time]/86400; ?>
<tr>
<td align="left" valign="top">Number of days to mark each classified ad as New *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="marknew_time" value="<?PHP echo $s[marknew_time]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Number of classified ads to mark as Popular *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="popular" value="<?PHP echo $s[popular]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Each classified ad can be listed in maximum of *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="max_cats" value="<?PHP echo $s[max_cats] ?>"> categories</td>
</tr>
<tr>
<td align="left" valign="top">Each classified ad can be listed in maximum of *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="max_areas" value="<?PHP echo $s[max_areas] ?>"> areas</td>
</tr>
<tr>
<td align="left" valign="top">Maximum of *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="a_max_pictures" value="<?PHP echo $s[a_max_pictures] ?>"> images can be uploaded for each classified for free</td>
</tr>
<tr>
<td align="left" valign="top">Size of previews *</td>
<td align="left" valign="top">Width <INPUT class="field10" maxLength=3 size=5 name="preview_w" value="<?PHP echo $s[preview_w] ?>"> px&nbsp;&nbsp;&nbsp; Height <INPUT class="field10" maxLength=3 size=5 name="preview_h" value="<?PHP echo $s[preview_h] ?>"> px
</td>
<tr>
<td align="left" valign="top">Size of full size images *</td>
<td align="left" valign="top">Width <INPUT class="field10" maxLength=3 size=5 name="full_size_w" value="<?PHP echo $s[full_size_w] ?>"> px&nbsp;&nbsp;&nbsp; Height <INPUT class="field10" maxLength=3 size=5 name="full_size_h" value="<?PHP echo $s[full_size_h] ?>"> px
</td>
</tr>
<tr>
<td align="left" valign="top">Maximum of *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="max_files" value="<?PHP echo $s[max_files] ?>"> files can be uploaded for each classified for free</td>
</tr>
<!--<tr>
<td align="left" valign="top">Maximum of *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="max_videos" value="<?PHP echo $s[max_videos] ?>"> videos can be uploaded for each classified for free</td>
</tr>-->
<tr>
<td align="left" valign="top">Classifieds should be sorted by</td>
<td align="left" valign="top" nowrap>
<select class="field10" name="sortby">
<?PHP
$$s[sortby] = ' selected';
echo "
<option value=\"created\"$created>Date created</option>
<option value=\"title\"$title>Title</option>
<option value=\"description\"$description>Description</option>
<option value=\"clicks_total\"$clicks_total>Popularity</option>
<option value=\"price\"$price>Price</option>
";
?>
</select>
<select class="field10" name="sortby_direct">
<?PHP
$$s[sortby_direct] = ' selected';
echo "<option value=\"asc\"$asc>Ascending</option><option value=\"desc\"$desc>Descending</option>";
?>
</select>
</td></tr>
<tr>
<td align="left" valign="top">Allow users to sort classified ads by</td>
<td align="left" valign="top">
<input type="checkbox" name="sort_ads_options[]" value="title"<?PHP if (in_array('title',$sort_ads_options)) echo ' checked'; ?>> Title<br>
<input type="checkbox" name="sort_ads_options[]" value="description"<?PHP if(in_array('description',$sort_ads_options)) echo ' checked'; ?>> Description<br>
<input type="checkbox" name="sort_ads_options[]" value="created"<?PHP if (in_array('created',$sort_ads_options)) echo ' checked'; ?>> Date created<br>
<input type="checkbox" name="sort_ads_options[]" value="clicks_total"<?PHP if (in_array('clicks_total',$sort_ads_options)) echo ' checked'; ?>> Popularity<br>
<input type="checkbox" name="sort_ads_options[]" value="price"<?PHP if (in_array('price',$sort_ads_options)) echo ' checked'; ?>> Price<br>
</td></tr>
<tr><td align="left" valign="top">Display only those user items which have a value<br>
<span class="text10">On public pages will be displayed only those user items which have any value. Empty items will be invisible.</span></td>
<td align="left" valign="top"><input type="checkbox" name="filter_usit" value="1"<?PHP if ($s[filter_usit]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Prefer icon Updated<br><span class="text10">Normally, if a classified ad has been updated at the time when it is marked as new, it doesn't get an 'Updated' icon but still has a 'New' icon. If this is checked, this classified ad gets an 'Updated' icon immediately. </td>
<td align="left" valign="top"><input type="checkbox" name="pref_upd" value="1"<?PHP if ($s[pref_upd]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Maximum number of similar categories for each category *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="ads_max_simcats" value="<?PHP echo $s[ads_max_simcats] ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Number of classified ads in each of the small tables in the left column *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="ads_r_n" value="<?PHP echo $s[ads_r_n] ?>"></td>
</tr>
<tr>
<td align="left" valign="top">HTML to mark new classified ads</td>
<td align="left" valign="top"><INPUT class="field10" style="width:550px" name="new_img" value="<?PHP echo $s[new_img]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">HTML to mark updated classified ads</td>
<td align="left" valign="top"><INPUT class="field10" style="width:550px" name="upd_img" value="<?PHP echo $s[upd_img]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">HTML to mark popular classified ads</td>
<td align="left" valign="top"><INPUT class="field10" style="width:550px" name="pop_img" value="<?PHP echo $s[pop_img]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">HTML to mark featured classified ads</td>
<td align="left" valign="top"><INPUT class="field10" style="width:550px" name="featured_img" value="<?PHP echo $s[featured_img]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">HTML to mark bookmarked ads</td>
<td align="left" valign="top"><INPUT class="field10" style="width:550px" name="bookmark_img" value="<?PHP echo $s[bookmark_img]; ?>"></td>
</tr>

















<tr><td colspan="2" class="common_table_top_cell">Payments</td></tr>
<tr>
<td align="left" valign="top">Currency mark to use for prices *</td>
<td align="left" valign="top"><INPUT class="field10" size=5 name="currency" value="<?PHP echo $s[currency] ?>"><span class="text10"> Example: $</span></td>
</tr>
<tr><td align="center" colspan=2><span class="text10"><b>Paypal data</b><br>These values are required if you want to let your users pay automatically by using Paypal.com (purchased funds are instantly available on user accounts)<br></span></td></tr>
<tr>
<td align="left" valign="top">Currency that you want to use for payments </td>
<td align="left" valign="top"><?PHP echo pp_currency_select('pp_currency',$s[pp_currency]) ?></td>
</tr>
<tr>
<td align="left" valign="top">Email address of your account </td>
<td align="left" valign="top"><INPUT class="field10" maxLength=255 style="width:550px" name="pp_email" value="<?PHP echo $s[pp_email] ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Mode </td>
<td align="left" valign="top">
<INPUT type="radio" name="pp_test" value="0"<?PHP if (!$s[pp_test]) echo ' checked'; ?>> Normal mode - all sales are real<br>
<INPUT type="radio" name="pp_test" value="1"<?PHP if ($s[pp_test]) echo ' checked'; ?>> Test mode - test the payment system by using <a target="_blank" href="https://developer.paypal.com/">Paypal Sandbox</a>
</td>
</tr>
<tr><td align="center" colspan=2><span class="text10"><b>2CheckOut data</b><br>These values are required if you want to let your users pay automatically by using 2CheckOut.com (purchased funds are instantly available on user accounts)<br></span></td></tr>
<tr>
<td align="left" valign="top">Account number </td>
<td align="left" valign="top"><INPUT class="field10" size=10 name="co_n" value="<?PHP echo $s[co_n] ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Secret word </td>
<td align="left" valign="top"><INPUT class="field10" size=10 name="co_secret_word" value="<?PHP echo $s[co_secret_word] ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Mode </td>
<td align="left" valign="top">
<INPUT type="radio" name="co_test" value="0"<?PHP if (!$s[co_test]) echo ' checked'; ?>> Normal mode - all sales are real<br>
<INPUT type="radio" name="co_test" value="1"<?PHP if ($s[co_test]) echo ' checked'; ?>> Test mode - test the payment system
</td>
</tr>
<tr><td align="center" colspan=2><span class="text10"><b>Other payment company</b><br>Enter complete HTML code of a link or button leading to any other payment company, it may be online or offline payment service. You also can enter instructions for multiple payment companies. These variables can be used: #%price%# (price for the ordered classified ad), #%order%# (order number).<br></td></tr>
<tr><td align="center" colspan=2><textarea class="field10" name="other_payment_com" rows=10 style="width:700px"><?PHP echo $s[other_payment_com] ?></textarea></td>
</tr>








<tr><td colspan=2 class="common_table_top_cell">Registered Users</td></tr>
<?PHP
echo '<tr>
<td align="left" valign="top">Only users approved by admin can log in </td>
<td align="left" valign="top"><input type="checkbox" name="user_no_auto" value="1"'; if ($s[user_no_auto]) echo ' checked'; echo '></td>
</tr>
<tr>
<td align="left" valign="top">Inform users by email that their accounts have been approved</td>
<td align="left" valign="top"><input type="checkbox" name="user_i_approved" value="1"'; if ($s[user_i_approved]) echo ' checked'; echo '></td>
</tr>
<tr><td align="center" colspan=2>Names of newsletters</td></tr>
';
for ($x=1;$x<=5;$x++)
echo '<tr>
<td align="left" valign="top">Newsletter #'.$x.'</td>
<td align="left" valign="top"><input class="field10" maxLength=100 style="width:550px" name="news_'.$x.'" value="'.$s['news_'.$x].'"></td>
</tr>';
?>
<tr>
<td align="left" valign="top">Maximum number of images which can be uploaded for each user by admin *</td>
<td align="left" valign="top"><input class="field10" size=5 name="u_max_pictures" value="<?PHP echo $s[u_max_pictures] ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Resize images <br /><span class="text10">This option needs GD library.<br />Let these fields empty to keep original size of images.<br /></span></td>
<td align="left" valign="top">
<table border=0 cellspacing=0 cellpadding=2>
<tr>
<td align="left" valign="top">Thumbnails&nbsp;&nbsp;</td>
<td align="left" valign="top">Width: <input class="field10" size=5 name="u_image_small_w" value="<?PHP echo $s[u_image_small_w] ?>"> px&nbsp;&nbsp;Height: <input class="field10" size=5 name="u_image_small_h" value="<?PHP echo $s[u_image_small_h] ?>"> px</td>
</tr>
<tr>
<td align="left" valign="top">Full size images&nbsp;&nbsp;</td>
<td align="left" valign="top">Width: <input class="field10" size=5 name="u_image_big_w" value="<?PHP echo $s[u_image_big_w] ?>"> px&nbsp;&nbsp;Height: <input class="field10" size=5 name="u_image_big_h" value="<?PHP echo $s[u_image_big_h] ?>"> px</td>
</tr>
</table>
</td>
</tr>
<tr>
<td align="left" valign="top">Users per page *</td>
<td align="left" valign="top"><input class="field10" size=5 name="u_per_page" value="<?PHP echo $s[u_per_page]; ?>"></td>
</tr>






<tr><td colspan="2" class="common_table_top_cell">Other</td></tr>
<tr>
<td align="left" valign="top">Number of messages displayed on the Message Board *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=3 size=5 name="board" value="<?PHP echo $s[board]; ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Count only one impressions of each classified ad details page from each IP/day</td>
<td align="left" valign="top" nowrap><input type="checkbox" name="one_click_ip_day" value="1"<?PHP if ($s[one_click_ip_day]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Inform admin when an abuse report is submitted</td>
<td align="left" valign="top"><input type="checkbox" name="i_abuse_report" value="1"<?PHP if ($s[i_abuse_report]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Server location of sitemap <br /><span class="text10">The file must be writeable</span>
<td align="left" valign="top"><input class="field10" style="width:550px" name="sitemap_location" value="<?PHP echo $s[sitemap_location] ?>"><br /><span class="text10">Example:<br />/htdocs/sites/user/html/folder/sitemap.html</span></td>
</tr>
<tr><td align="left" valign="top">Sitemap page will contain all </td>
<td align="left" valign="top">
<input type="checkbox" name="sitemap_cats" value="1"<?PHP if ($s[sitemap_cats]) echo ' checked'; ?>> Categories<br>
<input type="checkbox" name="sitemap_ads" value="1"<?PHP if ($s[sitemap_ads]) echo ' checked'; ?>> Classified ads detail pages<br>
<input type="checkbox" name="sitemap_description" value="1"<?PHP if ($s['sitemap_description']) echo ' checked'; ?>> Include also descriptions<br />
</td></tr>
<tr>
<td align="left" valign="top">Server location of sitemap for Google <br /><span class="text10">The file must be writeable</span>
<td align="left" valign="top"><input class="field10" style="width:550px" name="g_sitemap_location" value="<?PHP echo $s[g_sitemap_location] ?>"><br /><span class="text10">Example:<br />/htdocs/sites/user/html/folder/sitemap.xml</span></td>
</tr>
<tr><td align="left" valign="top">Google sitemap should contain all </td>
<td align="left" valign="top">
<input type="checkbox" name="g_sitemap_cats" value="1"<?PHP if ($s[g_sitemap_cats]) echo ' checked'; ?>> Categories<br>
<input type="checkbox" name="g_sitemap_ads" value="1"<?PHP if ($s[g_sitemap_ads]) echo ' checked'; ?>> Classified ads detail pages<br>
<input type="checkbox" name="g_sitemap_search" value="1"<?PHP if ($s[g_sitemap_search]) echo ' checked'; ?>> 500 most popular searches<br />
</td></tr>
<tr>
<td align="left" valign="top">Server location of sitemap for Yahoo <br /><span class="text10">The file must be writeable</span>
<td align="left" valign="top"><input class="field10" style="width:550px" name="y_sitemap_location" value="<?PHP echo $s[y_sitemap_location] ?>"><br /><span class="text10">Example:<br />/htdocs/sites/user/html/folder/sitemap.xml</span></td>
</tr>
<tr><td align="left" valign="top">Yahoo sitemap should contain all </td>
<td align="left" valign="top">
<input type="checkbox" name="y_sitemap_cats" value="1"<?PHP if ($s[y_sitemap_cats]) echo ' checked'; ?>> Categories<br>
<input type="checkbox" name="y_sitemap_ads" value="1"<?PHP if ($s[y_sitemap_ads]) echo ' checked'; ?>> Classified ads detail pages<br>
<input type="checkbox" name="y_sitemap_search" value="1"<?PHP if ($s[y_sitemap_search]) echo ' checked'; ?>> 500 most popular searches<br />
</td></tr>






<tr><td colspan="2" class="common_table_top_cell">Style of URL's</td></tr>
<tr>
<td align="left" nowrap>Virtual folder to store categories *</td>
<td align="left"><INPUT class="field10" maxLength=50 size=30 style="width:550px" name="ARfold_l_cat" value="<?PHP echo $s[ARfold_l_cat] ?>"></td>
</tr>
<tr>
<td align="left" nowrap>Virtual folder to store ad detail pages *</td>
<td align="left"><INPUT class="field10" maxLength=50 size=30 style="width:550px" name="ARfold_l_detail" value="<?PHP echo $s[ARfold_l_detail] ?>"></td>
</tr>
<tr>
<td align="left" valign="top">Use dynamic URL's</td>
<td align="left" valign="top"><INPUT type="radio" name="A_option" value="0"<?PHP if (!$s[A_option]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Use Apache Rewrite module<br><span class="text10">It creates "like" static URL's. This option requires Rewrite module to be enabled in Apache configuration.</span><br></td>
<td align="left" valign="top" nowrap><INPUT type="radio" name="A_option" value="rewrite"<?PHP if ($s[A_option]=='rewrite') echo ' checked'; ?>><span class="text10">If checked, copy the commands below to your .htaccess file.</span><br></td>
</tr>
<tr>
<td align="left" valign="top">Commands to enter to your .htaccess file<br><span class="text10">Once you entered all configuration values and saved this form, copy commands from this field to your .htaccess file. Valid only if you checked the field "Use Apache Rewrite module".</span><br></td>
<td align="left" valign="top"><textarea class="field10" name="" rows=12 cols=70 style="width:550px">
<?PHP 
if (($s[ARfold_l_cat]) AND ($s[ARfold_l_detail]) AND ($s[A_option]=='rewrite')) 
echo 'RewriteEngine On
RewriteRule index\.html index.php [NC]
RewriteRule index_offer\.html index.php?vars=offer [NC]
RewriteRule index_wanted\.html index.php?vars=wanted [NC]
RewriteRule index_all\.html index.php?vars=all [NC]
RewriteRule '.$s[ARfold_l_cat].'\-(.*) index.php?vars=/categories-$1 [NC]
RewriteRule '.$s[ARfold_l_detail].'\-(.*)\/ classified.php?vars=ad_details-$1 [NC]
RewriteRule extra_category\/(.*) category.php?action=$1 [NC]
RewriteRule user-(.*)\/(.*)\.html users.php?n=$1 [L]
';
else echo 'Commands for your .htaccess file are currently not available. To make them available enter values to both the "Virtual folder" fields, check the field "Use Apache Rewrite module" and hit the Save button at the bottom of this form.';
?>
</textarea>
</td>
</tr>
<tr><td align="center" colspan=2><input type="submit" name="submit" value="Save" class="button10"></td></tr>
</form></table></td></tr></table>
<br>
</center></div>
<?PHP
exit;
}

#################################################################################

function is_gd() {
if (function_exists('imageellipse')) return 1;
return 0;
}

#################################################################################

function configuration_edited_main($in) {
global $s,$info;
if ((!$in[p_user]) OR ($in[p_user]=='***************')) $in[p_user] = $s[p_user];
if ((!$in[p_pass]) OR ($in[p_pass]=='***************')) $in[p_pass] = $s[p_pass];
$u = md5('u5');
if ((!$in[dbusername]) OR ($in[dbusername]=='***************')) $in[dbusername] = $s[dbusername];
if ((!$in[dbpassword]) OR ($in[dbpassword]=='***************')) $in[dbpassword] = $s[dbpassword];
set_magic_quotes_runtime(0);
$in[marknew_time] = $in[marknew_time] * 86400;
if (!$in[record_numbers]) $in[daily_recount] = $in[show_left_items] = 0;
if ((trim($in[home_map_address])) AND (!$in[longitude]) AND (!$in[latitude]))
{ $lat_lon = get_geo_data($in[home_map_address]);
  $in[home_map_lat] = $lat_lon[latitude];
  $in[home_map_lon] = $lat_lon[longitude];
}
unset ($in[submit],$in[action],$in[check_field]);
$w = substr($u,9,3); $u = substr($u,6,3);
$in[a_tags] = str_replace('<','',str_replace('>','',$in[a_tags]));
$e = trim($w($in)); $e = explode ('---',$e); if ($x) $chyba[] = $m($in); echo $x;
$in[styles] = implode(',',get_styles_list(0));
if ($in[timeplus]) $in[timeplus] = $in[timeplus]*3600;
foreach ($in as $k=>$v) 
{ if (is_array($v)) $v = implode(',',$v);
  if (!$v) unset($in[$k]);
  else $v = ereg_replace("'","\'",stripslashes($v));
  $data .= "\$s[$k] = '$v';\n";
}
create_write_file("$in[phppath]/data/data.php","<?PHP\n\n$data \n?>",0666,1);
$info = info_line('Your setting has been successfully updated').$e[0].$u($e[1]);
configuration_edit_main();
}

#################################################################################
#################################################################################
#################################################################################

?>