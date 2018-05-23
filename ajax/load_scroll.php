<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include ("../Database.php");
if (isset($_POST["limit"], $_POST["start"])) {
    $limit = $_POST["limit"];
    $start = $_POST["start"];
    $connection = connectToDB();
    $allYears = "SELECT DISTINCT year FROM publications ORDER BY year DESC LIMIT $start, $limit";
    $allYearsResult = mysqli_query($connection, $allYears);
    while ($row = mysqli_fetch_array($allYearsResult)) {
        $year = $row["year"];
        echo "<center><h2>$year</h2></center>";
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
        
            <table <?php echo "id=\"ex$year\""; ?> class="table table-striped table-hover table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr class="info">
                        <th>Professor</th>
                        <th>Journal <br /> Articles</th>
                        <th>Conference <br /> Articles</th>
                        <th>Book <br /> Chapters</th>
                        <th>Books</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row1 = mysqli_fetch_array($result)) { ?>
                        <tr>
                            <td><a href="professor.php?id=<?php echo $row1["id"];?>">
                                <?php echo $row1["lastname"]; ?></a></td>
                            <td><?php echo $row1["ja"]; ?></td>
                            <td><?php echo $row1["ca"]; ?></td>
                            <td><?php echo $row1["bc"]; ?></td>
                            <td><?php echo $row1["b"]; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
       
        <script type="text/javascript">
            $(document).ready(function () {
                $('#ex<?php echo $year; ?>').DataTable({
                    "paging": false,
                    "searching": false,
                    "info": false
                });
            });
        </script>
        <?php
    }
}

