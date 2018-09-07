<?php

include("../../Database.php");

$dbc = connectToDB();
if (isset($_POST["id"])) {
    $id = $_POST["id"];
    $delete = "DELETE FROM prof_names WHERE id = $id";
    $result = mysqli_query($dbc, $delete);
    if($result){
        echo "Record deleted successfully";
    } else {
        echo mysqli_error($dbc);
    }
}
