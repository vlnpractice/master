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
if ((!$_SESSION['GC_admin_user']) AND (!$_COOKIE['GC_admin_user'])) { header('Location: login.php'); exit; }
?>
<html>
<head>
<title>Gold Classifieds - Administration</title>
<base target="_self"></head>
<frameset rows="1*" cols="200, 1*" border="0">
<frame name="left" scrolling="auto" marginwidth="0" marginheight="0" src="home.php?action=left_frame" frameBorder=no Resize>
<frame name="right" scrolling="auto" src="home.php?action=home" Resize frameBorder=NO>
</frameset>
</html>
