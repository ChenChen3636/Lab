<?php
function converse_filter_type($filter)
{
    $intList = ["Source_Port", "Source_IP", "Destination_IP", "Source_Port", "Destination_Port", "Third_Layer-Source_IP", "Third_Layer-Destination_IP", "Fourth_Layer-Source_Port", "Fourth_Layer-Destination_Port"];
    $strList = ["Connection_Type", "Protocol"];
    foreach ($intList as $index => $key) {
        if (array_key_exists($key, $filter)) {
            if ($key == "Source_IP" || $key == "Destination_IP") {
                $filter[$key] = ip2long($filter[$key]);
            } else if ($key == "Third_Layer-Source_IP" || $key == "Third_Layer-Destination_IP") {
                $newkey = str_replace("-", ".", $key);
                $filter[$newkey] = ip2long($filter[$key]);
                unset($filter[$key]);
             } else if ($key == "Fourth_Layer-Source_Port" || $key == "Fourth_Layer-Destination_Port") {
                $newkey = str_replace("-", ".", $key);
                $filter[$newkey] = intval($filter[$key]);
                unset($filter[$key]);
            }else{
                $filter[$key] = intval($filter[$key]);
            }
        }
    }
    foreach ($strList as $index => $key) {
        if (array_key_exists($key, $filter)) {
            if ($key == "Connection_Type" || "Protocol") {
                $filter[$key] = protocol($filter[$key])["int"];
            }
        }
    }
    return $filter;
}
function IP_convert($ip){
    if(is_numeric($ip)){
        $n_ip = long2ip($ip);
    }elseif(is_string($ip)){
        $n_ip = v6_convert($ip);
    }
    return $n_ip;
}
function v6_convert($v6){
    $v6_split = [];
    $n_v6 = "";
    $v6_split = str_split($v6,4);
    for($i=0;$i<count($v6_split);$i++){
        if($v6_split[$i] == "0000"){
            $v6_split[$i] = "0";
        }else{
            if(substr($v6_split[$i],0,3) === "000"){
                $n = str_split($v6_split[$i],1);
                $v6_split[$i] = $n[3];
            }elseif(substr($v6_split[$i],0,2) === "00"){
                $n = str_split($v6_split[$i],1);
                $v6_split[$i] = $n[2].$n[3];
            }elseif(substr($v6_split[$i],0,1) === "0"){
                $n = str_split($v6_split[$i],1);
                $v6_split[$i] = $n[1].$n[2].$n[3];
            }
        }
        if($i == 0){
            $n_v6 .= $v6_split[0];
        }else{
                $n_v6 .= ":";
                $n_v6 .= $v6_split[$i];
        }
    }
    return $n_v6;
}
function option()
{
    $intList = ["limit", "skip"];
    $result = [];
    foreach ($intList as $index => $key) {
        if (array_key_exists($key, $_POST)) {
            $result[$key] = intval($_POST[$key]);
        }
    }
}
function protocol($str)
{

    switch ($str) {
        case "ARP":
        case 2054:
            $type = array("int" => 2054, "str" => "ARP");
            break;
        case "TCP":
        case 6:
            $type = array("int" => 6, "str" => "TCP");
            break;
        case "UDP":
        case 17:
            $type = array("int" => 17, "str" => "UDP");
            break;
        case "ICMP":
        case 1:
            $type = array("int" => 1, "str" => "ICMP");
            break;
        case "HTTP":
        case 80:
            $type = array("int" => 80, "str" => "HTTP");
            break;
    }

    return $type;
}

function port($num){
    $str = "";
    for($i=0;$i<12;$i++){
        $even = $i%2;
        if($even == 1 && $i < 10){
            $str .= $num[$i].":";
        }
        else{
            $str .= $num[$i];
        }
    }
    return $str;
}

function showError($e, $e2)
{
    $btn_color = "";
    $title = "";
    $name = "";
    if (isset($e)) {
        if ($e != 0 && $e2 == 4096) {
            $name = "3 way";
            $btn_color = "btn-danger";
            $title = "3 way";
            $err_msg = "lost " . $e . " packets.";
        } elseif ($e == 0 && $e2 != 4096) {
            $name = "4 way";
            $btn_color = "btn-warning";
            $title = "4 way";
            switch ($e2) {
                case 4097:
                    $err_msg = "lost ack2";
                    break;
                case 4099:
                    $err_msg = "lost fin2/ack2";
                    break;
                case 4103:
                    $err_msg = "lost ack1/fin2/ack2";
                    break;
                case 4111:
                    $err_msg = "lost fin1/ack1/fin2/ack2";
                    break;
            }
        } elseif ($e != 0 && $e2 != 4096) {
            $name = "3 way/4 way";
            $btn_color = "btn-danger";
            $title = "3 way/4 way";
            switch ($e2) {
                case 4097:
                    $err_msg = "3way : lost " . $e . " packets.<br>
                            4way : lost ack2";
                    break;
                case 4099:
                    $err_msg = "3way : lost " . $e . " packets.<br>
                            4way : lost fin2/ack2";
                    break;
                case 4103:
                    $err_msg = "3way : lost " . $e . " packets.<br>
                            4way : lost ack1/fin2/ack2";
                    break;
                case 4111:
                    $err_msg = "3way : lost " . $e . " packets.<br>
                            4way : lost fin1/ack1/fin2/ack2";
                    break;
            }
        }
        $a = '<button type="button" class="btn btn-lg ' . $btn_color . ' example-popover inline-flex items-center" data-html="true" data-placement="left" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="top" title="' . $title . '" data-content="' . $err_msg . '" style="height:20px;"></button>';
    } else {
        $a = "";
    }
    return $a;
}
function showPktError($e)
{
    $err_msg = "";
    if ($e[0] == 134217729 || $e[1] == 134221825 || $e[2] == 134225921 || $e[3] == 136314881 || $e[4] == 138412033 || $e[5] == 140509185 || $e[6] == 140513281 || $e[7] == 140517377 || $e[8] == 142606337 || $e[9] == 144703489 || $e[10] == 146800641) {
        $err_msg = "error";
        $a = '<button type="button" class="btn btn-lg btn-danger example-popover inline-flex items-center" data-html="true" data-placement="left" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="top" title="' . $title . '" data-content="' . $err_msg . '" style="height:20px;"></button>';
    } else {
        $a = "";
    }

    return $a;
}
function show_pkt_error($e){
    $err_msg = "";
    foreach($e as $value){
        //var_dump($value);
        switch($value){
            case 134217729:
                $err_msg = "Zero Window";
                break;
            case 134221825:
                $err_msg = "Zero Window Probe";
                break;
            case 134225921:
                $err_msg = "Zero Window Probe Ack";
                break;
            case 136314881:
                $err_msg = "RST";
                break;
            case 138412033:
                $err_msg = "Segment not cap";
                break;
            case 140509185:
                $err_msg = "Normal";
                break;
            case 140513281:
                $err_msg = "Fast";
                break;
            case 140517377:
                $err_msg = "Spurious";
                break;
            case 142606337:
                $err_msg = "Dup";
                break;
            case 142703489:
                $err_msg = "Out Of Order";
                break;
            case 146800641:
                $err_msg = "Acked Unseen";
                break;
            default:
                $err_msg = "";
        }
   }
    if($err_msg == ""){
        $a = "";
    }else{
        $a = '<button type="button" class="btn btn-lg btn-danger example-popover inline-flex items-center" data-html="true" data-placement="left" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="' . $err_msg . '" style="height:20px;"></button>';
    }
    // $a = '<button type="button" class="btn btn-lg btn-danger example-popover inline-flex items-center" data-html="true" data-placement="left" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="' . $err_msg . '" style="height:20px;"></button>';
    return $a;
}
