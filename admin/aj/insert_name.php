<?php
include("../../Database.php");

$dbc = connectToDB();

$insertedName = mysqli_real_escape_string($dbc, trim($_POST["name"]));
$insertedNameParts = explode(" ", $insertedName);
$lengthOfFirstString = strlen($insertedNameParts[0]);
//echo $insertedNameParts[0]."  ";
$insertedNameParts[0] = preg_replace("/,/", "", $insertedNameParts[0]);
$name = $insertedNameParts[0]." ";
for($i=1; $i<count($insertedNameParts); $i++){
    $name .= $insertedNameParts[$i];
}
$profId = $_POST["prof_id"];

if(isset($_POST["operation"])){
    if($_POST["operation"] == "Add"){
        
        $insert = "INSERT INTO prof_names (prof_id, name) VALUES ($profId, '$name')";
        $result = mysqli_query($dbc, $insert);
        if($result){
            //echo $name ." ";
            echo "Data inserted";
        } else {
           echo mysqli_error($dbc);    
        }
        
    }
    
    if(($_POST["operation"]) == "Edit"){
        $id = $_POST["prof_id"];
        $update = "UPDATE prof_names SET name = '$name' WHERE id = $id";
        $result = mysqli_query($dbc, $update);
        if ($result) {
            echo "Data updated";
        } else {
            echo mysqli_error($dbc); 
        }
    }
    
}
mysqli_close($dbc);
