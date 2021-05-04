<?php
    function filter(){
        $intList = ["Source_Port","Source_IP","Destination_IP","Source_Port","Destination_Port","Third_Layer-Source_IP","Third_Layer-Destination_IP","Fourth_Layer-Source_Port","Fourth_Layer-Destination_Port"];
        $strList = ["type","Start_Time", "Connection_Type","Protocol"];
        $result = [];
        $new_key = "";

        foreach($intList AS $index=>$key){
            if(array_key_exists($key,$_POST)){
                $new_key = $key;

                if($key == "Source_IP" || $key == "Destination_IP"){
                    $_POST[$new_key] = ip2long($_POST[$key]);
                }elseif($key == "Third_Layer-Source_IP" || $key == "Third_Layer-Destination_IP" ){
                    $new_key = str_replace("-",".",$key);
                    $_POST[$key] = ip2long($_POST[$key]);
                }elseif($key == "Fourth_Layer-Source_Port" || $key == "Fourth_Layer-Destination_Port"){
                    $new_key = str_replace("-",".",$key);
                }
                $result[$new_key] = intval($_POST[$key]);
            }
        }

        foreach($strList AS $index=>$key){
            if(array_key_exists($key,$_POST)){
                $result[$key] = $_POST[$key];

                if($key == "Connection_Type" || "Protocol"){
                    $result[$key] = protocol($result[$key])["int"];
                }
            }
        }

        return $result;
    }
    function option(){
        $intList =["limit","skip"];
        $result=[];
        foreach($intList AS $index=>$key){
            if(array_key_exists($key,$_POST)){
                $result[$key] = intval($_POST[$key]);
            }
        }
    }
    function protocol($str){

        switch($str){
            case "ARP":
            case 2054:
                $type = array("int"=>2054, "str"=>"ARP");
                break;
            case "TCP":
            case 6:
                $type = array("int"=>6, "str"=>"TCP");
                break;
            case "UDP":
            case 17:
                $type = array("int"=>17, "str"=>"UDP");
                break;
            case "ICMP":
            case 1:
                $type = array("int"=>1, "str"=>"ICMP");
                break;
        }

      return $type;
    }

    function showError($e,$e2){
        if($e != 0 && $e2 == 0){
            $a = "style=\"background-color:yellow\"";

        }elseif($e == 0 && $e2 != 0){
            $a = "style=\"background-color:blue\"";

        }elseif($e != 0 && $e2 != 0){
            $a = "style=\"background-color:pink\"";

        }
        else{
            $a = "";
        }
        return $a;
    }
    function showPktError($e){
        if($e != 0 ){
            $a = "style=\"background-color:yellow\"";
        }else{
            $a = "";
        }

        return $a;
    }
?>