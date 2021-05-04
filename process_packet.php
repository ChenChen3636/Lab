<?php
$pdata = $_GET;
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
        #detail{
            overflow: auto;
            height: 100px;
        }
        #packet{
            overflow : auto;
            height: 500px;
        }
        tbody>tr{
            cursor: pointer;
        }
        thead tr th{
            position: sticky;
            top: 0;
            z-index: 2;
            background-color: white;
        }
        .on{
            background-color: #ccc;
        }
    </style>

    </head>
    <body>
        <div id="packet">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">NO.</th>
                        <th scope="col">Arrival Time</th>
                        <th scope="col">Protocol</th>
                        <th scope="col">Src. IP</th>
                        <th scope="col">Dest. IP</th>
                        <th scope="col">Src. MAC</th>
                        <th scope="col">Dest.MAC</th>
                        <th scope="col">Src Port</th>
                        <th scope="col">Dest. Port</th>
                        <th scope="col">Error</th>
                    </tr>
                </thead>
                <tbody id="pktbody">
                </tbody>
            </table>
        </div>
        <div id="detail">
        <table class="table table-hover" id="detailtable">
                <thead>
                </thead>
                <tbody id="detailbody">
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

        $('#packet').scroll(function(){
            
            var $this = $(this);
            var top = $this.scrollTop();
            var div_h = $("#packet").height();
            var table_h = $("#packet>table").height();

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
        
        count = $('#packet tr:last').find('td:first-child').text();
        console.log(count);
    }

    function read(){
        status = 1;
        var pdata = {
                        type:"packet",
                        status: status,
                        skip: count,
                    };
        var s_pdata = <?=json_encode($pdata)?>;
        var v_data = JSON.parse(JSON.stringify(s_pdata));
        console.log(v_data);
        if(v_data != ""){
            v_data["status"] = status;
            v_data["skip"] = count;
            pdata = v_data;
        }
        $.ajax({
            type: 'POST',
            url: 'db_process.php',
            data: pdata,
            success: function(msg) {
                //console.log(msg);
                $("#pktbody").append(msg);
                status = 0;
            }
        })

        $("thead").on("click","tr",function(){
            $(this).addClass("on");
        })
        
        $("tbody").on("click","tr",function(){
            
            var pid = $(this).attr("pid");

            var trs = $(this).parent().find("tr"); 
 				if(trs.hasClass("on")){ 
				    trs.removeClass("on");
				}
				$(this).addClass("on");
           
            $.ajax({
                type: 'POST',
                url: 'db_process.php',
                data: {
                    type: "PacketToDetail",
                    _id : pid,
                },
                async: false,
                success: function(msg) {
                    console.log(msg);
                    $("#detailtable").html(msg); 
                }
            })

           
        })
    }
       
</script>

