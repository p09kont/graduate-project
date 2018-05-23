<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="container" style="width: 800px;" align ="center">
            <table  class="table table-striped table-hover table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr class="info">
                        <th>Professor</th>
                        <th>Journal <br /> Papers</th>
                        <th>Conference Papers</th>
                        <th>Book Chapters</th>
                        <th>Books</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row1 = mysqli_fetch_array($result)) { ?>
                        <tr>
                            <td><a href="professor.php?lastname=<?php echo $row1["lastname"];?>">
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
        </div>