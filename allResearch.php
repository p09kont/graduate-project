<!DOCTYPE html>

<?php
include("./Database.php");
include("./include/auxFunctions.php");
$connection = connectToDB();
$allResearch = "SELECT t1.id, t1.ln, t2.ja, t3.ca, t4.bc, t5.b
                        FROM
                          (SELECT id AS id, lastname AS ln FROM di_professors) AS t1
                        JOIN
                          (SELECT d.id AS id, COUNT(ja.pub_id) AS ja FROM 
                                di_professors d 
                                INNER JOIN persons pe ON d.id = pe.prof_id
                                INNER JOIN authorships a ON pe.id = a.pers_id
                                INNER JOIN publications p ON a.pub_id = p.id
                                INNER JOIN journal_articles ja ON p.id = ja.pub_id
                                GROUP BY d.id
                                ORDER BY d.id ASC) AS t2 ON t1.id = t2.id
                        JOIN 
                                (SELECT d.id AS id, COUNT(ca.pub_id) AS ca FROM 
                                di_professors d 
                                INNER JOIN persons pe ON d.id = pe.prof_id
                                INNER JOIN authorships a ON pe.id = a.pers_id
                                INNER JOIN publications p ON a.pub_id = p.id
                                INNER JOIN conference_articles ca ON p.id = ca.pub_id
                                GROUP BY d.id
                                ORDER BY d.id ASC) AS t3 ON t2.id = t3.id
                        JOIN
                                (SELECT d.id AS id, COUNT(bc.pub_id) AS bc FROM 
                                di_professors d 
                                INNER JOIN persons pe ON d.id = pe.prof_id
                                INNER JOIN authorships a ON pe.id = a.pers_id
                                INNER JOIN publications p ON a.pub_id = p.id
                                LEFT JOIN book_chapters bc ON p.id = bc.pub_id
                                GROUP BY d.id
                                ORDER BY d.id ASC) AS t4 ON t3.id = t4.id
                        JOIN
                                (SELECT d.id AS id, COUNT(b.pub_id) AS b FROM 
                                di_professors d 
                                INNER JOIN persons pe ON d.id = pe.prof_id
                                INNER JOIN authorships a ON pe.id = a.pers_id
                                INNER JOIN publications p ON a.pub_id = p.id
                                LEFT JOIN books b ON p.id = b.pub_id
                                GROUP BY d.id
                                ORDER BY d.id ASC) AS t5 ON t4.id = t5.id";
$allResearchResult = mysqli_query($connection, $allResearch);

$totalCitationsSql = "SELECT d.lastname, SUM(p.cited_by) AS totalCitations FROM publications p
                        INNER JOIN authorships a ON p.id = a.pub_id
                        INNER JOIN persons pe ON pe.id = a.pers_id
                        INNER JOIN di_professors d On d.id = pe.prof_id
                        GROUP BY d.lastname
                        ORDER BY totalCitations ASC";
$totalCitationsRes = mysqli_query($connection, $totalCitationsSql);

$averageCitationsSql = "SELECT d.lastname, AVG(p.cited_by) AS cit_avg FROM publications p
                        INNER JOIN authorships a ON p.id = a.pub_id
                        INNER JOIN persons pe ON pe.id = a.pers_id
                        INNER JOIN di_professors d On d.id = pe.prof_id
                        GROUP BY d.lastname
                        ORDER BY cit_avg ASC";
$averageCitationsRes = mysqli_query($connection, $averageCitationsSql);

$professorsIdAndLastname = "SELECT id, lastname FROM di_professors ORDER BY id ASC";
$idAndLastnameRes = $q = mysqli_query($connection, $professorsIdAndLastname);
$allProfessorsHIndex = array();
while ($rowId = mysqli_fetch_array($idAndLastnameRes)) {
    $id = $rowId["id"];
    $lastname = $rowId["lastname"];
    $sql = "SELECT p.cited_by FROM publications p 
        INNER JOIN authorships a ON p.id = a.pub_id
        INNER JOIN persons pe ON a.pers_id = pe.id
        INNER JOIN di_professors d ON pe.prof_id = d.id
        WHERE d.id = $id
        ORDER BY p.cited_by ASC";
    $result = mysqli_query($connection, $sql);
    $citations = array();
    while ($row = mysqli_fetch_array($result)) {
        //echo $row["cited_by"];
        $citations[] = $row["cited_by"];
    }
    $hIndex = getProfessorsHIndex($citations);
    $allProfessorsHIndex["$lastname"] = $hIndex;
}
asort($allProfessorsHIndex);

$contAllPublicationTypesSql = "SELECT t1.ja, t2.ca, t3.bc, t4.b FROM
                                (SELECT COUNT(j.pub_id) AS ja FROM 
                                journal_articles j ) AS t1
                                JOIN
                                (SELECT COUNT(c.pub_id) AS ca FROM 
                                conference_articles c) AS t2
                                JOIN
                                (SELECT COUNT(bc.pub_id) AS bc FROM 
                                book_chapters bc) AS t3
                                JOIN
                                (SELECT COUNT(b.pub_id) AS b FROM 
                                books b) AS t4";
$contAllPublicationTypesResult = mysqli_query($connection, $contAllPublicationTypesSql);
?>

<html>

    <head>
        <meta charset="UTF-8">
        <?php
        include("./include/head.php");
        ?>
        <script type = "text/javascript" src = "https://www.gstatic.com/charts/loader.js"></script>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/chartStyle.css">
        <title>All Research</title>

    </head>
    <body>
        <?php
        $pageName = 'All research';
        include("./include/nav.php");
        ?>  
        <div class="container">
            <h2>All Research</h2>

            <!--<div class="row">-->
            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Document Types</h2>
                <div class="card-body">
                    <table class="table table-striped table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Document Types</th>
                                <th>Number</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $allTYpesRow = mysqli_fetch_array($contAllPublicationTypesResult);
                            ?>
                            <tr>
                                <td>Journal Articles</td>
                                <td><?php echo $allTYpesRow["ja"]; ?></td>
                            </tr>
                            <tr>
                                <td>Conference Articles</td>
                                <td><?php echo $allTYpesRow["ca"]; ?></td>
                            </tr>
                            <tr>
                                <td>Book Chapters</td>
                                <td><?php echo $allTYpesRow["bc"]; ?></td>
                            </tr>
                            <tr>
                                <td>Books</td>
                                <td><?php echo $allTYpesRow["b"]; ?></td>
                            </tr>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th><?php echo $allTYpesRow["ja"] + $allTYpesRow["ca"] + $allTYpesRow["bc"] + $allTYpesRow["b"]; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!--<div class="col">-->


            <!--</div>-->
            <!--<div class="col">
                <div id="pieChartOfAllDocumentTypes"></div>
            </div>-->
            <!--</div>-->
            <!--<div class="row">-->
            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Publications</h2>
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#all">All Publications</a>
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

                            <table id="all-research-pubs-table" border="0" class="table table-striped  table-bordered"  cellspacing="0" width="100%">
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
                                        <th>Type</th>
                                        <th>Cited by</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div id="journal-articles" class="tab-pane fade"><br>

                            <table id="all-research-journal-articles-table" border="0" class="test table table-striped  table-bordered"  cellspacing="0" width="100%">
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

                            <table id="all-research-conference-articles-table" border="0" class="test table table-striped  table-bordered"  cellspacing="0" width="100%">
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

                            <table id="all-research-book-chapters-table" border="0" class="test table table-striped  table-bordered"  cellspacing="0" width="100%">
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

                            <table id="all-research-books-table" border="0" class="test table table-striped  table-bordered"  cellspacing="0" width="100%">
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
                </div>
            </div>

            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Faculty research</h2>
                <div class="card-body">
                    <table id="allresearch" class="table table-striped table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr class="info">
                                <th>Professor</th>
                                <th>Journal <br /> Articles</th>
                                <th>Conference <br /> Articles</th>
                                <th>Book <br /> Chapters</th>
                                <th>Books</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_array($allResearchResult)) {
                                //$total = 0;
                                $total = $row["ja"] + $row["ca"] + $row["bc"] + $row["b"];
                                ?>
                                <tr>
                                    <td><a href="professor.php?id=<?php echo $row["id"]; ?>">
                                            <?php echo $row["ln"]; ?></a></td>
                                    <td><?php echo $row["ja"]; ?></td>
                                    <td><?php echo $row["ca"]; ?></td>
                                    <td><?php echo $row["bc"]; ?></td>
                                    <td><?php echo $row["b"]; ?></td>
                                    <td><?php echo $total; ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!--</div>--> 
            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Total citations chart</h2>
                <div class="card-body">
                    <div id="totalCitationsChart"></div>
                </div>
            </div>
            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Average ciatations chart</h2>
                <div class="card-body">
                    <div id="avarageCitationsChart"></div>
                </div>
            </div>
            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">H - index chart</h2>
                <div class="card-body">
                    <div id="h-indexChart"></div>
                </div>
            </div>
            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Cooperations network</h2>
                <div class="card-body">
                    <div id="network"></div>
                </div>
            </div>
        </div>
        <?php include ("./include/footer.php"); ?>






        <div id = "nodeTooltip" class="hiddenTooltip tooltip">
            <center><strong><span id="nodeName"></span></strong></center>
            <p><span id="numOfCoauthors"></span></p>
            <p id="details"></p>
        </div>

        <div id="linkTooltip" class="hiddenTooltip tooltip">
            <p><strong><span id="name1"></span></strong> and 
                <strong><span id="name2"></span></strong></p>
            <p><span id="common"></span> </p>
        </div>
        <script type="text/javascript" src="js/allResearchDataTables.js"></script>
        <script type="text/javascript" src="js/DataTablesFormatFunctions.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#allresearch').DataTable({
                    "paging": false,
                    "searching": false,
                    "info": false
                });
            });
        </script>
        <script type="text/javascript">
            google.charts.load('current', {'packages': ['corechart']});
            google.charts.setOnLoadCallback(drawnTotalCitationsChart);
            google.charts.setOnLoadCallback(drawnAvaregaCitationsChart);
            google.charts.setOnLoadCallback(drawnHIndexChart);
            google.charts.setOnLoadCallback(drawnAllDocumentTypesPieChart);


            function drawnTotalCitationsChart() {
                var divId = document.getElementById('totalCitationsChart');
                var data = google.visualization.arrayToDataTable([
                    ['Lastname', 'Total citations'],
<?php
while ($rowCit = mysqli_fetch_array($totalCitationsRes)) {
    echo "['" . $rowCit["lastname"] . "'," . $rowCit["totalCitations"] . "],";
}
?>
                ]);
                var options = {
                    title: 'Total Citations',
                    titleTextStyle: {
                        //fontName: 'Helvetica',
                        bold: false
                    },
                    width: '100%',
                    height: 500,
                    fontName: 'Calibri',
                    vAxis: {
                        title: 'Toatal Citations',
                        titleTextStyle: {
                            //color: 'blue',
                            //fontName: 'Helvetica',
                            bold: true
                                    //italic: false
                        },
                        textStyle: {
                            //fontName: 'Helvetica',
                            bold: false
                        }
                    },
                    legend: {
                        position: 'bottom'
                                //textStyle: {fontName: 'Helvetica'}
                    }
                };
                var chart = new google.visualization.ColumnChart(divId);
                chart.draw(data, options);
            }

            function drawnAvaregaCitationsChart() {
                var divId = document.getElementById('avarageCitationsChart');
                var data = google.visualization.arrayToDataTable([
                    ['Lastname', 'AverageCitations'],
<?php
while ($rowAvCit = mysqli_fetch_array($averageCitationsRes)) {
    echo "['" . $rowAvCit["lastname"] . "'," . number_format($rowAvCit["cit_avg"], 2) . "],";
}
?>
                ]);
                var options = {
                    title: 'Citations per document',
                    width: '100%',
                    height: 500,
                    vAxis: {title: 'Citations per document'},
                    legend: {position: 'bottom'}
                };
                var chart = new google.visualization.ColumnChart(divId);
                chart.draw(data, options);
            }

            function drawnHIndexChart() {
                var divId = document.getElementById('h-indexChart');
                var data = google.visualization.arrayToDataTable([
                    ['Lastname', 'h-index'],
<?php
foreach ($allProfessorsHIndex as $key => $value) {
    echo "['" . $key . "'," . $value . "],";
}
?>
                ]);
                var options = {
                    title: 'h-index',
                    width: '100%',
                    height: 500,
                    vAxis: {title: 'h-index'},
                    legend: {position: 'bottom'}
                };
                var chart = new google.visualization.ColumnChart(divId);
                chart.draw(data, options);

                //google.visualization.events.addListener(chart, 'select', selectHandler);

                /*function selectHandler(){
                 var selectedItem = chart.getSelection()[0];
                 if (selectedItem) {
                 var value = data.getValue(selectedItem.row, selectedItem.column);
                 alert('The user selected ' + value);
                 }
                 
                 }*/
            }

            function drawnAllDocumentTypesPieChart() {
                var divId = document.getElementById('pieChartOfAllDocumentTypes');
                var data = google.visualization.arrayToDataTable([
                    ['Document Type', 'percent'],
                    ['Journal Articles', <?php echo $allTYpesRow["ja"]; ?>],
                    ['Conference Articles',<?php echo $allTYpesRow["ca"]; ?>],
                    ['Book chapters',<?php echo $allTYpesRow["bc"]; ?>],
                    ['Books',<?php echo $allTYpesRow["b"]; ?>]
                ]);
                var options = {
                    title: 'DocTypes',
                    width: '100%',
                    height: 500
                            //vAxis: {title: 'h-index'},
                            //legend: {position: 'bottom'}
                };
                var chart = new google.visualization.PieChart(divId);
                chart.draw(data, options);
            }

        </script>
        <script src="https://d3js.org/d3.v3.min.js"></script>
        <script type="text/javascript" src="js/network.js"></script>
        <?php mysqli_close($connection); ?>
    </body>
</html>
