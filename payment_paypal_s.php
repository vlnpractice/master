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
$s['no_test'] = 1;
get_messages('payment_process.php');
$s[my_paypal_test] = 0;
$s[my_paypal_test_verified] = 0;
if ($s[my_paypal_test]) $s[mail] = 'rizi@3bv.com';
if ($s[my_paypal_test]) mail($s[mail],'Paypal payment 1','Start',"From: $s[mail]");
set_time_limit(60);
if ($s[pp_test]) $s[paypal_domain] = 'www.sandbox.paypal.com'; else $s[paypal_domain] = 'www.paypal.com';
//if (($s[my_paypal_test]) OR ($s[pp_test])) $s[paypal_domain] = 'www.sandbox.paypal.com'; else $s[paypal_domain] = 'www.paypal.com';
foreach ($_POST as $k=>$v) $email_info .= "POST: $k - $v\n";
if ($s[my_paypal_test]) mail($s[mail],'Paypal payment 2',"Paypal domain: $s[paypal_domain]\n\n$email_info","From: $s[mail]");
if ($_POST[txn_type]) paypal_main();
header("Status: 404 Not Found"); exit;

##################################################################################
##################################################################################
##################################################################################

function paypal_main() {
global $s,$m;

header("Status: 200 OK");
$out = 'cmd=_notify-validate';
foreach ($_POST as $k => $v)
{ $v = stripslashes($v);
  if ((!eregi("^[_0-9a-z-]{1,30}$",$k)) OR (!strcasecmp($k,'cmd'))) unset ($k,$v);
  if (trim($k)) { $from_pp[$k] = $v; $out .= '&'.$k.'='.urlencode($v); }
}
unset ($_POST);

if ($s[my_paypal_test])
{ foreach ($from_pp as $k=>$v) $email_info .= "from_pp $k - $v\n";
  foreach ($out as $k=>$v) $email_info .= "out $k - $v\n";
  mail($s[mail],'Paypal payment 3',$email_info,"From: $s[mail]");
}

$socket = curl_init("https://$s[paypal_domain]/cgi-bin/webscr");
curl_setopt($socket, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($socket, CURLOPT_POST, 1);
curl_setopt($socket, CURLOPT_RETURNTRANSFER,1);
curl_setopt($socket, CURLOPT_POSTFIELDS, $out);
curl_setopt($socket, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($socket, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($socket, CURLOPT_FORBID_REUSE, 1);
curl_setopt($socket, CURLOPT_HTTPHEADER, array('Connection: Close'));
if(!($pp_decision = curl_exec($socket)))
{ $curl_error = curl_error($socket);
  if ($s[my_paypal_test]) mail($s[mail],'Paypal payment curl_error',$curl_error,"From: $s[mail]");
  $problem[] = $m[no_connect];
}
curl_close($socket);

$pp_decision = strtolower(trim($pp_decision));
if ($s[my_paypal_test])
{ $email_info = "pp_decision: $pp_decision\n\nSocket: $socket\nout: $out\n";
  mail($s[mail],'Paypal payment 4',$email_info,"From: $s[mail]");
  unset($email_info);
}

$from_pp = replace_array_text($from_pp);
$from_pp[custom] = str_replace('AMP','',str_replace('LUG','',$from_pp[custom]));
if ($s[my_paypal_test_verified]) { $pp_decision = 'verified'; $from_pp[payment_status] = 'Completed'; }
$order_data = get_order_variables($from_pp[custom]);
if ($s[my_paypal_test])
{ foreach ($order_data as $k=>$v) $email_info .= "Order data: $k - $v\n";
  mail($s[mail],'Paypal payment 5',$email_info,"From: $s[mail]");
}
if ($from_pp[amount3]) $from_pp[mc_gross] = $from_pp[mc_amount3];
//if ($from_pp[parent_txn_id]) $parent_txn_id = $from_pp[parent_txn_id];
//elseif ($from_pp[initial_payment_txn_id]) $parent_txn_id = $from_pp[initial_payment_txn_id];
/*if ($order_data[n])
{ $q = dq("select * from $s[pr]users_payments where order_n = '$order_data[n]'",0);
  $orig_users_payments = mysql_fetch_assoc($q);
  $orig_order_vars = get_order_variables($orig_users_payments[order_n]);
}*/
if ($s[my_paypal_test_verified]) $from_pp[payment_status] = 'Completed';


$s[paypal_subscription_id] = $from_pp[subscr_id]; // unique id for the subscription
$s[paypal_transaction_id] = $from_pp[txn_id]; // unique for the transaction (may be used multiple times when payment via check)
$s[paypal_ipn_id] = $from_pp[ipn_track_id]; // unique for ipn call


if ($pp_decision=='invalid') $problem[] = $m[invalid];
elseif ($pp_decision=='verified')
{ if ($from_pp[mc_gross]!=$order_data[price]) $problem[] = "$m[wrong_price_1] $order_data[price]. $m[wrong_price_2] $from_pp[mc_gross]";
  if ($from_pp[mc_currency]!=$s[pp_currency]) $problem[] = "$m[wrong_currency_1] $s[pp_currency]. $m[wrong_currency_2] $from_pp[mc_gross]";
  if ($from_pp[business]==$s[pp_email]) $ok[1] = 1;
  if ($from_pp[receiver_email]==$s[pp_email]) $ok[2] = 1;
  if (strcmp($from_pp[business],$s[pp_email])==0) $ok[3] = 1;
  if (strcmp($from_pp[receiver_email],$s[pp_email])==0) $ok[4] = 1;
  if ( ($from_pp[business]!=$s[pp_email]) AND ($from_pp[receiver_email]!=$s[pp_email]) AND (strcmp($from_pp[business],$s[pp_email])!=0) AND (strcmp($from_pp[receiver_email],$s[pp_email])!=0) ) $problem[] = "$m[wrong_pp_email] - $ok[1] - $ok[2] - $ok[3] - $ok[4] - $from_pp[business] - $from_pp[receiver_email] - $s[pp_email]";
/*
txn_type:
	subscr_signup
	subscr_cancel
	subscr_modify
	subscr_payment
subscr_failed
*/
  if ($from_pp[txn_type]=='subscr_cancel')
  { dq("update $s[pr]users_payments set canceled = '$s[cas]' where paypal_subscription_id  = '$s[paypal_subscription_id]' and canceled = 0",1);
    my_send_mail('','',$s[mail],0,'Paypal subscription canceled',"Order number $order_data[n]\nUser number $order_data[user]\n",0);
    exit;
  }
  elseif ($from_pp[txn_type]=='subscr_modify')
  { my_send_mail('','',$s[mail],0,'Paypal subscription modified',"Order number $order_data[n]\nUser account has NOT been modified. If it should be deleted or edited, you can do it in admin area\n",0);
    exit;
  }
  elseif ($from_pp[txn_type]=='subscr_payment')
  { $success = 1;
    paypal_process_order($order_data,$from_pp,$success,$problem,$pp_decision);
    exit;
  }
  /*
  elseif ($from_pp[txn_type]=='subscr_signup') // only signup, not payment
  { $success = 1;
    paypal_process_order($order_data,$from_pp,$success,$problem,$pp_decision);
    exit;
  }*/
  else exit;
}
else
{ $problem[] = $m[na_error];
  paypal_process_order($order_data,$from_pp,$success,$problem,$pp_decision);
}



exit;
}



##################################################################################

function paypal_process_order($order_data,$from_pp,$success,$problem,$pp_decision) {
global $s,$m;
if ($success)
{ if ($problem) $info = $m[success_errors].'<br />'.implode('<br />',$problem);
  else { $paid = 1; $info = $m[order_success]; }
}
else $info = $m[failed].'<br />'.implode('<br />',$problem);
$notes = "RAW DATA RECEIVED FROM PAYPAL\n"; foreach ($from_pp as $k=>$v) $notes .= "$k: $v\n";
$mysql = order_update_payment_info($order_data[n],$paid,'PayPal',$info,$notes);

if ($paid) $admin_info = 'Order WAS MARKED AS PAID.'; else $admin_info = 'Order WAS NOT MARKED AS PAID.';
if ($from_pp[payment_status]=='Pending') $pending_reason = " ($from_pp[pending_reason])";

$email_admin = "A new subscription payment has been sent by Paypal. You can see its details below.
$admin_info
Result: ".ucfirst($pp_decision)."
Currency and amount: $s[currency]$order_data[price]
Payment status: $from_pp[payment_status]$pending_reason
Order number: $from_pp[custom]\n\n";
$email_admin .= "USER RECEIVED THE FOLLOWING MESSAGE:\n".str_replace('<br />',"\n",$info)."\n\n";
$email_admin .= $notes."\n\n";

if ($s[my_paypal_test]) mail($s[mail],'Paypal payment 7',"$email_admin\n\n$mysql\n\n","From: $s[mail]");
my_send_mail('','',$s[mail],0,'Paypal payment subscription',$email_admin,0);
if ($s[money_mail]) { my_send_mail('','',$s[money_mail],0,'Paypal payment',$email_admin,1); }
}

##################################################################################
##################################################################################
##################################################################################

?>