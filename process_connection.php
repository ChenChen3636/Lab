<?php
$data = $_GET;
//var_dump($data);
?>
<html>

<head>
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

    <!-- jQuery and JS bundle w/ Popper.js -->
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script> -->
    <!-- JavaScript Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

    <!--自訂css-->
    <!-- <link rel="stylesheet" href="./css/flash.css"> -->

    <style>
        #item {
            height: calc(100vh);
            overflow: auto;
        }

        thead tr th {
            position: sticky;
            top: 0;
            z-index: 2;
            background-color: white;
        }

        .field_hide {
            display: none;
        }
    </style>

</head>

<body>
    <div>
        <select id="select_col" class="form-control" style="width: 20px;">
            <option>Maximum_TimeInterval</option>
            <option>Minimum_TimeInterval</option>
            <option>Average_TimeInterval</option>
            <option>Maximum_A2Bbytes</option>
            <option>Maximum_B2Abytes</option>
            <option>Minimum_A2Bbytes</option>
            <option>Minimum_B2Abytes</option>
            <option>Maximum_bytes</option>
            <option>Minimum_bytes</option>
            <option>Average_bytes</option>
        </select>
    </div>
    <div id="item">
        <table class="table table-hover" id="Ctable">
            <thead>
                <tr class="clickable-row">
                    <th scope="col">NO.</th>
                    <th scope="col">Protocol</th>
                    <th scope="col">Start Time</th>
                    <th scope="col">Duration (s)</th>
                    <th scope="col">Src. IP</th>
                    <th scope="col">Dest. IP</th>
                    <th scope="col">Src. Port</th>
                    <th scope="col">Dest. Port</th>
                    <th scope="col">Packet</th>
                    <th scope="col">error</th>
                    <th scope="col" class="Maximum_TimeInterval">Maximum_TimeInterval
                        <img class="visibility" src="./icon/visibility.png" alt="" style="height: 20px;width:20px;">
                    </th>
                    <th scope="col" class="Minimum_TimeInterval">Minimum_TimeInterval
                        <img class="visibility" src="./icon/visibility.png" alt="" style="height: 20px;width:20px;">
                    </th>
                    <th scope="col" class="Average_TimeInterval">Average_TimeInterval
                        <img class="visibility" src="./icon/visibility.png" alt="" style="height: 20px;width:20px;">
                    </th>

                    <!-- <th scope="col">error</th> -->
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</body>

</html>
<script>
    read();
    var status = 0;
    var count = 0;
    var limit = {
        "count": 0,
        "end": 200,
        "start": 0
    };

    $(function() {
        //當 #test 中捲軸捲動時

        $('#item').scroll(function() {

            var $this = $(this);
            var top = $this.scrollTop();
            var div_h = $("#item").height();
            var table_h = $("#item>table").height();

            if (top >= table_h - div_h && status == 0) {
                read();
            } else {
                countNum();

            }


            console.log(top, div_h, table_h, status);
        });
    });


    function countNum() {

        count = $('#item tr:last').find('td:first-child').text();
        console.log(count);
    }
    //---------------------------------------------AJAX-----------------------------------------------
    function read() {
        status = 1;
        var s_data = {
            type: "connection",
            status: status,
            skip: count,
        };
        var data = <?= json_encode($data) ?>;
        console.log(data);
        var vdata = JSON.parse(JSON.stringify(data));
        console.log(vdata);
        //先做判斷是否有條件傳入，如果沒有就用預設值
        if (data != "") {
            vdata["status"] = status;
            vdata["skip"] = count;
            s_data = vdata;
        }
        $.ajax({
            type: 'POST',
            url: 'db_process.php',
            data: s_data,
            async: true,
            success: function(msg) {
                console.log(msg);
                $("tbody").append(msg);
                status = 0;
                $('[data-toggle="popover"]').popover()
            }
        });

    }


    $(function() {
        $("#Ctable>tbody").on("click", ".example-popover", function() {
            $(this).popover();
        });
        $(".visibility").on("click", function() {
            var col_class = $(this).parent().attr("class");
            $("." + col_class).css("display", "none");

        })
        $("#select_col").change(function() {
            var txt = $("#select_col").find("option:selected").text();
            $("." + txt).css("display", "table-cell");
        })
    })
</script>