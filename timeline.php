<!DOCTYPE html>

<html>
    <?php
    include("./Database.php");
    $connection = connectToDB();
    $allYears = "SELECT DISTINCT year FROM publications ORDER BY year ASC";
    $firstYear = "SELECT MIN(year) AS firstYear FROM publications";
    $allYearsResult = mysqli_query($connection, $allYears);

    $firstYearResult = mysqli_query($connection, $firstYear);
    $r = mysqli_fetch_array($firstYearResult);
    $first = $r["firstYear"];
    ?>
    <head>
        <meta charset="UTF-8">
        <?php
        include("./include/head.php");
        ?>
        <!-- link to css file -->
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/chartStyle.css">
        <!--<link rel="stylesheet" type="text/css" href="css/reset.css">-->
        <link rel="stylesheet" type="text/css" href="css/timelineStyle.css">
        <script type="text/javascript" src="js/timeline.js"></script>
        <title>Timeline</title>
    </head>
    <body>
        <?php
        $pageName = 'Timeline';
        include("./include/nav.php");
        ?>


        <div class="container">
            <h2>Research years timeline</h2>
            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary">Research years timeline</h2>
                <div class="card-body">
                    <section class="cd-horizontal-timeline">
                        <div class="timeline">
                            <div class="events-wrapper">
                                <div class="events">
                                    <ol>
                                        <?php
                                        while ($row = mysqli_fetch_array($allYearsResult)):
                                            $year = $row["year"];
                                            //echo $year;
                                            if ($year == $first) {
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
                            </div><!-- .events-wrapper -->
                            <ul class="cd-timeline-navigation">
                                <li><a href="#0" class="prev inactive">Prev</a></li>
                                <li><a href="#0" class="next">Next</a></li>
                            </ul> <!-- .cd-timeline-navigation -->
                        </div><!-- .timeline -->
                        <div class="events-content">
                            <ol>
                                <?php
                                mysqli_data_seek($allYearsResult, 0);
                                while ($row1 = mysqli_fetch_array($allYearsResult)):
                                    $year = $row1["year"];
                                    if ($year == $first) {
                                        $class = "selected";
                                    } else {
                                        $class = '';
                                    }
                                    $query = "SELECT t1.id, t1.lastname, t2.ja, t3.ca, t4.bc, t5.b
                                FROM
                            (SELECT id AS id, lastname  FROM di_professors) AS t1
                                JOIN
                            (SELECT d.id AS id, COUNT(ja.pub_id) AS ja FROM 
                                di_professors d 
                                INNER JOIN persons pe ON d.id = pe.prof_id
                                INNER JOIN authorships a ON pe.id = a.pers_id
                                INNER JOIN publications p ON a.pub_id = p.id
                                LEFT JOIN journal_articles ja ON p.id = ja.pub_id AND p.year = $year
                                GROUP BY d.id
                                ORDER BY d.id ASC) AS t2 ON t1.id = t2.id
                                JOIN 
                            (SELECT d.id AS id, COUNT(ca.pub_id) AS ca FROM 
                                di_professors d 
                                INNER JOIN persons pe ON d.id = pe.prof_id
                                INNER JOIN authorships a ON pe.id = a.pers_id
                                INNER JOIN publications p ON a.pub_id = p.id
                                LEFT JOIN conference_articles ca ON p.id = ca.pub_id AND p.year = $year
                                GROUP BY d.id
                                ORDER BY d.id ASC) AS t3 ON t2.id = t3.id
                                JOIN
                            (SELECT d.id AS id, COUNT(bc.pub_id) AS bc FROM 
                                di_professors d 
                                INNER JOIN persons pe ON d.id = pe.prof_id
                                INNER JOIN authorships a ON pe.id = a.pers_id
                                INNER JOIN publications p ON a.pub_id = p.id
                                LEFT JOIN book_chapters bc ON p.id = bc.pub_id AND p.year = $year
                                GROUP BY d.id
                                ORDER BY d.id ASC) AS t4 ON t3.id = t4.id
                                JOIN
                            (SELECT d.id AS id, COUNT(b.pub_id) AS b FROM 
                                di_professors d 
                                INNER JOIN persons pe ON d.id = pe.prof_id
                                INNER JOIN authorships a ON pe.id = a.pers_id
                                INNER JOIN publications p ON a.pub_id = p.id
                                LEFT JOIN books b ON p.id = b.pub_id AND p.year = $year
                                GROUP BY d.id
                                ORDER BY d.id ASC) AS t5 ON t4.id = t5.id";
                                    $result = mysqli_query($connection, $query);
                                    ?>
                                    <li class="<?php echo $class; ?>" data-date="31/12/<?php echo $year; ?>">
                                        <h2><?php echo $year ?></h2>
                                        <table <?php echo "id=\"ex$year\""; ?> class="table table-striped table-hover table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
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
                                                while ($row2 = mysqli_fetch_array($result)):
                                                    $total = $row2["ja"] + $row2["ca"] + $row2["bc"] + $row2["b"];
                                                    ?>
                                                    <tr>
                                                        <td><a href="professor.php?id=<?php echo $row2["id"]; ?>"> 
                                                                <?php echo $row2["lastname"]; ?></a></td>
                                                        <td><?php echo $row2["ja"]; ?></td>
                                                        <td><?php echo $row2["ca"]; ?></td>
                                                        <td><?php echo $row2["bc"]; ?></td>
                                                        <td><?php echo $row2["b"]; ?></td>
                                                        <td><?php echo $total; ?></td>
                                                    </tr>
                                                    <?php
                                                endwhile;
                                                ?>
                                            </tbody>
                                        </table><br>
                                        <script type="text/javascript">
                                            $(document).ready(function () {
                                                $('#ex<?php echo $year; ?>').DataTable({
                                                    "paging": false,
                                                    "searching": false,
                                                    "info": false,
                                                    "order": [[5, 'desc']]
                                                });
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
                <h2 class="card-header text-white text-center bg-primary">Pablications chart</h2>
                <div class="card-body">
                    <center><div id="allPublicationsLineChart"></div></center>
                </div>
            </div>
        </div>
        <?php include("./include/footer.php");?>
        <div id="publsLinePoint-tooltip" class="hiddenTooltip tooltip">
            <center><strong><span id="yearPoint"></span></strong></center>
            Publications: <b><span id="numOfPubls"></span></b>
        </div>
        
        <script src="https://d3js.org/d3.v3.min.js"></script>
        <script type="text/javascript" src="js/allResearchLineChart.js"></script>
        <?php mysqli_close($connection)?>
    </body>
</html>
