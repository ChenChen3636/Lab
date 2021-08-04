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
</head>
<body>  
  <div  style="border:10px rgba(255,255,255,0) solid; ">

    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <p><a class="btn btn-primary btn-sm" data-toggle="collapse" href="#set_condition" role="button" aria-expanded="false">SET condition</a></p>
        </div>

        <div class="col" style="text-align:right;">
          <button type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="left" title="DNS query hostname" data-html="true" data-content="Node(bule): source ip address<br>Node(orange): server hostname<br>Link: number of connection">
            <img src="./img/graphic_information.png" alt="information"></img>
          </button>
        </div>

      </div>
    </div>

    <div class="collapse" id="set_condition">
      <div class="card card-body">

        <form action="./graph_driver/dataFilter_dns.php" method="GET">
          <div class="form-group">
            <h5><label for="data_time_range">Time range</label><br></h5>
            <input type="text" class="form-control" name="daterange_dns"  value="<?php echo $_GET["lastDate"]?>" >
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col">
                <h5><label for="Max_num_nodes">Maximum number of nodes display</label></h5>
                <input type="text" class="form-control" name="Max_num_nodes"  value = 100 required>
              </div>
              <div class="col">
                <h5><label for="Percentage_label_display">Percentage of label to display</label></h5>
                <input type="range" class="form-control-range" id="per_lab_show" name="per_lab_show" min="0" max="100" value = 30 oninput="document.getElementById('rangeval').innerText = document.getElementById('per_lab_show').value+'%'">
                <h5><span style="color:black; font-weight:bold;" id="rangeval">30%</span></h5>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="card-body">
              <h5 class="card-header"><input type="checkbox" onchange="document.getElementById('srcIpFilter_dns').disabled = !this.checked; document.getElementById('srcIpFilter_dns').disabled = !this.checked;" name='ipCheckbox'/> IPv4 filter</h5>
              
                <label for="srcIp_filter">Source</label>
                <input type="text" class="form-control" name="srcIpFilter_dns"  id="srcIpFilter_dns" minlength="7" maxlength="15" size="15" pattern="^((\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.){3}(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])|(n\.n\.n\.n)$" disabled="disabled" placeholder="XXX . XXX . XXX . XXX" required>

            </div>
          </div>

          <table>
            <tr>
              <td><button type="submit" class="btn btn-primary btn" id="submit_graph">Submit</button></td>
              <td>&nbsp;</td>
              <td><div id="loader" style="display:none;">data processing...<image id="graph_loading" src="./img/loading.gif" ></image></div></td>
            </tr>
          </table>

        </form>

      </div>
    </div>
   
  </div>
</body>
</html>

<script>

  $('#submit_graph').click(function () {
    var loading_gif= document.getElementById('loader');
    loading_gif.removeAttribute('style');
  })
  
$(function() {
  $('input[name="daterange_dns"]').daterangepicker({
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


<!-- graphic -->
<style>

.links line {
  stroke: #999;
  stroke-opacity: 0.4;
}

.nodes circle {
  stroke: #fff;
  stroke-width: 1.5px;
}

text {
  font-family: sans-serif;
  font-size: 18px;
}

</style>
<svg width="1280" height="1080"></svg>    <!-- width="960" height="600" -->
<script src="https://d3js.org/d3.v4.min.js"></script>
<script>

var svg = d3.select("svg"),
    width = +svg.attr("width"),
    height = +svg.attr("height");

var color = d3.scaleOrdinal(d3.schemeCategory20);

var simulation = d3.forceSimulation()
    .force("link", d3.forceLink().id(function(d) { return d.node_attr; }))
    .force("charge", d3.forceManyBody().strength(-5))
    .force("collision", d3.forceCollide().radius(d => 30))
    .force("center", d3.forceCenter(width / 2, height / 2));

d3.json("./graph_driver/data/d3js_DNS.json", function(error, graph) {
  if (error) throw error;

  var link = svg.append("g")
      .attr("class", "links")
    .selectAll("line")
    .data(graph.links)
    .enter().append("line")
      .attr("stroke-width", function(d) { return Math.sqrt(d.con_num); });

  var node = svg.append("g")
      .attr("class", "nodes")
    .selectAll("g")
    .data(graph.nodes)
    .enter().append("g")
 
  //node info
  var circles = node.append("circle")
      .attr("r", function(d) {       //node size
        if(d.group == 1){
          return 7;
        }else{
          return 6+Math.pow(d.link_num, 1/3); 
        }
      })
      .attr("fill", function(d) {       //group node color
        if(d.group == 1){
          return "#66B3FF";	      //blue src ip
        }else if(d.group == 2){
          return "#FF9224";       //orange dns ip
        }else if(d.group == 3){
          return "#02C874";       //green dns_hostName ip
        }else if(d.group == 4){
          return "#FF0000";       //red not found
        }
      })
      .call(d3.drag()     //node?��?
          .on("start", dragstarted)
          .on("drag", dragged)
          .on("end", dragended));

  var lables = node.append("text")
      .text(function(d) {
        var node_msg = d.node_attr
        if(d.highlight == 1){
          return node_msg.substr(1);
        }
      })
      .attr('x', 6)
      .attr('y', 3);
      //.call(d3.drag()     //text?��?
          //.on("start", dragstarted)
          //.on("drag", dragged)
          //.on("end", dragended));

  node.append("title")
      .text(function(d) { return d.node_attr; });

  simulation
      .nodes(graph.nodes)
      .on("tick", ticked);

  simulation.force("link")
      .links(graph.links);

  function ticked() {
    link
        .attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });

    node
        .attr("transform", function(d) {
          return "translate(" + d.x + "," + d.y + ")";
        })
  }
});

function dragstarted(d) {
  if (!d3.event.active) simulation.alphaTarget(0.3).restart();
  d.fx = d.x;
  d.fy = d.y;
}

function dragged(d) {
  d.fx = d3.event.x;
  d.fy = d3.event.y;
}

function dragended(d) {
  if (!d3.event.active) simulation.alphaTarget(0);
  d.fx = null;
  d.fy = null;
}

</script>