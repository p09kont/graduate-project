<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include("../Database.php");
if (isset($_POST["limit"], $_POST["start"])) {
    $limit = $_POST["limit"];
    $start = $_POST["start"];
    $connection = connectToDB();
    $professors = "SELECT lastname FROM di_professors ORDER BY lastname ASC LIMIT $start, $limit";
    $result = mysqli_query($connection, $professors);
    while ($row = mysqli_fetch_array($result)) {
        $lastname = $row["lastname"];
        
        $query = "SELECT t1.year, t1.ja, t2.ca, t3.bc, t4.b FROM 
                (SELECT  p.year, COUNT(ja.pub_id) AS ja FROM 
                        di_professors d 
                        INNER JOIN persons pe ON d.id = pe.prof_id
                        INNER JOIN authorships a ON pe.id = a.pers_id
                        INNER JOIN publications p ON a.pub_id = p.id
                        LEFT JOIN journal_articles ja ON p.id = ja.pub_id 
                        WHERE d.lastname = '$lastname'
                        GROUP BY p.year
                        ORDER BY p.year ASC) AS t1
                JOIN
                (SELECT  p.year, COUNT(ca.pub_id) AS ca FROM 
                        di_professors d 
                        INNER JOIN persons pe ON d.id = pe.prof_id
                        INNER JOIN authorships a ON pe.id = a.pers_id
                        INNER JOIN publications p ON a.pub_id = p.id
                        LEFT JOIN conference_articles ca ON p.id = ca.pub_id 
                        WHERE d.lastname = '$lastname'
                        GROUP BY p.year
                        ORDER BY p.year ASC) AS t2 ON t1.year = t2.year
                JOIN
                        (SELECT  p.year, COUNT(bc.pub_id) AS bc FROM 
                        di_professors d 
                        INNER JOIN persons pe ON d.id = pe.prof_id
                        INNER JOIN authorships a ON pe.id = a.pers_id
                        INNER JOIN publications p ON a.pub_id = p.id
                        LEFT JOIN book_chapters bc ON p.id = bc.pub_id 
                        WHERE d.lastname = '$lastname'
                        GROUP BY p.year
                        ORDER BY p.year ASC)AS t3 ON t2.year = t3.year
                JOIN
                (SELECT  p.year, COUNT(b.pub_id) AS b FROM 
                        di_professors d 
                        INNER JOIN persons pe ON d.id = pe.prof_id
                        INNER JOIN authorships a ON pe.id = a.pers_id
                        INNER JOIN publications p ON a.pub_id = p.id
                        LEFT JOIN books b ON p.id = b.pub_id 
                        WHERE d.lastname = '$lastname'
                        GROUP BY p.year
                        ORDER BY p.year ASC) AS t4 ON t3.year = t4.year";
        $queryResult = mysqli_query($connection, $query);
        //if (!$queryResult) {
        //    echo mysqli_error($connection);
        //}
        ?>
        <div class="container" style="width: 800px;" align ="center">
            <?php echo "<h2>$lastname</h2>";?>
            <table <?php echo "id=\"$lastname\""; ?> class="table table-striped table-hover table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr class="info">
                        <th>Year</th>
                        <th>Journal <br /> Papers</th>
                        <th>Conference <br /> Papers</th>
                        <th>Book <br/> Chapters</th>
                        <th>Books</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $jaCounter = 0;
                    $caCounter = 0;
                    $bcCounter = 0;
                    $bKCounter = 0;
                    
                    while ($row1 = mysqli_fetch_array($queryResult)) { ?>
                        <tr>
                            <td><?php echo $row1["year"] ?></td>
                            <td><?php echo $row1["ja"]; ?></td>
                            <td><?php echo $row1["ca"]; ?></td>
                            <td><?php echo $row1["bc"]; ?></td>
                            <td><?php echo $row1["b"]; ?></td>
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
                        <th><?php echo $jaCounter;?></th>
                        <th><?php echo $caCounter;?></th>
                        <th><?php echo $bcCounter;?></th>
                        <th><?php echo $bKCounter;?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                $('#<?php echo $lastname; ?>').DataTable({
                    "paging": false,
                    "searching": false,
                    "info": false,
                    "order": [[ 0, "desc" ]]
                });
            });
        </script>
        <?php
    }
}



  