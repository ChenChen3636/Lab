<?php

    $filename = rand().".pcap";
    // $tr_start = intval($_POST["start"]);
    // $tr_end = intval($_POST["end"]);
    $tr_start = 1625241600;
    $tr_end = 1625241800;
    $condition = "";
    $command = "./merge -fn " . $filename . " -tr " . $tr_start . " " . $tr_end;
    $out = shell_exec($command);
    echo $out;
   // echo $command;

    while($out){
        break;
    }

    $chmod_command = "chmod 777 ./ordered_".$filename;
    shell_exec($chmod_command);
    ob_clean();

//     header('Pragma: public');
//     header('Expires: 0');
//     header('Last-Modified: ' . gmdate('D, d M Y H:i:s ') . ' GMT');
//     header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//     header('Cache-Control: private', false);
//     header('Content-Transfer-Encoding: binary');

    $file = "./ordered_".$filename;

    if(file_exists($file)){
        header("Content-type:application/octet-stream");
        $file_name = basename($file);
        header("Content-Disposition:attachment;filename = ".$file_name);
        header("Accept-ranges:bytes");
        header("Accept-length:".filesize($file));
        readfile($file);
        }else{
        echo "<script>alert('檔案不存在')</script>";
        }
    unlink($filename);
    unlink($file);


?>





