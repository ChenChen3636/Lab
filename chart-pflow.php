<?php
// ini_set('display_errors', '1');
// error_reporting(E_ALL);

use function PHPSTORM_META\type;

require_once __DIR__ . '/vendor/autoload.php';
require_once './function_search.php';

$filter = $_POST["filter"];
//var_dump("123+", $filter);
$filter["Arrival_Time"]['$gt'] = intval($filter["Arrival_Time"]['$gt']);
$filter["Arrival_Time"]['$lt'] = intval($filter["Arrival_Time"]['$lt']);

$filter = converse_filter_type($filter);
//var_dump("abc+", $filter);


$collection = (new MongoDB\Client)->cgudb->packet_ary_collection;
if (!$collection) {
    echo "error";
}
$result = [];
$document = $collection->find($filter);

foreach ($document as $index => $row) {
    $start_time = intval($row["Arrival_Time"]);
    $len = intval($row["Len"]);
    $min = intval(date("i", $start_time));
    $min = $min - $min % 5;
    $curr_time = strtotime(date("Y/m/d H:", $start_time) . $min);

    if (array_key_exists($curr_time, $result)) {
        $result[$curr_time] += $len;
    } else {
        $result[$curr_time] = $len;
    }
}
for($i=$curr_time;$i>0;$i-=300){
    if(array_key_exists($i,$result)){
        $result[$i] = ($result[$i]/1024)/300;
        $result[$i] = number_format($result[$i],2);
    }else{
        break;
    }
}

echo json_encode($result);
