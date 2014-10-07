<?
mysql_connect('host', 'redmine-user', 'psw') or
    die('mysql Error: ' . mysql_error());
mysql_select_db('redmine');
?>
