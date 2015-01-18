<?PHP

#################################################
##                                             ##
##               Link Up Gold                  ##
##       http://www.phpwebscripts.com/         ##
##       e-mail: info@phpwebscripts.com        ##
##                                             ##
##                                             ##
##               version:  8.0                 ##
##            copyright (c) 2012               ##
##                                             ##
##  This script is not freeware nor shareware  ##
##    Please do no distribute it by any way    ##
##                                             ##
#################################################

include('./common.php');

if (substr($_GET[file],0,13)!='/uploads/pdf/') exit;
if (!file_exists("$s[phppath]$_GET[file]")) exit;
$filename = str_replace('/uploads/pdf/','',$_GET[file]);
$file = "$s[phppath]$_GET[file]";
/*
header("Content-type: application/octet-stream\n" );
header("Content-Disposition: filename=$filename");
header("Content-Disposition: inline; filename=$filename");
header("Content-Length: " . filesize("$s[phppath]$_GET[file]"));
header('Accept-Ranges: bytes');
readfile("$s[phppath]$_GET[file]");
*/
header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);

//foreach ($_GET as $k => $v) echo "$k - $v<br>\n";
//echo '<meta HTTP-EQUIV="REFRESH" content="0; url='.$s[site_url].$_GET[file].'">';
//http://www.violinpractice.com/redir.php?file=/uploads/pdf/15-1-1391890173.pdf
?>