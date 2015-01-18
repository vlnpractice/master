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
get_messages('index.php');
include($s[phppath].'/data/data_forms.php');

switch ($_GET[action]) {
case 'category'					: category_ajax($_GET);
case 'category_pages_list'		: category_pages_list_ajax($_GET);
}

#############################################################################
#############################################################################
#############################################################################

function category_ajax($in) {
global $s,$m;

//foreach ($in as $k=>$v) echo "$k - $v<br>";


if (!is_numeric($_GET[n])) exit;
$table = "$s[pr]ads";
$perpage = $s[per_page];

$a = get_category_variables($in[n]); if ($a[alias_of]) $a = get_category_variables($a[alias_of]);
//check_access_rights("c_$in[what]",$a[n],$a);
if ((!$a[n]) AND (!$in[area])) exit;
if (!$a[tmpl_one]) $a[tmpl_one] = 'ad_a.txt';

if ((!$_GET[page]) OR (!is_numeric($_GET[page]))) { $from = 0; $_GET[page] = 1; } else $from = $perpage * ($_GET[page]-1); 
$sortby = find_order_by_ads($_GET[sort],$_GET[direction]); //!!!!!!!!!!!!!!!!!!!
$where = 'where '.get_where_fixed_part(0,$in[n],0,$in[area],$s[cas],$_SESSION[this_offer_wanted]);
$q = dq("select count(*) from $table $where",1);

$total = mysql_fetch_row($q); $a[total] = $total[0];

//echo "select * from $table $where order by $sortby limit $from,$perpage";

$q = dq("select * from $table $where order by $sortby limit $from,$perpage",1);
while ($x = mysql_fetch_assoc($q)) { $item[] = $x; $numbers[] = $x[n]; }
//echo "select * from $table $where order by $sortby limit $from,$perpage";

if ($numbers)
{ foreach ($item as $k => $d) $item[$k][category] = $a[n];
  $a[items] = get_complete_ads($item,$numbers,$a[tmpl_one]);
}
else exit;
page_from_template_no_headers('category_ajax.txt',$a);
}

##################################################################################

function category_pages_list_ajax($in) {
global $s,$m;
//foreach ($in as $k=>$v) echo "-- $k - $v<br>";
if (!is_numeric($in[n])) exit;
$perpage = $s[per_page];

$s[this_cat] = $in[n];
$s[this_area] = $in[area];
$s[this_sort] = $in[sort];
$s[this_direction] = $in[direction];
//$s[this_offer_wanted] = $_SESSION[this_offer_wanted];

echo stripslashes(category_pages_list('ad',$in[n],'',$in[area],round($in[total]),$in[page]));
}

##################################################################################
##################################################################################
##################################################################################

?>