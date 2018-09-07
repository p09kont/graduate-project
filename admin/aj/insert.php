<?php


include("../../Database.php");
include("./function.php");

$dbc = connectToDB();


$firstname = mysqli_real_escape_string($dbc, trim($_POST["first_name"]));
$lastname = mysqli_real_escape_string($dbc, trim($_POST["last_name"]));
$property = mysqli_real_escape_string($dbc, trim($_POST["property"]));



if (isset($_POST["operation"])) {
    if ($_POST["operation"] == "Add") {



        $image = '';
        $csv = '';
        
        $insert = "INSERT INTO di_professors (firstname, lastname, property) "
                . "VALUES ('$firstname', '$lastname', '$property')";
        $result = mysqli_query($dbc, $insert);
        
        $last_id = mysqli_insert_id($dbc);
        
        if($_FILES["user_image"]["name"] != ''){
            $image =  upload_image($last_id);
        }
        if($_FILES["user_csv"]["name"] != ''){
            $csv = upload_csv($last_id);
        }
        
        if (!empty($result)) {
            $update = "UPDATE di_professors SET image = '$image', csv = '$csv' WHERE id = $last_id";
            if(mysqli_query($dbc, $update)){
                echo "New record created successfully.";
            } else {
                echo  mysqli_error($dbc);
            }
                   
        } else {
            echo  mysqli_error($dbc);
        }
    }

    if ($_POST["operation"] == "Edit") {
        
        $id = $_POST["user_id"];

        $image = '';
        if ($_FILES["user_image"]["name"] != "") {
            $image = upload_image($id);
        } else {
            $image = $_POST["hidden_user_image"];
        }
        $csv = '';
        if($_FILES["user_csv"]["name"] != ""){
            $csv = upload_csv($id);
        } else {
            $csv = $_POST["hidden_user_csv"];
        }
        
        
        $update = "UPDATE di_professors SET firstname = '$firstname', lastname = '$lastname', "
                . "property = '$property', csv = '$csv', image = '$image' WHERE id = $id";
        if (mysqli_query($dbc, $update)) {
            echo "Data updated";
        }
    }
}
mysqli_close($dbc);
