<?php
    date_default_timezone_set("Asia/Taipei");        //reset timezone

    $daterange_throughput = $_GET["daterange_throughput"];
;

    $daterangeAB = explode(" ~ ", $daterange_throughput);


    print("data processing...");




    $tsA = strtotime("$daterangeAB[0]");
    $tsB = strtotime("$daterangeAB[1]");
    


    //print $tsA."->".$tsB;


    exec("python3 /var/www/html/graph_page/graph_driver/d3js_tcpThroughput.py y ".$tsA." ".$tsB, $result);

    //disable cache
    // HTTP/1.1  
    //header("Cache-Control: no-store, no-cache, must-revalidate");
    //header("Cache-Control: post-check=0, pre-check=0", false);
    // HTTP/1.0
    //header("Pragma: no-cache");


    echo "<script>document.location.href='../graph_tcpThroughput.php?lastDate=$daterangeAB[0] ~ $daterangeAB[1]';</script>"
?>
