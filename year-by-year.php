<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <?php
        include("./Database.php");
        $id = $_GET["id"];
        $dbc = connectToDB();
        $sql = "SELECT * FROM di_professors WHERE id = $id";
        $query = mysqli_query($dbc, $sql);
        $res = mysqli_fetch_assoc($query);
        $firstname = $res["firstname"];
        $lastname = $res["lastname"];
        $property = $res["property"];
        //echo $id;
        ?>
        <meta charset="UTF-8">
        <?php include './include/head.php'; ?>
        <!-- link to css file -->
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>Research of <?php echo $firstname . " " . $lastname; ?> year by year</title>
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
        
        
        <div id="theId"><?php echo $id; ?></div>
        <div class="container">
            <h2> <?php echo $firstname . " " . $lastname; ?> </h2>
            <p>Research year by year</p>
            <a href="professor.php?id=<?php echo $id;?>"><?php echo $firstname." ".$lastname;?> all research</a>
            <div id="load_data_prof"></div>
            <div id="load_data_message"></div>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                var limit = 3;
                var start = 0;
                var id = $('#theId').text();
                var action = 'inactive';
                function loadData(limit, start, id) {
                    $.ajax({
                        url: "./ajax/prof_year_by_year.php",
                        method: "POST",
                        data: {limit: limit, start: start, id: id},
                        cache: false,
                        success: function (data) {
                            $('#load_data_prof').append(data);
                            if (data === '') {
                                $('#load_data_message').html("");
                                action = 'active';
                            } else {
                                $('#load_data_message').html('<center><p><img src ="./resources/loader.gif">Loading...</p></center>');
                                action = 'inactive';
                            }
                        }
                    });
                }
                if (action === 'inactive') {
                    action = 'active';
                    loadData(limit, start, id);
                }
                $(window).scroll(function () {
                    if ($(window).scrollTop() + $(window).height() > $('#load_data_prof').height()
                            && action === 'inactive') {
                        action = 'active';
                        start = start + limit;
                        setTimeout(function () {
                            loadData(limit, start, id);
                        }, 1000);
                    }
                });
            });
        </script>
        
    </body>
</html>
