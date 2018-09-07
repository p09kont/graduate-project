<!DOCTYPE html>

<html>
    <?php
    include("./Database.php");
    include("./include/auxFunctions.php");
    $id = $_GET["id"];
    $dbc = connectToDB();
    $sql = "SELECT * FROM di_professors WHERE id = $id";
    $query = mysqli_query($dbc, $sql);
    $res = mysqli_fetch_assoc($query);
    $firstname = $res["firstname"];
    $lastname = $res["lastname"];
    $property = $res["property"];
    $image = $res["image"];
    
    if(!is_null($res["updated"])){
        $lastUpdate = strtotime($res["updated"]);
        $date = date("d F Y", $lastUpdate);
    }
    else{
        $date = "--";
    }

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
    $row = mysqli_fetch_array($aggregateRes);

    // H index query
    $citationsQuery = "SELECT p.cited_by FROM publications p 
                INNER JOIN authorships a ON p.id = a.pub_id
                INNER JOIN persons pe ON a.pers_id = pe.id
                INNER JOIN di_professors d ON pe.prof_id = d.id
                WHERE d.id = $id
                ORDER BY p.cited_by ASC";
    $citationsQueryResult = mysqli_query($dbc, $citationsQuery);
    $thisProfessorCitations = array();
    while ($citationsRow = mysqli_fetch_array($citationsQueryResult)) {
        $thisProfessorCitations[] = $citationsRow["cited_by"];
    }
    $thisProfessorHIndex = getProfessorsHIndex($thisProfessorCitations);

    // Afill co-autors query
    $affiliatedCoAuthorsSQL = "SELECT  t2.prof_id, t3.lastname, COUNT(t2.prof_id) AS plithos
                                FROM
                                  (SELECT p.id AS id, p.title AS title FROM publications p
                                INNER JOIN authorships a ON p.id = a.pub_id
                                INNER JOIN persons pe ON pe.id = a.pers_id
                                INNER JOIN di_professors d ON d.id = pe.prof_id
                                WHERE d.id = $id) AS t1
                                LEFT JOIN
                                  (SELECT  p.id AS id, p.title AS title, pe.name AS authors, pe.prof_id  FROM  di_professors d 
                                RIGHT JOIN persons pe ON d.id = pe.id
                                RIGHT JOIN authorships a ON pe.id = a.pers_id 
                                RIGHT JOIN publications p ON a.pub_id = p.id) AS t2 ON t1.id = t2.id
                                INNER JOIN (SELECT id AS id, lastname FROM di_professors) AS t3 ON t3.id =t2.prof_id 
                                WHERE (t2.prof_id IS NOT NULL AND t2.prof_id <> $id)
                                GROUP BY t2.prof_id";
    $affiliatedRun = mysqli_query($dbc, $affiliatedCoAuthorsSQL);
    $affilCoAuthorsNumber = mysqli_num_rows($affiliatedRun);

    // Non affil co-authors query
    $nonCASql = "SELECT   t2.authors, COUNT(t2.authors) AS plithos, t2.prof_id, t2.peID
                FROM
                  (SELECT p.id AS id, p.title AS title FROM publications p
                INNER JOIN authorships a ON p.id = a.pub_id
                INNER JOIN persons pe ON pe.id = a.pers_id
                INNER JOIN di_professors d ON d.id = pe.prof_id
                WHERE d.id =$id) AS t1
                LEFT JOIN
                  (SELECT  p.id AS id, p.title AS title, pe.id AS peID, pe.name AS authors, pe.prof_id  FROM  di_professors d 
                RIGHT JOIN persons pe ON d.id = pe.id
                RIGHT JOIN authorships a ON pe.id = a.pers_id 
                RIGHT JOIN publications p ON a.pub_id = p.id) AS t2 ON t1.id = t2.id
                WHERE t2.prof_id IS NULL
                GROUP BY t2.authors
                ORDER BY plithos DESC";
    $nonCArun = mysqli_query($dbc, $nonCASql);
    $nonAffilCoAuthorsNumber = mysqli_num_rows($nonCArun);

    //Queries for timeline
    $thisProfessorAllYears = "SELECT DISTINCT year FROM publications p
                                INNER JOIN authorships a ON p.id = a.pub_id
                                INNER JOIN persons pe ON pe.id = a.pers_id
                                INNER JOIN di_professors d ON d.id = pe.prof_id
                                WHERE d.id = $id
                                ORDER BY year ASC";

    $thisProfessorFirstAndLastYear = "SELECT MIN(year) AS firstYear, MAX(year) AS lastYear FROM publications p
                                INNER JOIN authorships a ON p.id = a.pub_id
                                INNER JOIN persons pe ON pe.id = a.pers_id
                                INNER JOIN di_professors d ON d.id = pe.prof_id
                                WHERE d.id = $id";

    $runAllYears = mysqli_query($dbc, $thisProfessorAllYears);
    $runFirstLastYear = mysqli_query($dbc, $thisProfessorFirstAndLastYear);
    $firstLastYearRow = mysqli_fetch_array($runFirstLastYear);
    $firstYear = $firstLastYearRow["firstYear"];
    $lastYear = $firstLastYearRow["lastYear"];
    ?>
    <head>
        <meta charset="UTF-8">
        <?php include './include/head.php'; ?>


        <!-- link to css file -->
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/chartStyle.css">
        <!--<link rel="stylesheet" type="text/css" href="css/reset.css">-->
        <link rel="stylesheet" type="text/css" href="css/timelineStyle.css">
        <script type="text/javascript" src="js/timeline.js"></script>



        <title>Research information of <?php echo $firstname . " " . $lastname ?></title>

    </head>
    <body>
        <?php
        $pageName = 'Professors';
        include("./include/nav.php");
        ?>     
        <div id="theId"><?php echo $id; ?></div>
        <div class="container">
            <div class="card border-primary">
                <div class="card-body ">
                    <img class="float-left mr-3 img-thumbnail" src="resources/prof_photos/<?php echo $image; ?>" alt="Card image cap" width="200" height="200">
                    <h1><?php echo $firstname; ?> <?php echo $lastname; ?></h1>
                    <p><i><?php echo $property; ?></i></p><hr>

                    <div class="row">
                        <div class="col-xl-4 col-sm-6 mb-3">
                            <div class="card text-white bg-warning o-hidden h-100">
                                <div class="card-body">
                                    <div class="card-body-icon">
                                        <i class="fa fa-book"></i>
                                    </div>
                                    <h2><?php echo $row["ja"] + $row["ca"] + $row["bc"] + $row["b"]; ?></h2>
                                    <div class="mr-5"><h5>Publications</h5></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6 mb-3">
                            <div class="card text-white bg-warning o-hidden h-100">
                                <div class="card-body">
                                    <div class="card-body-icon">
                                        <i class="fa fa-header"></i>
                                    </div>
                                    <h2><?php echo $thisProfessorHIndex; ?></h2>
                                    <div class="mr-5"><h5>H - Index</h5></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6 mb-3">
                            <div class="card text-white bg-warning o-hidden h-100">
                                <div class="card-body">
                                    <div class="card-body-icon">
                                        <i class="fa fa-group"></i>
                                    </div>
                                    <h2><?php echo $affilCoAuthorsNumber + $nonAffilCoAuthorsNumber; ?></h2>
                                    <div class="mr-5"><h5>Co-authors</h5></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer small text-muted">Updated: <?php echo $date; ?></div>
            </div>

            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Publications of <?php echo "$firstname $lastname"; ?></h2>
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
                                        <th>Type</th>
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
                </div>
            </div>
            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">h - index chart of <?php echo "$firstname $lastname"; ?> </h2>
                <div class="card-body">
                    <center><div id="h-indexChart"></div></center>
                </div>
            </div>

            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Co-authors of <?php echo "$firstname $lastname"; ?> </h2>
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#allCo-authors">All Co-authors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#affiCo-authors">Affiliated Co-authors</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#nonAffilCo-authors">Non Affiliated Co-authors</a>
                        </li>         
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content border">
                        <div id="allCo-authors" class="tab-pane active"><br>
                            <table id="allCoAuthorsTable" border="0" class="test table table-striped  table-bordered"  cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>With</th>
                                        <th>Publications</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>With</th>
                                        <th>Publications</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div id="affiCo-authors" class="tab-pane fade"><br>
                            <table id="affiliatedCoAuthorsTable" border="0" class="test table table-striped  table-bordered"  cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>With</th>
                                        <th>Publications</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($rowAffil = mysqli_fetch_array($affiliatedRun)) {
                                        ?>
                                        <tr>
                                            <td><a href="professor.php?id=<?php echo $rowAffil["prof_id"] ?>"><?php echo $rowAffil["lastname"]; ?></a></td>
                                            <td><a href="affilMutual.php?id1=<?php echo $id; ?>&id2=<?php echo $rowAffil["prof_id"]; ?>">
                                                    <?php echo $rowAffil["plithos"]; ?></a></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>With</th>
                                        <th>Publications</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div id="nonAffilCo-authors" class="tab-pane fade"><br>
                            <table id="noAffiliatedCoAuthorsTable" border="0" class="test table table-striped  table-bordered"  cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>With</th>
                                        <th>Publications</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while ($nonCArow = mysqli_fetch_array($nonCArun)) {
                                        ?>
                                        <tr>
                                            <td><?php echo $nonCArow["authors"]; ?></td>
                                            <td><a href="noAffilMutual.php?id1=<?php echo $id; ?>&id2=<?php echo $nonCArow["peID"]; ?>">
                                                    <?php echo $nonCArow["plithos"]; ?></a></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>With</th>
                                        <th>Publications</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Research Timeline of <?php echo "$firstname $lastname"; ?> </h2>
                <div class="card-body">
                    <section class="cd-horizontal-timeline">
                        <div class="timeline">
                            <div class="events-wrapper">
                                <div class="events">
                                    <ol>
                                        <?php
                                        while ($allYearsRow = mysqli_fetch_array($runAllYears)):
                                            $year = $allYearsRow["year"];
                                            //echo $year;
                                            if ($year == $firstYear) {
                                                $class = "selected";
                                            } else {
                                                $class = '';
                                            }
                                            ?>
                                            <li><a href="#0" data-date="31/12/<?php echo $year; ?>" class="<?php echo $class; ?>"><?php echo $year; ?></a></li>
                                            <?php
                                        endwhile;
                                        ?>
                                    </ol>
                                    <span class="filling-line" aria-hidden="true"></span>
                                </div> <!-- .events -->
                            </div> <!-- .events-wrapper -->
                            <ul class="cd-timeline-navigation">
                                <li><a href="#0" class="prev inactive">Prev</a></li>
                                <li><a href="#0" class="next">Next</a></li>
                            </ul> <!-- .cd-timeline-navigation -->
                        </div> <!-- .timeline -->
                        <div class="events-content">
                            <ol>
                                <?php
                                mysqli_data_seek($runAllYears, 0);
                                while ($allYearsRow2 = mysqli_fetch_array($runAllYears)) :
                                    $year = $allYearsRow2["year"];
                                    if ($year == $firstYear) {
                                        $class = "selected";
                                    } else {
                                        $class = '';
                                    }
                                    //Bale to query edo
                                    //mporei kaia na min xreiazete an doulepsi o ajax
                                    ?>
                                    <li class="<?php echo $class; ?>" data-date="31/12/<?php echo $year; ?>">
                                        <h2><?php echo $year ?></h2>
                                        <table <?php echo "id=\"prof$id" . "Year$year\"" ?> border="0" class="table table-striped  table-bordered"  cellspacing="0" width="100%">
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
                                        <script type="text/javascript">
                                            $(document).ready(function () {
                                                var professorID = $("#theId").text();
                                                var year = <?php echo $year; ?>;
                                                var thisYearPublsTableBody = '#<?php echo"prof$id" . "Year$year"; ?> tbody';
                                                var thisYearPublsTable = $('#<?php echo "prof$id" . "Year$year"; ?>').DataTable({
                                                    "ajax": "./ajax/fetchAll.php?id=" + professorID + "&year=" + year,
                                                    "columns": [
                                                        {
                                                            "className": 'details-control',
                                                            "orderable": false,
                                                            "data": null,
                                                            "defaultContent": ''
                                                        },
                                                        {"data": "title"},
                                                        {"data": "year"},
                                                        {"data": "type"},
                                                        {"data": "cited_by"}
                                                    ],
                                                    "order": [[2, 'desc']]
                                                });
                                                rowChilds(thisYearPublsTableBody, thisYearPublsTable, formatAll);
                                            });
                                        </script>
                                    </li>
                                    <?php
                                endwhile;
                                ?>
                            </ol>
                        </div>
                    </section>
                </div>
            </div>


            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Pablications chart of <?php echo "$firstname $lastname"; ?> </h2>
                <div class="card-body">
                    <center><div id="publicationsLineChart"></div></center>
                </div>
            </div>

            <br/>
            <br/>
            <!--<div id="chart_div"></div>-->
            <!--<div id="load_data_prof"></div>-->
            <!--<div id="load_data_message"></div>-->
        </div>
        <?php include("./include/footer.php");?>
        

        <div id="citations-tooltip" class="hiddenTooltip tooltip">
            <strong>Paper:<span id="paperOrder"></span></strong> <span id="paperTitle"></span><br>
            <strong>Cited by: </strong><span id="paperCitations"></span>
        </div>

        <div id="hIndex-tooltip" class="hiddenTooltip tooltip">
            <strong> h-index: </strong><span id="hIndexLabel"></span>
        </div>

        <div id="publsLinePoint-tooltip" class="hiddenTooltip tooltip">
            <center><strong><span id="yearPoint"></span></strong></center>
            Publications: <b><span id="numOfPubls"></span></b>
        </div>

        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript" src="js/DataTablesFormatFunctions.js"></script>
        <script src="https://d3js.org/d3.v3.min.js"></script>
        <script type="text/javascript" src="js/charts.js"></script>        
        <?php mysqli_close($dbc) ?>
    </body>
</html>
