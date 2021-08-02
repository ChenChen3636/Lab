<?php
// echo "\n\nsystem\n";
// system('ls -al', $out);
// var_dump($out);

// echo "\n\nexec";
// exec('ls', $output, $return_var);
// echo "\nreturn_var:";
// print_r($return_var);
// echo "\noutput:";
// print_r($output);

// echo "\n\nshell_exec";
// $output = shell_exec('ls');
// echo "\noutput:";
// print_r($output);


// $filename = '/var/www/html/index.html';        //獲取檔名
// header('content-disposition:attachment;filename=' . $filename);    //告訴瀏覽器通過何種方式處理檔案
// header('content-length:' . filesize($filename));    //下載檔案的大小
// readfile($filename);	 //讀取檔案
$filename = "july.pcap";
$start = 1625245200;
$end = 1625245600;
$condition = "";
$command = "cd /PCAP_DB/DB-Running;./merge -fn " . $filename . " -tr " . $start . " " . $end . " " . $command;
exec($command, $output);
print_r($output);
