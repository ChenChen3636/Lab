<?php
    date_default_timezone_set("Asia/Taipei");        //reset timezone

    $daterange_dns = $_GET["daterange_dns"];
    $srcIpFilter_dns = $_GET["srcIpFilter_dns"];
    $ipFilter_ck = $_GET["ipCheckbox"];

    $daterangeAB = explode(" ~ ", $daterange_dns);


    print("data processing...");
    //system("pwd",$result);
    //print ("pwd: ".$result);

    //echo "<br>".exec("whoami");
    //echo "<br>".exec("pwd"); 

    

    //exec("python3 /var/www/html/graph_page/graph_driver/d3js_conPktDelay_v3.py y 1615600800 1615608000")


    $tsA = strtotime("$daterangeAB[0]");
    $tsB = strtotime("$daterangeAB[1]");
    


    //print $tsA."->".$tsB;


    if($ipFilter_ck == "on"){
        exec("python3 /var/www/html/graph_page/graph_driver/d3js_DNS_v2.py y ".$tsA." ".$tsB." y ".$srcIpFilter_dns, $result);
        //print("<br>python3 /var/www/html/graph_page/graph_driver/d3js_DNS_v2.py y ".$tsA." ".$tsB." y ".$srcIpFilter_dns);
    }else{
        exec("python3 /var/www/html/graph_page/graph_driver/d3js_DNS_v2.py y ".$tsA." ".$tsB." n", $result);
        //print("<br>python3 /var/www/html/graph_page/graph_driver/d3js_DNS_v2.py y ".$tsA." ".$tsB." n");
    } 

    //disable cache
    // HTTP/1.1  
    //header("Cache-Control: no-store, no-cache, must-revalidate");
    //header("Cache-Control: post-check=0, pre-check=0", false);
    // HTTP/1.0
    //header("Pragma: no-cache");

    //header("Location: ../graph_DNS.php? lastDate=".$daterange_dns);

    echo "<script>document.location.href='../graph_DNS.php?lastDate=$daterangeAB[0] ~ $daterangeAB[1]';</script>"
?>
