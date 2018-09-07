<?php

include("../../Database.php");
include("./function.php");
$dbc = connectToDB();
if (isset($_POST["user_id"])) {
    $id = $_POST["user_id"];
    $image = get_image_name($dbc, $id);
    $csv = get_csv_file_name($dbc, $id);
    if ($image != '') {
        unlink("../../resources/prof_photos/".$image);
    }
    if($csv !=''){
        unlink("../../resources/csv/".$csv);
    }
    
    $delete = "DELETE FROM di_professors WHERE id = $id";
    
    if(mysqli_query($dbc, $delete)){
        echo "Record deleted successfully";
    }
    mysqli_close($dbc);
}
//mysqli_close($conn);