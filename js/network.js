/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var width = 1000;
var height = 800;
var center = {"x": width / 2, "y": height / 2};


var svg = d3.select("#network").append("svg").attr({"width": width, "height": height}).append("g");

d3.json("./ajax/networkData.php", function (error, dataset) {
    if (error) {
        throw error;
    }
    var idToNode = {};
    var nodes = dataset.nodes;
    var links = dataset.links;
    var totalNodes = nodes.length;

    nodes.forEach(function (n) {
        idToNode[n.id] = n;
    });

    links.forEach(function (e) {
        e.source = idToNode[e.source];
        e.target = idToNode[e.target];
    });

    var force = d3.layout.force().size([width, height]).nodes(nodes).charge(-200);

    var link = svg.selectAll(".link").data(links).enter().append("line").attr("stroke-width", function (d) {
        return d.weight;
    }).attr("class", "link");



    var node = svg.selectAll(".node")
            .data(nodes)
            .enter()
            .append("g")
            .attr("class", "node");

    node.append("circle").attr("r", function (d) {
        return 8 + (d.influence / totalNodes) * 20;
    }).attr("fill", "steelblue");

    node.append('title');

    var text = node.append('text').attr('dy', '0.32em').text(function (d) {
        return d.lastname + " (" + d.influence + ")";
    });


    force.on("tick", function (e) {
        node.each(function (d, i) {
            radial(d, i, e.alpha, totalNodes);
        });

        node.attr("cx", function (d) {
            return d.x;
        }).attr("cy", function (d) {
            return d.y;
        });



        link.attr("x1", function (d) {
            return d.source.x;
        });

        link.attr("y1", function (d) {
            return d.source.y;
        });

        link.attr("x2", function (d) {
            return d.target.x;
        });

        link.attr("y2", function (d) {
            return d.target.y;
        });

        text.attr('x', function (d) {
            var angle = Math.trunc(Math.atan2(d.y - center.y, d.x - center.x) * 180 / Math.PI);
            if (angle < -92 || angle > 92) {
                return -20;
            } else {
                return 20;
            }
        });

        text.style('text-anchor', function (d) {
            var angle = Math.trunc(Math.atan2(d.y - center.y, d.x - center.x) * 180 / Math.PI);
            if (angle >= -92 && angle <= 92) {
                return 'start';
            } else {
                return 'end';
            }
        });

        text.attr('transform', function (d) {
            var angle = Math.trunc(Math.atan2(d.y - center.y, d.x - center.x) * 180 / Math.PI);
            var parAngle = 180 + angle;
            if (angle >= -92 && angle <= 92) {
                return 'rotate(' + angle + ')';
            } else {
                return 'rotate(' + parAngle + ')';
            }
        });

        node.attr("transform", function (d) {
            return "translate(" + d.x + "," + d.y + ")";
        });

    });
    force.start();

    link.on('mouseover', linkMouseOver);

    link.on('mouseout', linkMouseOut);

    link.on('click', function (d) {
        var id1 = d.source.id;
        var id2 = d.target.id;
        location.href = "affilMutual.php?id1="+ id1 +"&id2="+ id2;
    });

    node.on('mouseover', nodeMouseOver);

    node.on('mouseout', nodeMouseOut);
    
    node.on('click', function(d){
        var id = d.id;
        location.href = "professor.php?id="+id+"#affiliatedCoAuthorsTable";
    });

    function linkMouseOver(l) {
        var xPosition = d3.event.pageX + 10;
        var yPosition = d3.event.pageY + 10;
        link.style('stroke', null).style('stroke-opacity', null);
        d3.select(this).style('stroke', '#d62333').style('stroke-opacity', 1);
        node.selectAll('circle').style('fill', null);

        node.filter(function (n) {
            return n === l.source || n === l.target;
        }).selectAll('circle').style('fill', '#66ff33');

        node.selectAll('text').style('fill', null);

        node.filter(function (n) {
            return n === l.source || n === l.target;
        }).selectAll('text').style('fill', 'red');

        d3.select("#linkTooltip")
                .style("left", xPosition + "px")
                .style("top", yPosition + "px")
                .select("#name1")
                .text(l.source.lastname);

        d3.select("#linkTooltip").select("#name2").text(l.target.lastname);

        d3.select("#linkTooltip").select("#common").html(function () {
            if (l.weight === 1) {
                return '<strong>' + l.weight + '</strong> mutual publication';
            } else {
                return '<strong>' + l.weight + '</strong> mutual publications';
            }
        });

        d3.select("#linkTooltip").classed("hiddenTooltip", false);
    }

    function linkMouseOut() {
        link.style('stroke', null).style('stroke-opacity', null);
        node.selectAll('circle').style('fill', null);
        node.selectAll('text').style('fill', null);
        d3.select("#linkTooltip").classed("hiddenTooltip", true);
    }

    function nodeMouseOver(n) {
        var xPosition = d3.event.pageX + 10;
        var yPosition = d3.event.pageY + 10;
        var nodesToHighlight = [];
        var nodesWithWeights = [];
        var weightedNode = {};

        link.style('stroke', null).style('stroke-opacity', null);
        node.style('fill', null);
        node.selectAll('text').style('fill', null);

        link.filter(function (l) {
            if (n === l.source) {
                nodesToHighlight.push(l.target);
                weightedNode = l.target;
                weightedNode.weight = l.weight;
                nodesWithWeights.push(weightedNode);
                return true;
            } else if (n === l.target) {
                nodesToHighlight.push(l.source);
                weightedNode = l.source;
                weightedNode.weight = l.weight;
                nodesWithWeights.push(weightedNode);
                return true;
            } else {
                return false;
            }
        }).style('stroke', '#d62333').style('stroke-opacity', 1);


        d3.select(this).selectAll('circle').style('fill', 'orange');
        d3.select(this).selectAll('text').style('fill', 'orange');

        node.filter(function (n) {
            return nodesToHighlight.indexOf(n) >= 0;
        }).selectAll('circle').style('fill', '#66ff33');

        node.filter(function (n) {
            return nodesToHighlight.indexOf(n) >= 0;
        }).selectAll('text').style('fill', 'red');

        d3.select("#nodeTooltip")
                .style("left", xPosition + "px")
                .style("top", yPosition + "px")
                .select("#nodeName")
                .text(n.lastname);

        d3.select("#nodeTooltip").select("#numOfCoauthors").html(function () {
            //var commonPublications = 0;
            //nodesWithWeights.forEach(function (x) {
            //    commonPublications += x.weight;
            //});
            return '<b>' + n.influence + '</b> coauthors.' ;
        });

        d3.select("#nodeTooltip").select("#details").html(function () {
            var output = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
            nodesWithWeights.forEach(function (x) {
                output += '<tr><td>' + x.lastname + ':</td><td align="center">' + x.weight + '</td></tr>';
            });
            output += '</table>';
            return output;
        });

        d3.select("#nodeTooltip").classed("hiddenTooltip", false);
    }

    function nodeMouseOut() {
        node.style('stroke', null).style('stroke-opacity', null);
        node.selectAll('circle').style('fill', null);
        node.selectAll('text').style('fill', null);
        link.style('stroke', null).style('stroke-opacity', null);
        d3.select("#nodeTooltip").classed("hiddenTooltip", true);
    }

});



function radial(data, index, alpha, numOfNodes) {
    
    var divisionAngle;
    numOfNodes > 20 ? divisionAngle = 18 : divisionAngle = 360/numOfNodes;
    var startAngle = 0;
    var radius = 400;
    var currentAngle = startAngle + (divisionAngle * index);
    var currentAngleRadians = currentAngle * Math.PI / 180;
    // the 500 & 250 are to center the circle we are creating
    var radialPoint = {
        x: center.x + radius * Math.cos(currentAngleRadians),
        y: center.y + radius * Math.sin(currentAngleRadians)
    };


    // here we attenuate the effect of the centering
    // by the alpha of the force layout. 
    // this gives other forces - like gravity -
    // to have an effect on the nodes
    var affectSize = alpha * 0.1;

    // here we adjust the x / y coordinates stored in our
    // data to move them closer to where we want them
    // this doesn't move the visual circles yet - 
    // we will do that in moveToRadial
    data.x += (radialPoint.x - data.x) * affectSize;
    data.y += (radialPoint.y - data.y) * affectSize;


}
