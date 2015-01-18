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

switch ($_GET[action]) {
case 'configuration_edit_submit_forms'	: configuration_edit_submit_forms();
}
switch ($_POST[action]) {
case 'configuration_edited_submit_forms': configuration_edited_submit_forms($_POST);
}
	
#################################################################################
#################################################################################
#################################################################################

function configuration_edited_submit_forms($form) {
global $info;
include('../data/data.php');
set_magic_quotes_runtime(0);
unset ($form[submit],$form[action],$form[check_field]);
$form[a_tags] = str_replace('<','',str_replace('>','',$form[a_tags]));

foreach ($form as $k=>$v) 
{ if (is_array($v)) $v = implode(',',$v);
  if (!$v) unset($form[$k]);
  else $v = ereg_replace("'","\'",stripslashes($v));
  $data .= "\$s[$k] = '$v';\n";
}

$data = "<?PHP\n\n$data \n?>";
if (!$sb = fopen("$s[phppath]/data/data_forms.php",'w')) problem ("Cannot write to file data_forms.php in your data directory. Make sure that your data directory exists and has 777 permission and the file data_forms.php inside has permission 666. Cannot continue.");

$zapis = fwrite($sb, $data);
fclose($sb);
if (!$zapis) $info = info_line('Can not write to file "data_forms.php".','Make sure that your data directory exists and has 777 permission and the file "data_forms.php" inside has permission 666. Cannot continue.');
else $info = info_line('Your configuration has been successfully updated');
configuration_edit_submit_forms();
}

#################################################################################

function configuration_edit_submit_forms() {
global $info;
include("../data/data_forms.php");
$s = stripslashes_array($s);
foreach ($s as $k=>$v) $s[$k] = htmlspecialchars($v);
$x = explode(',',$s[a_tags]); foreach ($x as $k => $v) { if ($v) $x[$k] = "<$v>"; $s[a_tags] = implode(',',$x); }
$x = explode(',',$s[sort_ads_options]); foreach ($x as $k => $v) { $sort_ads_options[$k] = $v; }
ih();
echo $info;
?>
<form method="POST" action="configuration_forms.php"><?PHP echo check_field_create('admin') ?>
<INPUT type="hidden" name="action" value="configuration_edited_submit_forms">
<table border="0" width="99%" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Configuration - Submit Forms</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center" colspan="2">These options are valid for forms on public pages only</td></tr>
<tr><td colspan="2" class="common_table_top_cell">Classified Ads</td></tr>


<tr><td align="left" valign="top">Submit form fields</td>
<td align="left" valign="top">

<table border=0 cellspacing=0 cellpadding=2>
<tr>
<td align="left" valign="top">&nbsp;</td>
<td align="center" valign="top">Available</td>
<td align="center" valign="top">Required</td>
</tr>
<tr>
<td align="left" valign="top">Title </td>
<td align="center" valign="top"><input type="checkbox" name="ad_v_title" value="1"<?PHP if ($s[ad_v_title]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="ad_r_title" value="1"<?PHP if ($s[ad_r_title]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Description </td>
<td align="center" valign="top"><input type="checkbox" name="ad_v_description" value="1"<?PHP if ($s[ad_v_description]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="ad_r_description" value="1"<?PHP if ($s[ad_r_description]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Detailed description </td>
<td align="center" valign="top"><input type="checkbox" name="ad_v_detail" value="1"<?PHP if ($s[ad_v_detail]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="ad_r_detail" value="1"<?PHP if ($s[ad_r_detail]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Keywords </td>
<td align="center" valign="top"><input type="checkbox" name="ad_v_keywords" value="1"<?PHP if ($s[ad_v_keywords]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="ad_r_keywords" value="1"<?PHP if ($s[ad_r_keywords]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Address to show on a map<br><span class="text10">When this field is required, it also checks if the address exists in Google database. If it doesn't exist, the submission is rejected.<br></span></td>
<td align="center" valign="top"><input type="checkbox" name="ad_v_address" value="1"<?PHP if ($s[ad_v_address]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="ad_r_address" value="1"<?PHP if ($s[ad_r_address]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Youtube video URL </td>
<td align="center" valign="top"><input type="checkbox" name="ad_v_youtube_video" value="1"<?PHP if ($s[ad_v_youtube_video]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="ad_r_youtube_video" value="1"<?PHP if ($s[ad_r_youtube_video]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Price (if available in the selected category)</td>
<td align="center" valign="top"><input type="checkbox" name="ad_v_price" value="1"<?PHP if ($s[ad_v_price]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="ad_r_price" value="1"<?PHP if ($s[ad_r_price]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">URL </td>
<td align="center" valign="top"><input type="checkbox" name="ad_v_url" value="1"<?PHP if ($s[ad_v_url]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="ad_r_url" value="1"<?PHP if ($s[ad_r_url]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Name </td>
<td align="center" valign="top"><input type="checkbox" name="ad_v_name" value="1"<?PHP if ($s[ad_v_name]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="ad_r_name" value="1"<?PHP if ($s[ad_r_name]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Email </td>
<td align="center" valign="top"><input type="checkbox" name="ad_v_email" value="1"<?PHP if ($s[ad_v_email]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="ad_r_email" value="1"<?PHP if ($s[ad_r_email]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Phone 1</td>
<td align="center" valign="top"><input type="checkbox" name="ad_v_pub_phone1" value="1"<?PHP if ($s[ad_v_pub_phone1]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="ad_r_pub_phone1" value="1"<?PHP if ($s[ad_r_pub_phone1]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Phone 2</td>
<td align="center" valign="top"><input type="checkbox" name="ad_v_pub_phone2" value="1"<?PHP if ($s[ad_v_pub_phone2]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="ad_r_v_pub_phone2" value="1"<?PHP if ($s[ad_r_v_pub_phone2]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">HTML editor for Detailed description </td>
<td align="center" valign="top"><input type="checkbox" name="a_details_html_editor" value="1"<?PHP if ($s[a_details_html_editor]) echo ' checked'; ?>></td>
<td align="center" valign="top">&nbsp;</td>
</tr>
<tr>
<td align="left" valign="top">Number of fields to upload an image </td>
<td align="center" valign="top"><input class="field10" maxLength=3 size=2 name="a_max_pictures_users" value="<?PHP echo $s[a_max_pictures_users] ?>"></td>
<td align="center" valign="top">&nbsp;</td>
</tr>
<tr>
<td align="left" valign="top">Number of fields to upload a file </td>
<td align="center" valign="top"><input class="field10" maxLength=3 size=2 name="a_max_files_users" value="<?PHP echo $s[a_max_files_users] ?>"></td>
<td align="center" valign="top">&nbsp;</td>
</tr>
<?PHP if (is_gd())
{ echo '<tr>
  <td align="left" valign="top">CAPTCHA image test <a href="#help-captcha">What\'s that?</a><br /></td>
  <td align="center" valign="top"><input type="checkbox" name="ad_v_captcha" value="1"'; if ($s[ad_v_captcha]) echo ' checked'; echo '></td>
  <td align="center" valign="top">&nbsp;</td>
  </tr>';
}
?>
</table>

</td></tr>






<tr>
<td align="left" valign="top">Allowed sizes in characters <br><span class="text10">Enter the lowest required number of characters to the first field, the biggest allowed number of characters to the second field<br></span></td>
<td align="left" valign="top" nowrap>
Title <INPUT class="field10" maxLength=5 size=5 name="ad_min_title" value="<?PHP echo $s[ad_min_title] ?>"> - <INPUT class="field10" maxLength=5 size=5 name="ad_max_title" value="<?PHP echo $s[ad_max_title] ?>"> 255 is maximum<br>
Description <INPUT class="field10" maxLength=5 size=5 name="ad_min_description" value="<?PHP echo $s[ad_min_description] ?>"> - <INPUT class="field10" maxLength=5 size=5 name="ad_max_description" value="<?PHP echo $s[ad_max_description] ?>"> 255 is maximum<br>
Detailed description <INPUT class="field10" maxLength=5 size=5 name="ad_min_detail" value="<?PHP echo $s[ad_min_detail] ?>"> - <INPUT class="field10" maxLength=5 size=5 name="ad_max_detail" value="<?PHP echo $s[ad_max_detail] ?>"><br>
Keywords <INPUT class="field10" maxLength=5 size=5 name="ad_min_keywords" value="<?PHP echo $s[ad_min_keywords] ?>"> - <INPUT class="field10" maxLength=5 size=5 name="ad_max_keywords" value="<?PHP echo $s[ad_max_keywords] ?>"><br>
</td>
</tr>
<tr>
<td align="left" valign="top">Allow to submit each classified ad to a single category and a single area only<br><span class="text10">If checked, only administrators have the right to submit classified ads to multiple categories/areas</span></td>
<td align="left" valign="top"><input type="checkbox" name="mult_cats_admin" value="1"<?PHP if ($s[mult_cats_admin]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Convert title<br>
<span class="text10">This converts all characters in classified ads titles to lowercase letters and the first character of each word to upper case</td>
<td align="left" valign="top"><input type="checkbox" name="convert_title" value="1"<?PHP if ($s[convert_title]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Convert description<br><span class="text10">This converts all characters in the description to lowercase letters except the first letter of the description</span></td>
<td align="left" valign="top"><input type="checkbox" name="convert_description" value="1"<?PHP if ($s[convert_description]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Who can submit classified ads</td>
<td align="left">
<INPUT type="radio" name="post_ads_who" value="0"<?PHP if (!$s[post_ads_who]) echo ' checked'; ?>> Any visitor<br>
<INPUT type="radio" name="post_ads_who" value="1"<?PHP if ($s[post_ads_who]==1) echo ' checked'; ?>> Only registered users<br>
<INPUT type="radio" name="post_ads_who" value="2"<?PHP if ($s[post_ads_who]==2) echo ' checked'; ?>> Only registered users who have been approved by admin for this action<br>
</tr>
<tr>
<td align="left" valign="top">Ads submitted by unregistered users must be confirmed by an email link<br><span class="text10">If checked, it sends an email with a confirmation link to owners of ads which have been added by unregistered users. These ads become active or are moved to the queue once the owner clicks to the link.<br>Enter a value also to the field below.<br></span></td>
<td align="left" valign="top"><input type="checkbox" name="ad_email_confirm" value="1"<?PHP if ($s[ad_email_confirm]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Delete unconfirmed ads after </td>
<td align="left"><INPUT class="field10" maxLength=10 size=10 name="ad_unconfirm_delete" value="<?PHP echo $s[ad_unconfirm_delete] ?>"> days</td>
</tr>
<tr>
<td align="left" valign="top">Auto-approve all new/edited classified ads added by registered users<br><span class="text10">If checked, all submissions are automatically added to the database without reviewing</span></td>
<td align="left" valign="top"><input type="checkbox" name="ad_autoapr_user" value="1"<?PHP if ($s[ad_autoapr_user]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Auto-approve all new classified ads added by everyone else</td>
<td align="left" valign="top"><input type="checkbox" name="ad_autoapr" value="1"<?PHP if ($s[ad_autoapr]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Email to admin when a classified ad has been submitted or updated</td>
<td align="left" valign="top" nowrap><input type="checkbox" name="new_email_admin" value="1"<?PHP if ($s[new_email_admin]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Email to owner of a classified ad immediately after its submission</td>
<td align="left" valign="top"><input type="checkbox" name="new_email_owner" value="1"<?PHP if ($s[new_email_owner]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Email to the owner of each classified ad once it has been approved by an admin</td>
<td align="left" valign="top"><input type="checkbox" name="i_approved" value="1"<?PHP if ($s[i_approved]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Check file format of uploaded files<br><a href="configuration_formats.php">More info & list of file formats</a></td>
<td align="left">
<INPUT type="radio" name="file_ext_by_mime" value="1"<?PHP if ($s[file_ext_by_mime]) echo ' checked'; ?>> Yes, check the format by Mime type of each uploaded file<br>
<INPUT type="radio" name="file_ext_by_mime" value="0"<?PHP if (!$s[file_ext_by_mime]) echo ' checked'; ?>> No, accept extensions of uploaded files<br></td>
</tr>
<tr>
<td align="left" valign="top">Maximum size of each file uploaded by users</td>
<td align="left"><INPUT class="field10" maxLength=10 size=10 name="max_filesize" value="<?PHP echo $s[max_filesize] ?>"> bytes</td>
</tr>
<tr>
<td align="left" valign="top">Check file format of uploaded images</td>
<td align="left">
<INPUT type="radio" name="img_ext_by_mime" value="1"<?PHP if ($s[img_ext_by_mime]) echo ' checked'; ?>> Yes, check the format by Mime type of each uploaded images<br>
<INPUT type="radio" name="img_ext_by_mime" value="0"<?PHP if (!$s[img_ext_by_mime]) echo ' checked'; ?>> No, accept extensions of uploaded images<br></td>
</tr>







<tr><td colspan="2" class="common_table_top_cell">Users</td></tr>


<tr><td align="left" valign="top">Submit form fields</td>
<td align="left" valign="top">

<table border=0 cellspacing=0 cellpadding=2>
<tr>
<td align="left" valign="top">&nbsp;</td>
<td align="center" valign="top">Available</td>
<td align="center" valign="top">Required</td>
</tr>
<tr>
<td align="left" valign="top">Name</td>
<td align="center" valign="top"><input type="checkbox" name="u_v_name" value="1"<?PHP if ($s[u_v_name]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="u_r_name" value="1"<?PHP if ($s[u_r_name]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Company</td>
<td align="center" valign="top"><input type="checkbox" name="u_v_company" value="1"<?PHP if ($s[u_v_company]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="u_r_company" value="1"<?PHP if ($s[u_r_company]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Address line 1</td>
<td align="center" valign="top"><input type="checkbox" name="u_v_address1" value="1"<?PHP if ($s[u_v_address1]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="u_r_address1" value="1"<?PHP if ($s[u_r_address1]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Address line 2</td>
<td align="center" valign="top"><input type="checkbox" name="u_v_address2" value="1"<?PHP if ($s[u_v_address2]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="u_r_address2" value="1"<?PHP if ($s[u_r_address2]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Address line 3</td>
<td align="center" valign="top"><input type="checkbox" name="u_v_address3" value="1"<?PHP if ($s[u_v_address3]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="u_r_address3" value="1"<?PHP if ($s[u_r_address3]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Country</td>
<td align="center" valign="top"><input type="checkbox" name="u_v_country" value="1"<?PHP if ($s[u_v_country]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="u_r_country" value="1"<?PHP if ($s[u_r_country]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Phone 1</td>
<td align="center" valign="top"><input type="checkbox" name="u_v_phone1" value="1"<?PHP if ($s[u_v_phone1]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="u_r_phone1" value="1"<?PHP if ($s[u_r_phone1]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Phone 2</td>
<td align="center" valign="top"><input type="checkbox" name="u_v_phone2" value="1"<?PHP if ($s[u_v_phone2]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="u_r_phone2" value="1"<?PHP if ($s[u_r_phone2]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Styles </td>
<td align="center" valign="top"><input type="checkbox" name="u_v_styles" value="1"<?PHP if ($s[u_v_styles]) echo ' checked'; ?>></td>
<td align="center" valign="top">&nbsp;</td>
</tr>
<tr>
<td align="left" valign="top">Site URL </td>
<td align="center" valign="top"><input type="checkbox" name="u_v_site_info" value="1"<?PHP if ($s[u_v_site_info]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="u_r_site_info" value="1"<?PHP if ($s[u_r_site_info]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Public article </td>
<td align="center" valign="top"><input type="checkbox" name="u_v_detail" value="1"<?PHP if ($s[u_v_detail]) echo ' checked'; ?>></td>
<td align="center" valign="top"><input type="checkbox" name="u_r_detail" value="1"<?PHP if ($s[u_r_detail]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">HTML editor for Users Article </td>
<td align="center" valign="top"><input type="checkbox" name="u_details_html_editor" value="1"<?PHP if ($s[u_details_html_editor]) echo ' checked'; ?>></td>
<td align="center" valign="top">&nbsp;</td>
</tr>
<tr>
<td align="left" valign="top">Newsletters </td>
<td align="center" valign="top"><input type="checkbox" name="u_v_newsletters" value="1"<?PHP if ($s[u_v_newsletters]) echo ' checked'; ?>></td>
<td align="center" valign="top">&nbsp;</td>
</tr>
<tr>
<td align="left" valign="top">Number of fields to upload an image </td>
<td align="center" valign="top"><input class="field10" maxLength=3 size=2 name="u_max_pictures_users" value="<?PHP echo $s[u_max_pictures_users] ?>"></td>
<td align="center" valign="top">&nbsp;</td>
</tr>
<?PHP if (is_gd())
{ echo '<tr>
  <td align="left" valign="top">CAPTCHA image test <a href="#help-captcha">What\'s that?</a><br /></td>
  <td align="center" valign="top"><input type="checkbox" name="u_v_captcha" value="1"'; if ($s[u_v_captcha]) echo ' checked'; echo '></td>
  <td align="center" valign="top">&nbsp;</td>
  </tr>';
}
?>
</table>
</td></tr>
<tr>
<td align="left" valign="top">New users receive email with a link to confirm their membership</td>
<td align="left" valign="top"><input type="checkbox" name="user_must_confirm" value="1"<?PHP if ($s[user_must_confirm]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Delete unconfirmed users after </td>
<td align="left" valign="top"><input class="field10" maxLength=5 size=5 name="user_unconfirmed_delete_after" value="<?PHP echo $s[user_unconfirmed_delete_after]; ?>"> days</td>
</tr>
<tr>
<td align="left" valign="top">Email admin when an user joined</td>
<td align="left" valign="top"><input type="checkbox" name="i_admin_user_joined" value="1"<?PHP if ($s[i_admin_user_joined]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Email admin when an account has been edited</td>
<td align="left" valign="top"><input type="checkbox" name="i_admin_user_edited" value="1"<?PHP if ($s[i_admin_user_edited]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Email user who has been approved by admin </td>
<td align="left" valign="top"><input type="checkbox" name="i_user_approved" value="1"<?PHP if ($s[i_user_approved]) echo ' checked'; ?>></td>
</tr>
<tr>
<td align="left" valign="top">Email user who edited account data</td>
<td align="left" valign="top"><input type="checkbox" name="i_user_who_edited" value="1"<?PHP if ($s[i_user_who_edited]) echo ' checked'; ?>></td>
</tr>





<tr><td colspan="2" class="common_table_top_cell">Comments</td></tr>
<tr><td align="left" valign="top">Submit form - visible items</td>
<td align="left" valign="top">

<input type="checkbox" name="comm_v_name" value="1"<?PHP if ($s[comm_v_name]) echo ' checked'; ?>>&nbsp;Name<br>
<input type="checkbox" name="comm_v_email" value="1"<?PHP if ($s[comm_v_email]) echo ' checked'; ?>>&nbsp;Email<br>
<?PHP if (is_gd()) { echo '<input type="checkbox" name="comm_v_captcha" value="1"'; if ($s[comm_v_captcha]) echo ' checked'; echo '>&nbsp;CAPTCHA image test <a href="#help-captcha">What\'s that?</a><br>'; } ?>
</td></tr>
<tr><td align="left" valign="top">Required items - submissions without them will be rejected</td>
<td align="left" valign="top">
<input type="checkbox" name="comm_r_name" value="1"<?PHP if ($s[comm_r_name]) echo ' checked'; ?>>&nbsp;Name<br>
<input type="checkbox" name="comm_r_email" value="1"<?PHP if ($s[comm_r_email]) echo ' checked'; ?>>&nbsp;Email<br>
</td></tr>
<tr>
<td align="left" valign="top">Maximum size of User Comment *</td>
<td align="left" valign="top"><INPUT class="field10" maxLength=5 size=5 name="m_comment" value="<?PHP echo $s[m_comment]; ?>"> characters</span></td>
</tr>
<tr><td align="left" valign="top">Each visitor can submit one comment only to each classified ad</td>
<td align="left" valign="top"><input type="checkbox" name="com_duplicate" value="1"<?PHP if ($s[com_duplicate]) echo ' checked'; ?>></td>
</tr>
<tr><td align="left" valign="top">Auto-approve all comments<br>
<span class="text10">All comments will be automatically added to the database without reviewing</span></td>
<td align="left" valign="top">
<input type="checkbox" name="com_autoapr" value="1"<?PHP if ($s[com_autoapr]) echo ' checked'; ?>>
</td></tr>
<tr><td align="left" valign="top">Only registered users can add comments</td>
<td align="left" valign="top">
<input type="checkbox" name="register_com" value="1"<?PHP if ($s[register_com]) echo ' checked'; ?>>
</td></tr>




<tr><td colspan="2" class="common_table_top_cell">Message Board</td></tr>
<tr><td align="left" valign="top">Submit form - visible items</td>
<td align="left" valign="top">
<input type="checkbox" name="board_v_name" value="1"<?PHP if ($s[board_v_name]) echo ' checked'; ?>>&nbsp;Name<br>
<input type="checkbox" name="board_v_email" value="1"<?PHP if ($s[board_v_email]) echo ' checked'; ?>>&nbsp;Email<br>
<?PHP if (is_gd()) { echo '<input type="checkbox" name="board_v_captcha" value="1"'; if ($s[board_v_captcha]) echo ' checked'; echo '>&nbsp;CAPTCHA image test <a href="#help-captcha">What\'s that?</a><br>'; } ?>
</td></tr>
<tr><td align="left" valign="top">Required items - submissions without them will be rejected</td>
<td align="left" valign="top">
<input type="checkbox" name="board_r_name" value="1"<?PHP if ($s[board_r_name]) echo ' checked'; ?>>&nbsp;Name<br>
<input type="checkbox" name="board_r_email" value="1"<?PHP if ($s[board_r_email]) echo ' checked'; ?>>&nbsp;Email<br>
</td></tr>
<tr>
<td align="left" valign="top">Maximum size of a Message </td>
<td align="left" valign="top"><INPUT class="field10" maxLength=5 size=5 name="board_max" value="<?PHP echo $s[board_max]; ?>"> characters</span></td>
</tr>
<tr><td align="left" valign="top">Only registered users can post messages</td>
<td align="left" valign="top">
<input type="checkbox" name="board_reg_only" value="1"<?PHP if ($s[board_reg_only]) echo ' checked'; ?>>
</td></tr>

<?PHP if (is_gd())
{ echo '<tr><td align="center" colspan=2 class="common_table_top_cell">CAPTCHA test in other forms</td></tr>
  <tr>
  <td align="left" valign="top">Message to us</td>
  <td align="left" valign="top"><input type="checkbox" name="message_to_us_captcha" value="1"'; if ($s[message_to_us_captcha]) echo ' checked'; echo '></td>
  </tr>
  <tr>
  <td align="left" valign="top">Message to link/article owner</td>
  <td align="left" valign="top"><input type="checkbox" name="message_owner_captcha" value="1"'; if ($s[message_owner_captcha]) echo ' checked'; echo '></td>
  </tr>
  <tr>
  <td align="left" valign="top">Tell a friend</td>
  <td align="left" valign="top"><input type="checkbox" name="tell_friend_captcha" value="1"'; if ($s[tell_friend_captcha]) echo ' checked'; echo '></td>
  </tr>
  <tr>
  <td align="left" valign="top">Error report</td>
  <td align="left" valign="top"><input type="checkbox" name="error_report_captcha" value="1"'; if ($s[error_report_captcha]) echo ' checked'; echo '></td>
  </tr>
  <tr>
  <td align="left" valign="top">User login </td>
  <td align="left" valign="top"><input type="checkbox" name="user_login_captcha" value="1"'; if ($s[user_login_captcha]) echo ' checked'; echo '></td>
  </tr>';
}
?>

<tr><td align="center" colspan=2><input type="submit" name="submit" value="Save" class="button10"></td></tr>
</table></td></tr></table></form>
<?PHP if (is_gd()) echo '<br><a name="help-captcha"></a><b>CAPTCHA Image Test - What\'s That?</b><br>It displays an image with random characters, the person who fills in the form has to enter these characters to a form field. It is used to check if there is a live person, not a robot/computer.<br>';
echo '</center></div>';
exit;
}


#################################################################################

function is_gd() {
if (function_exists('imageellipse')) return 1;
return 0;
}

#################################################################################
#################################################################################
#################################################################################

?>