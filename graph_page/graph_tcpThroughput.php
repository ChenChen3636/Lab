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

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>

    body { font: 12px Arial;}

    path { 
        stroke: steelblue;
        stroke-width: 2;
        fill: none;
    }

    .axis path,
    .axis line {
        fill: none;
        stroke: grey;
        stroke-width: 1;
        shape-rendering: crispEdges;
    }

    </style>

</head>
<body>
  
<div  style="border:10px rgba(255,255,255,0) solid; ">

    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <table>
            <tr>
                <td><p><a class="btn btn-primary btn-sm" data-toggle="collapse" href="#set_condition" role="button" aria-expanded="false" >SET condition</a></p></td>
            </tr>
          </table>
        </div>

        <div class="col" style="text-align:right;">
          <button type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="left" title="Protocol hierarchy" data-html="true" data-content="">
            <img src="./img/graphic_information.png" alt="information"></img>
          </button>
        </div>

      </div>
    </div>

    <div class="collapse" id="set_condition">
      <div class="card card-body">

        <form action="./graph_driver/dataFilter_tcpThroughput.php" method="GET">

          <div class="form-group">
            <h5><label for="data_time_range">Time range</label></h5>
            <input type="text" class="form-control" name="daterange_throughput"  value= "<?php echo $_GET["lastDate"]?>">
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
	  $('input[name="daterange_throughput"]').daterangepicker({
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
    $('[data-toggle="popover"]').popover();   
  });

</script>


<script src="//d3js.org/d3.v3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.17/d3.min.js"></script>

<style>

text {
  font-family: sans-serif;
  font-size: 15px;
}

</style>



<script>

var margin = {top: 30, right: 100, bottom: 30, left: 100},
    width = 1680 - margin.left - margin.right,
    height = 640 - margin.top - margin.bottom;

var parseDate = d3.time.format("%Y-%m-%dT%H:%M").parse;

var x = d3.time.scale().range([0, width]);
var y0 = d3.scale.linear().range([height, 0]);
var y1 = d3.scale.linear().range([height, 0]);



var xAxis = d3.svg.axis().scale(x)
    .orient("bottom").ticks(20);

var yAxisLeft = d3.svg.axis().scale(y0)
    .orient("left").ticks(10);

var yAxisRight = d3.svg.axis().scale(y1)
    .orient("right").ticks(10); 

var valueline = d3.svg.line()
    .x(function(d) { return x(d.date); })
    .y(function(d) { return y0(d.throughput/1000.0); });
    
var valueline2 = d3.svg.line()
    .x(function(d) { return x(d.date); })
    .y(function(d) { return y1(d.pkt_num); });
  
var svg = d3.select("body")
        .append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    svg.append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 0 - margin.left)
      .attr("x",0 - (height / 2))
      .attr("dy", "3em")
      .style("text-anchor", "middle")
      .text("Throughput (KB)");  

    svg.append("text")
      .attr("transform", "rotate(90)")
      .attr("y", 0 - (width + margin.right))
      .attr("x", (height/2))
      .attr("dy", "3em")
      .style("text-anchor", "middle")
      .text("Packets");  


// Get the data
d3.csv("./graph_driver/data/d3js_TCPthroughput.csv", function(error, data) {
    data.forEach(function(d) {
        d.date = parseDate(d.date);
        d.throughput = +d.throughput;
        d.pkt_num = +d.pkt_num;
    });

    // Scale the range of the data
    x.domain(d3.extent(data, function(d) { return d.date; }));
    y0.domain([0, d3.max(data, function(d) {
		return Math.max(d.throughput/1000.0); })]); 
    y1.domain([0, d3.max(data, function(d) { 
		return Math.max(d.pkt_num); })]);

    svg.append("path")        // Add the valueline path.
        .attr("d", valueline(data));

    svg.append("path")        // Add the valueline2 path.
        .style("stroke", "red")
        .attr("d", valueline2(data));

    svg.append("g")            // Add the X Axis
        .attr("class", "x axis")
        .attr("transform", "translate(0," + height + ")")
        .call(xAxis);

    svg.append("g")
        .attr("class", "y axis")
        .style("fill", "steelblue")
        .call(yAxisLeft);	

    svg.append("g")				
        .attr("class", "y axis")	
        .attr("transform", "translate(" + width + " ,0)")	
        .style("fill", "red")		
        .call(yAxisRight);

});

</script>