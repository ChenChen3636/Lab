<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>

<!DOCTYPE html>
<meta charset="utf-8">
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
  font-size: 12px;
}

</style>
<svg width="1500" height="1280"></svg>    <!-- width="960" height="600" -->
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

d3.json("./graph_driver/data/d3js_dnsAnsNS.json", function(error, graph) {
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
          return 7; 
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
      .text(function(d) {return d.node_attr;})
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