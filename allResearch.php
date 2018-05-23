<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
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
?>

<html>

    <head>
        <meta charset="UTF-8">
<?php
include("./include/head.php");
?>
        <script type = "text/javascript" src = "https://www.gstatic.com/charts/loader.js"></script>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>All Research</title>
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
                        <a class="nav-link active" href="allResearch.php">All research</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="allYears.php">All years</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="professors.php">Professors</a>
                    </li>
                </ul>
            </div>    
        </nav>

        <div class="container">
            <h2>All Research</h2>
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
//mysqli_close($connection);
?>
                </tbody>
            </table>
        </div>
        <br />
        <br />
        <div id="totalCitationsChart"></div>
        <div id="avarageCitationsChart"></div>
        <div id="h-indexChart"></div>
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


            function drawnTotalCitationsChart() {
                var divId = document.getElementById('totalCitationsChart');
                var data = google.visualization.arrayToDataTable([
                    ['Lastname', 'Citations'],
                    <?php
                    while ($rowCit = mysqli_fetch_array($totalCitationsRes)) {
                        echo "['" . $rowCit["lastname"] . "'," . $rowCit["totalCitations"] . "],";
                    }
                    ?>
                ]);
                var options = {
                    title: 'Total Citations',
                    width: '100%',
                    height: 500,
                    vAxis: {title: 'Toatal Citations'},
                    legend: {position: 'bottom'}
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
            
            function drawnHIndexChart(){
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
            }

        </script>
<?php mysqli_close($connection); ?>
    </body>
</html>
