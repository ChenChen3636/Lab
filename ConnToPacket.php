<?php
 $id = $_GET["key_conn"];
 $id_detail = $_GET["key_conntopacket"];
 $select = $_GET["select"];
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
    <!-- <link rel="stylesheet" href="./flash.css"> -->
    <style>
        thead tr th{
            position: sticky;
            z-index: 2;
            top: 0;
            background-color: white;
        }
        #ab{
            position: fixed;
            top: 0;
            z-index: 3;
        }
        #tt{
            height: 500px;
            overflow: auto;
            margin-top: 30px;

        }
        #detail{
            height: 100px;
            overflow: auto;
        }
        tbody>tr{
            cursor: pointer;
        }

        .on{
            background-color: #ccc;
        }

    </style>
    </head>
    <body>
        <div id="ab">
            <a  href="javascript:history.back()"><img src="./icon/back.png" style="height: 30px;width:30px;"alt=""></a>
        </div>
        <div id="tt">
            <table class="table table-hover">
                <thead>
                    <tr>
                    <th scope="col">NO.</th>
                        <th scope="col">Arrival Time</th>
                        <th scope="col">Protocol</th>
                        <th scope="col">Src. IP</th>
                        <th scope="col">Dest. IP</th>
                        <th scope="col">Src. MAC</th>
                        <th scope="col">Dest. MAC</th>
                        <th scope="col">Src. Port</th>
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
                <tbody>
                </tbody>
            </table>
        </div>
    </body>
</html>
<script>
            $.ajax({
            type: 'POST',
            url: 'db_process.php',
            data: {
                type: "ConnToPacket",
                id : "<?=$id?>",
            },
            success: function(msg) {
                console.log(msg);
                $("#pktbody").html(msg);
            }
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




</script>
