<?php
    // ini_set('display_errors','1');
    // error_reporting(E_ALL);
    use function PHPSTORM_META\type;
    require_once __DIR__ . '/vendor/autoload.php';

    $start = intval($_POST["start"]);
    $end = intval($_POST["end"]);


    $collection = (new MongoDB\Client)->cgudb->connection_collection;
        if(!$collection){
            echo "error";
        }
    $str = [];
    $document = $collection->find(array("Start_Time" => array('$gt' => $start,'$lt' => $end)));
    // $document = $collection->find(array("Start_Time" => array('$gt' => 1608480000,'$lt' => 1618075400)),['limit'=>200,'skip'=>0]);

    // $document = $collection->find();
    foreach ($document as $index => $row) {
        $time = $row["Connection_Folder_Path"];
        $str[$time] = (array_key_exists($time, $str))? $str[$time]+1: 1;
    }
   echo json_encode($str);


?>