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
case 'latitudes_import'				: latitudes_import($_GET[country]);
case 'latitudes_show'				: latitudes_show($_GET[country]);
case 'latitude_edit'				: latitude_edit($_GET[what],$_GET[n]);
}
switch ($_POST[action]) {
case 'latitudes_uploaded'			: latitudes_uploaded($_POST,$_FILES[latitudes_file]);
case 'latitude_edited'				: latitude_edited($_POST);
}
latitudes_home();

#################################################################################

function latitudes_auto() {
global $s;
ih();


$file_url = 'http://geolite.maxmind.com/download/geoip/database/GeoIPCountryCSV.zip';
$file = fopen($file_url,'r') or problem("Unable to download file $file_url");
$openfile = fopen("$s[phppath]/data/ip_list.zip",'w') or problem("Unable to write to file $s[phppath]/data/ip_list.zip");
while ($data = fread($file,1000))
{ increase_print_time(5,1);
  fwrite($openfile,$data) or problem("Unable to write to file $s[phppath]/data/ip_list.zip");
}
fclose ($file);
fclose($openfile);
echo "<br><br><b>File downloaded, starting to unpack it</b><br>";
$zip = new ZipArchive();   
if ($zip->open("$s[phppath]/data/ip_list.zip")!==TRUE) problem("Could not open archive");
$zip->extractTo("$s[phppath]/data");
$zip->close();
unlink("$s[phppath]/data/ip_list.zip");
echo "<br><br><b>File unpacked, starting import it to your database</b><br><br>";
$in[latitudes_uploaded_file] = "GeoIPCountryWhois.csv";
$in[delete_current_data] = 1;
latitudes_uploaded($in);
}

#################################################################################

function latitudes_home() {
global $s;
ih();
echo $s[info];

echo '
<table border="0" width="800" cellspacing="0" cellpadding="10" class="common_table">
<tr><td class="common_table_top_cell">Info</td></tr>
<tr><td align="left">
This database contains latitudes and longitudes of individual cities. It\'s used by the radius searching which is available on public pages. If you want to use this feature, download the following files:<br>
At <a target="_blank" href="http://download.geonames.org/export/zip/">http://download.geonames.org/export/zip/</a>, download the file allCountries.zip.<br>
Also download the file <a target="_blank" href="http://www.maxmind.com/GeoIPCity-534-Location.csv">http://www.maxmind.com/GeoIPCity-534-Location.csv</a>.<br>
<br>
Unpack the zip allCountries.zip and upload the file allCountries.txt and also the file GeoIPCity-534-Location.csv to your data directory in Gold Classifieds. Then use the form below to import cities in selected country(ies). Once the import has been finished, you can check the data and optionally edit it by using the link "Show existing data" below. Please import only data of countries which should be used.<br>
Also make sure to enable this feature in <a href="configuration_main.php">Configuration</a>.
</td></tr><table>
<br>
<table border="0" width="800" cellspacing="0" cellpadding="0" class="common_table">
<tr><td class="common_table_top_cell">Show & Import Data</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="inside_table">';
$q = dq("select * from $s[pr]countries order by name",1);
while ($country=mysql_fetch_assoc($q))
{ if ($country[flag]) $flag = '<img border="0" src="'.$s[site_url].'/images/flags/small/'.$country[flag].'">'; else $flag = '';
  $x++;
  $country[name] = strip_replace_once($country[name]);
  if ($country[allowed]) $checked = ' checked'; else $checked = '';
  echo '<tr>
  <td align="center" width="50">'.$flag.'</td>
  <td align="left" width="400">'.$country[name].'</td>
  <td align="center" width="200"><a href="latitudes.php?action=latitudes_show&country='.$country[code].'">Show existing data</a></td>
  <td align="center" width="200"><a href="latitudes.php?action=latitudes_import&country='.$country[code].'">Import data</a></td>
  </tr>';
}
echo '</table></td></tr></table><br><br>';
ift();
}


#################################################################################

function latitudes_import($in_country) {
global $s;
if (!file_exists("$s[phppath]/data/allCountries.txt"))
{ $s[info] = info_line("The file allCountries.txt does not exist in your data directory");
  latitudes_home();
}

$q = dq("select * from $s[pr]countries where code = '$in_country'",1);
$country_vars = mysql_fetch_assoc($q);

$fd = fopen ("$s[phppath]/data/allCountries.txt",'r');
while (!feof ($fd))
{ increase_print_time(2,1);
  $line = trim(fgets($fd,10000));
  if (!$line) continue;
  list($country,$zip,$city,$region,$x,$x,$x,$x,$x,$lat,$lon) = explode("\t",$line);
  if ($in_country!=$country) continue;
  //echo "($country,$zip,$city,$region,$x,$x,$x,$x,$x,$lat,$lon)<br>";
  $zip = str_replace(' ','',$zip);
  $city = replace_once_text($city); if ($s[charset]!='UTF-8') $city = iconv('UTF-8',$s[charset],$city);
  $region = replace_once_text($region); if ($s[charset]!='UTF-8') $region = iconv('UTF-8',$s[charset],$region);
  dq("insert into $s[pr]city_zip values(NULL,'$country','$country_vars[name]','$region','$city','$zip','$lat','$lon')",0); // must be 0
  $pocet++; 
}
fclose ($fd);

if (!$pocet)
{ $file = file("$s[phppath]/data/fips_include.txt");
  foreach ($file as $k=>$line)
  { list($country,$code,$region) = explode(",",trim($line));
    if ($in_country!=$country) continue;
    $code = trim(str_replace('"','',$code));
    $region = trim(str_replace('"','',$region));
    $region = replace_once_text($region);// if ($s[charset]!='UTF-8') $region = iconv('UTF-8',$s[charset],$region); not needed
    $regions[$code] = $region;
  }
  if ($regions)
  { $fd = fopen ("$s[phppath]/data/GeoIPCity-534-Location.csv",'r');
    while (!feof ($fd))
    { increase_print_time(2,1);
      $line = trim(fgets($fd,10000));
      if (!$line) continue;
      list($x,$country,$region,$city,$zip,$lat,$lon) = explode(",",$line);
      $country = str_replace('"','',$country);
      if ($in_country!=$country) continue;
      //echo "($country,$zip,$city,$region,$x,$x,$x,$x,$x,$lat,$lon)<br>";
      $city = str_replace('"','',$city); if (!trim($city)) continue;
      $region = str_replace('"','',$region); $region_name = $regions[$region];
      $zip = str_replace('"','',$zip);
      $city = replace_once_text($city); if ($s[charset]!='ISO-8859-1') $city = iconv('ISO-8859-1',$s[charset],$city);
      //echo "insert into $s[pr]city_zip values(NULL,'$in_country','$country_vars[name]','$region_name','$city','$zip','$lat','$lon')";
      dq("insert into $s[pr]city_zip values(NULL,'$in_country','$country_vars[name]','$region_name','$city','$zip','$lat','$lon')",0); // must be 0
      $pocet++; 
    }
    fclose ($fd);
  }
}

increase_print_time(2,'end');
$s[info] = info_line("Records imported: $pocet");
latitudes_home();
}

#################################################################################

function latitude_edit($what,$n) {
global $s;
$q = dq("select * from $s[pr]city_zip where n = '$n'",1);
$a = mysql_fetch_assoc($q);
$hidden_array[action] = 'latitude_edited';
$hidden_array[n] = $n;
$hidden_array[what] = $what;
echo ajax_form('POST',$hidden_array,$what,$a[$what]);
exit;
}

#################################################################################

function latitude_edited($in) {
global $s;
$value = $in[$in[what]];
dq("update $s[pr]city_zip set $in[what] = '$value' where n = '$in[n]'",1);
echo ajax_form_link($value,"$s[site_url]/administration/latitudes.php?action=latitude_edit&what=$in[what]&n=$in[n]");
exit;
}

#################################################################################

function latitudes_show($in_country) {
global $s;
ih();
$q = dq("select * from $s[pr]countries where code = '$in_country'",1);
$country_vars = mysql_fetch_assoc($q);

echo '
<table border="0" width="800" cellspacing="0" cellpadding="2" class="common_table">
<tr><td class="common_table_top_cell">Cities and Postal codes in '.$country_vars[name].'</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="inside_table">
<tr>
<td align="center" colspan=5>To edit selected record click to the pencil icon.</td>
</tr>
<tr>
<td align="left"><b>Region</b></td>
<td align="left"><b>City</b></td>
<td align="left"><b>Postal code</b></td>
<td align="left"><b>Latitude</b></td>
<td align="left"><b>Longitude</b></td>
</tr>';
$q = dq("select * from $s[pr]countries where code = '$in_country'",1);
$country_vars = mysql_fetch_assoc($q);
$q = dq("select * from $s[pr]city_zip where country = '$in_country' order by region,city",1);
while ($b = mysql_fetch_assoc($q))
{ echo '<tr>
  <td align="left" nowrap>'.ajax_form_link($b[region],"$s[site_url]/administration/latitudes.php?action=latitude_edit&what=region&n=$b[n]").'</td>
  <td align="left" nowrap>'.ajax_form_link($b[city],"$s[site_url]/administration/latitudes.php?action=latitude_edit&what=city&n=$b[n]").'</td>
  <td align="left" nowrap>'.ajax_form_link($b[zip],"$s[site_url]/administration/latitudes.php?action=latitude_edit&what=zip&n=$b[n]").'</td>
  <td align="left" nowrap>'.ajax_form_link($b[lat],"$s[site_url]/administration/latitudes.php?action=latitude_edit&what=lat&n=$b[n]").'</td>
  <td align="left" nowrap>'.ajax_form_link($b[lon],"$s[site_url]/administration/latitudes.php?action=latitude_edit&what=lon&n=$b[n]").'</td>
  </tr>';
}
echo '</table></td></tr></table>';
ift();
}


#################################################################################

function latitudes_uploaded($in,$in_file) {
global $s;
$file = "$s[phppath]/data/latitudes_file";
if (is_uploaded_file($in_file[tmp_name])) 
{ if (file_exists($file)) unlink($file);
  move_uploaded_file($in_file[tmp_name],$file);
  chmod ($file,0644);
}
elseif ((trim($in[latitudes_uploaded_file])) AND (file_exists("$s[phppath]/data/$in[latitudes_uploaded_file]")))
{ if (file_exists($file)) unlink($file);
  rename("$s[phppath]/data/$in[latitudes_uploaded_file]",$file);
}
else { $s[upload_error] = 1; latitudes_home(); }
if ($in[delete_current_data]) dq("delete from $s[pr]latitudes",1);
$fd = fopen ($file,'r');
while (!feof ($fd))
{ $buffer = fgets($fd,10000);
  if (!trim($buffer)) continue;
  $pocet++; 
  $x = explode(',',trim($buffer));
  set_time_limit(300);
  dq("insert into $s[pr]latitudes values(".str_replace('\"','"',$x[2]).','.str_replace('\"','"',$x[3]).','.str_replace('\"','"',$x[4]).")",1);
  increase_print_time(2,1);
}
fclose ($fd);
unlink ($file);
increase_print_time(2,'end');
$s[info] = info_line('Import successful. Records imported: '.$pocet);
latitudes_home();
}


#################################################################################

?>