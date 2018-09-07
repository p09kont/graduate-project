

drawnAllResearchLineChart();

function drawnAllResearchLineChart(){
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



    var lineChartSvg = d3.select("#allPublicationsLineChart")
            .append("svg").attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    d3.json("./ajax/lineChartData.php", function (error, data) {

        if (error) {
            throw error;
        }

        var lineData = data.line;

        //console.log(lineData);
        var maximumOfPublications = d3.max(lineData, function (d) {
            return d.publications;
        });
        var minimumOfPublications = d3.min(lineData, function(d){
            return d.publications;
        });
        //console.log(minimumOfPublications);
        var lastYear = d3.max(lineData, function (d) {
            return d.year;
        });
        var firstYear = d3.min(lineData, function (d) {
            return d.year;
        });

        //console.log(firstYear, lastYear);

        //console.log(lineData[lineData.length - 1].year);

        xScale.domain([firstYear, lastYear + 1]);
        yScale.domain([minimumOfPublications, maximumOfPublications + 1]);

        xAxis.innerTickSize(-height).outerTickSize(0).ticks(lineData.length);
        //tickPadding(lineData.length);
        yAxis.innerTickSize(-width).outerTickSize(0).ticks(20);



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


