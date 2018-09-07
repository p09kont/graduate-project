<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <?php include("./include/head.php"); ?>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>Professors</title>
    </head>
    <body>
        <?php
        $pageName = 'Professors';
        include("./include/nav.php");
        ?>

        <div class="container">
            <?php
            include("./Database.php");
            $dbc = connectToDB();
            $sql = "SELECT * FROM di_professors ORDER BY id ASC";
            $query = mysqli_query($dbc, $sql);
            ?>
            <div class="row">

                <?php
                $r = mysqli_query($dbc, $sql);
                while ($row = mysqli_fetch_array($r)) {
                    $id = $row["id"];
                    $firstName = $row["firstname"];
                    $lastName = $row["lastname"];
                    $prop = $row["property"];
                    $image = $row["image"];
                    ?>
                    <div class="col-sm-4">
                        <div id="professorsCrads" class="card" >
                            <!--<img class="card-img-top"  src="resources/prof_photos/<?php //echo $id  ?>.jpg" alt="Card image">-->
                            <div class="card-body">
                                <img class="card-img-top"  src="resources/prof_photos/<?php echo $image ?>" alt="Card image" width="200" height="200">
                                <h5 class="card-title"><?php echo $firstName . " " . $lastName; ?></h5>
                                <p class="card-text"><i><?php echo $prop; ?></i></p>
                                <a href="professor.php?id=<?php echo $id; ?>" class="btn btn-primary">See Profile</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>            
        </div>
        <?php include("./include/footer.php");?>
    </body>
</html>
