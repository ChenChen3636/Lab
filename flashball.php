<?php
// ini_set('display_errors','1');
// error_reporting(E_ALL);
session_start();
require_once './session.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" href="./icon/ball.ico">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <!-- jQuery and JS bundle w/ Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>


    <!-- time -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- 引入 ECharts 文件 -->
    <script src="https://cdn.staticfile.org/echarts/4.3.0/echarts.min.js"></script>

    <link rel="stylesheet" href="./css/flash.css">
    <title>flashball</title>

    <style>
        #test {
            height: 100px;
            width: 100%;
        }

        #main_div {
            width: 100%;
            overflow: auto;
            height: calc(100vh - 420px);
        }
        .hide {
            display: none;
        }
        .dropdown-menu>label {
            display:block;
            margin:0;
            line-height:10px;
            left: 10px;
        }
        [type="checkbox"] {
            position: relative;
            top: 13px;
            left:-85px;
            cursor: pointer;
        }
        .dropdown-menu>p {
            margin:-8px 10px 0 30px;
        }
        .dropdown-menu.show{
            height:300px;
            overflow:auto;
        }
        .invalid{
            cursor: not-allowed;
        }
    </style>

</head>
<body>
    <div class="container-fluid">
        <div style="position: relative; z-index:2; width: 100%;">
            <div class="row">
                <div class="col-md-12">
                    <div class="row navbar">
                        <div class="col-md-8">
                            <nav class="nav">
                                <a href="flashball.php"> <img src="./icon/ball.png" alt="home" class="logo"></a>
                                <a class="nav-link active" href="#">主頁</a>
                                <a class="nav-link" href="./graph_page/index.html">圖表分析</a>
                            </nav>
                        </div>
                        <div class="col-md-4">
                            <?php echo $_COOKIE["hi"]; ?>
                            <button type="button" class="btn btn-outline-light logout" onclick="location.href='logout.php'">logout</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="background-color:cornsilk; box-shadow: 0px 10px 5px -8px #CCC">
                <div class="col-md-4">
                    <!-- 選擇connection or packet -->
                    <button id="conne" href="" target="" class="select pick_page btn active" value="connection">連線</button>
                    <label for=""></label>
                    <button id="pack" href="" target="" class="select pick_page btn " value="packet">封包</button>
                </div>
                <div class="col-md-4 align-self-center" style="display:flex;justify-content:center;align-items:center;">
                    <!--選擇時間範圍-->
                    <input id="reportrange" type="text" style="background: #fff; cursor: pointer; text-align:center;width: 300px; border:0px">
                </div>
            </div>
        </div>

        <!-- 圖表背景 -->
        <div class="row">
            <div class="col">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-link active" id="tab-cflow" data-toggle="tab" href="#nav-cflow" role="tab" aria-controls="nav-cflow" aria-selected="true">連線數量</a>
                        <a class="nav-link" id="tab-pflow" data-toggle="tab" href="#nav-pflow" role="tab" aria-controls="nav-pflow" aria-selected="false">封包流量</a>
                        <a class="nav-link" id="tab-src-rank" data-toggle="tab" href="#nav-src-rank" role="tab" aria-controls="nav-src-rank" aria-selected="false">來源端連線排名</a>
                        <a class="nav-link" id="tab-dest-rank" data-toggle="tab" href="#nav-dest-rank" role="tab" aria-controls="nav-dest-rank" aria-selected="false">目的端連線排名</a>
                        <a class="nav-link" id="tab-error" data-toggle="tab" href="#nav-error" role="tab" aria-controls="nav-error" aria-selected="false">連線異常分析</a>
                    </div>
                </nav>

                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-cflow" role="tabpanel" aria-labelledby="nav-cflow-tab">
                        <div id="chart-cflow" style="height:200px;"></div>
                    </div>
                    <div class="tab-pane fade" id="nav-pflow" role="tabpanel" aria-labelledby="nav-pflow-tab">
                        <div id="chart-pflow" stpflow style="height:200px;"></div>
                    </div>
                    <div class="tab-pane fade" id="nav-src-rank" role="tabpanel" aria-labelledby="nav-src-rank-tab">
                        <div style="display:flex;justify-content:space-between;">
                            <div id="bar-rank" style="height:200px;width:30%"></div>
                            <div id="chart-rank" style="height:200px;"></div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-dest-rank" role="tabpanel" aria-labelledby="nav-dest-rank-tab">
                        <div style="display:flex;justify-content:space-between;">
                            <div id="bar-dest-rank" style="height:200px;width:30%"></div>
                            <div id="chart-dest-rank" style="height:200px;"></div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-error" role="tabpanel" aria-labelledby="nav-error-tab">
                        <div id="chart-error" style="height:200px;width:30%" ></div>
                    </div>

                </div>
            </div>
        </div>
        <div class="row" style="margin-top:10px">
            <div class="col"></div>
            <div class="col align-self-center"  style="display:flex;justify-content:center;align-items:center;">
                <?php require_once './pagination.php' ?>
            </div>
            <div class="col" style="float:right;display:flex;justify-content:flex-end">
                <div style="margin: 10px 10px 0px 10px">
                    <label>共 </label>
                    <a id="total_page"></a>
                    <label>頁</label>
                </div>
                <div id="resetPacket" style="display:none">
                            <button id="reset" class="btn" data-toggle="tooltip" data-placement="top" title="回到所有連線的封包列表" style="height:40px;width:40pxvertical-align:middle;padding:0px 10px 0px 8px;margin-right:5px;background-color:#80a9d5">
                                <img src="./icon/recycle.png" alt="" style="height:25px;width25px;">
                            </button>
                </div>
                <!-- 欄位選項 -->
                <div class="dropdown-check-list" >
                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height:40px;width:40px;background-color:#68ae78">
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <input type="checkbox" value="Maximum_TimeInterval" class="dropdown-item"><p>Maximum_TimeInterval</p>
                        <input type="checkbox" value="Minimum_TimeInterval" class="dropdown-item"><p>Minimum_TimeInterval</p>
                        <input type="checkbox" value="Average_TimeInterval" class="dropdown-item"><p>Average_TimeInterval</p>
                        <input type="checkbox" value="Maximum_A2Bbytes" class="dropdown-item"><p>Maximum_A2Bbytes</p>
                        <input type="checkbox" value="Maximum_B2Abytes" class="dropdown-item"><p>Maximum_B2Abytes</p>
                        <input type="checkbox" value="Minimum_A2Bbytes" class="dropdown-item"><p>Minimum_A2Bbytes</p>
                        <input type="checkbox" value="Minimum_B2Abytes" class="dropdown-item"><p>Minimum_B2Abytes</p>
                        <input type="checkbox" value="Maximum_bytes" class="dropdown-item"><p>Maximum_bytes</p>
                        <input type="checkbox" value="Minimum_bytes" class="dropdown-item"><p>Minimum_bytes</p>
                        <input type="checkbox" value="Average_bytes" class="dropdown-item"><p>Average_bytes</p>
                        <input type="checkbox" value="SYN" class="dropdown-item"><p>SYN</p>
                        <input type="checkbox" value="FIN" class="dropdown-item"><p>FIN</p>
                        <input type="checkbox" value="RST" class="dropdown-item"><p>RST</p>
                        <input type="checkbox" value="PSH" class="dropdown-item"><p>PSH</p>
                        <input type="checkbox" value="URG" class="dropdown-item"><p>URG</p>
                    </div>
                    <!-- filter -->
                    <button type="button" class="btn"  data-toggle="modal" data-target="#exampleModalCenter" style="height:40px;width:40px;vertical-align:middle;padding:0px 10px 0px 8px;background-color:#f0b685">
                        <img src="./icon/loupe.png" alt="" style ="height:25px;width:25px">
                    </button>
                    <!-- download -->
                    <button type="button" id="btn-download" class="btn" style="height:40px;width:40px;vertical-align:middle;padding:0px 10px 0px 8px;background-color:#f8d362" data-toggle="tooltip" data-placement="top" title="下載PCAP檔" >
                        <img src="./icon/download.png" alt="" style="height:25px;width:25px";>
                    </button>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <div class="spinner-border text-primary" role="status" id="loading">
                <span class="sr-only"></span>
            </div>
        </div>
        <!--  -->
        <!--主頁div -->
        <!--  -->

        <div id="main_div">
            <!--//-------------------------- connection --------------------------//-->
            <div id="session_connection">
                <table class="table table-hover" id="table_connection">
                    <thead>
                        <tr class="clickable-row">
                            <th scope="col">No.</th>
                            <th scope="col">Protocol</th>
                            <th scope="col">Start time</th>
                            <th scope="col">duration(s)</th>
                            <th scope="col">Source IP</th>
                            <th scope="col">Destination IP</th>
                            <th scope="col">Source Port</th>
                            <th scope="col">Destination Port</th>
                            <th scope="col">Packets</th>
                            <th scope="col">Error</th>
                            <th scope="col">Score</th>
                            <th scope="col" class = "Maximum_TimeInterval" >Maximum TimeInterval</th>
                            <th scope="col" class = "Minimum_TimeInterval">Minimum TimeInterval</th>
                            <th scope="col" class = "Average_TimeInterval">Average TimeInterval</th>
                            <th scope="col" class = "Maximum_A2Bbytes">Maximum A2Bbytes</th>
                            <th scope="col" class = "Maximum_B2Abytes">Maximum B2Abytes</th>
                            <th scope="col" class = "Minimum_A2Bbytes">Minimum A2Bbytes</th>
                            <th scope="col" class = "Minimum_B2Abytes">Minimum B2Abytes</th>
                            <th scope="col" class = "Maximum_bytes">Maximum bytes</th>
                            <th scope="col" class = "Minimum_bytes">Minimum bytes</th>
                            <th scope="col" class = "Average_bytes">Average bytes</th>
                            <th scope="col" class = "SYN">SYN</th>
                            <th scope="col" class = "FIN">FIN</th>
                            <th scope="col" class = "RST">RST</th>
                            <th scope="col" class = "PSH">PSH</th>
                            <th scope="col" class = "URG">URG</th>
                            <!-- <th scope="col" class = ""></th> -->
                        </tr>
                    </thead>
                    <tbody id="tbody_connection">
                    </tbody>
                </table>
            </div>

            <!-- //-------------------- packet ----------------------// -->
            <div id="session_packet" style="display: none;">
                <div id="main_packet" style="height:400px;overflow:auto">
                    <table class="table table-hover" id="table_packet">
                        <thead>
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Arrival Time</th>
                                <th scope="col">Protocol</th>
                                <th scope="col">Source IP</th>
                                <th scope="col">Destination IP</th>
                                <th scope="col">Source MAC</th>
                                <th scope="col">Destination MAC</th>
                                <th scope="col">Source Port</th>
                                <th scope="col">Destination Port</th>
                                <th scope="col">Error</th>
                            </tr>
                            </thead>
                        <tbody id="tbody_packet">
                        </tbody>
                    </table>
                 </div>

                <div id="detail">
                    <table class="table table-hover" id="packet_detal">
                        <thead>
                        </thead>
                        <tbody id="tbody_packet_detail">
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
</body>

</html>


<!-- 條件搜尋的Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">搜尋</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="radio" value="connection" id="conn_box" name="pick" style="display: none;">
                <input type="radio" value="packet" id="pkt_box" name="pick" style="display: none;">
                <div style="display:grid;grid-template-columns: repeat(5, 1fr)">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="ICMP" value="ICMP" style="width:20px;height:20px">
                        <label class="form-check-label" for="ICMP">ICMP</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="ARP" value="ARP" style="width:20px;height:20px">
                        <label class="form-check-label" for="ARP">ARP</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="TCP" value="TCP" style="width:20px;height:20px">
                        <label class="form-check-label" for="inlineCheckbox1">TCP</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="UDP" value="UDP" style="width:20px;height:20px">
                        <label class="form-check-label" for="UDP">UDP</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" id="HTTP" value="HTTP" style="width:20px;height:20px">
                        <label class="form-check-label" for="HTTP">HTTP</label>
                    </div>
                </div>
                <br>
                <label for="srcIP">IP:</label>
                <input type="text" name="Third_Layer-Source_IP" id="Source_IP" placeholder="Src IP">
                <input type="text" name="Third_Layer-Destination_IP" id="Destination_IP" placeholder="Dst IP">
                <br>
                <label for="srcPort">Port: </label>
                <input type="text" name="Fourth_Layer-Source_Port" id="Source_Port" placeholder="Src Port">
                <input type="text" name="Fourth_Layer-Destination_Port" id="Destination_Port" placeholder="Dst Port">
            </div>
            <div class="modal-footer">
                <button id="btn_search" class="btn btn-secondary" data-dismiss="modal">search</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================================================================================================== -->
<!-- JS -->
<!-- =========================================================================================================================================================== -->

<script>
    // var start = moment().startOf('day').unix();
    // var end = moment().unix();
    var start = 1628438400;
    var end = 1628488740;
    var data = [];
    var filter = {};
    var filter_connection = {};
    var filter_packet = {};
    var page_status ={
        "connection" : 1,
        "packet" : 1
    }
    var type = $(".pick_page.active").attr("value");
    var limit = {
        "count": 0,
        "skip": 0,
        "end": 200
    };

    /** ------------------------------------------------------*
     * get time
    /** ------------------------------------------------------*/
    function get_time(start, end) {
        filter_connection["Start_Time"] = {};
        filter_packet["Arrival_Time"] = {};
        filter_connection["Start_Time"]["$gt"] = start;
        filter_connection["Start_Time"]["$lt"] = end;
        filter_packet["Arrival_Time"]["$gt"] = start;
        filter_packet["Arrival_Time"]["$lt"] = end;
    }
    /** ------------------------------------------------------*
     * total page
    /** ------------------------------------------------------*/
    function page_total(count) {
        var total = Math.ceil(count / limit.end);
        $("#total_page").html(total, " ");
    }
    /** ------------------------------------------------------*
     * 歸零分頁
    /** ------------------------------------------------------*/
    function reset_page(){
        pagination.init();
        limit.skip = 0;
    }
    /** ------------------------------------------------------*
     * query資料，丟目標位置跟條件
    /** ------------------------------------------------------*/
    function data_query(target, filter) {
        $("#loading").show();
        //select_col();
        if (target == "connection") {
            filter = filter_connection;
        } else if (target == "packet") {
            filter = filter_packet;
        }
        get_time(start, end);
        $.ajax({
            type: 'POST',
            url: 'db_process.php',
            dataType: 'json',
            data: {
                filter: filter,
                type: target,
                limit: limit
            },
            async: true,
            success: function(msg) {
                //console.log(msg);
                var score = msg.score;
                $(`#tbody_${type}`).html(msg.data);
                select_col();
                $('[data-toggle="popover"]').popover()
                limit.count = msg.count;
                final_page(Math.ceil(limit.count/limit.end))
                page_total(msg.count);
                pagination.status(msg.count, limit.end);
                if(type == "connection"){
                    $("#tbody_connection>tr").each(function(index,element){
                        // console.log(index,element);
                        var col_id = $(element).attr("id");
                        if(score.hasOwnProperty(col_id)){
                            $(element).find(".col_score").html(score[col_id]);
                            //console.log($(element).find(".col_score"));
                        }
                    })
                }
                $("#loading").hide();
            }
        });
    }
    function chart_set(){
       chart(start, end);
       packet_chart(start,end);
       line_chart(start,end,"chart-rank","src");
       line_chart(start,end,"chart-dest-rank","dest");
       bar_rank(start,end,"bar-rank","src");
       bar_rank(start,end,"bar-dest-rank","dest");
       error_chart();
    }

    /** ------------------------------------------------------*
     * 轉換timestamp to date
    /** ------------------------------------------------------*/
    function timestamp_to_date(timestamp) {
        var newdate = [];
        var Month = [];
        var date = [];
        var Hours = [];
        var Minutes = [];
        var format = [];
        var window_width;
        for (var i = 0; i < timestamp.length; i++) {
            newdate[i] = new Date(timestamp[i] * 1000);
            Month[i] = newdate[i].getMonth() + 1;
            date[i] = newdate[i].getDate();
            Hours[i] = "0" + newdate[i].getHours();
            Minutes[i] = "0" + newdate[i].getMinutes();
            format[i] = Month[i] + "/" + date[i] + " " + Hours[i].substr(-2) + ":" + Minutes[i].substr(-2);
        }
        return format;
    }
    /** ------------------------------------------------------*
     * checkbox
     ** ------------------------------------------------------*/
     function select_col(){
         $("input[type='checkbox']").each(function(){
             colname = $(this).val();
             if(!$(this).prop("checked")){
                $(`.${colname}`).addClass("hide");
             }
         })
     }
    /** ------------------------------------------------------*
     * echart function
    /** ------------------------------------------------------*/
    function chart(start, end) {
        $.ajax({
            type: 'POST',
            url: 'chart-cflow.php',
            data: {
                filter: filter_connection,
            },
            dataType: "json",
            success: function(msg) {
                // console.log(Object.keys(msg));
                // console.log(Object.values(msg));

                var myChart = echarts.init(document.getElementById('chart-cflow'));
                var date = Object.keys(msg);
                var data = Object.values(msg);

                date = timestamp_to_date(date);

                option = {
                    tooltip: {
                        trigger: 'axis',
                        position: function(pt) {
                            return [pt[0], '10%'];
                        }
                    },
                    title: {
                        y: 0,
                        left: 'center',
                        text: '連線數量(筆/五分鐘)',
                        textStyle: {
                            fontSize: 20,
                            fontFamily: "MingLiU",
                        }
                    },
                    toolbox: {
                        feature: {
                            saveAsImage: {}
                        }
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: date
                    },
                    yAxis: {
                        type: 'value',
                        boundaryGap: [0, '0%']
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    series: [{
                        name: '連線量',
                        type: 'line',
                        symbol: 'none',
                        sampling: 'lttb',
                        itemStyle: {
                            color: 'rgb(255, 70, 131)'
                        },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                offset: 0,
                                color: 'rgb(255, 158, 68)'
                            }, {
                                offset: 1,
                                color: 'rgb(255, 70, 131)'
                            }])
                        },
                        data: data
                    }]
                };
                myChart.setOption(option);
            }
        });
    }
    function packet_chart(start,end){
        var chartDom = document.getElementById('chart-pflow');
        var myChart = echarts.init(chartDom);
        var option;

        $.ajax({
            type: 'POST',
            url: 'chart-pflow.php',
            data: {
                filter: filter_packet,
            },
            dataType: "json",
            success: function(msg) {

                var date = Object.keys(msg);
                var data = Object.values(msg);

                date = timestamp_to_date(date);
                option = {
                    tooltip: {
                        trigger: 'axis',
                        position: function (pt) {
                            return [pt[0], '0%'];
                        }
                    },
                    title: {
                        y: 0,
                        left: 'center',
                        textStyle: {
                            fontSize: 20,
                            fontFamily: "MingLiU"
                        },
                        text: '封包流量(bytes/sec)'
                    },
                    toolbox: {
                        feature: {
                            saveAsImage: {}
                        }
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: date
                    },
                    yAxis: {
                        type: 'value',
                        boundaryGap: [0, '0%']
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    series: [
                        {
                            name: 'KBps',
                            type: 'line',
                            smooth: true,
                            symbol: 'none',
                            areaStyle: {},
                            itemStyle: {
                                color: '#8696a7'
                            },
                            areaStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: '#9ca8b8'
                                }, {
                                    offset: 1,
                                    color: '#8696a7'
                                }])
                            },
                            data: data
                        }
                    ]
                };
                myChart.setOption(option);
            }
        });
    }
    function bar_rank(start,end,div_id,type){
        if(type == "src"){
            var t = "來源端"
        }else if(type == "dest"){
            var t = "目的端"
        }
        $.ajax({
            type: 'POST',
            url: 'chart-bar-rank.php',
            data: {
                filter: filter_connection,
                type: type
            },
            dataType: "json",
            success: function(msg) {
                var chartDom = document.getElementById(div_id);
                var myChart = echarts.init(chartDom);
                var option;
                var data = msg.data;
                var count = msg.count;
                var others = msg.others;

                option = {
                    title: {
                        text: t+'連線量排名',
                        subtext: '標示前五名連線',
                        left: 'left',
                        y: 0,
                        textStyle: {
                            fontSize: 20,
                            fontFamily: "MingLiU"
                        },
                    },
                    tooltip: {
                        trigger: 'item'
                    },
                    legend: {
                        orient: 'vertical',
                        left: 'left',
                        padding: [60,0,0,10]
                    },
                    series: [
                        {
                            name: '連線IP',
                            type: 'pie',
                            radius: '80%',
                            data: [
                                {value: count[0], name: data[0],itemStyle:{color: '#DC143C'}},
                                {value: count[1], name: data[1],itemStyle:{color: '#ffc107'}},
                                {value: count[2], name: data[2],itemStyle:{color: '#9ACD32'}},
                                {value: count[3], name: data[3],itemStyle:{color: '#4169E1'}},
                                {value: count[4], name: data[4],itemStyle:{color: '#FFB6C1'}},
                                {value: others, name: "others",itemStyle:{color: '#87CEEB'}}
                            ],
                            center: ['65%','55%'],
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            },
                            grid: {
                                left: '3%',
                                right: '4%',
                                bottom: '3%',
                                containLabel: true
                            },

                        }
                    ]
                };

                myChart.setOption(option);
            }
        });
    }
    function line_chart(start,end,div_id,type){
        var chartDom = document.getElementById(div_id);
        var myChart = echarts.init(chartDom);
        var option;
        $.ajax({
            type: 'POST',
            url: 'chart-rank.php',
            data: {
                filter: filter_connection,
                type: type
            },
            dataType: "json",
            success: function(msg) {
                $("#chart-load").hide();
                var data = msg.data;
                var x = Object.keys(msg.x);
                x = timestamp_to_date(x);
                var each0 = Object.values(msg.each_result[0]);
                var each1 = Object.values(msg.each_result[1]);
                var each2 = Object.values(msg.each_result[2]);
                var each3 = Object.values(msg.each_result[3]);
                var each4 = Object.values(msg.each_result[4]);

                option = {
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        padding: 20,
                        itemGap: 30,
                        data: data
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    toolbox: {
                        feature: {
                            saveAsImage: {}
                        }
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: x
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: [
                        {
                            name: data[0],
                            type: 'line',
                            data: each0,
                            color: '#DC143C',
                            width: 1.5
                        },
                        {
                            name: data[1],
                            type: 'line',
                            data: each1,
                            color: '#ffc107'
                        },
                        {
                            name: data[2],
                            type: 'line',
                            data: each2,
                            color: '#9ACD32'
                        },
                        {
                            name: data[3],
                            type: 'line',
                            data: each3,
                            color: '#4169E1'
                        },
                        {
                            name: data[4],
                            type: 'line',
                            data: each4,
                            color: '#FFB6C1'
                        }
                    ]
                };

                myChart.setOption(option);
            }
        });
    }

    function error_chart(){
        var chartDom = document.getElementById('chart-error');
        var myChart = echarts.init(chartDom);
        var option;

        option = {
            title: {
                left: 'center',
                text: '連線異常分析統計圖',
                subtext: '某時段異常分析',
                textStyle: {
                            fontSize: 20,
                            fontFamily: "MingLiU"
                        },
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                boundaryGap: false
            },
            yAxis: {
                type: 'category',
                data: ['3way', '4way']
            },
            series: [
                {
                    type: 'bar',
                    data: [18203,23489],
                    color: '#deaf68'
                },
            ]
        };

        myChart.setOption(option);
    }


    /** ------------------------------------------------------*
     *
     ** ------------------------------------------------------*/
    $(function() {
        var width = $(window).width();
        $("#chart-cflow").width(width);
        $("#chart-pflow").width(width);
        $("#bar-rank").width(width*0.3);
        $("#chart-rank").width(width*0.7);
        $("#bar-dest-rank").width(width*0.3);
        $("#chart-dest-rank").width(width*0.7);
        $("#chart-error").width(width);
        pagination.init();
        $('[data-toggle="tooltip"]').tooltip()
        /** ------------------------------------------------------*
         * 網頁一讀取，先query預設資料
        /** ------------------------------------------------------*/
        data_query(type, filter);
        chart_set();
        /** ------------------------------------------------------*
         * 選擇connection還是packet頁面
        /** ------------------------------------------------------*/
        $(".pick_page").on("click", function() {
            var id = $(this).attr("id");
            if (id == "conne") {
                $("#session_packet").css("display", "none");
                $("#session_connection").css("display", "block");
                $("#pack").removeClass("active");
                $("#conne").addClass("active");
                $("#resetPacket").css("display","none");
                $("#midPage").find(".page-link").attr("value",page_status.connection);
                limit.skip = (page_status.connection * limit.end);
                pagination.run($("#midPage"),limit.count,limit.end);
                $("#dropdownMenuButton").css("display","inline-block");
            } else if (id == "pack") {
                $("#session_connection").css("display", "none");
                $("#session_packet").css("display", "block");
                $("#conne").removeClass("active")
                $("#pack").addClass("active");
                $("#resetPacket").css("display","inline-block");
                page_status.connection = $(".page-item.active>.page-link").attr("value");
                limit.skip = 0;
                pagination.init();
                $("#dropdownMenuButton").css("display","none");
            }
            type = $(".pick_page.active").attr("value");

            data_query(type, filter);
        })
        /** ------------------------------------------------------*
         * 時間顯示
        /** ------------------------------------------------------*/
        $('#reportrange').daterangepicker({
            // startDate: moment().startOf('day'),
            // endDate: moment(),
            startDate: 1625241600,
            endDate: 1625275680,
            showDropdowns: true,
            timePicker: true,
            locale: {
                format: 'M/DD hh:mm A'
            }
        });
        /** ------------------------------------------------------*
         * 選擇時間
        /** ------------------------------------------------------*/
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            var type = $(".pick_page.active").attr("value");
            start = Date.parse(picker.startDate.format('YYYY-MM-DD HH:mm')) / 1000;
            end = Date.parse(picker.endDate.format('YYYY-MM-DD HH:mm')) / 1000;
            reset_page();
            data_query(type, filter);
            chart_set();
        });
        /** ------------------------------------------------------*
         * 選擇條件
        /** ------------------------------------------------------*/
        $("#btn_search").on("click", function() {
            filter = {};
            filter_connection = {};
            filter_packet = {};
            $(this).parent().prev().children().children().children("input[type='checkbox']").each(function() {
                if($(this).prop('checked')){
                    filter_connection["Connection_Type"] = $(this).val();
                    filter_packet["Protocol"] = $(this).val();
                }
            });
            $(this).parent().prev().children("input[type=text]").each(function() {
                if ($(this).val() != "") {
                    filter_connection[$(this).attr("id")] = $(this).val();
                    filter_packet[$(this).attr("name")] = $(this).val();
                }
            });
            reset_page();
            data_query(type, filter);
            chart_set();
        });

       /** ------------------------------------------------------*
        * 監控分頁
       /** ------------------------------------------------------*/

        $(".pagination").on("click", ".page-item", function() {
            var classList = $(this).attr("class").split(" ");
            if (classList.indexOf("disabled") == -1) {
                var value = parseInt($(this).find(".page-link").attr("value"));
                limit.skip = (value - 1) * limit.end;
                // console.log(value, limit.skip, limit.end);
                pagination.run($(this), limit.count, limit.end);
                data_query(type, filter);
            }

        })
        /** ------------------------------------------------------*
         * onnection連到packet
        /** ------------------------------------------------------*/
        $("tbody").on("click",".connectionToPacket",function(){
            var fKey = $(this).attr("value");
            type = "packet";
            filter_packet["Foreign_Key"] = fKey;
            data_query(type,filter);
            $("#pack").trigger("click");
        })
        /** ------------------------------------------------------*
         * 拿掉packet的foreign key 條件
        /** ------------------------------------------------------*/
        $("#reset").on("click",function(){
            delete filter_packet["Foreign_Key"];
            // console.log(filter_packet);
            data_query(type,filter);
            $("#tbody_packet_detail").empty();
        })
        /** ------------------------------------------------------*
         * packet的detail
        /** ------------------------------------------------------*/
        $("#tbody_packet").on("click","tr",function(){
            var pid = $(this).attr("pid");
            var num = $(this).attr("num");

            $.ajax({
                type: 'POST',
                url: 'db_process.php',
                dataType: 'json',
                data: {
                    pid : pid,
                    num : num,
                    type : "PacketToDetail"
                },
                async: true,
                success: function(msg) {
                    // console.log(msg);
                    $("#tbody_packet_detail").html(msg);
                }
            });
        })
         /** ------------------------------------------------------*
         * column select
         ** ------------------------------------------------------*/
        $("input[type='checkbox']").on("click",function(){
            var colname = $(this).val();
            if($(this).prop("checked")){
                $(`.${colname}`).removeClass("hide");
            }else{
                $(`.${colname}`).addClass("hide");
            }
        })
        /** ------------------------------------------------------*
         * download click
         ** ------------------------------------------------------*/
        $("#btn-download").on("click",function(){

            let reader  = new FileReader();
            function downloadFile(){
                xhttp_request('download.php', function(response){
                    var blob = new Blob([response], {type: "application/octet-stream"});
                    reader.readAsArrayBuffer(blob);

                    let url = URL.createObjectURL(blob);
                    let dom_a = document.createElement('a');

                    dom_a.href = url ;
                    dom_a.download = "test.pcap" ;

                    document.getElementsByTagName('body')[0].appendChild(dom_a);
                    dom_a.click();
                    dom_a.remove();
                });
            }

            function xhttp_request(url, callback){
                var data = 'start='+start+"&end="+end;
                var xhttp  = new XMLHttpRequest();
                xhttp.open("POST",url,true);
                xhttp.responseType  = "arraybuffer" ;
                xhttp.setRequestHeader('Content-type',"application/x-www-form-urlencoded; charset=UTF-8");
                xhttp.onload  = function(){
                    if(this.status == 200 ){
                        callback(this.response);
                    }
                };
                xhttp.send(data);
            }
            downloadFile();

        });

    });

</script>