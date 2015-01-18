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

if (($_GET[cat]) OR ($_GET[area]) OR ($_GET[action]=='home'))
{
	
if (($_GET[cat]) AND (is_numeric($_GET[cat]))) $this_category = $_GET[cat]; else $this_category = 0;

if ($this_category) $where = "(parent = '$this_category' or n = '$this_category')"; 
else $where = "level = '1'";
$q = dq("select * from $s[pr]cats where $where and visible = 1 order by level,rank,title",1);
while ($x=mysql_fetch_assoc($q)) { $cats[$x[n]] = $x; $numbers[] = $x[n]; }

foreach ($cats as $k=>$category)
{ if ($category[n]==$this_category) $categories .= '<a target="_top" href="'.add_this_area(category_url('ad',$category[n],$category[alias_of],1,$category[rewrite_url])).'"><b>'.$category[title].$left_items.'</b></a><br>';
  else $categories .= '<a target="_top" href="'.add_this_area(category_url('ad',$category[n],$category[alias_of],1,$category[rewrite_url])).'">'.$category[title].$left_items.'</a><br>';
}
$categories = str_replace('-page_n','',str_replace('-extra_commands','',$categories));

if ($_GET[action]=='home')
{ $latitude = $s[home_map_lat];
  $longitude = $s[home_map_lon];
  $zoom = $s[home_map_zoom];
  $q = dq("select * from $s[pr]areas where level = 1 and latitude != 0.0000000 and longitude != 0.0000000",1);
  while ($x=mysql_fetch_assoc($q))
  { $pocet++;
    if (!$x[image2]) $x[image2] = "$s[site_url]/images/icon_folder.gif";
    $html = '<div style="width:200px;height:150px;overflow:auto;background-color:#E0E9FE;padding:7px;color:#000000;"><img border=0 src="'.$x[image2].'"> <a target="_top" href="'.$s[site_url].'/'.$s[ARfold_l_cat].'-0-'.$x[n].'/'.$x[rewrite_url].'.html" style="font-size:18px;font-weight:bold;">'.$x[title].'</a><br>'.str_replace('area_n',$x[n],str_replace('area_rewrite',$x[rewrite_url],$categories)).'</div>';
    $points .= "
    var mymappoint$pocet = new google.maps.LatLng('$x[latitude]','$x[longitude]');
    createMarker(mymappoint$pocet,'$html','$x[title]','$x[image2]');";
  }
}
elseif (($_GET[area]) AND (is_numeric($_GET[area])))
{ $area_vars = get_area_variables($_GET[area]);
  if (!$area_vars[n]) exit;
  $latitude = $area_vars[latitude];
  $longitude = $area_vars[longitude];
  $zoom = $area_vars[map_zoom];
  $level = $area_vars[level] + 1;
  $q = dq("select * from $s[pr]areas where level = '$level' and parent = '$area_vars[n]' and latitude != 0.0000000 and longitude != 0.0000000",1);
  while ($x=mysql_fetch_assoc($q))
  { $pocet++;
    if ($x[image2]) $icon1 = $x[image2]; else $icon1 = "$s[site_url]/images/map_icon1.png";
    if ($x[image2]) $icon2 = $x[image2]; else $icon2 = "$s[site_url]/images/map_icon2.png";
    $html = '<div style="width:200px;height:150px;overflow:auto;background-color:#E0E9FE;padding:7px;color:#000000;"><img border=0 src="'.$icon2.'"> <a target="_top" href="'.$s[site_url].'/'.$s[ARfold_l_cat].'-0-'.$x[n].'/'.$x[rewrite_url].'.html" style="font-size:18px;font-weight:bold;">'.$x[title].'</a><br>'.str_replace('area_n',$x[n],str_replace('area_rewrite',$x[rewrite_url],$categories)).'</div>';
    $points .= "
    var mymappoint$pocet = new google.maps.LatLng('$x[latitude]','$x[longitude]');
    createMarker(mymappoint$pocet,'$html','$x[title]','$icon1');";
  }
}
else exit;

}
elseif (($_GET[ad]) AND (is_numeric($_GET[ad])))
{ $ad_vars = get_ad_variables($_GET[ad]);
  if (!$ad_vars[n]) $ad_vars = get_ad_variables($_GET[ad]);
  if (!$ad_vars[n]) exit;
  $longitude = $ad_vars[longitude]; $latitude = $ad_vars[latitude];
  $zoom = 10;
  $icon1 = "$s[site_url]/images/map_icon1.png";
  $title = $ad_vars[title];
  $html = '<span style="font-size:15px;font-weight:bold;">'.htmlspecialchars($ad_vars[title]).'</span><br>'.htmlspecialchars($ad_vars[description]);
  $points .= "
  var mymappoint$pocet = new google.maps.LatLng('$latitude','$longitude');
  createMarker(mymappoint$pocet,'<div style=\"width:200px;height:150px;overflow:auto;background-color:#E0E9FE;padding:7px;color:#000000;\">".$html."</div>','$title','$icon1');";
}
else exit;

if ($s[A_option]!='rewrite')
{ $points = str_replace("$s[site_url]/index_offer.html","$s[site_url]/index.php?vars=offer",$points);
  $points = str_replace("$s[site_url]/index_wanted.html","$s[site_url]/index.php?vars=wanted",$points);
  $points = str_replace("$s[site_url]/index_all.html","$s[site_url]/index.php?vars=all",$points);
  $points = str_replace("$s[site_url]/$s[ARfold_l_detail]-","$s[site_url]/classified.php?vars=",$points);
  $points = str_replace("$s[site_url]/$s[ARfold_l_cat]-","$s[site_url]/index.php?vars=/$s[ARfold_l_cat]-",$points);
  $points = str_replace("$s[site_url]/extra_category/","$s[site_url]/category.php?action=",$points);
}

echo '<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<LINK href="'.$s[site_url].'/styles/Sky/styles.css" rel="StyleSheet">
<style type="text/css">
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}
#map_canvas {
  height: 100%;
}
</style>
<script type="text/javascript"
    src="http://maps.googleapis.com/maps/api/js?sensor=false">
</script>
<script type="text/javascript">
var mymap = new google.maps.LatLng('.($latitude+0.008).','.($longitude-0.02).');
var marker;
var map;




function createMarker(point,html,title,icon) {
var infowindow = new google.maps.InfoWindow({
        content: html
    });

var marker = new google.maps.Marker({
    position: point,
    map: map,
    title: title,
    icon: icon
  });
    google.maps.event.addListener(marker, \'click\', function() {
      infowindow.open(map,marker);
    });

  return marker;
}

function initialize() {
  var mapOptions = {
    zoom: '.$zoom.',
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    center: mymap
  };
  map = new google.maps.Map(document.getElementById("map_canvas"),
      mapOptions);
'.$points.'
}

</script>
</head>
<body onload="initialize()">
  <div id="map_canvas"></div>
</body>
</html>
';

/*
var mymappoint = new google.maps.LatLng('.($latitude).','.($longitude).');
createMarker(mymappoint, \'kkkkkkkkkkkkkkk\', \'mmmmmmmmmmmmmmmmmmmmm\');
    
var mymappoint1 = new google.maps.LatLng('.($latitude+1).','.($longitude+1).');
createMarker(mymappoint1, \'kkk22222222222kkkkk\', \'mmm11111111mmmmmmm\');
    
*/

?>