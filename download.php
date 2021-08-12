<?php
// ini_set('display_errors','1');
// error_reporting(E_ALL);
    $filename = rand().".pcap";
    $tr_start = intval($_POST["start"]);
    $tr_end = intval($_POST["end"]);
    $protocol = $_POST["Protocol"];
    $srcIP = intval($_POST["srcIP"]);
    $dstIP = intval($_POST["dstIP"]);
    $srcPort = intval($_POST["srcPort"]);
    //var_dump($_POST);
    // $tr_start = 1625241600;
    // $tr_end = 1625241800;
    $condition = "";
    $command = "./merge -fn " . $filename . " -tr " . $tr_start . " " . $tr_end;
    $out = shell_exec($command);
   // echo $out;
   //echo $command;

    while($out){
        break;
    }

    $chmod_command = "chmod 777 ./ordered_".$filename;
    shell_exec($chmod_command);
    ob_clean();

    $file = "./ordered_".$filename;

   if(file_exists($file)){
        header("Content-type:application/octet-stream");
        $file_name = basename($file);
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-Disposition:attachment;filename = ".$file_name);
        header("Accept-ranges:bytes");
        header("Accept-length:".filesize($file));
//        // ob_start();
        readfile($file);
//        // ob_end_flush();
    }else{
        echo "<script>alert('檔案不存在')</script>";
    }
    unlink($filename);
    unlink($file);



?>





