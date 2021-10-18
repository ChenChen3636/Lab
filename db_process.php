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
$collection_name = $_POST["collection"];
if($collection_name = "collection"){
    $connection_collection = "connection_collection";
    $packet_collection = "packet_ary_collection";
    $score_collection = "connection_score_collection";
}else{
    $connection_collection = "con_".$collection_name;
    $packet_collection = "pkt_".$collection_name;
    $score_collection = "score_".$colllection_name;
}

/** ------------------------------------------------------*
 * connection db query
 ** ------------------------------------------------------*/
if ($type == "connection") {
    $filter["Start_Time"]['$gt'] = intval($filter["Start_Time"]['$gt']);
    $filter["Start_Time"]['$lt'] = intval($filter["Start_Time"]['$lt']);
    $collection = (new MongoDB\Client)->cgudb->$connection_collection;
    if (!$collection) {
        echo "error";
    }
    $str = "";
    $document = $collection->find($filter, $option);
    $document_count = $collection->count($filter);
    foreach ($document as $index => $row) {

        $SourceIP = IP_convert($row['Source_IP']);
        $DestinationIP = IP_convert($row['Destination_IP']);
        $packet = $row['A2Bpacket'] + $row['B2Apacket'];
        $id = $row['Foreign_Key'];
        $duration = floor($row["Connection_Duration"] * 1000) / 1000;
        $max_timeinterval = floor($row["Maximum_TimeInterval"] * 1000) / 1000;
        $min_timeinterval = floor($row["Minimum_TimeInterval"] * 1000) / 1000;
        $connection_type = protocol($row["Connection_Type"])["str"];
        $color = showError($row['Error_Code'][0], $row['Error_Code'][1]);

        $str .= '<tr id=' . $id . '>
                        <td ><img src="./icon/download.png" value="'.$id.'" class="download-icon" style="width:20px;hight:20px"></td>
                        <td>' . ($index + 1 + $limit["skip"]) . '</td>
                        <td>' . $color . '</td>
                        <td class="col_score"></td>
                        <td>' . $connection_type . '</td>
                        <td>' . date("Y-m-d H:i:s", $row['Start_Time']) . '</td>
                        <td>' . $duration . '</td>
                        <td>' . $SourceIP . '</td>
                        <td>' . $DestinationIP . '</td>
                        <td>' . $row['Source_Port'] . '</td>
                        <td>' . $row['Destination_Port'] . '</td>
                        <td><a class="connectionToPacket" value = "'.$id.'" style="cursor:pointer;color:blue">' . $packet . '</a></td>
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

    $collection_score = (new MongoDB\Client)->cgudb->$score_collection;
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
    $collection = (new MongoDB\Client)->cgudb->$packet_collection;
    $document = $collection->find($filter, $option);
    $document_count = $collection->count($filter);
    foreach ($document as $index => $row) {
        $Pid = $row["_id"];
        $SourceIP = IP_convert($row['Third_Layer']['Source_IP']);
        $DestinationIP = IP_convert($row['Third_Layer']['Destination_IP']);
        $packet = $row['A2Bpacket'] + $row['B2Apacket'];
        $protocol = protocol($row["Protocol"])["str"];
        $error = [];
        $ms = explode(".", $row['Arrival_Time']);
        $sourceMac = port($row['Second_Layer']['Source_MAC']);
        $destinationMac = port($row['Second_Layer']['Destination_MAC']);
        for ($i = 0; $i < 11; $i++) {
            $error[$i] = $row['Fourth_Layer']['Fourth_Layer_Option']['Err_Code'][$i];
        }
        $show = show_pkt_error($error);
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
    $collection = (new MongoDB\Client)->cgudb->$packet_collection;
    $document = $collection->find(['_id' => new MongoDB\BSON\ObjectID($id)]);
    foreach ($document as $index => $row) {
        $SourceIP = IP_convert($row['Third_Layer']['Source_IP']);
        $DestinationIP = IP_convert($row['Third_Layer']['Destination_IP']);
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
        if($type == "TCP"){
            $version = $row["Third_Layer"]["Third_Layer_Option"]["Version"];
            $nextHeader = $row["Third_Layer"]["Third_Layer_Option"]["Next_Header"];
            $headerLen = $row["Third_Layer"]["Third_Layer_Option"]["Header_Len"];
            $totalLen = $row["Third_Layer"]["Third_Layer_Option"]["Total_Length"];
            $iden = $row["Third_Layer"]["Third_Layer_Option"]["Identification"];
            $timeToLive = $row["Third_Layer"]["Third_Layer_Option"]["Time_To_Live"];
            $sourcePort = $row["Fourth_Layer"]["Source_Port"];
            $destinationPort = $row["Fourth_Layer"]["Destination_Port"];
            $flag = $row["Fourth_Layer"]["Fourth_Layer_Option"]["Flags"];
            $windowSize = $row["Fourth_Layer"]["Fourth_Layer_Option"]["Window_Size"];
            $SequenceNumber = $row["Fourth_Layer"]["Fourth_Layer_Option"]["Sequence_Number"];
            $AcknowledgeNumber = $row["Fourth_Layer"]["Fourth_Layer_Option"]["Acknowledge_Number"];

            $str .=
            '
            <p style="font-weight:bold">No.' . $num .'</p><p></p>
            <p style="font-weight:bold">Length:</p>' . $row['Len'] . '
            <p style="font-weight:bold">Arrival Time: </p>' . date("Y-m-d H:i", $ms[0]) . "." . $ms[1] . '
            <p style="font-weight:bold">Relative Time: </p>' . $relative . '
            <p style="font-weight:bold">Protocol: </p>' . $type . '
            <p style="font-weight:bold;color:blue">[Second Layer]</p><p></p>
            <p style="font-weight:bold">Sourece Mac: </p>' . $sourceMac . '
            <p style="font-weight:bold">Destination Mac: </p>' . $destinationMac . '
            <p style="font-weight:bold;color:blue">[Third Layer]</p><p></p>
            <p style="font-weight:bold">Source IP: </p>' . $SourceIP . '
            <p style="font-weight:bold">Destination IP: </p>' . $DestinationIP . '
            <p style="font-weight:bold">Version: </p>' . $version . '
            <p style="font-weight:bold">Next Header: </p>' . $nextHeader . '
            <p style="font-weight:bold">Header Length: </p>' . $headerLen . '
            <p style="font-weight:bold">Total Length: </p>' . $totalLen . '
            <p style="font-weight:bold">Identificaion: </p>' . $iden . '
            <p style="font-weight:bold">TTL: </p>' . $timeToLive. '
            <p style="font-weight:bold;color:blue">[Four Layer]</p><p></p>
            <p style="font-weight:bold">Source Port: </p>' . $sourcePort. '
            <p style="font-weight:bold">Destination Port: </p>' . $destinationPort. '
            <p style="font-weight:bold">Flag: </p>' . $flag. '
            <p style="font-weight:bold">Window Size: </p>' . $windowSize. '
            <p style="font-weight:bold">Sequence Number: </p>' . $SequenceNumber. '
            <p style="font-weight:bold">Acknowledge Number: </p>' . $AcknowledgeNumber. '
            <p style="font-weight:bold">Error: </p>' . $show;
        }elseif($type == "UDP"){
            $version = $row["Third_Layer"]["Third_Layer_Option"]["Version"];
            $nextHeader = $row["Third_Layer"]["Third_Layer_Option"]["Next_Header"];
            $headerLen = $row["Third_Layer"]["Third_Layer_Option"]["Header_Len"];
            $totalLen = $row["Third_Layer"]["Third_Layer_Option"]["Total_Length"];
            $iden = $row["Third_Layer"]["Third_Layer_Option"]["Identification"];
            $timeToLive = $row["Third_Layer"]["Third_Layer_Option"]["Time_To_Live"];
            $sourcePort = $row["Fourth_Layer"]["Source_Port"];
            $destinationPort = $row["Fourth_Layer"]["Destination_Port"];

            $str .=
            '
            <p style="font-weight:bold">No.' . $num .'</p><p></p>
            <p style="font-weight:bold">Length:</p>' . $row['Len'] . '
            <p style="font-weight:bold">Arrival Time: </p>' . date("Y-m-d H:i", $ms[0]) . "." . $ms[1] . '
            <p style="font-weight:bold">Relative Time: </p>' . $relative . '
            <p style="font-weight:bold">Protocol: </p>' . $type . '
            <p style="font-weight:bold;color:blue">[Second Layer]</p><p></p>
            <p style="font-weight:bold">Sourece Mac: </p>' . $sourceMac . '
            <p style="font-weight:bold">Destination Mac: </p>' . $destinationMac . '
            <p style="font-weight:bold;color:blue">[Third Layer]</p><p></p>
            <p style="font-weight:bold">Source IP: </p>' . $SourceIP . '
            <p style="font-weight:bold">Destination IP: </p>' . $DestinationIP . '
            <p style="font-weight:bold">Version: </p>' . $version . '
            <p style="font-weight:bold">Next Header: </p>' . $nextHeader . '
            <p style="font-weight:bold">Header Length: </p>' . $headerLen . '
            <p style="font-weight:bold">Total Length: </p>' . $totalLen . '
            <p style="font-weight:bold">Identificaion: </p>' . $iden . '
            <p style="font-weight:bold">TTL: </p>' . $timeToLive. '
            <p style="font-weight:bold;color:blue">[Four Layer]</p><p></p>
            <p style="font-weight:bold">Source Port: </p>' . $sourcePort. '
            <p style="font-weight:bold">Destination Port: </p>' . $destinationPort.'
            <p style="font-weight:bold">Error: </p>' . $show;

        }else{
            $str .=
            '
            <p style="font-weight:bold">No.' . $num .'</p><p></p>
            <p style="font-weight:bold">Length:</p>' . $row['Len'] . '
            <p style="font-weight:bold">Arrival Time: </p>' . date("Y-m-d H:i", $ms[0]) . "." . $ms[1] . '
            <p style="font-weight:bold">Relative Time: </p>' . $relative . '
            <p style="font-weight:bold">Protocol: </p>' . $type . '
            <p style="font-weight:bold;color:blue">[Second Layer]</p><p></p>
            <p style="font-weight:bold">Sourece Mac: </p>' . $sourceMac . '
            <p style="font-weight:bold">Destination Mac: </p>' . $destinationMac . '
            <p style="font-weight:bold;color:blue">[Third Layer]</p><p></p>
            <p style="font-weight:bold">Source IP: </p>' . $SourceIP . '
            <p style="font-weight:bold">Destination IP: </p>' . $DestinationIP . '
            <p style="font-weight:bold;color:blue">[Four Layer]</p><p></p>
            <p style="font-weight:bold">Error: </p>' . $show;
        }
    }
    echo json_encode($str);
}
