<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include("../../Database.php");

if(isset($_POST["user_id"])){
    $id = $_POST["user_id"];
    $output = array();
    
    $dbc = connectToDB();
    
    $query = "SELECT * FROM di_professors WHERE id = $id LIMIT 1";
    $result = mysqli_query($dbc, $query);
    while ($row = mysqli_fetch_array($result)) {
        $output["firstname"] = $row["firstname"];
        $output["lastname"] = $row["lastname"];
        $output["property"] = $row["property"];
        //$output["scopusId"] = $row["scopus_id"];
        if($row["image"] !=''){
            $output["user_image"] = '<img src="../resources/prof_photos/'.$row["image"].'" class="img-thumbnail" width="50" height="35" /><input type="hidden" name="hidden_user_image" value="'.$row["image"].'" />';
        }
        else{
            $output["user_image"] = '<input type="hidden" name="hidden_user_image" value="" />';
        }
        if($row["csv"] !=''){
            $output["user_csv"] = '<br/>'. $row["csv"].'<input type="hidden" name="hidden_user_csv" value="'.$row["csv"].'" />';
        } else {
            $output["user_csv"] = '<input type="hidden" name="hidden_user_csv" value="" />';
        }
    }
    echo json_encode($output);
    mysqli_close($dbc);
}