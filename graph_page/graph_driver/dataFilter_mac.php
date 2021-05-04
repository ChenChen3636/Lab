<?php
    date_default_timezone_set("Asia/Taipei");        //reset timezone

    $daterange_mac = $_GET["daterange_mac"];

    $daterangeAB = explode(" ~ ", $daterange_mac);


    print("data processing...");



    $tsA = strtotime("$daterangeAB[0]");
    $tsB = strtotime("$daterangeAB[1]");
    


    //print $tsA."->".$tsB;


    //echo '<script>alert("complete!")</script>';
    //echo("<script>console.log('".$result."');</script>");
    //echo "python3 /var/www/html/graph_page/graph_driver/d3js_macAB_v4.py y ".$tsA." ".$tsB;

    exec("python3 /var/www/html/graph_page/graph_driver/d3js_macAB_v4.py y ".$tsA." ".$tsB, $result);

    //echo $result;

    /* 
    //disable cache
    // HTTP/1.1 
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    // HTTP/1.0
    header("Pragma: no-cache");

    header("Location: ../graph_mac.php? lastDate=".$daterange_mac);
    */

    echo "<script>document.location.href='../graph_mac.php?lastDate=$daterangeAB[0] ~ $daterangeAB[1]';</script>"
?>
