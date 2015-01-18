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
include($s[phppath].'/administration/rebuild_functions.php');
check_admin('reset_rebuild');
ih();

switch ($_GET[action]) {
case 'reset_rebuild_home'		: reset_rebuild_home();
case 'do_daily_job'				: daily_job(0); break;
case 'do_rebuild_index'			: rebuild_index_categories(0); rebuild_index_categories_groups(0); break;
case 'do_count_stats'			: count_stats(0); break;
case 'do_delete_old'			: delete_old(0); break;
case 'do_delete_statistic_days'	: delete_statistic_days($_GET); break;
case 'do_update_popular'		: update_popular(0); break;
case 'do_create_in_files'		: create_in_files(0,0); break;
case 'do_recount_all_items'		: $s[info] = recount_all_items(1); break;
case 'do_repair_path_ads'		: $s[info] = repair_path_ads(1); break;
case 'do_delete_expired_items'	: delete_expired_items(); break;
case 'reset_all_question'		: reset_all_question();
case 'create_google_sitemap'	: create_google_sitemap(1); break;
case 'create_yahoo_sitemap'		: create_yahoo_sitemap(1); break;
case 'create_index'				: create_index(1); break;
}
switch ($_POST[action]) {
case 'reset_rebuild'			: reset_rebuild($_POST[what]);
case 'reset_all'				: reset_all();
}
reset_rebuild_home();

##################################################################################
##################################################################################
##################################################################################

function reset_rebuild_home() {
global $s;
//$s[d
load_times();
if ($s[info]) echo '<span class="text10a_bold">'.$s[info].'</span><br>';
?>
<form method="get" action="rebuild.php" name="form1"><?PHP echo check_field_create('admin') ?>
<table border="0" width="95%" cellspacing="0" cellpadding="0" class="common_table">
<tr><td colspan="2" class="common_table_top_cell">Reset & Rebuild</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center" nowrap colspan=2><?PHP echo 'Current time '.datum(0,1) ?><br><br></td></tr>
<tr>
<td align="center" valign="top"><input type="radio" name="action" value="do_daily_job" checked></td>
<td align="left">Daily job (this job last done: <?PHP echo datum($s[times_d],1); ?>)<br><span class="text10">It recounts statistics, rebuilds categories on the home page and classifieds in the left column, updates popular classifieds, deletes unconfirmed user accounts older than 24 hours, deletes IP records that are used to count statistics. It also optionally removes classifieds that are no more valid (can be enabled/disabled in configuration).<br>You should configure the script to run this job automatically once a day, however if you need to do it manually, use this function.</span></td>
</tr>
<tr>
<td align="center"><input type="radio" name="action" value="do_rebuild_index"></td>
<td align="left" nowrap>Rebuild home page - index.php <span class="text10">(Part on the daily job)</span></td>
</tr>
<tr>
<td align="center"><input type="radio" name="action" value="do_count_stats"></td>
<td align="left" nowrap>Recount statistic </span><span class="text10">(Part on the daily job)</td>
</tr>
<tr>
<td align="center" valign="top"><input type="radio" name="action" value="do_delete_old"></td>
<td align="left">Delete old data<br><span class="text10">It deletes unconfirmed user accounts older than 24 hours, deletes IP records that are used to count statistics. It also optionally removes classifieds that are no more valid (can be enabled/disabled in configuration).<br>(Part on the daily job)</span></td>
</tr>
<tr>
<td align="center"><input type="radio" name="action" value="do_update_popular"></td>
<td align="left" nowrap>Update popular classified ads <span class="text10">(Part on the daily job)</span></td>
</tr>
<tr>
<td align="center"><input type="radio" name="action" value="do_create_in_files"></td>
<td align="left" nowrap>Update inluded files <span class="text10">(Part on the daily job)</span></td>
</tr>
<tr>
<td align="center" valign="top"><input type="radio" name="action" value="do_delete_expired_items"></td>
<td align="left" nowrap>Delete expired classified ads <span class="text10"><br>Removes all items which are no more valid (have been valid in the past)</span></td>
</tr>
<tr>
<td align="center" valign="top"><input type="radio" name="action" value="create_google_sitemap"></td>
<td align="left" nowrap>Create sitemap for Google<br /></td>
</tr>
<tr>
<td align="center" valign="top"><input type="radio" name="action" value="create_yahoo_sitemap"></td>
<td align="left" nowrap>Create URL list (sitemap) for Yahoo<br /></td>
</tr>
<tr>
<td align="center" valign="top"><input type="radio" name="action" value="create_index"></td>
<td align="left" nowrap>Repair index for searching<br /></td>
</tr>
<tr>
<td align="center" valign="top"><input type="radio" name="action" value="reset_all_question"></td>
<td align="left" nowrap>Reset overall statistic<br><span class="text10">It resets overall statistic (numbers of hits in/out) to zero.</span></td>
</tr>
<tr>
<td align="left" valign="top"><input type="radio" name="action" value="do_delete_statistic_days"></td>
<td align="left"> Delete daily statistics for 
<select name="delete_days_month" class="field10"><option value="1">January</option><option value="2">February</option><option value="3">March</option><option value="4">April</option><option value="5">May</option><option value="6">June</option><option value="7">July</option><option value="8">August</option><option value="9">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option></select> 
<input name="delete_days_year" size="4" maxlength="4" class="field10" value="2007"> and before</td>
</tr>
<tr>
<td align="center" valign="top"><input type="radio" name="action" value="do_repair_path_ads"></td>
<td align="left">Repair paths of classified ads
</td>
<tr>
<td align="center" valign="top"><input type="radio" name="action" value="do_recount_all_items"></td>
<td align="left">Recount classified ads in individual categories <select class="field10" name="do_recount_all_items_category"><option value="0">All categories</option><?PHP echo  select_list_first_categories($_GET[do_recount_all_items_category]) ?></select><span class="text10"><br>It may take up to several minutes to recount all items.</span>
</td>
</tr>
<tr><td align="center" colspan=2><input type="submit" name="submit" value="Submit" class="button10"></td></tr>
</table></td></tr></table></form><br>


<table width="95%" border="0" cellspacing="0" cellpadding="0" class="common_table">
<tr><td class="common_table_top_cell" colspan="2">Crontab Commands</td></tr>
<tr><td align="center">
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="inside_table">
<tr><td align="center">You can use one of these commands to create a crontab command to run the daily job. Note that not each of these commands can be used on each server. If you are not sure which of them can work for you, ask your server admin for help or email us.<br>Please set it to run once a day at 12.01am (0.01). Once you configured the correct crontab command, uncheck the field which runs the Daily job automatically. This field is available in Configuration.</td></tr>
<?PHP
echo '<tr><td align="center">/usr/bin/wget --spider -q \''.$s[site_url].'/rebuild.php?word='.$s[secretword].'\'</td></tr>';
echo '<tr><td align="center">/usr/local/bin/wget --spider -q \''.$s[site_url].'/rebuild.php?word='.$s[secretword].'\'</td></tr>';
echo '<tr><td align="center">/usr/bin/lynx -dump \''.$s[site_url].'/rebuild.php?word='.$s[secretword].'\' >/dev/null</td></tr>';
echo '<tr><td align="center">/usr/local/bin/lynx -dump \''.$s[site_url].'/rebuild.php?word='.$s[secretword].'\' >/dev/null</td></tr>';
echo '<tr><td align="center">GET \''.$s[site_url].'/rebuild.php?word='.$s[secretword].'\'</td></tr>';
echo '<tr><td align="center">curl --silent \''.$s[site_url].'/rebuild.php?word='.$s[secretword].'\'</td></tr>';
echo '<tr><td align="center">/usr/bin/php -dump \''.$s[site_url].'/rebuild.php?word='.$s[secretword].'\' >/dev/null</td></tr>';
echo '<tr><td align="center">/usr/local/bin/php -dump \''.$s[site_url].'/rebuild.php?word='.$s[secretword].'\' >/dev/null</td></tr>';
?>
</table>
</td></tr></table>

<?PHP
ift();
}

##################################################################################
##################################################################################
##################################################################################

?>