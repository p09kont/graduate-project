<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include("../Database.php");
if (isset($_POST["limit"], $_POST["start"], $_POST["lastname"])) {
    $limit = $_POST["limit"];
    $start = $_POST["start"];
    $lastname = $_POST["lastname"];
    $connection = connectToDB();
    $thisProfAllYears = "SELECT DISTINCT year FROM publications p
                            INNER JOIN authorships a ON p.id = a.pub_id
                            INNER JOIN persons pe ON pe.id = a.pers_id
                            INNER JOIN di_professors d ON d.id = pe.prof_id
                            WHERE d.lastname = '$lastname' "
            . "ORDER BY year DESC  LIMIT $start, $limit";
    $result = mysqli_query($connection, $thisProfAllYears);


    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            $year = $row["year"];
            echo "<center><h2>$year</h2></center>";

            $journalArticlesOnCurrentYear = "SELECT p.title, p.cited_by FROM di_professors d \n"
                    . "INNER JOIN persons pe ON d.id = pe.prof_id\n"
                    . "INNER JOIN authorships a ON pe.id = a.pers_id\n"
                    . "INNER JOIN publications p ON a.pub_id = p.id\n"
                    . "INNER JOIN journal_articles ja ON p.id = ja.pub_id\n"
                    . "WHERE d.lastname = '$lastname' AND p.year = $year";
            $jResult = mysqli_query($connection, $journalArticlesOnCurrentYear);

            $confArticlesOnCurrentYear = "SELECT p.title, p.cited_by FROM di_professors d \n"
                    . "INNER JOIN persons pe ON d.id = pe.prof_id\n"
                    . "INNER JOIN authorships a ON pe.id = a.pers_id\n"
                    . "INNER JOIN publications p ON a.pub_id = p.id\n"
                    . "INNER JOIN conference_articles ca ON p.id = ca.pub_id\n"
                    . "WHERE d.lastname = '$lastname' AND p.year = $year";
            $cResult = mysqli_query($connection, $confArticlesOnCurrentYear);

            $bookChaptersOnCurrentYear = "SELECT p.title, p.cited_by FROM di_professors d \n"
                    . "INNER JOIN persons pe ON d.id = pe.prof_id\n"
                    . "INNER JOIN authorships a ON pe.id = a.pers_id\n"
                    . "INNER JOIN publications p ON a.pub_id = p.id\n"
                    . "INNER JOIN book_chapters bc ON p.id = bc.pub_id\n"
                    . "WHERE d.lastname = '$lastname' AND p.year = $year";
            $bcResult = mysqli_query($connection, $bookChaptersOnCurrentYear);

            $booksOnCurrentYear = "SELECT p.title, p.cited_by FROM di_professors d \n"
                    . "INNER JOIN persons pe ON d.id = pe.prof_id\n"
                    . "INNER JOIN authorships a ON pe.id = a.pers_id\n"
                    . "INNER JOIN publications p ON a.pub_id = p.id\n"
                    . "INNER JOIN books b ON p.id = b.pub_id\n"
                    . "WHERE d.lastname = '$lastname' AND p.year = $year";
            $bkResult = mysqli_query($connection, $booksOnCurrentYear);

            if (mysqli_num_rows($jResult) > 0) {
                echo "<center><h3>Journal Articles</h3></center>";
                ?>
                
                    <table <?php echo "id=\"ja$year\""; ?> class="table table-striped table-hover table-bordered" width="100%" cellspacing="0"  >
                        <thead class="info">
                            <tr>
                                <th>Title</th>
                                <th>Cited By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rowJ = mysqli_fetch_array($jResult)) { ?>
                                <tr>
                                    <td><?php echo $rowJ["title"]; ?></td>
                                    <td><?php echo $rowJ["cited_by"] ?></td>
                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>

                
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#ja<?php echo $year; ?>').DataTable({
                            "paging": false,
                            "searching": false,
                            "info": false
                        });
                    });
                </script>
                <?php
            }

            if (mysqli_num_rows($cResult) > 0) {
                echo "<center><h3>Conference Articles</h3></center>";
                ?>
                
                    <table <?php echo "id=\"ca$year\""; ?> class="table table-striped table-hover table-bordered" width="100%" cellspacing="0"  >
                        <thead class="info">
                            <tr>
                                <th>Title</th>
                                <th>Cited By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rowCa = mysqli_fetch_array($cResult)) { ?>
                                <tr>
                                    <td><?php echo $rowCa["title"]; ?></td>
                                    <td><?php echo $rowCa["cited_by"] ?></td>
                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>

                
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#ca<?php echo $year; ?>').DataTable({
                            "paging": false,
                            "searching": false,
                            "info": false
                        });
                    });
                </script>
                <?php
            }


            if (mysqli_num_rows($bcResult) > 0) {
                echo "<center><h3>Book Chapters</h3></center>";
                ?>
                
                    <table <?php echo "id=\"bc$year\""; ?> class="table table-striped table-hover table-bordered" width="100%" cellspacing="0"  >
                        <thead class="info">
                            <tr>
                                <th>Title</th>
                                <th>Cited By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rowBc = mysqli_fetch_array($bcResult)) { ?>
                                <tr>
                                    <td><?php echo $rowBc["title"]; ?></td>
                                    <td><?php echo $rowBc["cited_by"] ?></td>
                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>

                
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#bc<?php echo $year; ?>').DataTable({
                            "paging": false,
                            "searching": false,
                            "info": false
                        });
                    });
                </script>
                <?php
            }
            
            if (mysqli_num_rows($bkResult) > 0) {
                echo "<center><h3>Books</h3></center>";
                ?>
                
                    <table <?php echo "id=\"bk$year\""; ?> class="table table-striped table-hover table-bordered" width="100%" cellspacing="0"  >
                        <thead class="info">
                            <tr>
                                <th>Title</th>
                                <th>Cited By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($rowBk = mysqli_fetch_array($bkResult)) { ?>
                                <tr>
                                    <td><?php echo $rowBk["title"]; ?></td>
                                    <td><?php echo $rowBk["cited_by"] ?></td>
                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>

                
                <script type="text/javascript">
                    $(document).ready(function () {
                        $('#bk<?php echo $year; ?>').DataTable({
                            "paging": false,
                            "searching": false,
                            "info": false
                        });
                    });
                </script>
                <?php
            }
            
        }
    }
}
