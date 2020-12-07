<?php
require_once __DIR__ . "/session.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <!-- jQuery and JS bundle w/ Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <!-- time -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <!-- 引入 ECharts 文件 -->
    <script src="https://cdn.staticfile.org/echarts/4.3.0/echarts.min.js"></script>

    <title>flashball</title>

    <style>
        .logout {
            float: right;
            height: 30px;
            line-height: 1px;
        }

        .navbar {
            background-color: #B0DE6B;
            color: #fff;
            height: 40px;
            line-height: 10px;
            padding: 0px;

        }

        .active {
            color: darkslategrey;
        }

        .logo {
            width: 30px;
            height: 30px;
        }

        .searchbar {}

        .toolbar {
            height: 25px;
        }

        .tool {
            height: 25px;
            line-height: 20px;

        }

        a {
            color: #fff
        }

        .search {
            float: right;
        }
    </style>

</head>

<body>

    <div class="container-fluid">
        <div style="position: fixed; z-index:2; width:98%;">
            <div class="row">
                <div class="col-md-12">
                    <div class="row navbar">
                        <div class="col-md-8">
                            <nav class="nav">
                                <a href="flashball.php"> <img src="ball.png" alt="home" class="logo"></a>
                                <a class="nav-link active" href="#">Session</a>
                                <a class="nav-link" href="graph.php">Graph</a>
                                <a class="nav-link" href="help.php">Help</a>
                            </nav>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-light logout" onclick="location.href='logout.php'">logout</button>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="background-color:cornsilk;">
                <div class="col-md-2">
                    <img src="play.png" alt="start" class="toolbar">
                    <img src="stop.png" alt="stop" class="toolbar">
                </div>
                <div class="col-md-8">
                    <!--選擇時間範圍-->
                    <div id="reportrange" style="background: #fff; cursor: pointer; padding; width: 330px;height:26px; margin:2px auto; text-align:center">
                        <i class="fa fa-calendar"></i>
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>

                    <script type="text/javascript">
                        $(function() {

                            var start = moment().subtract(29, 'days');
                            var end = moment();

                            function cb(start, end) {
                                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                            }

                            $('#reportrange').daterangepicker({
                                startDate: start,
                                endDate: end,
                                ranges: {
                                    'Today': [moment(), moment()],
                                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                                }
                            }, cb);

                            cb(start, end);

                        });
                    </script>
                </div>
                <div class="col-md-2">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-light logo search" data-toggle="modal" data-target="#exampleModalCenter" style="padding:0px;background-color:transparent;border-color:transparent">
                        <div style="background-color: transparent;">
                            <img src="loupe.png" alt="search" class="toolbar">
                        </div>
                    </button>


                </div>
            </div>
        </div>


        <div style="padding-top:70px ;">
            <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
            <div id="main" style="width:100%;height:200px;float:0 auto;background-color:aliceblue "></div>
            <script>
                // 基于准备好的dom，初始化echarts实例
                var myChart = echarts.init(document.getElementById('main'));

                // 指定图表的配置项和数据
                var option = {
                    legend: {},
                    tooltip: {},
                    dataset: {
                        dimensions: ['product', '2015', '2016', '2017'],
                        source: [{
                                product: 'packet',
                                '2015': 43.3,
                                '2016': 85.8,
                                '2017': 93.7
                            },
                            {
                                product: 'network',
                                '2015': 83.1,
                                '2016': 73.4,
                                '2017': 55.1
                            },
                            {
                                product: 'frame',
                                '2015': 86.4,
                                '2016': 65.2,
                                '2017': 82.5
                            },
                            {
                                product: 'TCP',
                                '2015': 72.4,
                                '2016': 53.9,
                                '2017': 39.1
                            }
                        ]
                    },
                    xAxis: {
                        type: 'category'
                    },
                    yAxis: {},
                    // Declare several bar series, each will be mapped
                    // to a column of dataset.source by default.
                    series: [{
                            type: 'bar'
                        },
                        {
                            type: 'bar'
                        },
                        {
                            type: 'bar'
                        }
                    ]
                };

                // 使用刚指定的配置项和数据显示图表。
                myChart.setOption(option);
            </script>

            <div>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">NO.</th>
                            <th scope="col">Type</th>
                            <th scope="col">Start Time</th>
                            <th scope="col">Stop Time</th>
                            <th scope="col">Source IP</th>
                            <th scope="col">Source Port</th>
                            <th scope="col">Destination IP</th>
                            <th scope="col">Packet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // require_once __DIR__ . '/vendor/autoload.php';

                        // $collection = (new MongoDB\Client)->cgudb->connection_collection;
                        // $document = $collection->find();

                        // foreach($document as $index){
                        //     $str ='<tr>
                        //     <td>'.$document['_id'].'</td>
                        //     <td>'.$document['Connection_Type'].'</td>
                        //     <td>'.$document['time'].'</td>
                        //     <td>'.$document['time'].'</td>
                        //     <td>'.$document['Source_IP'].'</td>
                        //     <td>'.$document['Source_Port'].'</td>
                        //     <td>'.$document['Destination_IP'].'</td>
                        //     <td>'.$document['A2Bpacket'].'</td>
                        //     </tr>';

                        //     echo $str;
                        // }   
                        ?>
                        <?php
                        // require_once __DIR__ . '/vendor/autoload.php';

                        // $collection = (new MongoDB\Client)->cgudb->connection_collection;
                        // $document = $collection->find();
                        // foreach ($document as $index => $row) {
                        //     $str = '<tr>
                        //              <td>' . $index . '</td>
                        //              <td>' . $row['Connection_Type'] . '</td>
                        //              <td>' . $row['time'] . '</td>
                        //              <td>' . $row['time'] . '</td>
                        //              <td>' . $row['Source_IP'] . '</td>
                        //              <td>' . $row['Source_Port'] . '</td>
                        //              <td>' . $row['Destination_IP'] . '</td>
                        //              <td>' . $row['A2Bpacket'] . '</td>
                        //              </tr>';

                        //     echo $str;
                            //var_dump($row['Connection_Type']);
                        // }



                        ?>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-hover table-sm">
                        <thead>

                        </thead>
                        <tbody id="tbody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
                <div>
                    <label for="IP">IPv4:</label>
                    <input type="text" name="IP" placeholder="163.25.50.20">
                </div>
                <div>
                    <label for="Port">Port:</label>
                    <input type="text" name="Port" placeholder="22">
                </div>
                <div>

                    <input type="checkbox" name="TCP">
                    <label for="TCP">TCP</label>
                    <input type="checkbox" name="UDP">
                    <label for="UDP">UDP</label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div class="spinner-border" role="status">
  <span class="sr-only">Loading...</span>
</div>

<script>
    $.ajax({
        type: 'POST',
        url: 'index.html',
        data: {
            name: 'name',
            password: 'password'
        },
        success: function(msg) {
            console.log(msg["password"]);
        }
    });
    $name = $_POST["name"];
    console.log($name);
</script>