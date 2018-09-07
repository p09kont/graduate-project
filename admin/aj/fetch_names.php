<?php

$profId = $_GET["id"];
include("../../Database.php");

$dbc = connectToDB();

$col = array(
    0 => 'name',
);
//echo $col[0];
$data = array();
$query = "SELECT * FROM prof_names WHERE prof_id = $profId";
$result = mysqli_query($dbc, $query);
$totalData = mysqli_num_rows($result);
$totalFilter = $totalData;

//$searchQuery = "SELECT name FROM prof_names WHERE prof_";
if (!empty($_POST["search"]["value"])) {
    
    $query .= " AND name LIKE '%" . $_POST["search"]["value"] . "%'";
    $searchResult = mysqli_query($dbc, $query);
    
    $totalFilter = mysqli_num_rows($searchResult);
}



$query .= " ORDER BY " . $col[$_POST["order"][0]["column"]] . " " . $_POST["order"][0]["dir"] . " LIMIT " .
        $_POST["start"] . " , " . $_POST["length"] . " ";
//echo $_POST["order"][0]["column"];
$finalResult = mysqli_query($dbc, $query);
if(!$finalResult){
    echo mysqli_error($dbc);
}
while ($row = mysqli_fetch_array($finalResult)) {
    $sub_array = array();
  
    $sub_array["id"] = $row["id"];
    $sub_array["name"] = $row["name"];
    $sub_array["edit"] = '<button type="button" name="edit" id="' . $row["id"] . '" class="btn btn-success btn-sm edit">Edit</button>';
    $sub_array["delete"] = '<button type="button" name="delete" id="' . $row["id"] . '" class="btn btn-danger btn-sm delete">Delete</button>';
    $data[] = $sub_array;
}
$json_data = array(
    "drawn" => intval($_POST["draw"]),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFilter),
    "data" => $data
);
echo json_encode($json_data);

