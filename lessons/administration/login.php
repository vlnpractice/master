<?PHP

#################################################
##                                             ##
##              Gold Classifieds                ##
##         http://www.abscripts.com/           ##
##         e-mail: mail@abscripts.com          ##
##                                             ##
##                 Version 3.5                 ##
##            copyright (c) 2011               ##
##                                             ##
##  This script is not freeware nor shareware  ##
##    Please do no distribute it by any way    ##
##                                             ##
#################################################

$s['no_test'] = 1;
include('./common.php');
if ($_GET[action]=='log_off') log_off();
login($_POST);

##################################################################################
##################################################################################
##################################################################################

function log_off() {
global $s;
session_destroy();
setcookie (GC_admin_user,$_COOKIE[GC_admin_user],$s[cas]-604800); 
setcookie (GC_admin_password,$_COOKIE[GC_admin_password],$s[cas]-604800); 
setcookie (GC_admin_n,$_COOKIE[GC_admin_n],$s[cas]-604800); 
if (!$s[info])
$s[info] = info_line('You have been logged off');
}

##################################################################################

function login($form) {
global $s;
check_if_too_many_logins('admin',"$s[pr]admins",'','');
if (!$form)
{ echo '<script>
  <!--
  if (window!= top)
  top.location.href=location.href
  -->
  </script>';
  ih();
  echo $s[info];
  ?>
  <table border="0" width="200" cellspacing="2" cellpadding="4" class="common_table">
  <form method="POST" action="login.php">
  <tr>
  <td align="left">Username</td>
  <td align="left"><input class="field10" name="username" size=15 maxlength=15 value="<?PHP echo $s[pusername] ?>"></td>
  </tr>
  <tr>
  <td align="left">Password</td>
  <td align="left"><input class="field10" type="password" name="password" size=15 maxlength=15 value="<?PHP echo $s[pupassword] ?>"></td>
  </tr>
  <tr>
  <td align="left" nowrap>Remember me</td>
  <td align="left"><input type="checkbox" value="1" name="remember_me"<?PHP if ($_COOKIE[GC_admin_user]) echo ' checked'; ?>></td>
  </tr>
  <tr><td colspan=2 align="center"><input type="submit" value="Submit" name="B1" class="button10"></td>
  </tr></form></table>
  <?PHP
  exit;
}
$form[username] = replace_once_text($form[username]); $password = md5($form[password]);
$q = dq("select n from $s[pr]admins where username = '$form[username]' AND password = '$password'",1);
$data = mysql_fetch_row($q);
if (!$data[0])
{ check_if_too_many_logins('admin',"$s[pr]admins",$form[username],$form[password]);
  $s[info] = info_line('Wrong username or password. Please try again.');
  $s[pusername] = $form[username]; $s[pupassword] = $form[password];
  login(0); exit;
}

if ($form[remember_me])
{ setcookie (GC_admin_user,$form[username],$s[cas]+31536000); 
  setcookie (GC_admin_password,$password,$s[cas]+31536000); 
  setcookie (GC_admin_n,$data[0],$s[cas]+31536000); 
}
else
{ $_SESSION['GC_admin_user'] = $form[username];
  $_SESSION['GC_admin_password'] = $password;
  $_SESSION['GC_admin_n'] = $data[0];
}
header ("Location: index.php");
}

#########################################################################

function get_ip() {
if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
elseif (getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
elseif (getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
else $ip = 'UNKNOWN';
return $ip;
}

##################################################################################
##################################################################################
##################################################################################

?>