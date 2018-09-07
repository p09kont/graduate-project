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
        
        <!-- link to css file -->
        <link rel="stylesheet" type="text/css" href="css/indexStyle.css">
        <title>Index</title>
    </head>
    <body>
        <?php
        // put your code here
        ?>
        <div class="container">
            <div id="homePageContainer">
                <div>
                    <div class="h2">
                        <h2>Web site for the presentation of the research activity of the 
                            <a href="http://di.ionio.gr/" target="_blank">Informatics Department</a> 
                            of the <a href="https://ionio.gr/" target="_blank">Ionian University</a>.
                        </h2>
                    </div>
                    <div class="par">
                        <p>The site is based on data collected by <a href="https://www.scopus.com/" target="_blank">Scopus</a>.</p>
                    </div> 
                    <div class="row">
                        <div class="col-xl-4 col-sm-6 mb-5">
                            <a class="cardLink" href="allResearch.php">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h2>All Research</h2>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-4 col-sm-6 mb-5">
                            <a class="cardLink" href="professors.php">
                                <div class="card  h-100">
                                <div class="card-body">
                                    <h2>Professors</h2>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="col-xl-4 col-sm-6 mb-5">
                            <a class="cardLink" href="allYears.php">
                                <div class="card  h-100">
                                <div class="card-body">
                                    <h2>All years</h2>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="col-xl-4 col-sm-6 mb-5">
                            <a class="cardLink" href="timeline.php">
                                <div class="card  h-100">
                                <div class="card-body">
                                    <h2>Timeline</h2>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="col-xl-4 col-sm-6 mb-5">
                            <a class="cardLink" href="journals.php">
                                <div class="card  h-100">
                                <div class="card-body">
                                    <h2>Journals</h2>
                                </div>
                            </div>
                            </a> 
                        </div>
                        <div class="col-xl-4 col-sm-6 mb-5">
                            <a class="cardLink" href="conferences.php">
                                <div class="card  h-100">
                                <div class="card-body">
                                    <h2>Conferences</h2>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>   
        </div>
    </body>
</html>
