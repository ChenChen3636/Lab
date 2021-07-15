<?php
// ini_set('display_errors', '1');
// error_reporting(E_ALL);

use function PHPSTORM_META\type;

require_once __DIR__ . '/vendor/autoload.php';
require_once './function_search.php';

$filter = $_POST["filter"];
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
$document = $collection->find($filter);

foreach ($document as $index => $row) {
    $start_time = intval($row["Start_Time"]);
    $min = intval(date("i", $start_time));
    $min = $min - $min % 5;
    $curr_time = strtotime(date("Y/m/d H:", $start_time) . $min);

    if (array_key_exists($curr_time, $result)) {
        $result[$curr_time]++;
    } else {
        $result[$curr_time] = 1;
    }
}
echo json_encode($result);
