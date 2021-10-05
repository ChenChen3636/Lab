<?php
// ini_set('display_errors','1');
// error_reporting(E_ALL);
    $filename = $_POST["filename"];

    //var_dump($filename)
    $command = "./one_pcap -fk " . $filename;
    $out = shell_exec($command);
    //echo $out;
    //echo $command;

    while($out){
        break;
    }

    $chmod_command = "chmod 777 ./conn/".$filename.".pcap";
    shell_exec($chmod_command);
    ob_clean();

    $file = "./conn/".$filename.".pcap";

   if(file_exists($file)){
        header("Content-type:application/octet-stream");
        $file_name = basename($file);
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-Disposition:attachment;filename = ".$file_name);
        header("Accept-ranges:bytes");
        header("Accept-length:".filesize($file));
        // ob_start();
        readfile($file);
        // ob_end_flush();
    }else{
        echo "<script>alert('檔案不存在')</script>";
    }
   //unlink($filename);
   unlink($file);



?>





