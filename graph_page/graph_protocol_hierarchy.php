<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


$json_string = file_get_contents("./graph_driver/data/d3js_protocol_hierarchy.json");
$timeRange_ary = json_decode($json_string, true);


$timeRange_data = "";

foreach ($timeRange_ary as $key => $value)
{
    switch ( $key ) {
        case 'dataTime' :
          $timeRange_data = $value;
            break;
    }
}

//echo $timeRange_data;

//echo "<script>console.log('" . $timeRange_data. "' );</script>";

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

    text {
    font: 12px sans-serif;
    }

    rect.background {
    fill: white;
    }

    .axis {
    shape-rendering: crispEdges;
    }

    .axis path,
    .axis line {
    fill: none;
    stroke: #000;
    }


    .card-header {
      padding: .3rem 1.0rem;
    }

    .card-body {
        padding: 0.5rem;
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
                <td><p><a class="btn btn-primary" data-toggle="collapse" href="#set_condition" role="button" aria-expanded="false" >Search</a></p></td>
            </tr>
          </table>
        </div>

        <div class="col" style="text-align:center; display:flex;justify-content: center">
            <div class="card text-white bg-secondary mb-3" style="max-width: 22rem;">
              <div class="card-body">
                <p class="card-text"><?php echo $timeRange_data?></p>
              </div>
            </div>
          </div>

        <div class="col" style="text-align:right;">
          <button type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="left" title="Protocol hierarchy" data-html="true" data-content="data range: <?php echo $timeRange_data?>">
            <label></label>
          </button>
        </div>

      </div>
    </div>

    <div class="collapse" id="set_condition">
      <div class="card card-body">

        <form action="./graph_driver/dataFilter_hierarchy.php" method="GET">

          <div class="form-group">
            <h5><label for="data_time_range">Time range</label></h5>
            <input type="text" class="form-control" name="daterange_hierarchy"  value= "<?php echo $_GET["lastDate"]?>">
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
	  $('input[name="daterange_hierarchy"]').daterangepicker({
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


<script src="//d3js.org/d3.v3.min.js"></script>


<style>

text {
  font-family: sans-serif;
  font-size: 15px;
}

</style>

<script>

var margin = {top: 50, right: 200, bottom: 0, left: 200},
    width = 1280 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

var x = d3.scale.linear()
    .range([0, width]);

var barHeight = 20;

var color = d3.scale.ordinal()
    .range(["steelblue", "#ccc"]);

var duration = 750,
    delay = 25;

var partition = d3.layout.partition()
    .value(function(d) { return d.size; });

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("top");

var svg = d3.select("body").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
    .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");


    svg.append("text")      // text label for the x axis
        .attr("x", (width)/2 )
        .attr("y",  -35)
        .style("text-anchor", "middle")
        .text("Bytes"); 


svg.append("rect")
    .attr("class", "background")
    .attr("width", width)
    .attr("height", height)
    .on("click", up);


svg.append("g")
    .attr("class", "x axis");


svg.append("g")
    .attr("class", "y axis")
  .append("line")
    .attr("y1", "100%");

d3.json("./graph_driver/data/d3js_protocol_hierarchy.json", function(error, root) {
  if (error) throw error;

  partition.nodes(root);
  x.domain([0, root.value]).nice();
  down(root, 0);
});

function down(d, i) {
  if (!d.children || this.__transition__) return;
  var end = duration + d.children.length * delay;

  // Mark any currently-displayed bars as exiting.
  var exit = svg.selectAll(".enter")
      .attr("class", "exit");

  // Entering nodes immediately obscure the clicked-on bar, so hide it.
  exit.selectAll("rect").filter(function(p) { return p === d; })
      .style("fill-opacity", 1e-6);

  // Enter the new bars for the clicked-on data.
  // Per above, entering bars are immediately visible.
  var enter = bar(d)
      .attr("transform", stack(i))
      .style("opacity", 1);

  // Have the text fade-in, even though the bars are visible.
  // Color the bars as parents; they will fade to children if appropriate.
  enter.select("text").style("fill-opacity", 1e-6);
  enter.select("rect").style("fill", color(true));

  // Update the x-scale domain.
  x.domain([0, d3.max(d.children, function(d) { return d.value/1000.0; })]).nice();

  // Update the x-axis.
  svg.selectAll(".x.axis")
      .transition()
      .duration(duration)
      .call(xAxis);

  // Transition entering bars to their new position.
  var enterTransition = enter.transition()
      .duration(duration)
      .delay(function(d, i) { return i * delay; })
      .attr("transform", function(d, i) { return "translate(0," + barHeight * i * 1.2 + ")"; });

  // Transition entering text.
  enterTransition.select("text")
      .style("fill-opacity", function(d) {
            if(d.value != 0){
                return 1
            }else{
                return 1
            }
        });

  // Transition entering rects to the new x-scale.
  enterTransition.select("rect")
      .attr("width", function(d) { return x(d.value/1000.0); })
      .style("fill", function(d) { return color(!!d.children); });



  // Transition exiting bars to fade out.
  var exitTransition = exit.transition()
      .duration(duration)
      .style("opacity", 1e-6)
      .remove();

  // Transition exiting bars to the new x-scale.
  exitTransition.selectAll("rect")
      .attr("width", function(d) { return x(d.value/1000.0); });

  // Rebind the current node to the background.
  svg.select(".background")
      .datum(d)
    .transition()
      .duration(end);

  d.index = i;
}

function up(d) {
  if (!d.parent || this.__transition__) return;
  var end = duration + d.children.length * delay;

  // Mark any currently-displayed bars as exiting.
  var exit = svg.selectAll(".enter")
      .attr("class", "exit");

  // Enter the new bars for the clicked-on data's parent.
  var enter = bar(d.parent)
      .attr("transform", function(d, i) { return "translate(0," + barHeight * i * 1.2 + ")"; })
      .style("opacity", 1e-6);

  // Color the bars as appropriate.
  // Exiting nodes will obscure the parent bar, so hide it.
  enter.select("rect")
      .style("fill", function(d) { return color(!!d.children); })
    .filter(function(p) { return p === d; })
      .style("fill-opacity", 1e-6);

  // Update the x-scale domain.
  x.domain([0, d3.max(d.parent.children, function(d) { return d.value/1000.0; })]).nice();

  // Update the x-axis.
  svg.selectAll(".x.axis").transition()
      .duration(duration)
      .call(xAxis);

  // Transition entering bars to fade in over the full duration.
  var enterTransition = enter.transition()
      .duration(end)
      .style("opacity", 1);

  // Transition entering rects to the new x-scale.
  // When the entering parent rect is done, make it visible!
  enterTransition.select("rect")
      .attr("width", function(d) { return x(d.value/1000.0); })
      .each("end", function(p) { if (p === d) d3.select(this).style("fill-opacity", null); });

  // Transition exiting bars to the parent's position.
  var exitTransition = exit.selectAll("g").transition()
      .duration(duration)
      .delay(function(d, i) { return i * delay; })
      .attr("transform", stack(d.index));

  // Transition exiting text to fade out.
  exitTransition.select("text")
      .style("fill-opacity", 1e-6);

  // Transition exiting rects to the new scale and fade to parent color.
  exitTransition.select("rect")
      .attr("width", function(d) { return x(d.value/1000.0); })
      .style("fill", color(true));

  // Remove exiting nodes when the last child has finished transitioning.
  exit.transition()
      .duration(end)
      .remove();

  // Rebind the current parent to the background.
  svg.select(".background")
      .datum(d.parent)
    .transition()
      .duration(end);
}

// Creates a set of bars for the given data node, at the specified index.
function bar(d) {
  var bar = svg.insert("g", ".y.axis")
      .attr("class", "enter")
      .attr("transform", "translate(0,5)")
    .selectAll("g")
      .data(d.children)
    .enter().append("g")
      .style("cursor", function(d) { return !d.children ? null : "pointer"; })
      .on("click", down);

  bar.append("text")
      .attr("x", -6)
      .attr("y", barHeight / 2)
      .attr("dy", ".35em")
      .style("text-anchor", "end")
      .text(function(d) {
        if(d.value/1000.0 > 1000.0){
          var text_hl = d.value/1000000.0;
          text_hl = text_hl.toFixed(2);
          return d.name+" ("+text_hl+" MB)"; 
        }else{
          return d.name+" ("+d.value/1000.0+" KB)"; 
        }
        
      });

  bar.append("rect")
      .attr("width", function(d) { return x(d.value/1000.0); })
      .attr("height", barHeight);

  return bar;
}

// A stateful closure for stacking bars horizontally.
function stack(i) {
  var x0 = 0;
  return function(d) {
    var tx = "translate(" + x0 + "," + barHeight * i * 1.2 + ")";
    x0 += x(d.value/1000.0);
    return tx;
  };
}

</script>