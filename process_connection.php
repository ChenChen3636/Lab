<?php
//$data = $_GET;
?>
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

        $('#connection_list').scroll(function() {

            var $this = $(this);
            var top = $this.scrollTop();
            var div_h = $("#connection_list").height();
            var table_h = $("#connection_list>table").height();

            if (top >= table_h - div_h && status == 0) {
                read();
            } else {
                countNum();

            }


            console.log(top, div_h, table_h, status);
        });
    });


    function countNum() {

        count = $('#connection_list tr:last').find('td:first-child').text();
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
                $("#connection_tbody").append(msg);
                status = 0;
                $('[data-toggle="popover"]').popover()
            }
        });

    }


    $(function() {
        $("#connection_table>tbody").on("click", ".example-popover", function() {
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