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

##################################################################################
##################################################################################
##################################################################################

function ad_statistic($in) {
global $s,$m;
$ad = check_ad_owner($in[n]);
$q = dq("select * from $s[pr]ads_stat where n = '$in[n]'",1);
$stat = mysql_fetch_assoc($q); $stat[last_reset] = datum($stat[reset_time],1);
foreach ($stat as $k=>$v) $stat['statistic_'.$k] = $v;
$stat[statistic_r] = number_format(($stat[i_detail]/$stat[i])*100,2);
$stat[statistic_reset_r] = number_format(($stat[reset_i_detail]/$stat[reset_i])*100,2);
if (is_numeric($in[y])) $stat[year] = $in[y]; else $stat[year] = year_number($s[cas]);
if (is_numeric($in[m])) $month = $in[m]; else $month = month_number($s[cas]);
$stat[month_name] = $m['m'.$month];
$monthly_stat = adv_link_get_monthly_stat($in[n],$stat[year],$month);
$ad = array_merge((array)$ad,(array)$stat,(array)$monthly_stat);
page_from_template('ad_statistic.html',$ad);
}

##################################################################################

function adv_link_get_monthly_stat($n,$year,$month) {
global $s;
$q = dq("select * from $s[pr]ads_stat_days where n = '$n' and y = '$year' and m = '$month'",1);
while ($data = mysql_fetch_assoc($q)) $a['day'.$data[d]] = $data;
$dni = date('t',mktime(0,0,0,$month,15,$year));
for ($x=1;$x<=$dni;$x++)
{ $data[day] = $x;
  if ($a["day$x"][i_detail]) $data[i_detail] = $a["day$x"][i_detail]; else $data[i_detail] = 0;
  $table .= parse_part('ad_statistic_day.txt',$data);
  $total_i += $data[i]; $total_i_detail += $data[i_detail]; 
}
$total_r = number_format(($total_i_detail/$total_i)*100,2);
if ($month==1) { $prev_m = 12; $prev_y = $year - 1; } else { $prev_m = $month - 1; $prev_y = $year; }
if ($month==12) { $next_m = 1; $next_y = $year + 1; } else { $next_m = $month + 1; $next_y = $year; }
$previous_month = "$s[site_url]/user.php?action=ad_statistic&n=$n&m=$prev_m&y=$prev_y";
$next_month = "$s[site_url]/user.php?action=ad_statistic&n=$n&m=$next_m&y=$next_y";
return array('statistic_days'=>$table,'days_i'=>$total_i,'days_i_detail'=>$total_i_detail,'days_r'=>$total_r,'previous_month'=>$previous_month,'next_month'=>$next_month);
}

##################################################################################

function ad_reset_statistic($n) {
global $s,$m;
$ad = check_ad_owner($n);
dq("update $s[pr]ads_stat set reset_i = '0', reset_i_detail = '0', reset_time = '$s[cas]' where n = '$n'",1);
$data[n] = $n;
ad_statistic($data);
}

##################################################################################
##################################################################################
##################################################################################

?>