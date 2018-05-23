<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <?php
    include("./Database.php");
    $id = $_GET["id"];
    $dbc = connectToDB();
    $sql = "SELECT * FROM di_professors WHERE id = $id";
    $query = mysqli_query($dbc, $sql);
    $res = mysqli_fetch_assoc($query);
    $firstname = $res["firstname"];
    $lastname = $res["lastname"];
    $property = $res["property"];

    $aggregate = "SELECT t1.lastname, t1.ja, t2.ca, t3.bc, t4.b FROM 
                    (SELECT  d.id, d.lastname, COUNT(ja.pub_id) AS ja FROM 
                            di_professors d 
                            INNER JOIN persons pe ON d.id = pe.prof_id
                            INNER JOIN authorships a ON pe.id = a.pers_id
                            INNER JOIN publications p ON a.pub_id = p.id
                            LEFT JOIN journal_articles ja ON p.id = ja.pub_id 
                            WHERE d.id = $id
                            ) AS t1
                    JOIN
                        (SELECT  d.id, d.lastname, COUNT(ca.pub_id) AS ca FROM 
                                di_professors d 
                                INNER JOIN persons pe ON d.id = pe.prof_id
                                INNER JOIN authorships a ON pe.id = a.pers_id
                                INNER JOIN publications p ON a.pub_id = p.id
                                LEFT JOIN conference_articles ca ON p.id = ca.pub_id 
                                WHERE d.id = $id
                                ) AS t2 ON t1.id = t2.id
                    JOIN
                        (SELECT  d.id, d.lastname, COUNT(bc.pub_id) AS bc FROM 
                            di_professors d 
                            INNER JOIN persons pe ON d.id = pe.prof_id
                            INNER JOIN authorships a ON pe.id = a.pers_id
                            INNER JOIN publications p ON a.pub_id = p.id
                            LEFT JOIN book_chapters bc ON p.id = bc.pub_id 
                            WHERE d.id = $id
                            )AS t3 ON t2.id = t3.id
                    JOIN
                        (SELECT   d.id, d.lastname, COUNT(b.pub_id) AS b FROM 
                            di_professors d 
                            INNER JOIN persons pe ON d.id = pe.prof_id
                            INNER JOIN authorships a ON pe.id = a.pers_id
                            INNER JOIN publications p ON a.pub_id = p.id
                            LEFT JOIN books b ON p.id = b.pub_id 
                            WHERE d.id = $id
                            ) AS t4 ON t3.id = t4.id";

    $aggregateRes = mysqli_query($dbc, $aggregate);
    ?>
    <head>
        <meta charset="UTF-8">
        <?php include './include/head.php'; ?>
        <script type = "text/javascript" src = "https://www.gstatic.com/charts/loader.js"></script>
        <!-- link to css file -->
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>Research information of <?php echo $firstname . " " . $lastname ?></title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md bg-primary navbar-dark fixed-top">
            <a class="navbar-brand" href="#"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="allResearch.php">All research</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="allYears.php">All years</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="professors.php">Professors</a>
                    </li>
                </ul>
            </div>    
        </nav>


        <div id="theId"><?php echo $id; ?></div>
        <div class="container">
            <h2><?php echo $firstname . " " . $lastname ?></h2>
            <h5>Aggregate data of all years research</h5>
            <div id="aggregateTable">
                <table id="aggregate" class="table table-striped table-hover table-bordered"  cellspacing="0">
                    <thead>
                        <tr>
                            <th>Journal <br /> Articles</th>
                            <th>Conference <br /> Articles</th>
                            <th>Book <br /> Chapters</th>
                            <th>Books</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_array($aggregateRes)) {
                            //$total = 0;
                            $total = $row["ja"] + $row["ca"] + $row["bc"] + $row["b"];
                            ?>
                            <tr>                           
                                <td><?php echo $row["ja"]; ?></td>
                                <td><?php echo $row["ca"]; ?></td>
                                <td><?php echo $row["bc"]; ?></td>
                                <td><?php echo $row["b"]; ?></td>
                                <td><?php echo $total; ?></td>
                            </tr>
                            <?php
                        }
                        //mysqli_close($connection);
                        ?>
                    </tbody>
                </table>
            </div>
            <a href="year-by-year.php?id=<?php echo $id; ?>">Research year by year</a>
            <br/>
            <br/>
            <h5>Analytical data</h5>





            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#all">All Research</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#journal-articles">Journal Articles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#conference-articles">Conference Articles</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#book-chapters">Book Chapters</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#books">Books</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content border">
                <div id="all" class="tab-pane active"><br>                  

                    <table id="all-pubs-table" border="0" class="table table-striped  table-bordered"  cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Type</th>
                                <th>Cited by</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>TYpe</th>
                                <th>Cited by</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div id="journal-articles" class="tab-pane fade"><br>

                    <table id="journal-articles-table" border="0" class="test table table-striped  table-bordered"  cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Cited by</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Cited by</th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
                <div id="conference-articles" class="tab-pane fade"><br>

                    <table id="conference-articles-table" border="0" class="test table table-striped  table-bordered"  cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Cited by</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Cited by</th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
                <div id="book-chapters" class="tab-pane fade"><br>

                    <table id="book-chapters-table" border="0" class="test table table-striped  table-bordered"  cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Cited by</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Cited by</th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
                <div id="books" class="tab-pane fade"><br>

                    <table id="books-table" border="0" class="test table table-striped  table-bordered"  cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Cited by</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Cited by</th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
                <br />
            </div>

            <br/>
            <br/>
            <div id="chart_div"></div>
            <!--<div id="load_data_prof"></div>-->
            <!--<div id="load_data_message"></div>-->
        </div>
       <!-- <script type="text/javascript">
            $(document).ready(function () {
                var limit = 3;
                var start = 0;
                var lastname = '<?php //echo $lastname;  ?>';
                var action = 'inactive';
                function loadData(limit, start, lastname) {
                    $.ajax({
                        url: "./ajax/load_prof.php",
                        method: "POST",
                        data: {limit: limit, start: start, lastname: lastname},
                        cache: false,
                        success: function (data) {
                            $('#load_data_prof').append(data);
                            if (data === '') {
                                $('#load_data_message').html("");
                                action = 'active';
                            } else {
                                $('#load_data_message').html('<center><p><img src ="http://demo.itsolutionstuff.com/plugin/loader.gif">Loading...</p></center>');
                                action = 'inactive';
                            }
                        }
                    });
                }
                if (action === 'inactive') {
                    action = 'active';
                    loadData(limit, start, lastname);
                }
                $(window).scroll(function () {
                    if ($(window).scrollTop() + $(window).height() > $('#load_data_prof').height()
                            && action === 'inactive') {
                        action = 'active';
                        start = start + limit;
                        setTimeout(function () {
                            loadData(limit, start, lastname);
                        }, 1000);
                    }
                });
            });
        </script>-->

        <script src="js/scripts.js"></script>
        <script type="text/javascript">
            var profId = document.getElementById('theId').innerHTML;
            //console.log(profId);
            //console.log("LLLLLLLLLLLLLLLLLLLLLLLLLL");
            google.charts.load('current', {'packages': ['corechart']});

            // Set a callback to run when the Google Visualization API is loaded.
            google.charts.setOnLoadCallback(drawChart);
            function drawChart() {
                var jsonData = $.ajax({
                    url: "./ajax/getChartData.php?id=" + profId,
                    dataType: "json",
                    async: false
                }).responseText;

                // Create our data table out of JSON data loaded from server.
                var data = new google.visualization.DataTable(jsonData);

                var options = {
                    'title': 'Citations Count For <?php echo $firstname . " " . $lastname  ?>',
                    'width': '100%',
                    'height': 500,
                    'legend': {position: 'bottom'},
                    
                    hAxis: {
                        title: 'Hello',
                        slantedText: true
                        //slantedTextAngle: 90
                    }
                    
                };

                // Instantiate and draw our chart, passing in some options.
                var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            }
        </script>
        <?php mysqli_close($dbc) ?>
    </body>
</html>
