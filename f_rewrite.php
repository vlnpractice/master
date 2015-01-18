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

$s[rewr_delim] = '/';

################################################################################
################################################################################
################################################################################

function rewrite_category($what,$in) {
global $s;

$array = explode('/',$in);
if ($array[0]=='extra') { $a[action] = $array[1]; return $a; }

$last = count($array)-1;
if ($array[$last]=='index') unset($array[$last]);
elseif ((strstr($array[$last],'page-')) OR (strstr($array[$last],'sort-')) OR (strstr($array[$last],'direction-'))) { $commands = $array[$last]; unset($array[$last]); }
$in = implode('/',$array);

$q = dq("select * from $s[pr]cats where use_for = '$what' and rewrite_url = '$in'",1);
$cat = mysql_fetch_assoc($q); $key[] = 'n'; $val[] = $cat[n];
if ($commands)
{ $y = explode('-',$commands);
  foreach ($y as $k=>$v) { if ($k%2) $val[] = $v; else $key[] = $v; }
}
foreach ($key as $k=>$v) $a[$v] = $val[$k];
return $a;
}

################################################################################

function rewrite_item($in) {
global $s;
if (is_numeric($in)) $a[n] = $in;
else
{ list($n,$page) = explode('-page-',$in);
  $a[n] = $n;
  if (is_numeric($page)) $a[page] = $page;
}
return $a;
}

################################################################################

function rewrite_category_url($what,$n,$page,$rewrite_url,$sort,$direction) {
global $s;
if (!$rewrite_url)
{ $q = dq("select rewrite_url from $s[pr]cats where use_for = '$what' and n  = '$n'",1);
  $path = mysql_fetch_row($q); $rewrite_url = $path[0];
}
if ($page>1) $extra_array[] = "page-$page";
if ($sort) { $extra_array[] = "sort-$sort"; if ($direction) $extra_array[] = "direction-$direction"; }
//else unset($sort,$direction);
if ($extra_array) $page_name = implode('-',$extra_array); else $page_name = 'index';
if ($page_name=='index') $complete = "$s[site_url]/".$s['ARfold_'.$what.'_cat']."/$rewrite_url$s[rewr_delim]";
else $complete = "$s[site_url]/".$s['ARfold_'.$what.'_cat']."/$rewrite_url$s[rewr_delim]$page_name.html";
$complete = preg_replace("/-$/",$s[rewr_delim],$complete);
return $complete;
}

################################################################################

function rewrite_special_category_url($what,$name,$page) {
global $s;
if ($page>1) $extra = "page-$page";
//if ($page>1) $page = "$s[rewr_delim]page$s[rewr_delim]$page"; else unset($page);
return "$s[site_url]/".$s['ARfold_'.$what.'_cat']."/extra/$name$extra.html";
}

################################################################################

function rewrite_replace_dynamic_urls($x) {
global $s;
foreach ($s[item_types_short] as $k=>$what)
{ $script = $s[item_types_scripts][$what];
  $x = str_replace("$s[site_url]/$script?action=popular",rewrite_special_category_url($what,'popular','','',1),$x);
  $x = str_replace("$s[site_url]/$script?action=new",rewrite_special_category_url($what,'new','','',1),$x);
  $x = str_replace("$s[site_url]/$script?action=pick",rewrite_special_category_url($what,'pick','','',1),$x);
  $x = str_replace("$s[site_url]/$script?action=top_rated",rewrite_special_category_url($what,'top_rated','','',1),$x);
}
return $x;
}

################################################################################

function rewrite_item_url($what,$n,$rewrite_url,$page,$category) {
global $s;
if (!$rewrite_url)
{ $table = $s[item_types_tables][$what];
  $q = dq("select rewrite_url from $table where n = '$n'",1);
  $x = mysql_fetch_assoc($q); $rewrite_url = $x[rewrite_url];
}
if ($page>1) $more = "-page-$page";
return "$s[site_url]/".$s['ARfold_'.$what.'_detail']."-$n$more/$rewrite_url.$s[AR_detail_extension]";
}

################################################################################
################################################################################
################################################################################

?>