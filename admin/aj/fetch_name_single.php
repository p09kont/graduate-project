<?php

include("../../Database.php");
$dbc = connectToDB();
if(isset($_POST["id"])){
    $id = $_POST["id"];
    $output = array();
    $select = "SELECT name FROM prof_names WHERE id = $id";
    $result = mysqli_query($dbc, $select);
    $row = mysqli_fetch_assoc($result);
    $output["name"] = $row["name"];
    echo json_encode($output);
}

