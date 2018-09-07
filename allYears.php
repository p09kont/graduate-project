<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <?php
    include("./Database.php");
    $dbc = connectToDB();
    $professors = "SELECT id, firstname, lastname FROM di_professors ORDER BY lastname ASC";
    $result = mysqli_query($dbc, $professors);
    $records = array();
    while ($rows = mysqli_fetch_assoc($result)) {
        $records[] = $rows;
    }
    ?>
    <head>
        <meta charset="UTF-8">
        <?php
        $pageName = 'All years';
        include("./include/head.php");
        ?>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>All Years</title>
        <style>
            body {
                position: relative;
            }
            ul.nav-pills {
                line-height: 80%;
                top: 70px;
                position: fixed;
            }
            ul.nav-pills a:hover {
                color: #007bff;
                background-color: white;
            }
            .col-sm-2{
                top: 0px;
                background-color:  #343a40;    
            }
            .col-sm-10{
                padding-top: 15px;
            }
            #containerFluid{
                margin-top: 55px;
            }


        </style>
    </head>
    <body data-spy="scroll" data-target="#scrollspy" data-offset="1">
        <?php
        include("./include/nav.php");
        ?>
        <div id="containerFluid" class="container-fluid">
            <div class="row">
                <nav class="col-sm-2" id="scrollspy">
                    <ul class="nav nav-pills flex-column">
                        <?php
                        $count = 0;
                        foreach ($records as $record) {
                            $id = $record["id"];
                            $lastname = $record["lastname"];
                            $count++;
                            $class = '';
                            if ($count == 1) {
                                $class = 'active';
                            }
                            ?>
                            <li class="nav-item">
                                <strong><a class="nav-link <?php echo $class; ?>" href="#<?php echo $lastname; ?>"><?php echo $lastname; ?></a></strong>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </nav>
                <div id="main" class="col-sm-10">
                    <?php
                    foreach ($records as $record) {
                        $id = $record["id"];
                        $lastname = $record["lastname"];
                        $firstname = $record["firstname"];
                        $query = "SELECT t1.year, t1.ja, t2.ca, t3.bc, t4.b FROM 
                                (SELECT  p.year, COUNT(ja.pub_id) AS ja FROM 
                                    di_professors d 
                                    INNER JOIN persons pe ON d.id = pe.prof_id
                                    INNER JOIN authorships a ON pe.id = a.pers_id
                                    INNER JOIN publications p ON a.pub_id = p.id
                                    LEFT JOIN journal_articles ja ON p.id = ja.pub_id 
                                    WHERE d.id = $id
                                    GROUP BY p.year
                                    ORDER BY p.year ASC) AS t1
                                JOIN
                                (SELECT  p.year, COUNT(ca.pub_id) AS ca FROM 
                                    di_professors d 
                                    INNER JOIN persons pe ON d.id = pe.prof_id
                                    INNER JOIN authorships a ON pe.id = a.pers_id
                                    INNER JOIN publications p ON a.pub_id = p.id
                                    LEFT JOIN conference_articles ca ON p.id = ca.pub_id 
                                    WHERE d.id = $id
                                    GROUP BY p.year
                                    ORDER BY p.year ASC) AS t2 ON t1.year = t2.year
                                    JOIN
                                (SELECT  p.year, COUNT(bc.pub_id) AS bc FROM 
                                    di_professors d 
                                    INNER JOIN persons pe ON d.id = pe.prof_id
                                    INNER JOIN authorships a ON pe.id = a.pers_id
                                    INNER JOIN publications p ON a.pub_id = p.id
                                    LEFT JOIN book_chapters bc ON p.id = bc.pub_id 
                                    WHERE d.id = $id
                                    GROUP BY p.year
                                    ORDER BY p.year ASC)AS t3 ON t2.year = t3.year
                                JOIN
                                (SELECT  p.year, COUNT(b.pub_id) AS b FROM 
                                    di_professors d 
                                    INNER JOIN persons pe ON d.id = pe.prof_id
                                    INNER JOIN authorships a ON pe.id = a.pers_id
                                    INNER JOIN publications p ON a.pub_id = p.id
                                    LEFT JOIN books b ON p.id = b.pub_id 
                                    WHERE d.id = $id
                                    GROUP BY p.year
                                    ORDER BY p.year ASC) AS t4 ON t3.year = t4.year";
                        $queryResult = mysqli_query($dbc, $query);
                        ?> 
                        <div <?php echo "id=\"$lastname\"" ?> class="card border-primary">
                            <h4 class="card-header text-white text-center bg-primary"><?php echo "$firstname $lastname"; ?></h4>
                            <div class="card-body">
                                <table <?php echo "id=\"table$id\""; ?> class="table table-striped table-hover table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr class="info">
                                            <th>Year</th>
                                            <th>Journal <br /> Papers</th>
                                            <th>Conference <br /> Papers</th>
                                            <th>Book <br/> Chapters</th>
                                            <th>Books</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $jaCounter = 0;
                                        $caCounter = 0;
                                        $bcCounter = 0;
                                        $bKCounter = 0;

                                        while ($row1 = mysqli_fetch_array($queryResult)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $row1["year"] ?></td>
                                                <td><?php echo $row1["ja"]; ?></td>
                                                <td><?php echo $row1["ca"]; ?></td>
                                                <td><?php echo $row1["bc"]; ?></td>
                                                <td><?php echo $row1["b"]; ?></td>
                                                <td><?php echo $row1["ja"] + $row1["ca"] + $row1["bc"] + $row1["b"]; ?></td>
                                            </tr>
                                            <?php
                                            $jaCounter += $row1["ja"];
                                            $caCounter += $row1["ca"];
                                            $bcCounter += $row1["bc"];
                                            $bKCounter += $row1["b"];
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th><?php echo $jaCounter; ?></th>
                                            <th><?php echo $caCounter; ?></th>
                                            <th><?php echo $bcCounter; ?></th>
                                            <th><?php echo $bKCounter; ?></th>
                                            <th><?php echo $jaCounter + $caCounter + $bcCounter + $bKCounter ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                $('#table<?php echo $id; ?>').DataTable({
                                    "paging": false,
                                    "searching": false,
                                    "info": false,
                                    "order": [[0, "desc"]]
                                });
                            });
                        </script>
                    <?php } ?>
                    <?php include("./include/footer.php");?>    
                </div>
            </div>
        </div>
    </body>
</html>
