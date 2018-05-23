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
                        <a class="nav-link" href="allResearch.php">All research</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="allYears.php">All years</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="professors.php">Professors</a>
                    </li>
                </ul>
            </div>    
        </nav>
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
                    ?>
                    <div class="col-sm-4">
                        <div class="card" >
                            <!--<img class="card-img-top"  src="resources/prof_photos/<?php //echo $id ?>.jpg" alt="Card image">-->
                            <div class="card-body">
                                <img class="card-img-top"  src="resources/prof_photos/<?php echo $id ?>.jpg" alt="Card image" width="200" height="200">
                                <h5 class="card-title"><?php echo $firstName . " " . $lastName; ?></h5>
                                <p class="card-text"><?php echo $prop;?></p>
                                <a href="professor.php?id=<?php echo $id;?>" class="btn btn-primary">See Profile</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>            
        </div>       
    </body>
</html>
