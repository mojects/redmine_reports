<?php

define("DECIMAL_DELIMITER", ".");

include("db.php");

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
from  
  time_entries t
  JOIN projects p ON (t.`project_id` = p.`id`)
LEFT JOIN issues i ON (i.id = t.issue_id)
  JOIN users    u ON (t.`user_id` = u.`id`)
LEFT  JOIN trackers tr ON (i.tracker_id = tr.id)
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
