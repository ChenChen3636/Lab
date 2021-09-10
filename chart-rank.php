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
$document = $collection->find($filter);

if($type == "src"){
    foreach ($document as $index => $row) {
        $start_time = intval($row["Start_Time"]);
        $srcIP = long2IP(intval($row["Source_IP"]));
        $min = intval(date("i", $start_time));
        $min = $min - $min % 5;
        $curr_time = strtotime(date("Y/m/d H:", $start_time) . $min);
        $ary_len = count($rank);
        if(!array_key_exists($curr_time,$date)){
            $xAxis[$curr_time] = 1;
        }
        if (array_key_exists($srcIP, $result)) {
            $result[$srcIP]++;
        } else {
            $result[$srcIP] = 1;
        }
    }

    if(array_key_exists("0.0.0.0", $result)){
        unset($result["0.0.0.0"]);
    }
    if(array_key_exists("224.0.0.252", $result)){
        unset($result["224.0.0.252"]);
    }
    if(array_key_exists("224.0.0.251", $result)){
        unset($result["224.0.0.251"]);
    }

    arsort($result);
    for($i=0;$i<5;$i++){
        array_push($host,array_keys($result)[$i]);
        array_push($conn_count,array_values($result)[$i]);
    }

    $each_result = [];
    $each_result[] = [];
    for($i=0;$i<5;$i++){
        $filter["Source_IP"] = IP2long($host[$i]);
        $each_document = $collection->find($filter);
        foreach($each_document as $index => $row){
            $start_time = intval($row["Start_Time"]);
            $min = intval(date("i", $start_time));
            $min = $min - $min % 5;
            $curr_time = strtotime(date("Y/m/d H:", $start_time) . $min);

            if (array_key_exists($curr_time, $each_result[$i])) {
                $each_result[$i][$curr_time]++;
            } else {
                $each_result[$i][$curr_time] = 1;
            }
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
        if(!array_key_exists($curr_time,$date)){
            $xAxis[$curr_time] = 1;
        }
        if(array_key_exists($dstIP, $result)){
            $result[$dstIP]++;
        }else{
            $result[$dstIP] = 1;
        }
    }

    if(array_key_exists("0.0.0.0", $result)){
        unset($result["0.0.0.0"]);
    }
    if(array_key_exists("224.0.0.252", $result)){
        unset($result["224.0.0.252"]);
    }
    if(array_key_exists("224.0.0.251", $result)){
        unset($result["224.0.0.251"]);
    }

    arsort($result);
    for($i=0;$i<5;$i++){
        array_push($host,array_keys($result)[$i]);
        array_push($conn_count,array_values($result)[$i]);
    }

    $each_result = [];
    $each_result[] = [];
    for($i=0;$i<5;$i++){
        $filter["Destination_IP"] = IP2long($host[$i]);
        $each_document = $collection->find($filter);
        foreach($each_document as $index => $row){
            $start_time = intval($row["Start_Time"]);
            $min = intval(date("i", $start_time));
            $min = $min - $min % 5;
            $curr_time = strtotime(date("Y/m/d H:", $start_time) . $min);

            if (array_key_exists($curr_time, $each_result[$i])) {
                $each_result[$i][$curr_time]++;
            } else {
                $each_result[$i][$curr_time] = 1;
            }
        }
    }
}

echo json_encode(array("data"=>$host,"x"=>$xAxis,"count"=>$conn_count,"each_result"=>$each_result));