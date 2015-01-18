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

include('./ads_functions.php');

switch ($_GET[action]) {
case 'abuse_reports'		: abuse_reports();
case 'abuse_report_delete'	: abuse_report_delete($_GET[n]);
}

##################################################################################
##################################################################################
##################################################################################

function abuse_reports() {
global $s;
ih();
$q = dq("select * from $s[pr]ads_abuse_reports",1);
if (!mysql_num_rows($q)) { echo info_line('<br><br><br><br><br>No One Abuse Report Found<br>'); ift(); }
echo $s[info];
echo page_title('Abuse Reports');
while ($report = mysql_fetch_assoc($q))
{ $report = stripslashes_array($report);
  $info = "Abuse Report #$report[n], name: $report[name], email: <a href=\"mailto:$report[email]\">$report[email]</a><br>
  Date: ".datum($report[time],0).", IP address: $report[ip]<br>Entered message: $report[text]</span><br>
  <a href=\"ads_reports.php?action=abuse_report_delete&n=$report[n]\">Delete this report</a><br>";
  $ad = get_ad_variables($report[ad]);
  if (!$ad[n]) dq("delete from $s[pr]ads_abuse_reports where n = $report[n]",1);
  else { echo $info; show_one_ad($ad); $pocet1 = 1; }
}
if (!$pocet1) { echo '<meta http-equiv="Refresh" content="0; URL=ads_reports.php?action=abuse_reports">'; exit; }
ift();
}

##################################################################################

function abuse_report_delete($n) {
global $s;
dq("delete from $s[pr]ads_abuse_reports where n = '$n'",1);
$s[info] = info_line('Selected Report Has Been Deleted');
abuse_reports();
}

######################################################################################
######################################################################################
######################################################################################

?>