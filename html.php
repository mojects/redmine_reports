<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
	<meta name="author" content="Admin" />
	<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8.custom.min.js"></script>
	<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.custom.css" rel="stylesheet" />	
	<title>Redmine Отчёт</title>
</head>

<body>

	<script type="text/javascript">
	$(function() {
		$("#datepicker").datepicker({altField: '#dt1', altFormat: 'yy-mm-dd'});
		<? if (!empty($_GET['dt1'])) {?>
			$("#datepicker").datepicker("setDate" , "<?=date ("m/d/Y", strtotime ($_GET['dt1']));?>");
		<? } else { ?>
			$("#datepicker").datepicker("setDate" , "-1m");
		<? } ?>
		
		
		$("#datepicker2").datepicker({altField: '#dt2', altFormat: 'yy-mm-dd'});
		<? if (!empty($_GET['dt2'])) {?>
			$("#datepicker2").datepicker("setDate" , "<?=date ("m/d/Y", strtotime ($_GET['dt2']));?>");
		<? } ?>
	});
	</script>

<?php
if (!empty($_GET['dt1']))
	$d = strtotime ($_GET['dt1']);
else
	$d = mktime();

$prev_start = date('Y-m-d', mktime(0,0,0, date("m", $d)-1, 1, date("Y", $d)));
$prev_end = date('Y-m-d', mktime(0,0,0, date("m", $d), 0, date("Y", $d)));

$curr_start = date('Y-m-d', mktime(0,0,0, date("m"), 1, date("Y")));
$curr_end = date('Y-m-d', mktime(0,0,0, date("m")+1, 0, date("Y")));

$next_start = date('Y-m-d', mktime(0,0,0, date("m", $d)+1, 1, date("Y", $d)));
$next_end = date('Y-m-d', mktime(0,0,0, date("m", $d)+2, 0, date("Y", $d)));
?>
<a href="?dt1=<?=$prev_start?>&dt2=<?=$prev_end?>">Prev month</a>
<a href="?dt1=<?=$curr_start?>&dt2=<?=$curr_end?>">This month</a>
<a href="?dt1=<?=$next_start?>&dt2=<?=$next_end?>">Next month</a>
| <a href="<?=$_SERVER['REQUEST_URI']?>&action=json">JSON</a>
| <a href="<?=$_SERVER['REQUEST_URI']?>&action=elastic">Post to elasticsearch</a>

<form action="?query" method="get" enctype="text/plain">

<input type="hidden" name="dt1" id="dt1" />
<input type="hidden" name="dt2" id="dt2" />

<div class="demo" style="width: 350px; float: left;">

Date from: <div id="datepicker"></div>

</div>

<div class="demo" style="width: 350px; float: left;">

Date till: <div id="datepicker2"></div>

</div>


<div class="demo" style="clear: left;">
</div>

<br /><br />

<input type="submit" />

</form>


<p>Total Records: <?=$total?> </p>


<?php

$last_login = "";
$total_time = 0;

while ($row = mysql_fetch_assoc($result)) {
	if ($last_login != $row['user']) {
		echo ":" . floor($total_time) . " ";
		$last_login = $row['user'];
		echo '<a href="#'.$row['user'].'">'.$row['user']."</a>";
		$total_time = 0;
	}
	$total_time += $row['hours'];
}
echo ":" . floor($total_time) . " ";
?>

<table border="1">
<thead>
	<th width="50">Work</th>
	<th width="250">Comment</th>
	<th width="50">Hours</th>	
        <th width="50">Date</th>
</thead>

<?php
$last_login = "";
$total_time = 0;

// Move to begining again:
mysql_data_seek($result, 0);
	
while ($row = mysql_fetch_assoc($result)) {
	// ECHO Previous user SUM 
	
	if ($last_login != "") echo_sum($row['user']);
	
	// ECHO USER
	
	if ($last_login != $row['user']) {
		$last_login = $row['user'];
		
	    printf (
			'<TR>
				<td style="font-weight: bold; font-size: 18px;" colspan="4"><a id="%s">%s</a></td>
			</TR>'
			, $last_login, $last_login);
		
	}
	
	
	// ECHO TIME ROW
	
    printf (
	"<TR>
		<TD>%s #%s: %s</TD>
		<TD>%s</TD>
		<TD>%s</TD>
		<TD>%s</TD>
	</TR>",
	$row['name'], $row['id'], htmlspecialchars($row['subject']),
	htmlspecialchars($row['comments']), str_replace('.', DECIMAL_DELIMITER, $row['hours']), $row['spent_on']);
	
	$total_time += $row['hours'];
	
}

echo_sum("");


function echo_sum($login) {
	global $total_time, $last_login;
	
	if ($last_login != $login) {
		
	    printf (
			'<TR>
				<td style="font-weight: bold; font-size: 18px;" colspan="2"> </td>
				<td style="font-weight: bold; font-size: 18px;"><strong>%s</strong</td>
				<td style="font-weight: bold; font-size: 18px;" colspan="1"> </td>
			</TR>'
			, $total_time);		
		
		$total_time = 0;
	}
	
}


mysql_free_result($result);
?>




</table>



<hr/>
<hr/>

<?php print_r($q); ?>

<hr/>

</body>
</html>