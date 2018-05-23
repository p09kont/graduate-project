<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
include("./Database.php");
if (isset($_POST["search"])) {
    $responce = "<ul><li>No data found!</li></ul>";
    $connection = connectToDB();
    $q = mysqli_real_escape_string($connection, $_POST["q"]);
    $sql = $sql = "SELECT name FROM persons WHERE name LIKE '%$q%' ORDER BY name LIMIT 10";
    $result = mysqli_query($connection, $sql);
    if (mysqli_num_rows($result) > 0) {
        $responce = '<ul>';
        while ($row = mysqli_fetch_array($result)) {
            $responce .= '<li>' . $row["name"] . '</li>';
        }
        $responce .= '</ul>';
    }
    exit($responce);
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--Bootstrap  libraries -->
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
        <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>

        <!--Jquery  libraries -->
        <script  src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.js"></script>



        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>Search</title>
        <style type="text/css">
           /* ul {
                float: left;
                list-style: none;
                padding: 0px;
                border: 1px solid black;
                margin-top: 0px;
                width: 100%;
            }
            li:hover{
                color: white;
                background: royalblue;
            }*/

            .ui-autocomplete {
                position: absolute;
                z-index: 1000;
                cursor: default;
                padding: 0;
                margin-top: 2px;
                list-style: none;
                background-color: #ffffff;
                border: 1px solid #ccc;
                    -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
                -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            }
            .ui-autocomplete > li {
                padding: 3px 20px;
            }
            .ui-autocomplete > li.ui-state-focus {
                background-color: #DDD;
            }
            .ui-helper-hidden-accessible {
                display: none;
            }


        </style>
    </head>
    <body>
        <?php
        // put your code here
        ?>

        <br /><br />
        <div class="container">
            <h2 align="center">Autocomplete Textbox using Bootstrap Typeahead with Ajax PHP</h2>
            <br /><br />
            <label>Search</label>
            <!--<input type="text" name="person" id="person" class="form-control input-lg" autocomplete="off" placeholder="Type Name" />-->
            <br /><br />
            <input type="text" placeholder="search" id="searchBox" class="form-control"/>
            <div id="response"></div>

            <br /><br />
            <!--<form action="" method="post">-->
                <div class="form-group ui-widget">
                <label for="author-search">Tags: </label>
                <input type="text" placeholder="Name" id="author-search" class="form-control" />
                </div>
           <!-- </form>-->

        </div>
        <br /><br />

        <br /><br />






        <form>
            <input type="text" name="author" list="authors" id="sugest"/>
            <datalist id="authors">

            </datalist>
        </form>

     <!--   <script type="text/javascript">
            $(document).ready(function () {
                $('#sugest').keyup(function () {
                    var author = $(this).val();
                    console.log(author);
                    $.get("./ajax/suggest.php", {name: $(this).val()}, function (data) {
                        $('datalist').empty();
                        $('datalist').html(data);
                    });
                    //var author = $(this).val();
                    //$.ajax({
                    //    url: "./ajax/suggest.php",
                    //    method: "GET",
                    //    data: {author: author},
                    //    success: function (data) {
                    //        $('datalist').empty();
                    //        $('datalist').html(data);
                    //    }

                    //});
                });
            });
        </script> -->
        <!--<script>
            $(document).ready(function () {
                $('#person').typeahead({
                    source: function (query, result)
                    {
                        $.ajax({
                            //url: "./ajax/suggest.php",
                            method: "POST",
                            data: {query: query},
                            dataType: "json",
                            success: function (data)
                            {
                                result($.map(data, function (item) {
                                    return item;
                                }));
                            }
                        });
                    }
                });
            });
        </script>-->
        <script type="text/javascript">
            $(document).ready(function () {
                $('#searchBox').keyup(function () {
                    var query = $(this).val();

                    if (query.length > 0) {
                        $.ajax({
                            url: "search.php",
                            method: "POST",
                            data: {
                                search: 1,
                                q: query
                            },
                            dataType: "text",
                            success: function (data) {
                                $('#response').html(data);
                            }
                        });

                    }
                });

                $(document).on('click', 'li', function () {
                    var name = $(this).text();
                    $('#searchBox').val(name);
                    $('#response').html("");
                });
            });
        </script>

        <script type="text/javascript">
            $(function () {
                $('#author-search').autocomplete({
                    source: "./ajax/suggest.php",
                    minLength: 1
                });
            });
        </script>
    </body>
</html>
