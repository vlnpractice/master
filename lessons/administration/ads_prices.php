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
check_admin('prices');

switch ($_GET[action]) {
case 'ads_prices_home'		: ads_prices_home($_GET[c],$_GET[load]);
}
switch ($_POST[action]) {
case 'ads_prices_edited'	: ads_prices_edited($_POST);
}

##################################################################################
##################################################################################
##################################################################################

function ads_prices_edited($in) {
global $s;
dq("delete from $s[pr]ads_prices where c = '$in[c]'",1);
for ($x=0;$x<=9;$x++)
{ $prices = $in[price][$x];
  if (!$prices[days]) continue;
  dq("insert into $s[pr]ads_prices values ('$in[c]','$prices[days]','$prices[ad]','$prices[bold]','$prices[featured]','$prices[home_page]','$prices[featured_gallery]','$prices[highlight]','$prices[paypal]','$prices[xtra_10_pictures]','$prices[xtra_10_files]','$prices[xtra_10_videos]')",1);
}
$s[info] = info_line('Prices Updated');
ads_prices_home($in[c]);
}

##################################################################################

function ads_prices_home($c,$load) {
global $s;
include("$s[phppath]/data/data_forms.php");
$options = array('ad'=>'Ad listing, no extra features','bold'=>'Bold listing','featured'=>'Featured ad (top of pages)','home_page'=>'Featured ad - home page','featured_gallery'=>'Listed in featured galleries','highlight'=>'Highlighted ad','paypal'=>'Paypal "Order Now" button','xtra_10_pictures'=>'Up to 10 extra pictures','xtra_10_files'=>'Up to 10 extra files');
if ($c)
{ $category = get_category_variables($c);
   if ($load) dq("delete from $s[pr]ads_prices where c = '$c'",1);
   else $q = dq("select * from $s[pr]ads_prices where c = '$c' order by days",1);
}
if (mysql_num_rows($q)) $category_specific = 1;
else $q = dq("select * from $s[pr]ads_prices where c = '0' order by days",1);
while ($x=mysql_fetch_assoc($q)) $prices[] = $x;
ih();
echo $s[info];
echo '<form action="ads_prices.php" method="post">'.check_field_create('admin').'
<input type="hidden" name="action" value="ads_prices_edited">
<input type="hidden" name="c" value="'.$c.'">
<table border="0" width="750" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Prices</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">';
if ($category) echo '<TR><TD align="center" colspan="11">These prices are valid for category <b>'.$category[title].'</b> and all its subcategories.</td></tr>';
else echo '<TR><TD align="center" colspan="11">These prices are valid for those categories which don\'t have defined their own prices</td></tr>';
echo '<tr>
<td align="left">Number of days</td>';
for ($x=0;$x<=9;$x++)
echo '<td align="center"><input class="field10" name="price['.$x.'][days]" value="'.$prices[$x][days].'" size=5 maxlength=10></td>';
echo '</tr>';
foreach ($options as $k=>$v) 
{ echo '<tr>
  <td align="left" nowrap>'.$v.'</td>';
  for ($x=0;$x<=9;$x++)
  { if ($k=='ad') $disabled = $prices[$x][disabled];
    echo '<td align="center"><input class="field10" name="price['.$x.']['.$k.']" value="'.number_format($prices[$x][$k],2).'" size=5 maxlength=10'.$disabled.'></td>';
  }
  echo '</tr>';
}
echo '<tr><td align="center" colspan="11"><input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</form></table></td></tr></table>';
if ($category_specific) echo '<br><a href="ads_prices.php?action=ads_prices_home&c='.$c.'&load=1">Cancel prices for this category and load default prices to the table above.</a><br>';
echo '<br>
<table border="0" width="750" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Info</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<TR><TD align="left">
You can offer a basic listing for free. To do so, enter the value "0" to those periods which should be free. For example if you want to offer 30 days for free, enter the number 0 to the line "Ad listing, no extra features" to all periods which long 30 days or less.
</td></tr></table></td></tr></table>';
ift();
}

######################################################################################
######################################################################################
######################################################################################

?>