<?php
require 'vendor/autoload.php';

$params = array();
$params['hosts'] = array (
    '9069069d703882de3b2526c586dcf1e6-us-east-1.foundcluster.com:9200' // Domain + Port
);

$client = new Elasticsearch\Client($params);
$params = array();
$params['index'] = 'timesheet1';
$params['type']  = 'timesheet1';

header('Content-Type:text/plain');

mysql_data_seek($result, 0);

$action["index"] = array(
    "_index" => "timesheet1",
    "_type" => "timesheet1"
    );

$out = array();

while ($row = mysql_fetch_assoc($result)) {

    $action["index"]["_id"] = $row['entry_id'];

    $i = array();
    $i['user'] = $row['user'];
    $i['project'] = $row['project'];
    $i['issue_id'] = $row['id'];
    $i['issue_title'] = $row['subject'];
    $i['comment'] = $row['comments'];
    $i['hours'] = $row['hours'];
    $i['timestamp'] = $row['spent_on'];
            
    $params['body'][] = array(
        'index' => array(
            '_id' => $row['entry_id']
        )
    );
    $params['body'][] = $i;

    $out[] = json_encode($action);
    $out[] = json_encode($i);
    
}

if ($_GET['action'] == "elastic") {

    $result = $client->bulk($params);
    print_r($result);
    
} else {
    echo join("\n", $out);
}


