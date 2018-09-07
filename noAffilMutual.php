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

    $id1 = $_GET["id1"];
    $id2 = $_GET["id2"];
    //echo $id1 . " " . $id2;
    $sql1 = "SELECT firstname, lastname FROM di_professors WHERE id = $id1";
    $query1 = mysqli_query($dbc, $sql1);
    $result1 = mysqli_fetch_assoc($query1);
    $firstname = $result1["firstname"];
    $lastname = $result1["lastname"];
    $sql2 = "SELECT name FROM persons WHERE id = $id2";
    $query2 = mysqli_query($dbc, $sql2);
    $result2 = mysqli_fetch_assoc($query2);
    $nameOfNoAffilPerson = $result2["name"];
    ?>
    <head>
        <meta charset="UTF-8">
        <?php include("./include/head.php"); ?>
        <!-- link to css file -->
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>Muttual Publications</title>
    </head>
    <body>
        <?php
        //echo "$firstname , $lastname, $nameOfNoAffilPerson";
        $pageName = 'Professors';
        include("./include/nav.php");
        ?>
        <div id="theId"><?php echo $id1; ?></div>
        <div id="prof2Id"><?php echo $id2 ?></div>
        <div class="container">
            <h1>Mutual Publications</h1>
            <div class="card border-primary">
                <h2 class="card-header text-white text-center bg-primary"> <?php echo "<strong>$firstname $lastname</strong> and <strong>$nameOfNoAffilPerson</strong>"; ?> </h2>
                <div class="card-body">
                    <table id="no-affil-mutual-pubs-table" border="0" class="table table-striped  table-bordered"  cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Type</th>
                                <th>Cited by</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Year</th>
                                <th>Type</th>
                                <th>Cited by</th>
                            </tr>
                        </tfoot>
                    </table>  
                </div>
            </div>
        </div>
        <?php include("./include/footer.php");?>
        <script type="text/javascript" src="js/scripts.js"></script>
        <script type="text/javascript" src="js/DataTablesFormatFunctions.js"></script>
        <?php mysqli_close($dbc) ?>
    </body>
</html>
