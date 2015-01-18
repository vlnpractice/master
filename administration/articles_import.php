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

include('./articles_functions.php');

switch ($_POST[action]) {
case 'articles_import_count'	: import_count('a','all_articles','articles_import.php','articles_imported',$_POST);
case 'articles_imported'		: articles_imported($_POST);
case 'articles_import_special'	: articles_import_special($_POST);
}
articles_import_form();

#############################################################################
#############################################################################
#############################################################################

function articles_import_form() {
global $s;
check_admin('all_articles');
$q = dq("select * from $s[pr]usit_list where use_for = 'a' order by rank",1);
while ($x = mysql_fetch_assoc($q)) $all_user_items_list[$x[item_n]] = $x[description ];
$pocet = 18 + mysql_num_rows($q);
ih();
/*
$s[a_max_cats] = 1;
echo '<form action="articles_import.php" method="post" name="form1">'.check_field_create('admin').'
<input type="hidden" name="action" value="articles_import_special">
<table border="0" width="900" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="4" class="common_table_top_cell">Automatic Import</td></tr>
<tr><td align="center" width="100%">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center" colspan="4">Enter path of the folder where are files with the articles to import. The first line in each file should be the title, other lines will be used as an article text.<br />
</td></tr>
<tr><td align="left" colspan="4" nowrap>File path <input style="width:650px;" name="datafile" class="field10" value="'.$s[phppath].'/"></td></tr>
<tr><td align="left" colspan="4" nowrap>'.str_replace('Categories','Import articles to category',categories_rows_form('a',$link)).'</td></tr>
<tr><td align="center" colspan="4" nowrap><input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</table></td></tr></table>
</form>
<br />';
*/
echo '<form ENCTYPE="multipart/form-data" action="articles_import.php" method="post" name="form1">'.check_field_create('admin').'
<input type="hidden" name="action" value="articles_import_count">
<table border="0" width="900" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="4" class="common_table_top_cell">Import Articles</td></tr>
<tr><td align="center" width="100%">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center" colspan="4">Structure of your file<br />
<span class="text10">Make sure to read Instructions below<br /></span>
</td></tr>';
for ($x=1;$x<=$pocet;$x++)
{ if ($x%2) echo '<tr>';
  ?>
  <td align="left" nowrap>Rank #<?PHP echo $x ?></td>
  <td align="left"><select class="select10" name="rank[<?PHP echo $x ?>]">
  <option value="0">None</option>
  <option value="title">Title</option>
  <option value="description">Subtitle</option>
  <option value="text">Full text</option>
  <option value="keywords">Keywords</option>
  <option value="categories">Numbers of categories</option>
  <option value="name">Owner's name</option>
  <option value="email">Owner's email</option>
  <option value="password">Password</option>
  <option value="created">Date created</option>
  <option value="picture">Picture URL</option>
  <option value="t1">Valid from</option>
  <option value="t2">Valid to</option>
  <option value="hits">Number of reads</option>
  <?PHP
  foreach ($all_user_items_list as $k=>$v) echo '<option value="user_item_'.$k.'">'.$v.'</option>';
  echo '</select></td>';
  if (!$x%2) echo '</tr>';
}
?>
<tr>
<td align="left" colspan="4" nowrap>Separator (it separates individual values on the same line) 
<select class="select10" name="separator">
<option value="|">|</option><option value=",">,</option><option value=";">;</option>
</select><br />
<span class="text10">The character which will be used as separator should not be used in any value </span>
</td></tr>
<tr><td align="left" colspan="4" nowrap>File <input style="width:650px;" type="file" name="datafile" class="field10"></td></tr>
<tr><td align="center" colspan="4" nowrap><input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</table></td></tr></table>
</form>
<br />

<table border="0" width="900" cellspacing="0" cellpadding="0" class="common_table">
<tr><td class="common_table_top_cell">Info & Instructions</td></tr>
<tr><td align="center" width="100%">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="left"><span class="text10">
All the data of each article must be placed on the same line.<br />
One blank line is required at the end of the file.<br />
Date format must be: mm/dd/yy.<br />
Required items: Title, at least one Category.<br />
Numbers of categories have to be separated by a space.<br />
User defined items - select boxes and radio must have one of the available values<br />
User defined items - select box can have only value 1 or 0<br />
If you'll not set a Date, current date will be used.<br />
If you'll not set a Password, a random passwords will be generated<br />
If you'll not set an Email, there will be used the email you entered in Configuration<br />
<br />
Create the text file very carefully.<br />The script grabs the articles and places them to the database without any kind of control.<br />
<br /></span>
<span class="text10a_bold">Example</span><br />
<span class="text10">If you have a file with the following structure:<br />
<span class="text10">
Title|Subtitle|Full&nbsp;text|Keywords|Category&nbsp;number|Owner's&nbsp;name|Owner's&nbsp;email|Date|Password<br />
Title|Subtitle|Full&nbsp;text|Keywords|Category&nbsp;number|Owner's&nbsp;name|Owner's&nbsp;email|Date|Password<br />
Title|Subtitle|Full&nbsp;text|Keywords|Category&nbsp;number|Owner's&nbsp;name|Owner's&nbsp;email|Date|Password<br />
Title|Subtitle|Full&nbsp;text|Keywords|Category&nbsp;number|Owner's&nbsp;name|Owner's&nbsp;email|Date|Password<br />
Title|Subtitle|Full&nbsp;text|Keywords|Category&nbsp;number|Owner's&nbsp;name|Owner's&nbsp;email|Date|Password<br />
.....<br /></span>
Select these values:<br />
Rank&nbsp;#1&nbsp;Title&nbsp;&nbsp; 
Rank&nbsp;#2&nbsp;Subtitle&nbsp;&nbsp; 
Rank&nbsp;#3&nbsp;Full&nbsp;text&nbsp;&nbsp; 
Rank&nbsp;#4&nbsp;Keywords&nbsp;&nbsp; 
Rank&nbsp;#5&nbsp;Category&nbsp;number&nbsp;&nbsp; 
Rank&nbsp;#6&nbsp;Owner's&nbsp;name&nbsp;&nbsp; 
Rank&nbsp;#7&nbsp;Owner's&nbsp;email&nbsp;&nbsp; 
Rank&nbsp;#8&nbsp;Date&nbsp;&nbsp; 
Rank&nbsp;#9&nbsp;Password
<br />
Separator <b>|</b><br />
<br /><br />
<?PHP
echo '<b>Problems?</b><br>If you cannot save your current data to the required format, we are able to do it for you. Please <a href="mailto:info@phpwebscripts.com">email us</a> for more info.';
echo '</td></tr></table></td></tr></table>';
ift();
}

#############################################################################

function articles_imported($data) {
global $s;
//foreach ($data[rank] as $k=>$v) echo "$k - $v<br />";//exit;
check_admin('all_articles');
$all_user_items_list = get_all_user_items_list('a');
$all_user_items_values = get_all_user_items_values('a');
ih();
echo '<br />';
$time0 = time(); $pocet = 0;

$delimiter = $data[separator]; if ($data[step]) $limit = "limit $data[step]";
$q = dq("select * from $s[pr]import_temp where what = 'a' order by n $limit",1);
while ($a = mysql_fetch_assoc($q))
{ $pocet ++; $to_delete_last = $a[n];
  $line = htmlspecialchars_decode($a[line],ENT_QUOTES);
  $pole = preg_split( "/[$delimiter]*\\\"([^\\\"]+)\\\"[$delimiter]*|[$delimiter]+/",trim(stripslashes($line)), 0, PREG_SPLIT_DELIM_CAPTURE );
//foreach ($pole as $k=>$v) echo "$k - $v<br>";continue;

  set_time_limit(600);
  if (time()>$time0+30) { header('X-pmaPing: Pong'); $time0 = time(); }
//  if (time()>($time1+10)) { $time1=time(); echo ' Working ... '.str_repeat (' ',4000); flush(); }
  if ((!trim($pole[0])) AND (!trim($pole[1])) AND (!trim($pole[2]))) continue;
  $pole = replace_array_text($pole);
  foreach ($data[rank] as $k=>$v) { $x = $k - 1; $$v = $pole[$x]; }  
  
  unset ($user_items); $arr = get_defined_vars();
  foreach ($arr as $k=>$v) 
  { if (strstr($k,'user_item_'))
    { $x = str_replace('user_item_','',$k); $user_items[$x] = $v; }
  }
  foreach ($all_user_items_list as $k=>$v)
  { if (isset($user_items[$v[item_n]]))
    { //echo $user_items[$v[item_n]];
	  if (($v[kind]=='text') OR ($v[kind]=='textarea') OR ($v[kind]=='htmlarea')) { $value_texts[$v[item_n]] = $user_items[$v[item_n]]; $value_codes[$v[item_n]] = ''; }
      elseif ($v[kind]=='checkbox') { $value_texts[$v[item_n]] = ''; $value_codes[$v[item_n]] = $user_items[$v[item_n]]; }
      elseif (($v[kind]=='select') OR ($v[kind]=='radio'))
      { $value_texts[$v[item_n]] = $user_items[$v[item_n]];
	    foreach ($all_user_items_values[$v[item_n]] as $k1=>$v1)
	    if ($v1==$user_items[$v[item_n]]) { $value_codes[$v[item_n]] = $k1; break; }
      }
    }
  }
  if (!$created) $cas = $s[cas]; else $cas = make_time($created);
  if ($t1) $t1 = make_time($t1); else $t1 = 0; if ($t2) $t2 = make_time($t2); else $t2 = 0;
  if (!$password) $password = substr(md5(substr($url,1).substr($title,2).substr($description,3)),15);
  //$x = parse_url($url); $url = "http://$x[host]/";
  if (!$email) $email = $s[mail];
  $c = categories_edited(explode(' ',$categories));
  $en_cats = has_some_enabled_categories('a',explode(' ',$categories));
  $text = refund_html($text);
  $rewrite_url = discover_rewrite_url($title,0,'a');
  $keywords = prepare_keywords($keywords);
  //echo "insert into $s[pr]articles values (NULL,'$title','$description','$text','$keywords','','$c[categories]','$c[categories_path]','0','$name','$email','$cas','0','$password','$hits','0','0','0','0','0','0','$t1','$t2','enabled','$en_cats','$rewrite_url')<br>";
  dq("insert into $s[pr]articles values (NULL,'$title','$description','$text','$keywords','','$map','$c[categories]','$c[categories_path]','0','$name','$email','$cas','0','$password','$hits','0','0','0','0','0','0','$t1','$t2','enabled','$en_cats','$rewrite_url')",1);
  $n = mysql_insert_id();
  add_update_user_items('a',$n,$all_user_items_list,$value_codes,$value_texts);
  $picture = trim($picture);
  if (trim($picture))
  { dq("insert into $s[pr]files values(NULL,'a','$n','0','1','$picture','$description','image','$uploaded[extension]','0')",1);
    dq("update $s[pr]articles set picture = '$picture' where n = '$n'",1);
  }
  update_item_index('a',$n);
  if (($data[step]) AND ($pocet>=$data[step])) break;
}
dq("delete from $s[pr]import_temp where what = 'a' and n <= '$to_delete_last'",1);
$q = dq("select * from $s[pr]import_temp where what = 'a'",1); $rest = mysql_num_rows($q);

$to = $data[from] + $data[step] - 1;
if ($rest)
{ echo 'Articles '.$data[from].' to '.$to.' created<br />
  <form method="post" action="articles_import.php">'.check_field_create('admin').'
  <input type="hidden" name="action" value="articles_imported">
  <input type="hidden" name="total_items" value="'.$data[total_items].'">
  <input type="hidden" name="separator" value="'.$data[separator].'">
  <input type="hidden" name="from" value="'.($to+1).'">
  <input type="hidden" name="step" value="'.$data[step].'">';
  foreach ($data[rank] as $k=>$v) echo '<input type="hidden" name="rank['.$k.']" value="'.$v.'">';
  echo '<td align="center"><input type="submit" name="submit" value="Import next 100 articles" class="button10"></td></form>';
}
else echo info_line('All articles created.','Now run function <a href="rebuild.php?action=reset_rebuild_home">Reset/rebuild</a> => Recount all articles and articles.');
exit;
}

#############################################################################

function articles_import_special($in) {
global $s;
//foreach ($in[article][0][categories] as $k=>$v) echo "$k - $v<br />";
$c = categories_edited($in[article][0][categories]);
check_admin('all_articles');
$all_user_items_list = get_all_user_items_list('a');
$all_user_items_values = get_all_user_items_values('a');
ih();
echo '<br />';
$time0 = time(); $pocet = 0;

$dr = opendir($in[datafile]);
rewinddir($dr);
while ($q = readdir($dr))
{ if (($q != '.') AND ($q != '..') AND (substr($q,0,1)!='.') AND (is_file("$in[datafile]/$q")))
  $files_array[] = $q;
}
closedir ($dr);

foreach ($files_array as $k => $file_name)
{ set_time_limit(600);
  $file = file("$in[datafile]/$file_name");
  $title = trim($file[0]);
  unset($file[0]);
  $text = trim(implode('',$file));
  if ((!$title) OR (!$text)) continue;

  foreach ($all_user_items_list as $k=>$v)
  { if (isset($user_items[$v[item_n]]))
    { //echo $user_items[$v[item_n]];
	  if (($v[kind]=='text') OR ($v[kind]=='textarea') OR ($v[kind]=='htmlarea')) { $value_texts[$v[item_n]] = $user_items[$v[item_n]]; $value_codes[$v[item_n]] = ''; }
      elseif ($v[kind]=='checkbox') { $value_texts[$v[item_n]] = ''; $value_codes[$v[item_n]] = $user_items[$v[item_n]]; }
      elseif (($v[kind]=='select') OR ($v[kind]=='radio'))
      { $value_texts[$v[item_n]] = $user_items[$v[item_n]];
	    foreach ($all_user_items_values[$v[item_n]] as $k1=>$v1)
	    if ($v1==$user_items[$v[item_n]]) { $value_codes[$v[item_n]] = $k1; break; }
      }
    }
  }
  $cas = $s[cas];
  $t1 = 0; $t2 = 0;
  $password = get_random_password();
  $email = $s[mail];
  $en_cats = 1;
  $text = refund_html(nl2br($text));
  $rewrite_url = discover_rewrite_url($title,0,'a');
  //echo "insert into $s[pr]articles values (NULL,'$title','$description','$text','$keywords','','$c[categories]','$c[categories_path]','0','$name','$email','$cas','0','$password','$hits','0','0','0','0','0','0','$t1','$t2','enabled','$en_cats','$rewrite_url')<br>";
  dq("insert into $s[pr]articles values (NULL,'$title','$description','$text','$keywords','','$map','$c[categories]','$c[categories_path]','0','$name','$email','$cas','0','$password','$hits','0','0','0','0','0','0','$t1','$t2','enabled','$en_cats','$rewrite_url')",1);
  $n = mysql_insert_id();
  add_update_user_items('a',$n,$all_user_items_list,$value_codes,$value_texts);
  update_item_index('a',$n);
  $pocet++;
}
echo info_line("$pocet articles created.",'Now run function <a href="rebuild.php?action=reset_rebuild_home">Reset/rebuild</a> => Recount all articles and articles.');
exit;
}

##################################################################################
##################################################################################
##################################################################################

?>