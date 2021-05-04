<html>
  <head>
    <title>Calendar Heatmap</title>
    <style>
      .body {
        height: 97%;
      }

      svg {
        height: 1800;
        width: 97%;
      }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/5.9.2/d3.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3-color/1.2.1/d3-color.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  </head>
  <body>
    <h4>Daily of internet traffic</h4>
    <svg id="svg"></svg>


    <script>
      
        var jsonData = (function() {
            var json = null;
            $.ajax({
                'async': false,
                'global': false,
                'url': "./graph_driver/data/d3js_calendar_conNum.json",
                'dataType': "json",
                'success': function(data) {
                    json = data;
                }
            });
            return json;
        })();


        jsonData.sort((a, b) => new Date(a.Date) - new Date(b.Date));

      const dateValues = jsonData.map(dv => ({
        date: d3.timeDay(new Date(dv.Date)),
        value: Number(dv.conBytes)
      }));

      const svg = d3.select("#svg");
      const { width, height } = document
        .getElementById("svg")
        .getBoundingClientRect();
      /*  
      svg.selectAll(".month")
        .data(function(d) { return d3.time.months(new Date(d, 0, 1), new Date(d + 1, 0, 1)); })
        .enter().append("path")
        .attr("class", "month")
        .attr("id", function(d,i){ return month[i] })
        .attr("d", monthPath);


      month = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']

      legend.append("text")
       .attr("class", function(d,i){ return month[i] })
       .style("text-anchor", "end")
       .attr("dy", "-.25em")
       .text(function(d,i){ return month[i] });
       */

      function draw() {
        const years = d3
          .nest()
          .key(d => d.date.getUTCFullYear())
          .entries(dateValues)
          .reverse();

        const values = dateValues.map(c => c.value);
        const maxValue = d3.max(values);
        const minValue = d3.min(values);

        const cellSize = 15;
        const yearHeight = cellSize * 7;

        const group = svg.append("g");

        const year = group
          .selectAll("g")
          .data(years)
          .join("g")
          .attr(
            "transform",
            (d, i) => `translate(50, ${yearHeight * i + cellSize * 1.5})`
          );

        year
          .append("text")
          .attr("x", -5)
          .attr("y", -30)
          .attr("text-anchor", "end")
          .attr("font-size", 16)
          .attr("font-weight", 550)
          .attr("transform", "rotate(270)")
          .text(d => d.key);

        const formatDay = d =>
          ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"][d.getUTCDay()];
        const countDay = d => d.getUTCDay();
        const timeWeek = d3.utcSunday;
        const formatDate = d3.utcFormat("%Y/%m/%d");    //labe date format
        const colorFn = d3
          .scaleSequential(d3.interpolateBuGn)
          .domain([Math.floor(minValue), Math.ceil(maxValue)]);
        const format = d3.format("+.2%");

        year
          .append("g")
          .attr("text-anchor", "end")
          .selectAll("text")
          .data(d3.range(7).map(i => new Date(1995, 0, i)))
          .join("text")
          .attr("x", -5)
          .attr("y", d => (countDay(d) + 0.5) * cellSize)
          .attr("dy", "0.31em")
          .attr("font-size", 12)
          .text(formatDay);

        year
          .append("g")
          .selectAll("rect")
          .data(d => d.values)
          .join("rect")
          .attr("width", cellSize - 1.5)
          .attr("height", cellSize - 1.5)
          .attr(
            "x",
            (d, i) => timeWeek.count(d3.utcYear(d.date), d.date) * cellSize + 10
          )
          .attr("y", d => countDay(d.date) * cellSize + 0.5)
          .attr("fill", d => colorFn(d.value))
          .append("title")
          .text(d => `${formatDate(d.date)}:  ${(d.value/1000000).toFixed(2)} MB (${d.value})`);   //lable show string

        const legend = group
          .append("g")
          .attr(
            "transform",
            `translate(10, ${years.length * yearHeight + cellSize * 4})`
          );

        const categoriesCount = 10;
        const categories = [...Array(categoriesCount)].map((_, i) => {
          const upperBound = (maxValue / categoriesCount) * (i + 1);
          const lowerBound = (maxValue / categoriesCount) * i;

          return {
            upperBound,
            lowerBound,
            color: d3.interpolateBuGn(upperBound / maxValue),
            selected: true
          };
        });

        const legendWidth = 60;

        function toggle(legend) {
          const { lowerBound, upperBound, selected } = legend;

          legend.selected = !selected;

          const highlightedDates = years.map(y => ({
            key: y.key,
            values: y.values.filter(
              v => v.value > lowerBound && v.value <= upperBound
            )
          }));

          year
            .data(highlightedDates)
            .selectAll("rect")
            .data(d => d.values, d => d.date)
            .transition()
            .duration(500)
            .attr("fill", d => (legend.selected ? colorFn(d.value) : "white"));
        }

        legend
          .selectAll("rect")
          .data(categories)
          .enter()
          .append("rect")
          .attr("fill", d => d.color)
          .attr("x", (d, i) => legendWidth * i)
          .attr("width", legendWidth)
          .attr("height", 15)
          .on("click", toggle);

        legend
          .selectAll("text")
          .data(categories)
          .join("text")
          .attr("transform", "rotate(90)")
          .attr("y", (d, i) => -legendWidth * i)
          .attr("dy", -30)
          .attr("x", 18)
          .attr("text-anchor", "start")
          .attr("font-size", 11)
          .text(d => `${d.lowerBound.toFixed(0)} - ${d.upperBound.toFixed(0)}`);
        /*
        legend
          .append("text")
          .attr("dy", -5)
          .attr("font-size", 14)
          .attr("text-decoration", "underline")
          .text("Click on category to select/deselect days");
        */
      }

      draw();
    </script>
  </body>
</html>