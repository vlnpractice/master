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

include_once('./common.php');
check_admin('ads');

###################################################################################
###################################################################################
###################################################################################

function show_one_ad($ad) {
global $s;

if (!is_array($ad)) $ad = get_ad_variables($ad,0);
$ad = stripslashes_array($ad);
//$user_items = user_defined_items_show($ad[n]);
list($images,$files,$videos) = get_item_files('a',$ad[n],0);


if ($ad[status]=='enabled') { $manage = '<a href="ad_details.php?action=ad_manage&what=0&n='.$ad[n].'">Disable</a>'; $enabled = 'Yes'; }
else { $manage = '<a href="ad_details.php?action=ad_manage&what=1&n='.$ad[n].'">Enable</a>'; $enabled = 'No'; }
if ( (($ad[updated]+$s[marknew_time]) > $s[cas]) AND ($s[pref_upd]) ) $icon = $s[upd_img];
elseif ( ($ad[created]+$s[marknew_time]) > $s[cas] ) $icon = $s[new_img];
elseif ( ($ad[updated]+$s[marknew_time]) > $s[cas] ) $icon = $s[upd_img];
//if ($ad[pick]) $icon .= "&nbsp $s[pick_img]";
if ($ad[popular]) $icon .= "&nbsp $s[pop_img]";
if ($ad[updated]) $updated = datum ($ad[updated],1); else $updated = 'Never yet';
$dates = get_dates_ads_text($ad);
if (ad_is_active($ad[t1],$ad[t2],$ad[status],0)) $active = '<font color="green">Ad is active</font>';
else $active = '<font color="red">Ad is inactive</font>';

include("$s[phppath]/styles/_common/messages/common.php");
foreach ($s[extra_options] as $k=>$v) if ($ad['x_'.$v.'_by']>$s[cas]) $extra[] = $m['xtra_'.$v];
if ($ad[x_files_by]>$s[cas]) $extra[] = "$m[xtra_files] $ad[x_files_max]";;
if ($ad[x_pictures_by]>$s[cas]) $extra[] = "$m[xtra_pictures] $ad[x_pictures_max]";;
if ($extra) $extra_features = implode(', ',$extra); else $extra_features = 'None';
if ($ad[show_checkbox]) $checkbox = '<input class="bbb" type="checkbox" name="ad[]" value="'.$ad[n].'">&nbsp;&nbsp;';
if ($ad[pub_phone1]) $public_phones[] = $ad[pub_phone1]; if ($ad[pub_phone2]) $public_phones[] = $ad[pub_phone2];
$owner = get_user_variables($ad[owner]);

echo '<table border="0" width="700" cellspacing="0" cellpadding="0" class="common_table">
<tr><td class="common_table_top_cell" style="text-align:left">&nbsp;'.$checkbox.$ad[title].'</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="left" colspan="2">'.$icon.'&nbsp;</td></tr>
<tr>
<td align="left" valign="top">Description</td>
<td align="left" valign="top">'.$ad[description].'&nbsp;</td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Details</td>
<td align="left" valign="top">'.$ad[detail].'&nbsp;</td>
</tr>';
if ($ad[offer_wanted]) echo '<tr>
<td align="left" valign="top" nowrap>Ad type</td>
<td align="left" valign="top">'.ucfirst($ad[offer_wanted]).'&nbsp;</td>
</tr>';
if ($ad[price]) echo '<tr>
<td align="left" valign="top" nowrap>Price</td>
<td align="left" valign="top">'.$s[currency].$ad[price].'&nbsp;</td>
</tr>';
echo '<tr>
<td align="left" valign="top" width="160" nowrap>Public URL</td>
<td align="left" valign="top" width="540"><a target="_blank" href="'.get_detail_page_url('ad',$ad[n],$ad[rewrite_url],0,1).'">'.get_detail_page_url('ad',$ad[n],$ad[rewrite_url],0,1).'</a></td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Areas</td>
<td align="left" valign="top">'.list_of_areas_for_item_admin($ad[a]).'</td>
</tr>
<tr>
<td align="left" valign="top">Categories</td>
<td align="left">'.list_of_categories_for_item_admin('ad',$ad[c]).'</td>
</tr>
<tr>
<td align="left" valign="top" nowrap>URL</td>
<td align="left" valign="top"><a target="_blank" href="'.$ad[url].'">'.$ad[url].'</a>&nbsp;</td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Owner\'s name</td>
<td align="left" valign="top">'.$ad[name].'&nbsp;</td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Owner\'s email</td>
<td align="left" valign="top">'.$ad[email].'&nbsp;</td>
</tr>
<tr>
<td align="left" valign="top" nowrap>Public phones</td>
<td align="left" valign="top">'.implode(', ',$public_phones).'&nbsp;</td>
</tr>
<tr>
<td align="left" nowrap>Owner </td>
<td align="left"><a href="users.php?action=users_searched&n='.$owner[n].'&sort=email&order=asc">'.$owner[email].'</a>&nbsp;</td>
</tr>
<tr>
<td align="left" valign="top" width="160" nowrap>Comments </td>
<td align="left" valign="top" width="540"><a href="comments.php?action=comments_view&ad='.$ad[n].'">'.$ad[comments].'</a>&nbsp;</td>
</tr>';

echo $user_items;
if (count($images[$ad[n]]))
{ echo '<tr>
  <td align="left" valign="top" nowrap>Pictures </td>
  <td align="left" valign="top">'; foreach ($images[$ad[n]] as $k=>$v) echo image_preview_code($v[n],$v[url],$v[big_url]); echo '</td>
  </tr>';
}
echo '<tr>
<td align="left" colspan=2><span class="text10">
Ad number: '.$ad[n].', Enabled: '.$enabled.', Valid from '.$dates[t1].' to '.$dates[t2].', '.$active.'<br>
Created: '.datum($ad[created],1).', Updated: '.$updated.'<br>
Extra features: '.$extra_features.'<br>
</td></tr>
<tr><td align="left" colspan=2>
['.$manage.']&nbsp;&nbsp;
[<a target="_self" href="ad_details.php?action=ad_edit&n='.$ad[n].'">Edit</a>]&nbsp;&nbsp;
[<a target="_self" href="javascript: go_to_delete(\'Are you sure?\',\'ad_details.php?action=ad_delete&n='.$ad[n].'\')">Delete</a>]&nbsp;&nbsp;
[<a target="_self" href="ad_details.php?action=ad_copy&n='.$ad[n].'">Copy</a>]&nbsp;&nbsp;
</td></tr></table>
</td></tr></table>
<br>';
}

######################################################################################

function ad_create_edit_form($in) {
global $s;
clean_item_files('a',$in[n]);
if ($in[old_n]) $load_n = $in[old_n]; else $load_n = $in[n];
if ($in[n]) $n = $in[n]; else { $n = 0; $in[status] = 'enabled'; $in[created] = $s[cas]; }
foreach ($in as $k=>$v) if ($v==1) $checked[$k] = ' checked';
$category = get_category_variables(get_ad_first_category($in[c]));
$offer_wanted[$in[offer_wanted]] = ' selected';
list($images,$files,$videos) = get_item_files('a',$in[n],$s[queue]);
if (!$in[city]) $in[city] = 'Unkown';
if (!$in[country]) $in[country] = 'Unkown';
if (!$in[region]) $in[region] = 'Unkown';
if (!$in[zip]) $in[zip] = 'Unkown';
if ($in[latitude]==-1) $in[latitude] = 'Unkown';
if ($in[longitude]==-1) $in[longitude] = 'Unkown';

echo '<table border="0" width="900" cellspacing="0" cellpadding="0" class="common_table">
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td nowrap class="common_table_top_cell" colspan="2">Public Info</td></tr>
<tr>
<td nowrap align="left" valign="top">Title </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][title]" value="'.$in[title].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">Short description </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][description]" value="'.$in[description].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top" colspan="2">Detailed description </td>
</tr>
<tr>
<td nowrap align="left" valign="top" colspan="2">'.get_fckeditor('ad['.$n.'][detail]',$in[detail],'AdminToolbar').'</td>
</tr>
<tr>
<td nowrap align="left" valign="top">Keywords<br><span class="text10">Separated by commas</span></td>
<td nowrap align="left" valign="top"><input name="ad['.$n.'][keywords]" value="'.$in[keywords].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">URL </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][url]" value="'.$in[url].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">Youtube video URL </td>
<td nowrap align="left" valign="top"><input name="ad['.$n.'][youtube_video]" value="'.$in[youtube_video].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">Phone 1</td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][pub_phone1]" value="'.$in[pub_phone1].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">Phone 2</td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][pub_phone2]" value="'.$in[pub_phone2].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">Mail address </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][address]" value="'.$in[address].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td align="left" valign="top" colspan="2">The fields below are used for the radius search feature. The system automatically generates these values by using the address entered above.</td>
</tr>
<tr>
<td nowrap align="left" valign="top">City </td>
<td nowrap align="left" valign="top">'.$in[city].'&nbsp;</td>
</tr>
<tr>
<td nowrap align="left" valign="top">Postal code </td>
<td nowrap align="left" valign="top">'.$in[zip].'&nbsp;</td>
</tr>
<tr>
<td nowrap align="left" valign="top">Region </td>
<td nowrap align="left" valign="top">'.$in[region].' ('.$in[country].')&nbsp;</td>
</tr>
<tr>
<td nowrap align="left" valign="top">Latitude </td>
<td nowrap align="left" valign="top">'.$in[latitude].'&nbsp;</td>
</tr>
<tr>
<td nowrap align="left" valign="top">Longitude </td>
<td nowrap align="left" valign="top">'.$in[longitude].'&nbsp;</td>
</tr>
';
/*
<tr>
<td nowrap align="left" valign="top">City </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][city]" value="'.$in[city].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">Postal code </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][zip]" value="'.$in[zip].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">Region </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][region]" value="'.$in[region].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">Region </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][country]" value="'.$in[country].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">Latitude </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][latitude]" value="'.$in[latitude].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">Longitude </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][longitude]" value="'.$in[longitude].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td align="left">Use the address related values above<br>(don\'t request these values from google) </td>
<td align="left" nowrap><input type="checkbox" name="ad['.$n.'][use_address]" value="1"></td>
</tr>
*/
echo usit_rows_form_admin($in[c],$load_n,'ad['.$n.']',0,'ad_edit');
if ($in[old_n]) echo '<tr><td nowrap align="center" valign="top" colspan="2">Fields to upload images and files will be available when the ad has been copied.</td></tr>';
else
{ echo images_form_admin('a',$in,$s[queue]);
  for ($x=1;$x<=($s[max_files]+$in[x_files_max]);$x++)
  { echo '<tr>
    <td nowrap align="left" valign="top">Upload a file #'.$x.'</td>
    <td nowrap align="left" valign="top"><input type="file" maxlength="255" size="57" name="file_upload['.$n.']['.$x.']" class="field10" style="width:550px">';
    if ($files[$load_n][$x][url])
    { echo '<br>Current file <a target="_blank" href="'.$files[$load_n][$x][url].'">'.str_replace("$s[site_url]/uploads/files/",'',$files[$load_n][$x][url]).'</a>, '.$files[$load_n][$x][size].' bytes';
      if ($n) echo '<br><input type="checkbox" name="ad['.$n.'][delete_file][]" value="'.$x.'"> Delete this file';
    }
    echo '</td>
    </tr>
    <tr>
    <td nowrap align="left" valign="top">File description</td>
    <td nowrap align="left" valign="top"><input maxlength="255" name="file_description['.$n.']['.$x.']" value="'.$files[$load_n][$x][description].'" class="field10" style="width:550px"></td>
    </tr>';	
  }
  for ($x=1;$x<=($s[max_videos]+$in[x_videos_max]);$x++)
  { echo '<tr>
    <td nowrap align="left" valign="top">Upload video #'.$x.'<br>Allowed extensions: '.implode(', ',$s[videos_extensions]).'</span></td>
    <td nowrap align="left" valign="top"><input type="file" maxlength="255" size="57" name="video_upload['.$n.']['.$x.']" class="field10" style="width:550px">';
    if ($video[$load_n][$x][url])
    { echo '<br>Current video <a target="_blank" href="'.$video[$load_n][$x][url].'">'.str_replace("$s[site_url]/uploads/video/",'',$video[$load_n][$x][url]).'</a>';
      if ($n) echo '<br><input type="checkbox" name="ad['.$n.'][delete_video][]" value="'.$x.'"> Delete this video';
    }
    echo '</td>
    </tr>
    <tr>
    <td nowrap align="left" valign="top">Or enter URL of video #'.$x.'</td>
    <td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][video_url]['.$x.']" value="'.$video[$load_n][$x][url].'" class="field10" style="width:550px"></td>
    </tr>
    <tr>
    <td nowrap align="left" valign="top">Description</td>
    <td nowrap align="left" valign="top"><input maxlength="255" name="video_description['.$n.']['.$x.']" value="'.$video[$load_n][$x][description].'" class="field10" style="width:550px"></td>
    </tr>';	
  }
}
echo '<tr><td nowrap class="common_table_top_cell" colspan="2">Features</td></tr>';
if ($n) echo '<tr>
<td nowrap align="left" valign="top">Number </td>
<td nowrap align="left" valign="top">'.$n.'</td>
</tr>
<tr>
<td nowrap align="left" valign="top">Public URL </td>
<td align="left" valign="top"><a target="_blank" href="'.get_detail_page_url('ad',$in[n],$in[rewrite_url],0,1).'">'.get_detail_page_url('ad',$in[n],$in[rewrite_url],0,1).'</a></td>
</tr>';
if ($category[offer_wanted])
echo '<tr>
<td align="left" valign="top">Ad type</td>
<td align="left" nowrap><select name="ad['.$n.'][offer_wanted]" class="field10"><option value="offer"'.$offer_wanted[offer].'>Offer</option><option value="wanted"'.$offer_wanted[wanted].'>Wanted</option></select></td>
</tr>';
if ($category[price])
echo '<tr>
<td align="left" valign="top">Price</td>
<td nowrap align="left" valign="top">'.$s[currency].'<input maxlength="10" size="10" name="ad['.$n.'][price]" value="'.$in[price].'" class="field10"></td>
</tr>';
echo areas_rows_form($in);
echo categories_rows_form('ad',$in);
echo '<tr>
<td nowrap align="left" valign="top">Rewrite URL </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][rewrite_url]" value="'.$in[rewrite_url].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td align="left"nowrap>Mark by bold until </td>
<td align="left">'.date_select($in[x_bold_by],'ad['.$n.'][x_bold_by]').'</td>
</tr>
<tr>
<td align="left"nowrap>Featured until </td>
<td align="left">'.date_select($in[x_featured_by],'ad['.$n.'][x_featured_by]').'</td>
</tr>
<tr>
<td align="left"nowrap>Home page placement until </td>
<td align="left">'.date_select($in[x_home_page_by],'ad['.$n.'][x_home_page_by]').'</td>
</tr>
<tr>
<td align="left" nowrap>Featured gallery until </td>
<td align="left">'.date_select($in[x_featured_gallery_by],'ad['.$n.'][x_featured_gallery_by]').'</td>
</tr>
<tr>
<td align="left"nowrap>Highlighted until </td>
<td align="left">'.date_select($in[x_highlight_by],'ad['.$n.'][x_highlight_by]').'</td>
</tr>
<tr>
<td align="left"nowrap>Extra pictures visible until </td>
<td align="left">'.date_select($in[x_pictures_by],'ad['.$n.'][x_pictures_by]').'</td>
</tr>
<tr>
<td nowrap align="left" valign="top">Maximum extra pictures </td>
<td nowrap align="left" valign="top"><input maxlength="10" size="10" name="ad['.$n.'][x_pictures_max]" value="'.$in[x_pictures_max].'" class="field10"></td>
</tr>
<tr>
<td align="left"nowrap>Extra files visible until </td>
<td align="left">'.date_select($in[x_files_by],'ad['.$n.'][x_files_by]').'</td>
</tr>
<tr>
<td nowrap align="left" valign="top">Maximum extra files </td>
<td nowrap align="left" valign="top"><input maxlength="10" size="10" name="ad['.$n.'][x_files_max]" value="'.$in[x_files_max].'" class="field10"></td>
</tr>
<tr>
<td align="left"nowrap>"Buy Now" by Paypal available until </td>
<td align="left">'.date_select($in[x_paypal_by],'ad['.$n.'][x_paypal_by]').'</td>
</tr>
<tr>
<td nowrap align="left" valign="top">Paypal email </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][x_paypal_email]" value="'.$in[x_paypal_email].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">Paypal currency </td>
<td nowrap align="left" valign="top">'.pp_currency_select('ad['.$n.'][x_paypal_currency]',$in[x_paypal_currency]).'</td>
</tr>
<tr>
<td nowrap align="left" valign="top">Paypal price </td>
<td nowrap align="left" valign="top"><input maxlength="10" size="10" name="ad['.$n.'][x_paypal_price]" value="'.$in[x_paypal_price].'" class="field10"></td>
</tr>
<tr>
<td align="left">Disable it after a click to paypal button </td>
<td align="left" nowrap><input type="checkbox" name="ad['.$n.'][x_paypal_disable]" value="1"'; if ($in[x_paypal_disable]) echo ' checked'; echo '></td>
</tr>
<tr>
<td align="left">Disabled due a click to paypal button </td>
<td align="left" nowrap><input type="checkbox" name="ad['.$n.'][x_paypal_disabled]" value="1"'; if ($in[x_paypal_disabled]) echo ' checked'; echo '></td>
</tr>
<tr>
<td align="left">Date & time created</td>
<td align="left">'.date_select($in[created],'ad['.$n.'][created]').' <input maxlength="5" name="ad['.$n.'][created_time]" value="'.date('H:i',$in[created]).'" class="field10" style="width:50px"> Correct time format: 15:26</td>
</tr>
<tr>
<td align="left" valign="top">Valid</td>
<td align="left" nowrap>From '.date_select($in[t1],'ad['.$n.'][t1]').' To '.date_select($in[t2],'ad['.$n.'][t2]').'</td>
</tr>';
if ($in[status]!='queue')
{ echo '<tr>
  <td nowrap align="left" valign="top">Enabled </td>
  <td nowrap align="left" valign="top"><input type="checkbox" name="ad['.$n.'][enabled]" value="1"'; if ($in[status]=='enabled') echo ' checked'; echo '></td>
  </tr>
  <tr>
  <td align="left">Mark it as Updated </td>
  <td align="left" nowrap><input type="checkbox" name="ad['.$n.'][mark_edited]" checked></td>
  </tr>';
}
echo '
<tr>
<td nowrap align="left" valign="top">Owner\'s name </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][name]" value="'.$in[name].'" class="field10" style="width:550px"></td>
</tr>
<tr>
<td nowrap align="left" valign="top">Owner\'s email </td>
<td nowrap align="left" valign="top"><input maxlength="255" name="ad['.$n.'][email]" value="'.$in[email].'" class="field10" style="width:550px"></td>
</tr>';
if ($in[status]=='queue')
{ echo '<tr><td nowrap class="common_table_top_cell" colspan="2">Options</td></tr>
  <tr>
  <td align="left" nowrap>Approve this classified ad </td>
  <td align="left" nowrap><input type="radio" name="ad['.$n.'][approve]" value="yes" id="approve_'.$n.'"><a class="link10" href="#" onClick="uncheck_both('.$n.'); return false;">Uncheck these boxes</a></td>
  </tr>
  <tr>
  <td align="left" nowrap>Reject this classified ad </td>
  <td align="left" nowrap><input type="radio" name="ad['.$n.'][approve]" value="no" id="reject_'.$n.'">and send email: <select class="field10" name="ad['.$n.'][reject_email]">'.$in[reject_emails].'</select>
   or <input type="checkbox" name="ad['.$n.'][reject_email_custom]" value="1" id="fullcust'.$n.'" onclick="show_hide_div(document.getElementById(\'fullcust'.$n.'\').checked,document.getElementById(\'test'.$n.'\'));" value="1"> Individual Message
  <tr><td align="left" colspan="2">
  <div id="test'.$n.'" style="display:none;">
  <table border=0 width=100% cellspacing=2 cellpadding=0>
  <tr>
  <td align="left">Subject</td>
  <td><input class="field10" name="ad['.$n.'][email_subject]" size="60"></td>
  </tr>
  <tr>
  <td align="left" valign="top">Text<br><span class="text10">Available variables:<br>#%title%# - Title<br>#%description%# - Description<br></span></td>
  <td><textarea class="field10" name="ad['.$n.'][email_text]" rows="10" cols="60"></textarea>
  </tr>
  </table></DIV>
  </td></tr>';
}
echo '</table></td></tr></table><br>';
}

#############################################################################

function ad_edited_process($in) {
global $s;
$created = get_timestamp($in[created][d],$in[created][m],$in[created][y],'start',$in[created_time]);
if ($in[mark_edited]) $edited = "edited = '$s[cas]',"; else $edited = ''; 
$t1 = get_timestamp($in[t1][d],$in[t1][m],$in[t1][y],'start');
$t2 = get_timestamp($in[t2][d],$in[t2][m],$in[t2][y],'end');
$in = replace_array_text($in);
$in[detail] = refund_html($in[detail]);
$in[categories] = array_unique($in[categories]);
$c_path = ad_edit_get_categories($in[categories]);
unset($x); foreach ($in[categories] as $k => $v) if ($v) $x[] = '_'.$v.'_'; $c = implode(' ',$x);
$a_path = ad_edit_get_areas($in[areas]);
unset($x); foreach ($in[areas] as $k => $v) if ($v) $x[] = '_'.$v.'_'; $a = implode(' ',$x);
$old = get_ad_variables($in[n],0);
$en_cats = has_some_enabled_categories($categories);
if (!$in[rewrite_url]) $in[rewrite_url] = discover_rewrite_url($in[title],0);
if ($in[enabled]) $status = 'enabled'; else $status = 'disabled';
$owner = get_usern($in[email]);
$in[zip] = str_replace(' ','',$in[zip]);

foreach ($s[extra_options] as $k=>$v)
{ $variable_name = 'x_'.$v.'_by';
  $$variable_name = get_timestamp($in['x_'.$v.'_by'][d],$in['x_'.$v.'_by'][m],$in['x_'.$v.'_by'][y],'end');
}
$x_pictures_by = get_timestamp($in[x_pictures_by][d],$in[x_pictures_by][m],$in[x_pictures_by][y],'end');
$x_files_by = get_timestamp($in[x_files_by][d],$in[x_files_by][m],$in[x_files_by][y],'end');
if ($in[use_address]) dq("update $s[pr]ads set latitude = '$in[latitude]', longitude = '$in[longitude]', country = '$in[country]', region = '$in[region]', city = '$in[city]', zip = '$in[zip]' where n = '$in[n]'",1);
else get_geo_data($in[address],$in[n],0);
dq("update $s[pr]ads set $edited created = '$created', title = '$in[title]', description = '$in[description]', detail = '$in[detail]', keywords = '$in[keywords]', address = '$in[address]', youtube_video = '$in[youtube_video]', offer_wanted = '$in[offer_wanted]', price = '$in[price]', c = '$c', c_path = '$c_path', a = '$a', a_path = '$a_path', url = '$in[url]', name = '$in[name]', email = '$in[email]', pub_phone1 = '$in[pub_phone1]', pub_phone2 = '$in[pub_phone2]', owner = '$owner[0]', t1 = '$t1', t2 = '$t2', status = '$status', rewrite_url = '$in[rewrite_url]', x_bold_by = '$x_bold_by', x_featured_by = '$x_featured_by', x_home_page_by = '$x_home_page_by', x_featured_gallery_by = '$x_featured_gallery_by', x_highlight_by = '$x_highlight_by', x_pictures_by = '$x_pictures_by', x_files_by = '$x_files_by', x_paypal_by = '$x_paypal_by', x_paypal_email = '$in[x_paypal_email]', x_paypal_currency = '$in[x_paypal_currency]', x_paypal_price = '$in[x_paypal_price]', x_files_max = '$in[x_files_max]', x_pictures_max = '$in[x_pictures_max]', en_cats = '$en_cats', x_paypal_disable = '$in[x_paypal_disable]', x_paypal_disabled = '$in[x_paypal_disabled]' where n = '$in[n]'",1);
add_update_user_items($in[n],0,ad_created_edited_get_usit($in[categories][0],$in));
if (!$s[dont_recount]) recount_ads_cats_areas($c_path,$old[c_path],$a_path,$old[a_path]);
recount_ads_for_owner($owner[0]);
update_item_index('ad',$in[n]);
upload_files('a',$in[n],$in,0,0,$in[delete_image],$in[delete_file]);
update_item_image1('a',$in[n]);
}

##################################################################################
##################################################################################
##################################################################################

function get_usern($email) {
global $s;
if (!$email) return false;
$q = dq("select n from $s[pr]users where email = '$email'",1);
$x = mysql_fetch_row($q);
if ($x[0]) return array($x[0],$email);
}

##################################################################################
##################################################################################
##################################################################################

?>