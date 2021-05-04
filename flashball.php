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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- time -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- 引入 ECharts 文件 -->
    <script src="https://cdn.staticfile.org/echarts/4.3.0/echarts.min.js"></script>

    <link rel="stylesheet" href="./css/flash.css">
    <title>flashball</title>

    <style>
        #test{
            height: 100px;
            width: 100%;
        }
        iframe{
            width: 100%;
            overflow: auto;
            height: calc(100vh - 276px);
        }
    </style>

</head>

<body>

    <div class="container-fluid">
        <div style="position: fixed; z-index:2; width:99%;">
            <div class="row">
                <div class="col-md-12">
                    <div class="row navbar">
                        <div class="col-md-8">
                            <nav class="nav">
                                <a href="flashball.php"> <img src="./icon/ball.png" alt="home" class="logo"></a>
                                <a class="nav-link active" href="#">Session</a>
                                <a class="nav-link" href="./graph_page/index.html">Graph</a>
                                <a class="nav-link" href="help.php">Help</a>
                            </nav>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-light logout" onclick="location.href='logout.php'">logout</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="background-color:cornsilk; box-shadow: 0px 11px 8px -8px #CCC, 0px -11px 8px -10px #CCC;">
                <div class="col-md-4">
                    <img src="./icon/play.png" alt="start" class="toolbar">
                    <img src="./icon/stop.png" alt="stop" class="toolbar">
                    <a  href="process_connection.php"  target="main_iframe" class="select">connection</a>
                    <label  for="">|</label>
                    <a  href="process_packet.php" target="main_iframe" class="select">packet</a>
                </div>
                <div class="col-md-4 align-self-center" style="display:flex;justify-content:center;align-items:center;">
                    <!--選擇時間範圍-->
                    <input id="reportrange" type="text" style="background: #fff; cursor: pointer; text-align:center;width: 300px; border:0px">
                </div>
                <div class="col-md-4">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-light logo search" data-toggle="modal" data-target="#exampleModalCenter" style="padding:0px;  background-color:transparent;border-color:transparent">
                        <div style="background-color: transparent;">
                            <img src="./icon/loupe.png" alt="search" class="toolbar">
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- 圖表背景 -->
        <div style="padding-top:70px ;">
            <div id="main" style="width:100%;height:200px;float:0 auto;background-color:aliceblue ;box-shadow: 0px 11px 8px -8px #CCC,0px -11px 8px -10px #CCC;"></div>
        </div>
        <!-- 主頁iframe -->
        <iframe name="main_iframe" id="main_iframe" src="process_connection.php"  frameborder="0"></iframe>
    </body>
</html>


<!-- Modal -->
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
                <input type="radio" value="connection" id="conn_box" name="pick" checked>
                <label for="conn_box">connection</label>
                <input type="radio" value="packet" id="pkt_box" name="pick">
                <label for="pkt_box">packet</label>
                <br>
                <label for="protocol">protocol: </label>
                <input type="text" name="Protocol" id="Connection_Type" placeholder="Protocol">
                <br>
                <label for="srcIP">IP:</label>
                <input type="text" name="Third_Layer-Source_IP" id="Source_IP" placeholder="Src IP">
                <input type="text" name="Third_Layer-Destination_IP" id="Destination_IP" placeholder="Dst IP">
                <br>
                <label for="srcPort">Port: </label>
                <input type="text" name="Fourth_Layer-Source_Port" id="Source_Port" placeholder="Src Port">
                <input type="text" name="Fourth_Layer-Destination_Port" id="Destination_Port" placeholder="Dst Port">
                <!-- <br>
                <label for="srcMac">Mac: </label>
                <input type="text" name="srcMac" id="Source_Mac" placeholder="Src Mac">
                <input type="text" name="dstMac" id="Destination_Mac" placeholder="Dst Mac">
                <br>
                <label for="connID">ID: </label>
                <input type="text" name="connID" id="connID" placeholder="connection ID">
                <input type="text" name="pktID" id="pktID" placeholder="packet ID"> -->
            </div>
            <div class="modal-footer">
                <button id="btn_search" class="btn btn-secondary" data-dismiss="modal">search</button>
            </div>
        </div>
    </div>
</div>


<script>
var start = moment().startOf('day').unix();
var end = moment().unix();

function chart(start, end){
    $.ajax({
        type: 'POST',
        url: 'echartTest.php',
        data: {
            start:start,
            end: end
        },
        dataType: "json",
        success: function(msg) {
            console.log(Object.keys(msg));
            console.log(Object.values(msg));

            var myChart = echarts.init(document.getElementById('main'));
            var date = Object.keys(msg);
            var data = Object.values(msg);

            for(var i=0;i<date.length;i++){
                var singleDate = date[i].split("_");
                date[i] = singleDate[0]+"-"+singleDate[1]+"-"+singleDate[2]+" "+singleDate[3]+":"+singleDate[4];
            }

            option = {
                tooltip: {
                    trigger: 'axis',
                    position: function (pt) {
                        return [pt[0], '10%'];
                    }
                },
                title: {
                    y: 10,
                    left: 'center',
                    text: '連線數量趨勢圖',
                    textStyle:{
                        fontSize:15,
                        fontFamily:"MingLiU",
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
                series: [
                    {
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
                    }
                ]
            };

            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);
        }
    });
}

$(function() {
    chart(start, end);


    $('#reportrange').daterangepicker({
        startDate: moment().startOf('isoWeek'),
        endDate: moment(),
        showDropdowns: true,
        timePicker24Hour: true,
        ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
           format: 'YYYY-MM-DD HH:mm'
        }
    });

    $('#reportrange').on('apply.daterangepicker',function(ev, picker) {
        start = Date.parse(picker.startDate.format('YYYY-MM-DD HH:mm'))/1000;
        end = Date.parse(picker.endDate.format('YYYY-MM-DD HH:mm'))/1000;

        chart(start, end);
    });
});

    $("#btn_search").on("click",function(){

        var type = $("input[name=pick]:checked").val();
        var a = "process_connection.php?type=connection&";
        var b = "process_packet.php?type=packet&";
        if(type == "connection"){
            $(this).parent().prev().children("input").each(function(){
                if($(this).val() != ""){
                    //alert($(this).attr("id")+":"+$(this).val());
                    a += $(this).attr('id') +'='+ $(this).val() +'&';
                }
            })
            console.log(a);
          $("#main_iframe").attr("src",a);

        }else if(type == "packet"){
            $(this).parent().prev().children("input").each(function(){
                if($(this).val() != ""){
                    b += $(this).attr("name") +"="+ $(this).val() +"&";
                }
            })
            console.log(b);
          $("#main_iframe").attr("src",b);
        }

    });


</script>
