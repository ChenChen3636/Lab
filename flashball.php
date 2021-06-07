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
        #test {
            height: 100px;
            width: 100%;
        }

        iframe {
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
                            <?php echo $_COOKIE["hi"]; ?>
                            <button type="button" class="btn btn-outline-light logout" onclick="location.href='logout.php'">logout</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="background-color:cornsilk; box-shadow: 0px 11px 8px -8px #CCC, 0px -11px 8px -10px #CCC;">
                <div class="col-md-4">
                    <img src="./icon/play.png" alt="start" class="toolbar">
                    <img src="./icon/stop.png" alt="stop" class="toolbar">
                    <p id="conne" href="" target="" class="select pick_page" value="connection" active="1">connection</p>
                    <label for="">|</label>
                    <p id="pack" href="" target="" class="select pick_page" value="packet">packet</p>
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
        <iframe name="main_iframe" id="main_iframe" src="" frameborder="0"></iframe>
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
                <input type="radio" value="connection" id="conn_box" name="pick" style="display: none;">
                <input type="radio" value="packet" id="pkt_box" name="pick" style="display: none;">
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
            </div>
            <div class="modal-footer">
                <button id="btn_search" class="btn btn-secondary" data-dismiss="modal">search</button>
            </div>
        </div>
    </div>
</div>


<script>
    var start = moment().startOf('day').unix();
    //var start = 1621612800;
    var end = moment().unix();
    //var end = 1621699199;
    var data = [];
    var filter = [];

    //----------------query資料，丟目標位置跟條件------------------------//
    function data_query(target, filter) {
        var prefix = (target === "connection") ? `process_connection.php?type=${target}&` : `process_packet.php?type=${target}&`;
        var page = "\$gt=" + start + "&\$lt=" + end + "&" + filter.join("&");
        $("#main_iframe").attr("src", prefix + page);
        chart(start, end);
    }

    //----------------轉換timestamp to date------------------------------//
    function timestamp_to_date(timestamp) {
        var newdate = [];
        var Month = [];
        var date = [];
        var Hours = [];
        var Minutes = [];
        var format = [];
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


    function chart(start, end) {
        $.ajax({
            type: 'POST',
            url: 'echartTest.php',
            data: {
                start: start,
                end: end
            },
            dataType: "json",
            success: function(msg) {
                console.log(Object.keys(msg));
                console.log(Object.values(msg));

                var myChart = echarts.init(document.getElementById('main'));
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
                        y: 10,
                        left: 'center',
                        text: '連線數量趨勢圖',
                        textStyle: {
                            fontSize: 15,
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

                // 使用刚指定的配置项和数据显示图表。
                myChart.setOption(option);
            }
        });
    }

    $(function() {

        //--------------網頁一讀取，先query預設資料---------------------------//
        data_query("connection", filter);
        //---------------選擇connection還是packet頁面------------------------//
        $(".pick_page").on("click", function() {
            var id = $(this).attr("id");
            filter = [];
            $(".pick_page").attr("active", 0);
            $(`#${id}`).attr("active", 1);
            data_query($(this).attr("value"), filter);
        })

        $('#reportrange').daterangepicker({
            startDate: moment().startOf('day'),
            endDate: moment(),
            // startDate: "2021-05-22 00:00 ",
            // endDate: "2021-05-22 23:59",
            showDropdowns: true,
            timePicker: true,
            locale: {
                format: 'M/DD hh:mm A'
            }
        });

        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            var type = $(".pick_page[active=1]").attr("value");
            start = Date.parse(picker.startDate.format('YYYY-MM-DD HH:mm')) / 1000;
            end = Date.parse(picker.endDate.format('YYYY-MM-DD HH:mm')) / 1000;

            data_query(type, filter);
        });

        $("#btn_search").on("click", function() {

            var type = $(".pick_page[active=1]").attr("value");
            filter = [];
            if (type == "connection") {
                $(this).parent().prev().children("input[type=text]").each(function() {
                    if ($(this).val() != "") {
                        filter.push($(this).attr('id') + "=" + $(this).val());
                    }
                })


            } else if (type == "packet") {
                $(this).parent().prev().children("input[type=text]").each(function() {
                    if ($(this).val() != "") {
                        filter.push($(this).attr('name') + "=" + $(this).val());
                    }
                })
            }
            data_query(type, filter);

        });


    });
</script>