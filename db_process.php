<?php
set_time_limit(3000);
    // ini_set('display_errors', '1');
    // error_reporting(E_ALL);

use function PHPSTORM_META\type;

require_once './function_search.php';
require_once __DIR__ . '/vendor/autoload.php';
$type = $_POST["type"];
$limit = $_POST["limit"];
$filter = $_POST["filter"];
$filter = converse_filter_type($filter);
$option = [];
$option["limit"] = intval($limit["end"]);
$option["skip"] = intval($limit["skip"]);

/** ------------------------------------------------------*
 * connection db query
 ** ------------------------------------------------------*/
if ($type == "connection") {
    $filter["Start_Time"]['$gt'] = intval($filter["Start_Time"]['$gt']);
    $filter["Start_Time"]['$lt'] = intval($filter["Start_Time"]['$lt']);
    $collection = (new MongoDB\Client)->cgudb->connection_collection;
    if (!$collection) {
        echo "error";
    }
    $str = "";
    $document = $collection->find($filter, $option);
    $document_count = $collection->count($filter);
    foreach ($document as $index => $row) {


        $SourceIP = long2ip($row['Source_IP']);
        $DestinationIP = long2ip($row['Destination_IP']);
        $packet = $row['A2Bpacket'] + $row['B2Apacket'];
        $id = $row['Foreign_Key'];
        $duration = floor($row["Connection_Duration"] * 1000) / 1000;
        $max_timeinterval = floor($row["Maximum_TimeInterval"] * 1000) / 1000;
        $min_timeinterval = floor($row["Minimum_TimeInterval"] * 1000) / 1000;
        $connection_type = protocol($row["Connection_Type"])["str"];
        $color = showError($row['Error_Code'][0], $row['Error_Code'][1]);

        $str .= '<tr id=' . $id . '>
                        <td>' . ($index + 1 + $limit["skip"]) . '</td>
                        <td>' . $connection_type . '</td>
                        <td>' . date("Y-m-d H:i:s", $row['Start_Time']) . '</td>
                        <td>' . $duration . '</td>
                        <td>' . $SourceIP . '</td>
                        <td>' . $DestinationIP . '</td>
                        <td>' . $row['Source_Port'] . '</td>
                        <td>' . $row['Destination_Port'] . '</td>
                        <td><a class="connectionToPacket" value = "'.$id.'" style="cursor:pointer;color:blue">' . $packet . '</a></td>
                        <td>' . $color . '</td>
                        <td class="col_score"></td>
                        <td class="Maximum_TimeInterval">' .$row["Maximum_TimeInterval"]. '</td>
                        <td class="Minimum_TimeInterval">' .$row["Minimum_TimeInterval"]. '</td>
                        <td class="Average_TimeInterval">' .$row["Average_TimeInterval"]. '</td>
                        <td class="Maximum_A2Bbytes">' .$row["Maximum_A2Bbytes"]. '</td>
                        <td class="Maximum_B2Abytes">' .$row["Maximum_B2Abytes"]. '</td>
                        <td class="Minimum_A2Bbytes">' .$row["Minimum_A2Bbytes"]. '</td>
                        <td class="Minimum_B2Abytes">' .$row["Minimum_B2Abytes"]. '</td>
                        <td class="Maximum_bytes">' .$row["Maximum_bytes"]. '</td>
                        <td class="Minimum_bytes">' .$row["Minimum_bytes"]. '</td>
                        <td class="SYN">' .$row["SYN_count"]. '</td>
                        <td class="FIN">' .$row["FIN_count"]. '</td>
                        <td class="RST">' .$row["RST_count"]. '</td>
                        <td class="PSH">' .$row["PSH_count"]. '</td>
                        <td class="URG">' .$row["URG_count"]. '</td>
                        </tr>';
    }

    $collection_score = (new MongoDB\Client)->cgudb->connection_score_collection;
    $document_score = $collection_score->find();
    $score = [];
    foreach($document_score as $index => $row){
        $key = $row["Foreign_Key"];
        $score[$key] = $row["Score"];
    }

    echo json_encode(array("data" => $str, "count" => $document_count, "Fkey" => $id, "score" => $score));
/** ------------------------------------------------------*
 * packet db query
 ** ------------------------------------------------------*/
} elseif ($type == "packet") {
    $filter["Arrival_Time"]['$gt'] = intval($filter["Arrival_Time"]['$gt']);
    $filter["Arrival_Time"]['$lt'] = intval($filter["Arrival_Time"]['$lt']);
    $str = "";
    $detail_str = "";
    $collection = (new MongoDB\Client)->cgudb->packet_ary_collection;
    $document = $collection->find($filter, $option);
    $document_count = $collection->count($filter);
    foreach ($document as $index => $row) {
        $Pid = $row["_id"];
        $SourceIP = long2ip($row['Third_Layer']['Source_IP']);
        $DestinationIP = long2ip($row['Third_Layer']['Destination_IP']);
        $packet = $row['A2Bpacket'] + $row['B2Apacket'];
        $protocol = protocol($row["Protocol"])["str"];
        $error = [];
        $ms = explode(".", $row['Arrival_Time']);
        $sourceMac = port($row['Second_Layer']['Source_MAC']);
        $destinationMac = port($row['Second_Layer']['Destination_MAC']);
        for ($i = 0; $i < 11; $i++) {
            $error[$i] = $row['Fourth_Layer']['Fourth_Layer_Option']['Err_Code'][$i];
        }
        $show = showPktError($error);
        $num = ($index + 1 + $limit["skip"]);

        $str .= '<tr pid = "' . $Pid . '" num = "'.$num.'">
                        <td>' . $num . '</td>
                        <td>' . date("Y-m-d H:i", $ms[0]) . "." . $ms[1] . '</td>
                        <td>' . $protocol . '</td>
                        <td>' . $SourceIP . '</td>
                        <td>' . $DestinationIP . '</td>
                        <td>' . $sourceMac . '</td>
                        <td>' . $destinationMac . '</td>
                        <td>' . $row['Fourth_Layer']['Source_Port'] . '</td>
                        <td>' . $row['Fourth_Layer']['Destination_Port'] . '</td>
                        <td>' . $show . '</td>
                        </tr>';
    }
    echo json_encode(array("data" => $str, "count" => $document_count));
/** ------------------------------------------------------*
 * packet's detail db query
 ** ------------------------------------------------------*/
} elseif ($type == "PacketToDetail") {
    $id = $_POST["pid"];
    $num = $_POST["num"];
    $str = "";
    $collection = (new MongoDB\Client)->cgudb->packet_ary_collection;
    $document = $collection->find(['_id' => new MongoDB\BSON\ObjectID($id)]);
    foreach ($document as $index => $row) {
        $SourceIP = long2ip($row['Third_Layer']['Source_IP']);
        $DestinationIP = long2ip($row['Third_Layer']['Destination_IP']);
        $packet = $row['A2Bpacket'] + $row['B2Apacket'];
        $relative = floor($row["Relative_Time"] * 1000) / 1000;
        $sourceMac = port($row['Second_Layer']['Source_MAC']);
        $destinationMac = port($row['Second_Layer']['Destination_MAC']);
        $type = protocol($row["Protocol"])["str"];
        $test = $row['_id'];
        $ms = explode(".", $row['Arrival_Time']);
        for ($i = 0; $i < 11; $i++) {
            $error[$i] = $row['Fourth_Layer']['Fourth_Layer_Option']['Err_Code'][$i];
        }
        $show = showPktError($error);

        $str .=
            '<tr>
                <th scope="col">No.</th>
                <th scope="col">Length(Byte)</th>
                <th scope="col">Arrival Time</th>
                <th scope="col">Relative Time</th>
                <th scope="col">Protocol</th>
                <th scope="col">Source MAC</th>
                <th scope="col">Destination MAC</th>
                <th scope="col">Source IP</th>
                <th scope="col">Destination IP</th>
                <th scope="col">Error</th>
            </tr>
            <tr>
            <td>' . $num .'</td>
            <td>' . $row['Len'] . '</td>
            <td>' . date("Y-m-d H:i", $ms[0]) . "." . $ms[1] . '</td>
            <td>' . $relative . '</td>
            <td>' . $type . '</td>
            <td>' . $sourceMac . '</td>
            <td>' . $destinationMac . '</td>
            <td>' . $SourceIP . '</td>
            <td>' . $DestinationIP . '</td>
            <td>' . $show.'</td>
            </tr>';
    }
    echo json_encode($str);
}
