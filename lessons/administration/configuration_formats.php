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
case 'configuration_formats_edited'	: configuration_formats_edited($_POST);
}
configuration_formats_form();
	
#################################################################################
#################################################################################
#################################################################################

function configuration_formats_edited($in) {
global $s;
dq("delete from $s[pr]file_types",1);
foreach ($in[extension] as $n=>$extension)
{ if (!trim($extension)) continue;
  $mime_type = $in[mime_type][$n]; $description = $in[description][$n]; $icon = $in[icon][$n]; $allowed = $in[allowed][$n];
  dq("insert into $s[pr]file_types values('$n','$extension','$mime_type','$icon','$description','$allowed')",1);
}

$s[info] = info_line('Your configuration has been successfully updated');
configuration_formats_form();
}

#################################################################################

function configuration_formats_form() {
global $s;
$formats = get_file_formats(0);
ih();
echo $s[info];
echo '<form method="POST" action="configuration_formats.php">'.check_field_create('admin').'
<INPUT type="hidden" name="action" value="configuration_formats_edited">
<table border="0" width="700" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Configuration - File Formats</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr>
<td align="center" valign="top">Ext.</td>
<td align="center" valign="top">Mime type</td>
<td align="center" valign="top">Description</td>
<td align="center" valign="top">Is<br>allowed</td>
<td align="center" valign="top">Icon</td>
<td align="center" valign="top">Current<br>icon</td>
</tr>';
foreach ($formats[extensions] as $n=>$extension)
{ if ($formats[file_types][$n][icon]) $current_icon = '<img border="0" src="'.$s[site_url].'/images/file_icons/'.$formats[file_types][$n][icon].'">';
  else $current_icon = '';
  echo '<tr>
  <td align="center" valign="top"><input class="field10" size=4 maxlength=10 name="extension['.$n.']" value="'.$extension.'" style="width:50px"></td>
  <td align="center" valign="top"><input class="field10" size=30 maxlength=100 name="mime_type['.$n.']" value="'.$formats[mime_types][$n].'" style="width:250px"></td>
  <td align="center" valign="top"><input class="field10" size=30 maxlength=100 name="description['.$n.']" value="'.$formats[file_types][$n][description].'" style="width:250px"></td>
  <td align="center" valign="top"><input type="checkbox" name="allowed['.$n.']" value="1"'; if ($formats[file_types][$n][allowed]) echo ' checked'; echo '></td>
  <td align="center" valign="top"><input class="field10" size=10 maxlength=50 name="icon['.$n.']" value="'.$formats[file_types][$n][icon].'" style="width:100px"></td>
  <td align="center" valign="top">'.$current_icon.'</td>
  </tr>';
  if ($n>$max_n) $max_n = $n;
}
for ($x=1;$x<=10;$x++)
{ $n = $max_n + $x;
  echo '<tr>
  <td align="center" valign="top"><input class="field10" size=4 maxlength=10 name="extension['.$n.']" style="width:50px"></td>
  <td align="center" valign="top"><input class="field10" size=30 maxlength=100 name="mime_type['.$n.']" style="width:250px"></td>
  <td align="center" valign="top"><input class="field10" size=30 maxlength=100 name="description['.$n.']" style="width:250px"></td>
  <td align="center" valign="top"><input type="checkbox" name="allowed['.$n.']" value="1"></td>
  <td align="center" valign="top"><input class="field10" size=10 maxlength=50 name="icon['.$n.']" style="width:100px"></td>
  <td align="center" valign="top">&nbsp;</td>
  </tr>';
}
echo '<tr><td align="center" colspan=6><input type="submit" name="submit" value="Save" class="button10"></td></tr>
</table></form></td></tr></table>
<br>
<INPUT type="hidden" name="action" value="configuration_formats_edited">
<table border="0" width="700" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Info</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="left">
<b>Ext.</b> File extension. If you selected that the script should accept extensions of uploaded files, these extensions are used to find out file formats. For example if someone uploaded a file with "txt" extension, it accepts that it\'s a text file.<br>
<b>Mime type</b> If you selected that the script should find formats of uploaded files by their mime types, the content of the "Mime type" field is used to find out the real format of each file which has been uploaded by users. For example if someone uploaded a file with "wav" extension but its Mime type was "text/plain", this file receives "txt" extension. Don\'t edit these fields if you are not sure.<br>
This is used only for files uploaded by owners of classified ads. To find a file format of files uploaded by admin is always used file extension.<br>
<b>Is allowed</b> Formats that are checked can be uploaded by owners of classified ads.<br>
This is used only for files uploaded by owners of classified ads. An admin can upload any file format.<br>
<br>
You can use the blank fields at the bottom of the form to add new files formats. To delete an existing file format make empty its "Ext." field.
Icons are stored in folder images/file_icons.
</td></tr></table></td></tr></table>';
exit;
}

#################################################################################
#################################################################################
#################################################################################

?>