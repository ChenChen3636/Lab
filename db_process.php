<?php
set_time_limit(3000);
// ini_set('display_errors', '1');
// error_reporting(E_ALL);

use function PHPSTORM_META\type;

require_once './function_search.php';
require_once __DIR__ . '/vendor/autoload.php';
$type = $_POST["type"];
$type_d = $_POST["type_d"];
$status = $_POST["status"];
$count = intval($_POST["skip"]);
$select = 0;
$start = $_POST["start"];
$end = $_POST["end"];
//var_dump(filter());



if ($type == "connection") {
    $collection = (new MongoDB\Client)->cgudb->connection_collection;
    if (!$collection) {
        echo "error";
    }

    if ($status == 1 || $count == 0) {
        // echo strtotime("now");
        $str = "";
        $document = $collection->find(filter(), ['limit' => 200, 'skip' => $count]);
        foreach ($document as $index => $row) {

            $SourceIP = long2ip($row['Source_IP']);
            $DestinationIP = long2ip($row['Destination_IP']);
            $packet = $row['A2Bpacket'] + $row['B2Apacket'];
            $type = "TCP";
            $id = $row['Foreign_Key'];
            $duration = floor($row["Connection_Duration"] * 1000) / 1000;
            $max_timeinterval = floor($row["Maximum_TimeInterval"] * 1000) / 1000;
            $min_timeinterval = floor($row["Minimum_TimeInterval"] * 1000) / 1000;
            $type = protocol($row["Connection_Type"])["str"];
            $color = showError($row['Error_Code'][0], $row['Error_Code'][1]);

            $str .= '<tr id=' . $id . '>
                        <td>' . ($index + 1 + $count) . '</td>
                        <td>' . $type . '</td>
                        <td>' . date("Y-m-d H:i:s", $row['Start_Time']) . '</td>
                        <td>' . $duration . '</td>
                        <td>' . $SourceIP . '</td>
                        <td>' . $DestinationIP . '</td>
                        <td>' . $row['Source_Port'] . '</td>
                        <td>' . $row['Destination_Port'] . '</td>
                        <td><a href=\'./ConnToPacket.php?key_conn=' . $id . '\'>' . $packet . '</a></td>
                        <td>' . $color . '</td>
                        <td class=" Maximum_TimeInterval">' . $max_timeinterval . '</td>
                        <td class=" Minimum_TimeInterval">' . $min_timeinterval . '</td>
                        </tr>';
        }
        echo $str;
    }
} elseif ($type == "packet") {
    $str = "";
    $collection = (new MongoDB\Client)->cgudb->packet_ary_collection;


    if ($status == 1 || $count == 0) {
        $document = $collection->find(filter(), ['limit' => 200, 'skip' => $count]);
        //$document = $collection->find(["Third_Layer-Source_IP"=>2021527806],['limit'=>2000]);
        foreach ($document as $index => $row) {
            $Pid = $row["_id"];
            $SourceIP = long2ip($row['Third_Layer']['Source_IP']);
            $DestinationIP = long2ip($row['Third_Layer']['Destination_IP']);
            $packet = $row['A2Bpacket'] + $row['B2Apacket'];
            $type = "TCP";
            $type = protocol($row["Protocol"])["str"];
            $error = [];

            $ms = explode(".", $row['Arrival_Time']);
            $sourceMac = $row['Second_Layer']['Source_MAC'][0] . $row['Second_Layer']['Source_MAC'][1] . ':' . $row['Second_Layer']['Source_MAC'][2] . $row['Second_Layer']['Source_MAC'][3] . ':' . $row['Second_Layer']['Source_MAC'][4] . $row['Second_Layer']['Source_MAC'][5] . ':' . $row['Second_Layer']['Source_MAC'][6] . $row['Second_Layer']['Source_MAC'][7] . ':' . $row['Second_Layer']['Source_MAC'][8] . $row['Second_Layer']['Source_MAC'][9] . ':' . $row['Second_Layer']['Source_MAC'][10] . $row['Second_Layer']['Source_MAC'][11];
            $destinationMac = $row['Second_Layer']['Destination_MAC'][0] . $row['Second_Layer']['Destination_MAC'][1] . ':' . $row['Second_Layer']['Destination_MAC'][2] . $row['Second_Layer']['Destination_MAC'][3] . ':' . $row['Second_Layer']['Destination_MAC'][4] . $row['Second_Layer']['Destination_MAC'][5] . ':' . $row['Second_Layer']['Destination_MAC'][6] . $row['Second_Layer']['Destination_MAC'][7] . ':' . $row['Second_Layer']['Destination_MAC'][8] . $row['Second_Layer']['Destination_MAC'][9] . ':' . $row['Second_Layer']['Destination_MAC'][10] . $row['Second_Layer']['Destination_MAC'][11];

            for ($i = 0; $i < 11; $i++) {
                $error[$i] = $row['Fourth_Layer']['Fourth_Layer_Option']['Err_Code'][$i];
            }
            $show = showPktError($error);

            $str .= '<tr pid = "' . $Pid . '">
                        <td>' . ($index + 1 + $count) . '</td>
                        <td>' . date("Y-m-d H:i", $ms[0]) . "." . $ms[1] . '</td>
                        <td>' . $type . '</td>
                        <td>' . $SourceIP . '</td>
                        <td>' . $DestinationIP . '</td>
                        <td>' . $sourceMac . '</td>
                        <td>' . $destinationMac . '</td>
                        <td>' . $row['Fourth_Layer']['Source_Port'] . '</td>
                        <td>' . $row['Fourth_Layer']['Destination_Port'] . '</td>
                        <td>' . $show . '</td>
                        </tr>';
        }
        echo $str;
    }
} elseif ($type == "ConnToPacket") {
    $id = $_POST["id"];
    $str = "";
    $collection = (new MongoDB\Client)->cgudb->packet_ary_collection;
    $document = $collection->find(['Foreign_Key' => $id]);
    foreach ($document as $index => $row) {
        $Pid = $row["_id"];
        $SourceIP = long2ip($row['Third_Layer']['Source_IP']);
        $DestinationIP = long2ip($row['Third_Layer']['Destination_IP']);
        $packet = $row['A2Bpacket'] + $row['B2Apacket'];
        $type = "TCP";

        $type = protocol($row["Protocol"])["str"];
        $ms = explode(".", $row['Arrival_Time']);

        $sourceMac = $row['Second_Layer']['Source_MAC'][0] . $row['Second_Layer']['Source_MAC'][1] . ':' . $row['Second_Layer']['Source_MAC'][2] . $row['Second_Layer']['Source_MAC'][3] . ':' . $row['Second_Layer']['Source_MAC'][4] . $row['Second_Layer']['Source_MAC'][5] . ':' . $row['Second_Layer']['Source_MAC'][6] . $row['Second_Layer']['Source_MAC'][7] . ':' . $row['Second_Layer']['Source_MAC'][8] . $row['Second_Layer']['Source_MAC'][9] . ':' . $row['Second_Layer']['Source_MAC'][10] . $row['Second_Layer']['Source_MAC'][11];
        $destinationMac = $row['Second_Layer']['Destination_MAC'][0] . $row['Second_Layer']['Destination_MAC'][1] . ':' . $row['Second_Layer']['Destination_MAC'][2] . $row['Second_Layer']['Destination_MAC'][3] . ':' . $row['Second_Layer']['Destination_MAC'][4] . $row['Second_Layer']['Destination_MAC'][5] . ':' . $row['Second_Layer']['Destination_MAC'][6] . $row['Second_Layer']['Destination_MAC'][7] . ':' . $row['Second_Layer']['Destination_MAC'][8] . $row['Second_Layer']['Destination_MAC'][9] . ':' . $row['Second_Layer']['Destination_MAC'][10] . $row['Second_Layer']['Destination_MAC'][11];

        $error = [];
        for ($i = 0; $i < 11; $i++) {
            $error[$i] = $row['Fourth_Layer']['Fourth_Layer_Option']['Err_Code'][$i];
        }
        $show = showPktError($error);

        $str .= '<tr id="toto" pid = "' . $Pid . '">
                    <td>' . ($index + 1) . '</td>
                    <td>' . date("Y-m-d H:i", $ms[0]) . "." . $ms[1] . '</td>
                    <td>' . $type . '</td>
                    <td>' . $SourceIP . '</td>
                    <td>' . $DestinationIP . '</td>
                    <td>' . $sourceMac . '</td>
                    <td>' . $destinationMac . '</td>
                    <td>' . $row['Fourth_Layer']['Source_Port'] . '</td>
                    <td>' . $row['Fourth_Layer']['Destination_Port'] . '</td>
                    <td>' . $show . '</td>
                    </tr>';

        //var_dump($row['Connection_Type'])
    }
    echo $str;
} elseif ($type == "PacketToDetail") {
    $id = $_POST["_id"];
    $str = "";
    $collection = (new MongoDB\Client)->cgudb->packet_ary_collection;
    $document = $collection->find(['_id' => new MongoDB\BSON\ObjectID($id)]);
    foreach ($document as $index => $row) {
        $SourceIP = long2ip($row['Third_Layer']['Source_IP']);
        $DestinationIP = long2ip($row['Third_Layer']['Destination_IP']);
        $packet = $row['A2Bpacket'] + $row['B2Apacket'];
        $relative = floor($row["Relative_Time"] * 1000) / 1000;
        $sourceMac = $row['Second_Layer']['Source_MAC'][0] . $row['Second_Layer']['Source_MAC'][1] . ':' . $row['Second_Layer']['Source_MAC'][2] . $row['Second_Layer']['Source_MAC'][3] . ':' . $row['Second_Layer']['Source_MAC'][4] . $row['Second_Layer']['Source_MAC'][5] . ':' . $row['Second_Layer']['Source_MAC'][6] . $row['Second_Layer']['Source_MAC'][7] . ':' . $row['Second_Layer']['Source_MAC'][8] . $row['Second_Layer']['Source_MAC'][9] . ':' . $row['Second_Layer']['Source_MAC'][10] . $row['Second_Layer']['Source_MAC'][11];
        $destinationMac = $row['Second_Layer']['Destination_MAC'][0] . $row['Second_Layer']['Destination_MAC'][1] . ':' . $row['Second_Layer']['Destination_MAC'][2] . $row['Second_Layer']['Destination_MAC'][3] . ':' . $row['Second_Layer']['Destination_MAC'][4] . $row['Second_Layer']['Destination_MAC'][5] . ':' . $row['Second_Layer']['Destination_MAC'][6] . $row['Second_Layer']['Destination_MAC'][7] . ':' . $row['Second_Layer']['Destination_MAC'][8] . $row['Second_Layer']['Destination_MAC'][9] . ':' . $row['Second_Layer']['Destination_MAC'][10] . $row['Second_Layer']['Destination_MAC'][11];

        $type = protocol($row["Protocol"])["str"];

        $test = $row['_id'];
        $ms = explode(".", $row['Arrival_Time']);

        $str .=
            '<tr>
                <th scope="col">Length</th>
                <th scope="col">Arrival Time</th>
                <th scope="col">Relative Time</th>
                <th scope="col">Protocol</th>
                <th scope="col">Source MAC</th>
                <th scope="col">Destination MAC</th>
                <th scope="col">Source IP</th>
                <th scope="col">Destination IP</th>
            </tr>
            <tr>
            <td>' . $row['Len'] . '</td>
            <td>' . date("Y-m-d H:i", $ms[0]) . "." . $ms[1] . '</td>
            <td>' . $relative . '</td>
            <td>' . $type . '</td>
            <td>' . $sourceMac . '</td>
            <td>' . $destinationMac . '</td>
            <td>' . $SourceIP . '</td>
            <td>' . $DestinationIP . '</td>
            </tr>';
    }
    echo $str;
}
