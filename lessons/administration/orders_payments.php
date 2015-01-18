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
check_admin('users');

switch ($_GET[action]) {
case 'orders_search'			: orders_search();
case 'order_mark_paid'			: order_mark_paid($_GET);
case 'payment_delete'			: payment_delete($_GET);
case 'orders_searched'			: orders_searched($_GET);
case 'queue'					: queue();
}
switch ($_POST[action]) {
}

#################################################################################
#################################################################################
#################################################################################

function queue() {
global $s;
$q = dq("select count(*) from $s[pr]ads_orders where paid = '0'",1);
$pocet = mysql_fetch_row($q); $pocet = $pocet[0];
if (!$pocet) echo info_line('No one order in the queue');
else
{ echo '<table border=0 width="500" cellspacing=0 cellpadding=2 class="common_table">
  <tr><td class="common_table_top_cell">Orders in the Queue: '.$pocet.'</td></tr>
  <tr><td align="center">Select number of orders to display per page<br>
  <form method="get" action="orders_payments.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="orders_searched">
  <input type="hidden" name="paid" value="no">
  <input type="hidden" name="edit_forms" value="1">
  <select name="perpage" class="field10"><option value="0">All</option>';
  if ($pocet>5) echo '<option value="5">5</option>';
  if ($pocet>10) echo '<option value="10">10</option>';
  if ($pocet>20) echo '<option value="20">20</option>';
  if ($pocet>30) echo '<option value="30">30</option>';
  echo '</select> 
  <input type="submit" value="Submit" name="B1" class="button10">
  </form></td></tr></table>';
}
echo '<br>';
}

############################################################################
############################################################################
############################################################################

function orders_search() {
global $s;
ih();
echo page_title('Orders & Payments');
queue();
echo '<form method="GET" action="orders_payments.php">'.check_field_create('admin').'
<input type="hidden" name="action" value="orders_searched">
<table border="0" width="500" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Search for Orders</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr>
<td align="left" nowrap>Order number </td>
<td align="left"><input class="field10" name="n" size=15 maxlength=10></td>
</tr>
<tr>
<td align="left" nowrap>Ad number </td>
<td align="left"><input class="field10" name="ad" size=15 maxlength=10></td>
</tr>
<tr>
<td align="left" nowrap>Owner\'s email </td>
<td align="left"><input class="field10" name="email" size=15 maxlength=15></td>
</tr>
<tr>
<td align="left" nowrap>Paid </td>
<td align="left"><select class="field10" name="paid"><option value="0">N/A</option><option value="yes">Paid only</option><option value="no">Unpaid only</option></select></td>
</tr>
<tr>
<td align="left" nowrap>Results per page </td>
<td align="left"><select class="field10" name="perpage"><option value="0">All</option><option value="10">10</option><option value="20">20</option><option value="50">50</option><option value="100">100</option><option value="200">200</option><option value="500">500</option></select></td>
</tr>
<tr>
<td align="left" nowrap>Sort by </td><td align="left"><select class="field10" name="sort"><option value="order_time">Date created</option><option value="user">Owner\'s username</option></select>
<select class="field10" name="order"><option value="asc">Ascending</option><option value="desc">Descending</option></select>
</td>
</tr>
<tr><td colspan=2 align="center"><input type="submit" value="Search" name="B1" class="button10"></td></tr>
</table></td></tr></table></form><br>';
exit;
}

############################################################################

function orders_searched($in) {
global $s;
$s[returnto] = urlencode("orders_payments.php?$_SERVER[QUERY_STRING]");
if ($in[n]) $where = "$s[pr]ads_orders.n = '$in[n]'";
else
{ if ($in[ad]) $w[] = "$s[pr]ads_orders.ad = '$in[ad]'";
  if ($in[user]) $w[] = "$s[pr]ads_orders.user = '$in[user]'";
  elseif ($in[email])
  { $q = dq("select n from $s[pr]users where email = '$in[email]'",1);
    $x = mysql_fetch_row($q); $w[] = "$s[pr]ads_orders.user = '$x[0]'";
  }
  if ($in[paid]=='yes') $w[] = "$s[pr]ads_orders.paid = '1'"; elseif ($in[paid]=='no') $w[] = "$s[pr]ads_orders.paid = '0'";
  if ($w) $where = join (' AND ',$w);
}
if ($where) $where = " where $where ";

if (!$in[from]) $in[from] = 0; else $in[from] = $in[from] - 1;
if ($in[perpage]) $limit = " limit $in[from],$in[perpage]";

if ($in[sort]) $orderby = "order by $in[sort] $in[order]";
$x = dq("select count(*) from $s[pr]ads_orders $where",1);
$pocet = mysql_fetch_row($x); $pocet = $pocet[0];

if ($where) $where .= "and $s[pr]ads_orders.user = $s[pr]users.n";
else $where = "where $s[pr]ads_orders.user = $s[pr]users.n";
$q = dq("select $s[pr]ads_orders.*,$s[pr]users.email from $s[pr]ads_orders,$s[pr]users $where $orderby  $limit",1); 

ih();
if ( ($in[perpage]) AND ($pocet>$in[perpage]) )
{ $rozcesti = "
  <form action=\"orders_payments.php\" method=\"GET\" name=\"form1\">".check_field_create('admin')."
  <input type=\"hidden\" name=\"action\" value=\"orders_searched\">";
  foreach ($in as $k => $v)
  { if ($v) $rozcesti .= "<input type=\"hidden\" name=\"$k\" value=\"$v\">\n"; }
  $rozcesti .= "Show records with begin of <select class=\"field10\" name=\"from\"><option value=\"1\">1</option>";
  $y = ceil($pocet/$in[perpage]);  
  for ($x=1;$x<$y;$x++)
  { $od = $x*$in[perpage]+1; $rozcesti .= "<option value=\"$od\">$od</option>"; }
  $rozcesti .= "</select>&nbsp;&nbsp;<input type=\"submit\" value=\"&nbsp;Submit&nbsp;\" name=\"B1\" class=\"button10\">
  </form>";
}
else $rozcesti = '<br>';

$od = $in[from]+1;
$do = $in[from]+$in[perpage]; if ($do>$pocet) $do = $pocet; if (!$in[perpage]) $do = $pocet;

echo $s[info].'<span class="text13a_bold">Orders Found: '.$pocet;
if ( ($pocet>1) AND ($od!=$do) ) echo ", Showing Orders $od - $do</span>\n";
echo "$rozcesti<br>";
$in[from] = $in[from] + 1;
while ($order=mysql_fetch_assoc($q))
show_one_payment($order);
ift();
}

############################################################################
############################################################################
############################################################################

function order_mark_paid($data) {
global $s;
order_update_payment_info($data[n],1,'N/A','Marked as paid by admin','',1);
ih();
echo info_line('Selected order has been marked as paid.');
if ($data[returnto]) echo '<a href="'.$data[returnto].'">Back to previous page</a>';
ift();
}

############################################################################

function payment_delete($data) {
global $s;
dq("delete from $s[pr]ads_orders where n = '$data[n]'",1);
dq("delete from $s[pr]ads_orders_parts where n = '$data[n]'",1);
ih();
echo info_line('Selected order has been deleted');
echo '<a href="javascript: history.go(-1)">Back to previous page</a>';
ift();
}

############################################################################

function show_one_payment($order) {
global $s,$m;
$q = dq("select * from $s[pr]ads_orders_parts where n = '$order[n]'",1);
$order_details = mysql_fetch_assoc($q);
include_once("$s[phppath]/styles/_common/messages/common.php");
foreach ($s[extra_options] as $k=>$v) if ($order_details[$v]) $extra[] = $m['xtra_'.$v];
if ($order_details[pictures]) $extra[] = "Extra pictures: $order_details[pictures]";
if ($order_details[files]) $extra[] = "Extra files: $order_details[files]";
if ($order[paid]) $paid = '<font color="#00BD30">Yes</font>'; 
else
{ $paid = '<font color="red">No</font>';
  $mark_paid = '[<a href="orders_payments.php?action=order_mark_paid&n='.$order[n].'&returnto='.$s[returnto].'">Mark it as paid</a>]';
}
$order = stripslashes_array($order);
echo '<table border="0" width="500" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Order #'.$order[n].'</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="left" colspan=2>Paid: '.$paid.', User: <a href="users.php?action=users_searched&n='.$order[user].'">'.$order[email].'</a></td></tr>
<tr>
<td align="left" width=150>Classified number </td>
<td align="left"><a href="ads_list.php?action=ads_searched&n='.$order_details[ad].'">'.$order_details[ad].'</a></td>
</tr>
<tr>
<td align="left" width=150>Amount </td>
<td align="left">'.$s[currency].$order[price].'</a></td>
</tr>
<tr>
<td align="left" width=150>Included options</td>
<td align="left">'.implode(', ',$extra).'</td>
</tr>
<tr>
<td align="left" width=150>Days </td>
<td align="left">'.$order_details[days].'</td>
</tr>
<tr>
<td align="left" width=150>Order time</td>
<td align="left">'.datum($order[order_time],1).'</td>
</tr>';
if ($order[info]) echo '<tr>
<td align="left" width=150>Details </td>
<td align="left">'.$order[info].'</td>
</tr>';
if ($order[notes]) echo '<tr>
<td align="left" width=150>Raw data </td>
<td align="left">'.$order[notes].'</td>
</tr>';
echo '<tr><td align="left" colspan=2>'.$mark_paid.'
[<a target="_self" href="orders_payments.php?action=payment_delete&n='.$order[n].'&returnto='.$s[returnto].'">Delete this order</a>]</td>
</tr></table>
</td></tr></table>
<br>';
}

############################################################################
############################################################################
############################################################################

?>