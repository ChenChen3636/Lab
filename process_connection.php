<?php
    $data = $_GET;
    //var_dump($data);
?>
<html>
    <head>
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <!-- jQuery and JS bundle w/ Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!--自訂css-->
    <!-- <link rel="stylesheet" href="./css/flash.css"> -->

    <style>
        #item{
            height: calc(100vh);
            overflow: auto;
        }
        thead tr th{
            position: sticky;
            top: 0;
            z-index: 2;
            background-color: white;
        }

    </style>

    </head>
    <body>
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
    
   $(function(){
       //當 #test 中捲軸捲動時

        $('#item').scroll(function(){
            
            var $this = $(this);
            var top = $this.scrollTop();
            var div_h = $("#item").height();
            var table_h = $("#item>table").height();

            if(top >= table_h-div_h && status == 0 ){                
               read();
            }
            else{
                countNum();
               
            }
            
        
            console.log(top, div_h, table_h,status);
        });
   });
    

    function countNum(){
        
        count = $('#item tr:last').find('td:first-child').text();
        console.log(count);
    }
//---------------------------------------------AJAX-----------------------------------------------
    function read(){
        status = 1;
        var s_data = {
                        type: "connection",
                        status: status,
                        skip: count,
                    };
        var data = <?=json_encode($data)?>;
        console.log(data);
        var vdata = JSON.parse(JSON.stringify(data));
        console.log(vdata);
        //先做判斷是否有條件傳入，如果沒有就用預設值
        if(data != ""){
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
            }
        });

    }
    
  

</script>

