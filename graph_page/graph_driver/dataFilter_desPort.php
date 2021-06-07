<?php
    date_default_timezone_set("Asia/Taipei");        //reset timezone

    $daterange_desPort = $_GET["daterange_desPort"];

    $Max_num_des = $_GET["Max_num_des"];
    $per_lab_show_des = $_GET["per_lab_show_des"];
    $Max_num_srcip = $_GET["Max_num_srcip"];
    $per_lab_show_srcip = $_GET["per_lab_show_srcip"];

    $srcIpFilter_ip = $_GET["srcIpFilter_desPort"];
    $ipFilter_ck = $_GET["ipCheckbox"];

    $daterangeAB = explode(" ~ ", $daterange_desPort);


    print("data processing...");


    $tsA = strtotime("$daterangeAB[0]");
    $tsB = strtotime("$daterangeAB[1]");



    if($ipFilter_ck == "on"){
        exec("python3 /var/www/html/graph_page/graph_driver/d3js_desPort_v3.py y ".$tsA." ".$tsB." y ".$srcIpFilter_ip." -HL ".$Max_num_des." ".$per_lab_show_des." ".$Max_num_srcip." ".$per_lab_show_srcip, $result);
        //print("<br>python3 /var/www/html/graph_page/graph_driver/d3js_desPort_v3.py y ".$tsA." ".$tsB." y ".$srcIpFilter_ip." -HL ".$Max_num_nodes." ".$per_lab_show);
    }else{
        exec("python3 /var/www/html/graph_page/graph_driver/d3js_desPort_v3.py y ".$tsA." ".$tsB." n"." -HL ".$Max_num_des." ".$per_lab_show_des." ".$Max_num_srcip." ".$per_lab_show_srcip, $result);
        //print("<br>python3 /var/www/html/graph_page/graph_driver/d3js_desPort_v3.py y ".$tsA." ".$tsB." n"." -HL ".$Max_num_des." ".$per_lab_show_des." ".$Max_num_srcip." ".$per_lab_show_srcip);
    }

    //disable cache
    // HTTP/1.1  
    //header("Cache-Control: no-store, no-cache, must-revalidate");
    //header("Cache-Control: post-check=0, pre-check=0", false);
    // HTTP/1.0
    //header("Pragma: no-cache");

    //header("Location: ../graph_desPort.php? lastDate=".$daterange_desPort);

    echo "<script>document.location.href='../graph_desPort.php?lastDate=$daterangeAB[0] ~ $daterangeAB[1]';</script>"
?>
