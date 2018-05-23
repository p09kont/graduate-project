<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <?php 
        include("./include/head.php");
        ?>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>All Years</title>
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
                        <a class="nav-link active" href="allYears.php">All years</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="professors.php">Professors</a>
                    </li>
                </ul>
            </div>    
        </nav>
        
        <div class="container">
            <div id="load_data"></div>
            <div id="load_data_message"></div>
            <br />
            <br />
        </div>

        <!--<script src="./js/scripts.js"></script>-->
        <script type="text/javascript">
            $(document).ready(function () {
                var limit = 3;
                var start = 0;
                var action = 'inactive';
                function loadData(limit, start) {
                    $.ajax({
                        url: "./ajax/load_year.php",
                        method: "POST",
                        data: {limit: limit, start: start},
                        cache: false,
                        success: function (data) {
                            $('#load_data').append(data);
                            if (data === '') {
                                $('#load_data_message').html("");
                                action = 'active';
                            } else {
                                $('#load_data_message').html('<center><p><img src = "./resources/loader.gif">Loading...</p></center>');
                                action = 'inactive';
                            }
                        }
                    });
                }
                if (action === 'inactive') {
                    action = 'active';
                    loadData(limit, start);
                }
                $(window).scroll(function () {
                    if ($(window).scrollTop() + $(window).height() > $('#load_data').height()
                            && action === 'inactive') {
                        action = 'active';
                        start = start + limit;
                        setTimeout(function () {
                            loadData(limit, start);
                        }, 1000);
                    }
                });
            });
        </script>
    </body>
</html>
