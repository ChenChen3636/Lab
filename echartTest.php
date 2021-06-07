<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

use function PHPSTORM_META\type;

require_once __DIR__ . '/vendor/autoload.php';

$start = intval($_POST["start"]);
$end = intval($_POST["end"]);


$collection = (new MongoDB\Client)->cgudb->connection_collection;
if (!$collection) {
    echo "error";
}
$result = [];
$document = $collection->find(array("Start_Time" => array('$gte' => $start, '$lt' => $end)));


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
