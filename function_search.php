<?php
function filter()
{
    $intList = ["Source_Port", "Source_IP", "Destination_IP", "Source_Port", "Destination_Port", "Third_Layer-Source_IP", "Third_Layer-Destination_IP", "Fourth_Layer-Source_Port", "Fourth_Layer-Destination_Port"];
    $strList = ["Connection_Type", "Protocol"];
    $timeList = ["\$gt", "\$lt"];
    $result = [];
    $timeRange = [];
    $new_key = "";

    foreach ($intList as $index => $key) {
        if (array_key_exists($key, $_POST)) {
            $new_key = $key;

            if ($key == "Source_IP" || $key == "Destination_IP") {
                $_POST[$new_key] = ip2long($_POST[$key]);
            } elseif ($key == "Third_Layer-Source_IP" || $key == "Third_Layer-Destination_IP") {
                $new_key = str_replace("-", ".", $key);
                $_POST[$key] = ip2long($_POST[$key]);
            } elseif ($key == "Fourth_Layer-Source_Port" || $key == "Fourth_Layer-Destination_Port") {
                $new_key = str_replace("-", ".", $key);
            }
            $result[$new_key] = intval($_POST[$key]);
        }
    }

    foreach ($strList as $index => $key) {
        if (array_key_exists($key, $_POST)) {
            $result[$key] = $_POST[$key];

            if ($key == "Connection_Type" || "Protocol") {
                $result[$key] = protocol($result[$key])["int"];
            }
        }
    }

    foreach ($timeList as $index => $key) {
        if (array_key_exists($key, $_POST)) {
            $timeRange[$key] = intval($_POST[$key]);
        }
        if ($_POST["type"] == "connection") {
            $result["Start_Time"] = $timeRange;
        } elseif ($_POST["type"] == "packet") {
            $result["Arrival_Time"] = $timeRange;
        }
    }

    return $result;
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
    }

    return $type;
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
