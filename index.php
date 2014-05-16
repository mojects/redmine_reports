<?php

define("DECIMAL_DELIMITER", ".");


mysql_connect('sysdb1ha.gtflix', 'redmine', 'eJ36WdPUhn2Ru7tB') or
    die('Nie można się połączyć: ' . mysql_error());
mysql_select_db('redmine');

mysql_query("set charset utf8;");

// ========================================

$q = "
select 
  u.`lastname` user,
    p.name project,
  tr.name,
  i.id,
  i.`subject`,
  t.comments,
  t.hours,
  t.spent_on,
  t.id entry_id
from  issues  i
  JOIN projects p ON (i.`project_id` = p.`id`)
  JOIN time_entries t ON (i.id = t.issue_id)
  JOIN users    u ON (t.`user_id` = u.`id`)
  JOIN trackers tr ON (i.tracker_id = tr.id)
where 1
  
";

if (!empty($_GET['dt1']) && !empty($_GET['dt2'])) {
	$q .= "AND spent_on BETWEEN  '". $_GET['dt1']. "' AND '" .$_GET['dt2'] . "'";
} 

$q .= " ORDER BY u.`login`, spent_on, t.created_on";


$result = q($q);
function q($query) {
	$result = mysql_query($query);

	echo mysql_error();

	return $result;
}


$total = mysql_num_rows($result);

// RENDER:

if ($_GET['action'] == "json" || $_GET['action'] == "elastic")
    include("json.php");
else
    include("html.php");



?>