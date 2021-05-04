<?php
 $id = $_GET["key_packet"];
 $id_detail = $_GET["key_conntopacket"];
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

    </head>
    <body>
        <div id="item">
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
    function read(){
        $.ajax({
            type: 'POST',
            url: 'db_process.php',
            data: {
                type: "PacketToDetail",
                _id : "<?=$id?>"
            },
            success: function(msg) {
                console.log(msg);
                $("#detailtable").html(msg);
               
            }
        })

    }


   read();
</script>
