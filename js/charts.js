/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var id = d3.select("#theId").text();

drawnHIndexChart();
drawnPublicationsPerYearLineChart();

function drawnHIndexChart() {
    var margin = {top: 30, right: 20, bottom: 50, left: 50};
    var width = 1000 - margin.left - margin.right;
    var height = 550 - margin.top - margin.bottom;

    var xScale = d3.scale.linear().range([0, width]);
    var yScale = d3.scale.linear().range([height, 0]);

    var xAxis = d3.svg.axis().scale(xScale).orient("bottom").ticks(10);
    var yAxis = d3.svg.axis().scale(yScale).orient("left").ticks(10);

// if you want to add grid to axis comment 2 previous commants
// and uncomment 2 folowing commants !!!

    /*var xAxis = d3.svg.axis()
     .scale(xScale)
     .orient("bottom")
     .innerTickSize(-height)
     .outerTickSize(0)
     .tickPadding(10);
     
     var yAxis = d3.svg.axis()
     .scale(yScale)
     .orient("left")
     .innerTickSize(-width)
     .outerTickSize(0)
     .tickPadding(10);*/

    var area1 = d3.svg.area()
            .x(function (d) {
                return xScale(d.paper);
            })
            .y1(function (d) {
                return yScale(d.citations);
            });

    var area2 = d3.svg.area()
            .x(function (d) {
                return xScale(d.x);
            })
            .y1(function (d) {
                return yScale(d.y);
            });

    var line1 = d3.svg.line().x(function (d) {
        return xScale(d.paper);
    }).y(function (d) {
        return yScale(d.citations);
    });

    var line2 = d3.svg.line().x(function (d) {
        return xScale(d.x);
    }).y(function (d) {
        return yScale(d.y);
    });

    var svg = d3.select("#h-indexChart")
            .append("svg").attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    d3.json("./ajax/hindexData.php?id=" + id, function (error, data) {

        if (error) {
            throw error;
        }

        var line1Data = data.line1;
        var hIndex = data.hIndex;
        //console.log(line1Data);
        var line2Data = [];
        //console.log(line1Data.length);
        var maximumOfCitations = d3.max(line1Data, function (d) {
            return d.citations;
        });
        var maximum = Math.max(line1Data.length, maximumOfCitations);

        //console.log(maximum);
        var i;
        for (i = 0; i <= maximum; i++) {
            line2Data.push({"x": i, "y": i});
        }

        //console.log(maximum);
        //console.log(line2Data);
        var line2DataMin = d3.min(line2Data, function (d) {
            return d.x;
        });
        var line2DataMax = d3.max(line2Data, function (d) {
            return d.x;
        });
        var points = [];
        points.push({"x": line2DataMin, "y": line2DataMin});
        points.push({"x": line2DataMax, "y": line2DataMax});

        //console.log(points);

        xScale.domain([0, maximum + 5]);
        yScale.domain([0, maximum + 5]);

        area1.y0(yScale(0));

        area2.y0(yScale(0));

        //svg.append("path").attr("class", "line").attr("d", line1(line1Data));
        svg.append("path").attr("d", area1(line1Data)).attr("fill", "#ffe4b2");

        svg.append("path").attr("class", "line1").attr("d", line1(line1Data));

        svg.append("path").attr("d", area2(line2Data)).attr("fill", "#66ff33").attr("opacity", 0.3);

        svg.append("path").attr("class", "line2").attr("d", line2(line2Data));

        svg.append("text")
                .attr("transform", "translate(" + (width / 2) + " ," + (height + margin.bottom) + ")")
                .style("text-anchor", "middle")
                .text("Documents");


        svg.append("g").attr("class", "x axis").attr("transform", "translate(0," + height + ")").call(xAxis);

        svg.append("g").attr("class", "y axis").call(yAxis);

        svg.append("text")
                .attr("transform", "rotate(-90)")
                .attr("y", 0 - margin.left)
                .attr("x", 0 - (height / 2))
                .attr("dy", "1em")
                .style("text-anchor", "middle")
                .text("Namber of Citations");

        var hIndexPoint = svg.selectAll(".h-index")
                .data(hIndex)
                .enter().append("circle") // Uses the enter().append() method
                .attr("class", "h-index") // Assign a class for styling
                .attr("cx", function (d) {
                    return xScale(d.x);
                }).attr("cy", function (d) {
            return yScale(d.y);
        }).attr("r", 8).attr("opacity", 0.7);

        var citationsPoints = svg.selectAll(".dot")
                .data(line1Data)
                .enter().append("circle") // Uses the enter().append() method
                .attr("class", "dot") // Assign a class for styling
                .attr("cx", function (d) {
                    return xScale(d.paper);
                }).attr("cy", function (d) {
            return yScale(d.citations);
        }).attr("r", 4);

        svg.selectAll(".dot2")
                .data(points)
                .enter().append("circle") // Uses the enter().append() method
                .attr("class", "dot2") // Assign a class for styling
                .attr("cx", function (d) {
                    return xScale(d.x);
                }).attr("cy", function (d) {
            return yScale(d.y);
        }).attr("r", 3);

        citationsPoints.on('mouseover', cMouseOver);

        citationsPoints.on('mouseout', cMouseOut);

        hIndexPoint.on('mouseover', hMouseOver);

        hIndexPoint.on('mouseout', hMouseOut);

        function cMouseOver(c) {
            var xPosition = d3.event.pageX + 30;
            var yPosition = d3.event.pageY - 100;

            d3.select(this).style('fill', '#007bff').attr('r', 7);

            d3.select("#citations-tooltip")
                    .style("left", xPosition + "px")
                    .style("top", yPosition + "px")
                    .select("#paperTitle")
                    .text(c.title);

            d3.select("#citations-tooltip").select("#paperOrder").text(c.paper);
            d3.select("#citations-tooltip").select("#paperCitations").text(c.citations);
            d3.select("#citations-tooltip").classed("hiddenTooltip", false);
        }

        function cMouseOut(c) {
            citationsPoints.style('fill', null).attr('r', 4);
            d3.select("#citations-tooltip").classed("hiddenTooltip", true);
        }

        function hMouseOver(h) {
            var xPosition = d3.event.pageX + 50;
            var yPosition = d3.event.pageY - 50;

            d3.select(this).style('stroke', '#007bff').style('stroke-width', 3);

            d3.select("#hIndex-tooltip")
                    .style("left", xPosition + "px")
                    .style("top", yPosition + "px")
                    .select("#hIndexLabel")
                    .text(h.x);

            d3.select("#hIndex-tooltip").classed("hiddenTooltip", false);
        }

        function hMouseOut(h) {
            hIndexPoint.style('stroke', null).style('stroke-width', null);
            d3.select("#hIndex-tooltip").classed("hiddenTooltip", true);
        }
    });
}


function drawnPublicationsPerYearLineChart() {
    var margin = {top: 30, right: 20, bottom: 60, left: 50};
    var width = 1000 - margin.left - margin.right;
    var height = 550 - margin.top - margin.bottom;

    var xScale = d3.scale.linear().range([0, width]);
    var yScale = d3.scale.linear().range([height, 0]);

    var xAxis = d3.svg.axis().scale(xScale).orient("bottom").tickFormat(d3.format("d"));
    var yAxis = d3.svg.axis().scale(yScale).orient("left");



    var publicationsLine = d3.svg.line().x(function (d) {
        return xScale(d.year);
    }).y(function (d) {
        return yScale(d.publications);
    });



    var lineChartSvg = d3.select("#publicationsLineChart")
            .append("svg").attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    d3.json("./ajax/lineChartData.php?id=" + id, function (error, data) {

        if (error) {
            throw error;
        }

        var lineData = data.line;

        //console.log(lineData);
        var maximumOfPublications = d3.max(lineData, function (d) {
            return d.publications;
        });
        var lastYear = d3.max(lineData, function (d) {
            return d.year;
        });
        var firstYear = d3.min(lineData, function (d) {
            return d.year;
        });

        //console.log(firstYear, lastYear);

        //console.log(lineData[lineData.length - 1].year);

        xScale.domain([firstYear, lastYear + 1]);
        yScale.domain([0, maximumOfPublications + 1]);

        xAxis.innerTickSize(-height).outerTickSize(0).ticks(lineData.length);
        //tickPadding(lineData.length);
        yAxis.innerTickSize(-width).outerTickSize(0).ticks(maximumOfPublications + 1);



        lineChartSvg.append("text").attr("transform", "translate(" + (width / 2) + " ," + (height + margin.bottom) + ")")
                .style("text-anchor", "middle").text("Years");


        lineChartSvg.append("g").attr("class", "x axis").attr("transform", "translate(0," + height + ")").call(xAxis)
                .selectAll("text").attr("y", -7).attr("x", -9)
                .attr("transform", "rotate(-90)").style("text-anchor", "end");


        lineChartSvg.append("g").attr("class", "y axis").call(yAxis);

        lineChartSvg.append("text")
                .attr("transform", "rotate(-90)")
                .attr("y", 0 - margin.left)
                .attr("x", 0 - (height / 2))
                .attr("dy", "1em")
                .style("text-anchor", "middle")
                .text("Namber of Publications");

        lineChartSvg.append("path").attr("class", "publsLine").attr("d", publicationsLine(lineData));

        var points = lineChartSvg.selectAll(".dot")
                .data(lineData)
                .enter().append("circle") // Uses the enter().append() method
                .attr("class", "dot") // Assign a class for styling
                .attr("cx", function (d) {
                    return xScale(d.year);
                })
                .attr("cy", function (d) {
                    return yScale(d.publications);
                })
                .attr("r", 5);

        points.on('mouseover', mouseOver);
        points.on('mouseout', mouseOut);
        

        function mouseOver(d) {
            var xPosition = d3.event.pageX + 30;
            var yPosition = d3.event.pageY - 100;

            d3.select(this).style('stroke-width', 10).style('stroke-opacity', 0.5).style('stroke', 'blue').attr('r', 7);

            d3.select("#publsLinePoint-tooltip")
                    .style("left", xPosition + "px")
                    .style("top", yPosition + "px")
                    .select("#yearPoint")
                    .text(d.year);
            d3.select("#numOfPubls").text(d.publications);

            d3.select("#publsLinePoint-tooltip").classed("hiddenTooltip", false);
        }

        function mouseOut(d) {
            points.style('fill', null).style('stroke-width', null).style('stroke', null)
                    .style('opacity', null).attr('r', 5);

            d3.select("#publsLinePoint-tooltip").classed("hiddenTooltip", true);
        }
    });
}