<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta http-equiv="Pragma" Content="no-cache" />
    <meta http-equiv="Expires" Content="0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>


    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


</head>
<body>
  
<div  style="border:10px rgba(255,255,255,0) solid; ">

    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <p><a class="btn btn-primary btn-sm" data-toggle="collapse" href="#set_condition" role="button" aria-expanded="false">SET condition</a></p>
        </div>

        <div class="col" style="text-align:right;">
          <button type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="left" title="Number of destination connection" data-html="true" data-content="Bubble: number of conncetion">
            <img src="./img/graphic_information.png" alt="information"></img>
          </button>
        </div>

      </div>
    </div>

    <div class="collapse" id="set_condition">
      <div class="card card-body">

        <form action="./graph_driver/dataFilter_bubbleCon.php" method="GET">
          <div class="form-group">
            <h5><label for="data_time_range">Time range</label></h5>
            <input type="text" class="form-control" name="daterange_bubbleCon"  value= "<?php echo $_GET["lastDate"]?>">
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col">
                <h5><label for="Max_num_nodes">Maximum number of nodes display</label></h5>
                <input type="text" class="form-control" name="Max_num_nodes"  value = 50 required>
              </div>

            </div>
          </div>

          
          <div class="form-group">
            <div class="card-body">
              <h5 class="card-header"><input type="checkbox" onchange="document.getElementById('srcIpFilter_ip').disabled = !this.checked; document.getElementById('desIpFilter_ip').disabled = !this.checked;" name='ipCheckbox'/> IPv4 filter</h5>
              
                <label for="srcIp_filter">Source (if not specify, input "n.n.n.n")</label>
                <input type="text" class="form-control" name="srcIpFilter_ip"  id="srcIpFilter_ip" minlength="7" maxlength="15" size="15" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])|(n\.n\.n\.n)$" disabled="disabled" placeholder="XXX . XXX . XXX . XXX" required>
              
                <label for="desIp_filter">Destination (if not specify, input "n.n.n.n")</label>
                <input type="text" class="form-control" name="desIpFilter_ip"  id="desIpFilter_ip" minlength="7" maxlength="15" size="15" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])|(n\.n\.n\.n)$" disabled="disabled" placeholder="XXX . XXX . XXX . XXX" required>
              
            </div>
          </div>

          <table>
          	<tr>
          		<td><button type="submit" class="btn btn-primary btn" id="submit_graph">Submit</button></td>
          		<td>&nbsp;&nbsp;</td>
          		<td><div id="loader" style="display:none;">data processing...<image id="graph_loading" src="./img/loading.gif"></image></div></td>
          	</tr>
          </table>

        </form>

      </div>
    </div>
   
</div>






</body>
</html>



<script>
/*
	$(document).ready(function() {							//refresh this page to update data
    	if(location.href.indexOf("#reloaded")==-1){
        	location.href=location.href+"#reloaded";
        	location.reload();
    	}
 	})
*/

	$('#submit_graph').click(function () {
		var loading_gif= document.getElementById('loader');
		loading_gif.removeAttribute('style');
    })


	$(function() {
	  $('input[name="daterange_bubbleCon"]').daterangepicker({
	    opens: "left",
	    timePicker: true,
	    timePicker24Hour: true,
	    linkedCalendars: false,
	    autoUpdateInput: true,
	    locale: {
	      format: "YYYY-MM-DD HH:mm",
	      separator: " ~ ",
	      applyLabel: "select",
	      resetLabel: "reset",
	    }
	  }, function(start, end, label) {
	    console.log(this.startDate.format(this.locale.format));
	    console.log(this.endDate.format(this.locale.format));
	  });
	});

    $(document).ready(function(){
        $('[data-toggle="popover"]').popover('show');   
    });
</script>



<script type="text/javascript" src="https://d3js.org/d3.v4.min.js"></script>
<script type="text/javascript">

        //src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"
        var jsonData = (function() {
            var json = null;
            $.ajax({
                'async': false,
                'global': false,
                'url': "./graph_driver/data/d3js_bubble_ipAB.json",
                'dataType': "json",
                'success': function(data) {
                    json = data;
                }
            });
            return json;
        })();


        var diameter = 1080;
        var color = d3.scaleOrdinal(d3.schemeCategory20);

        var bubble = d3.pack()
            .size([diameter, diameter])
            .padding(1.5);

        var svg = d3.select("body")
            .append("svg")
            .attr("width", diameter)
            .attr("height", diameter)
            .attr("class", "bubble");

        var nodes = d3.hierarchy(jsonData).sum(function(d) {      //fix
          if(d.des_link_num > 3)
            return d.des_link_num;
          else
            return;
        });

        var node = svg.selectAll(".node")
            .data(bubble(nodes).descendants())
            .enter()
            .filter(function(d){
                return  !d.children;
            })
            .append("g")
            .attr("class", "node")
            .attr("transform", function(d) {
                return "translate(" + d.x + "," + d.y + ")";
            });

        node.append("title")
            .text(function(d) {
                return d.data.ip_addr + " (" + d.data.des_link_num + ")";
            });

        node.append("circle")
            .attr("r", function(d) {
                return d.r;
            })
            .style("fill", function(d,i) {
                return color(i);
            });

        node.append("text")
            .attr("dy", ".2em")
            .style("text-anchor", "middle")
            .text(function(d) {
                return d.data.ip_addr;
            })
            .attr("font-family", "sans-serif")
            .attr("font-size", function(d){
                return d.r/4;
            })
            .attr("fill", "white");


        d3.select(self.frameElement)
            .style("height", diameter + "px");



</script>