<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <?php
    include("./Database.php");
    $dbc = connectToDB();
    $id = $_GET["id"];
    $sql = "SELECT c.name, c.date, c.location FROM conferences c WHERE c.id = $id";
    $query = mysqli_query($dbc, $sql);
    $result = mysqli_fetch_assoc($query);
    ?>
    <head>
        <meta charset="UTF-8">
        <?php include("./include/head.php"); ?>

        <!-- link to css file -->
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>Conference</title>
    </head>
    <body>
        <?php
        $pageName = 'Conferences';
        include("./include/nav.php");
        ?>
        <div id="theId"><?php echo $id; ?></div>
        <div class="container">
            <h2>Conference</h2>
            <hr>
            <ul>
                <li>
                    <b>Name:</b> <?php echo $result["name"] ?>
                </li>
                <?php
                if (!is_null($result["date"])) {
                    ?>
                    <li>
                        <b>Date:</b> <?php echo $result["date"] ?>
                    </li>
                    <?php
                }
                if(!is_null($result["location"])){
                    ?>
                    <li>
                        <b>Location:</b> <?php echo $result["location"] ?>
                    </li>
                <?php
                    
                }
                ?>      
            </ul>
            <div class="card border-primary">
                <h3 class="card-header text-white text-center bg-primary">Articles</h3>
                <div class="card-body">
                    <table id="this-conference-articles-table" border="0" class="test table table-striped  table-bordered"  cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Cited by</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Cited by</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <?php include("./include/footer.php"); ?>
        <script type="text/javascript" src="js/conferenceDataTables.js"></script>
        <script type="text/javascript" src="js/DataTablesFormatFunctions.js"></script>
        <?php mysqli_close($dbc) ?>
    </body>
</html>
