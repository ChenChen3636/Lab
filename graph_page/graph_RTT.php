<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <script src="https://cdn.staticfile.org/echarts/4.3.0/echarts.min.js"></script>
</head>
<body>

    <div id="main" style="width: 1280px;height:720px;"></div>
    <script type="text/javascript">


        var chartDom = document.getElementById('main');
        var myChart = echarts.init(chartDom);
        var option;

        var base = +new Date(2021, 9, 3);
        var oneDay = 24 * 3600 * 1000;

        var data = [[base, Math.random() * 300]];

        for (var i = 1; i < 20000; i++) {
            var now = new Date(base += oneDay);
            data.push([
                [now.getFullYear(), now.getMonth() + 1, now.getDate()].join('/'),
                Math.round((Math.random() - 0.5) * 20 + data[i - 1][1])
            ]);
        }


        console.log(data);

        option = {
            tooltip: {
                trigger: 'axis',
                position: function (pt) {
                    return [pt[0], '10%'];
                }
            },
            title: {
                left: 'center',
                text: 'Round Trip Time',
            },
            toolbox: {
                feature: {
                    dataZoom: {
                        yAxisIndex: 'none'
                    },
                    restore: {},
                    saveAsImage: {}
                }
            },
            xAxis: {
                type: 'time',
                boundaryGap: false
            },
            yAxis: {
                type: 'value',
                boundaryGap: [0, '100%']
            },
            dataZoom: [{
                type: 'inside',
                start: 0,
                end: 20
            }, {
                start: 0,
                end: 20
            }],
            series: [
                {
                    name: 'rtt',
                    type: 'line',
                    smooth: true,
                    symbol: 'none',
                    data: data
                }
            ]
        };

        option && myChart.setOption(option);

    </script>
</body>
</html>