<?php

include("../../Database.php");
$dbc = connectToDB();
if(isset($_POST["id"])){
    foreach ($_POST["id"] as $id) {
        $query = "DELETE FROM publications WHERE id = $id";
        $result= mysqli_query($dbc, $query);
        if(!$result){
            echo mysqli_error($dbc);
        }
    }
    echo "Records deleted";
}

