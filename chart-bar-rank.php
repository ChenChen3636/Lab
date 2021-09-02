<?php
// ini_set('display_errors', '1');
// error_reporting(E_ALL);

use function PHPSTORM_META\type;

require_once __DIR__ . '/vendor/autoload.php';
require_once './function_search.php';

$filter = $_POST["filter"];
$type = $_POST["type"];
//var_dump("123+", $filter);
$filter["Start_Time"]['$gt'] = intval($filter["Start_Time"]['$gt']);
$filter["Start_Time"]['$lt'] = intval($filter["Start_Time"]['$lt']);
$filter = converse_filter_type($filter);
//var_dump("abc+", $filter);

$collection = (new MongoDB\Client)->cgudb->connection_collection;
if (!$collection) {
    echo "error";
}
$result = [];
$host = [];
$xAxis = [];
$conn_count = [];
$five_total = 0;
$document = $collection->find($filter);
$document_count = $collection->count($filter);

if($type == "src"){
    foreach ($document as $index => $row) {
        $start_time = intval($row["Start_Time"]);
        $srcIP = long2IP(intval($row["Source_IP"]));
        $min = intval(date("i", $start_time));
        $min = $min - $min % 5;
        $curr_time = strtotime(date("Y/m/d H:", $start_time) . $min);
        $ary_len = count($rank);
        if (array_key_exists($srcIP, $result)) {
            $result[$srcIP]++;
        } else {
            $result[$srcIP] = 1;
        }
    }
}elseif($type == "dest"){
    foreach ($document as $index => $row) {
        $start_time = intval($row["Start_Time"]);
        $dstIP = long2IP(intval($row["Destination_IP"]));
        $min = intval(date("i", $start_time));
        $min = $min - $min % 5;
        $curr_time = strtotime(date("Y/m/d H:", $start_time) . $min);
        $ary_len = count($rank);
        if(array_key_exists($dstIP, $result)){
            $result[$dstIP]++;
        }else{
            $result[$dstIP] = 1;
        }
    }
}


arsort($result);
for($i=0;$i<5;$i++){
    array_push($host,array_keys($result)[$i]);
    $five_total += array_values($result)[$i];
    array_push($conn_count,array_values($result)[$i]);
}

$other_connections = $document_count - $five_total;

echo json_encode(array("data"=>$host,"count"=>$conn_count,"others"=>$other_connections));