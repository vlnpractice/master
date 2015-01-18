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
get_messages('user.php');
include_once("$s[phppath]/data/data_forms.php");
if (!$s[GC_u_n]) exit;
if (!$_POST) $_POST = $_GET; unset($_POST[vars]);//new ajax
$in = replace_array_text($_POST);
dq("delete from $s[pr]u_private_notes where user = '$s[GC_u_n]' AND what = '$in[what]' AND n = '$in[n]'",1);
dq("insert into $s[pr]u_private_notes values('$s[GC_u_n]','$in[what]','$in[n]','$in[notes]')",1);
echo stripslashes(notes_edit_box($in[what],$in[n],'<br>'.info_line($m[notes_saved])));
exit;

###############################################################################

?>