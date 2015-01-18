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
$s[selected_menu] = 1;
get_messages('user.php');

switch ($_GET[action]) {
case 'new'					: show_users('new');
case 'active'				: show_users('active');
case 'most_items'			: show_users('most_items');
}
switch ($_POST[action]) {
}
user_info($_GET);

#########################################################################
#########################################################################
#########################################################################

function show_users($what) {
global $s,$m;

if ($what=='new') { $a[title] = $m[new_users]; $order_by = "joined desc"; }
elseif ($what=='active') { $a[title] = 'Active users'; $order_by = "joined desc"; }
elseif ($what=='most_items') { $a[title] = $m[users_most_items]; $order_by = "items desc"; }

$from = 0;
$q = dq("select *,(links+articles+blogs) as items from $s[pr]users group by n order by $order_by limit $from,$s[u_per_page]",1);
while ($x = mysql_fetch_assoc($q)) { $item[] = $x; $numbers[] = $x[n]; }

$a[items] = get_complete_users($item,$numbers,'user_a.txt');
page_from_template('users_list.html',$a);
}

#########################################################################

function get_complete_users($user,$numbers,$template) {
global $s;//foreach ($user[0] as $k=>$v) echo "$k - $v<br>";
$s[u_columns] = 1;
$width = floor(100/$s[u_columns]);
foreach ($user as $k => $a)
{ if ($a[picture]) 
  { $a[image_1] = $a[picture];
	$big_file = preg_replace("/\/$a[n]-/","/$a[n]-big-",$a[picture]);
    if (file_exists(str_replace("$s[site_url]/","$s[phppath]/",$big_file))) $a[image_1_big] = $big_file;
    else $a[image_1_big] = $a[picture];
    $a[pictures]++;
  }
  if (!$a[pictures]) { $a[hide_pictures_begin] = '<!--'; $a[hide_pictures_end] = '-->'; }
  $a[title_no_tag] = strip_tags($a[title]);
  $a[item_details_url] = get_user_url($a[n]);
  $a[joined] = datum($a[joined],0);
  $a[user_rank] = $s['u_rank_n_'.$a[rank]];
  $complete_array[] = '<td valign="top" width="'.$width.'%">'.parse_part($template,$a).'</td>';
  $pocet++;
}
$rows = ceil($pocet/$s[u_columns]);
for ($x=$pocet+1;$x<=($rows*$s[u_columns]);$x++)
{ $complete_array[] = '<td>&nbsp;</td>';
  $pocet++;
}
for ($x=1;$x<=$rows;$x++)
{ $complete .= '<tr>';
  for ($y=($x-1)*$s[u_columns];$y<=$x*$s[u_columns]-1;$y++)
  $complete .= $complete_array[$y];
  $complete .= '</tr>';
}
return $complete;
}

#########################################################################
#########################################################################
#########################################################################

function user_info($in) {
global $s,$m;
if (is_numeric($in[n])) $a = get_user_variables($in[n]);
else 
{ $in = replace_array_text($in);
  $q = dq("select * from $s[pr]users where nick = '$in[n]'",1);
  $a = mysql_fetch_assoc($q);
}
if (!$a[showemail]) { $a[name] = $m[no_public]; $a[email] = $m[no_public]; }
$a[user_rank] = $s['u_rank_n_'.$a[rank]];
$a[joined] = datum($a[joined],0);

list($images,$files) = get_item_files('u',$a[n],0);
$images = detail_page_images($images[$a[n]],$a[n],0);
if ($images[full_size_image]) $a[pictures_gallery] = $images[full_size_image];
if ($images[pictures_gallery]) { $a[pictures_gallery] = $images[pictures_gallery]; $a[previews_width] = $images[previews_width]; }
if (!$a[pictures_gallery]) { $a[hide_image_begin] = '<!--'; $a[hide_image_end] = '-->'; }

$a[contact_box] = contact_box('u',$a[n],'',1);
$q = dq("select * from $s[pr]ads where owner = '$a[n]' order by created desc limit 50",1);
while ($x = mysql_fetch_assoc($q)) { $item[] = $x; $numbers[] = $x[n]; }
if ($numbers) $a[classifieds] = get_complete_ads($item,$numbers,'ad_a.txt');
else $a[classifieds] = '<td align="left" style="padding:20px;">'.$m[user_no_ads].'</td>';

if (!$a[detail]) $a[detail] = $m[no_user_article];
page_from_template('user_details.html',$a);
}

###############################################################################
###############################################################################
###############################################################################

?>