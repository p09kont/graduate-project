<?php


include("../../Database.php");


$dbc = connectToDB();

$col =array(
    1   => 'image',
    2   =>  'id',
    3   =>  'firstname',
    4   =>  'lastname',
    5   =>  'property',
    //6   =>  'scopus_id'
);
$query = '';
$output = array();
$query .= "SELECT * FROM di_professors ";
$queryResult = mysqli_query($dbc, $query);
$totalData = mysqli_num_rows($queryResult);
//$totalFilter = $totalData;

$searchQuery = "SELECT * FROM di_professors ";

if (!empty($_POST["search"]["value"])) {
    //$ord = $_POST["search"]["value"];
    $searchQuery .= 'WHERE id LIKE "%' . $_POST["search"]["value"] . '%" ';
    $searchQuery .= 'OR firstname LIKE "%' . $_POST["search"]["value"] . '%" ';
    $searchQuery .= 'OR lastname LIKE "%' . $_POST["search"]["value"] . '%" ';
    $searchQuery .= 'OR property LIKE "%' . $_POST["search"]["value"] . '%" ';
    //$searchQuery .= 'OR scopus_id LIKE "%' . $_POST["search"]["value"] . '%" ';
}
//if (isset($_POST["order"])) {
    //$query .= 'ORDER BY ' . $_POST['order']['0']['column'] . ' ' . $_POST['order']['0']['dir'] . ' ';
//} else {
    //$query .= 'ORDER BY id ASC ';
//}
//if ($_POST["length"] != -1) {
    //$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
//}

$searchResult = mysqli_query($dbc, $searchQuery);
if(!$searchResult){
    echo mysqli_error($dbc);
}
$totalFilter = mysqli_num_rows($searchResult);

$searchQuery .=" ORDER BY ".$col[$_REQUEST['order'][0]['column']]."   ".$_REQUEST['order'][0]['dir']."  LIMIT ".
    $_REQUEST['start']."  ,".$_REQUEST['length']."  ";

$searchResult = mysqli_query($dbc, $searchQuery);
//$statement = $connection->prepare($query);
//$statement->execute();
//$result = $statement->fetchAll();

$data = array();
//$filtered_rows = $statement->rowCount();
/*foreach ($result as $row) {
    $id = $row["id"];
    $q = "SELECT name FROM prof_names WHERE prof_id = $id";
    $statement = $connection->prepare($q);
    $statement->execute();
    $r = $statement->fetchAll();
    $names = array();
    foreach ($r as $ro) {

        $names[] = $ro["name"];
    }

    $image = '';
    if ($row["image"] != '') {
        $image = '<img src="upload/' . $row["image"] . '" class="img-thumbnail" width="50" height="35" />';
    } else {
        $image = '';
    }
    $sub_array = array();
    $sub_array["image"] = $image;
    $sub_array["id"] = $id;
    $sub_array["firstname"] = $row["firstname"];
    $sub_array["lastname"] = $row["lastname"];
    $sub_array["property"] = $row["property"];
    $sub_array["scopusId"] = $row["scopus_id"];
    $sub_array["othesNames"] = implode(", ", $names);
    $sub_array["update"] = '<button type="button" name="update" id="' . $row["id"] . '" class="btn btn-warning btn-sm update">Update</button>';
    $sub_array["delete"] = '<button type="button" name="delete" id="' . $row["id"] . '" class="btn btn-danger btn-sm delete">Delete</button>';
    $data[] = $sub_array;
}*/

while ($row = mysqli_fetch_array($searchResult)) {
    $sub_array = array();
    $id = $row["id"];
    $q = "SELECT name FROM prof_names WHERE prof_id = $id";
    $r = mysqli_query($dbc, $q);
    $names = array();
    while ($rowR = mysqli_fetch_array($r)) {
        $names[] = $rowR["name"];
    }
    if($row["image"] != ''){
        $image = '<img src="../resources/prof_photos/' . $row["image"] . '" class="img-thumbnail" width="50" height="35" />';
    }else{
        $image = '<img src="../resources/prof_photos/avatar.png"  class="img-thumbnail" width="50" height="35" />';
    }
    if($row["csv"] != ''){
        $csv = $row["csv"];
    }else{
        $csv = '';
    }
    
    $sub_array["DT_RowId"] = "row_$id";
    $sub_array["image"] = $image;
    $sub_array["id"] = $id;
    $sub_array["firstname"] = $row["firstname"];
    $sub_array["lastname"] = $row["lastname"];
    $sub_array["property"] = $row["property"];
    //$sub_array["scopusId"] = $row["scopus_id"];
    $sub_array["csv"] = $csv;
    if(!is_null($row["updated"])){
        $lastUpdate = strtotime($row["updated"]);
        $date = date("d/m/Y", $lastUpdate);
    }
    else{
        $date = "--";
    }
    
    $sub_array["lastUpdate"] = $date;
    $sub_array["otherNames"] = implode(", ", $names);
    $sub_array["data"] =  '<a href="names.php?id='.$id.'" class="btn btn-info btn-sm scopusNames" role="button">Names</a>&nbsp;'
                        . '<button type="button" name="insert" id="' . $row["id"] . '" class="btn btn-warning btn-sm insert" value="' . $row["firstname"] . ' '. $row["lastname"] . '">Insert</button>';
    $sub_array["manage"] = '<a href="publications.php?id=' . $id . '" class="btn btn-primary btn-sm publications" role="button">Publications</a>&nbsp;'
                         . '<button type="button" name="edit" id="' . $row["id"] . '" class="btn btn-success btn-sm edit">Edit</button>&nbsp;'
                         . '<button type="button" name="delete" id="' . $row["id"] . '" class="btn btn-danger btn-sm delete">Delete</button>';
    $data[] = $sub_array;
    
}


$output = array(
    "draw"              => intval($_POST["draw"]),
    "recordsTotal"      => intval($totalData),
    "recordsFiltered"   => intval($totalFilter),
    "data" => $data
);
echo json_encode($output);

//. '<button type="button" name="download" id="' . $row["id"] . '" class="btn btn-dark btn-sm download">Download</button>&nbsp;'
//<button type="button" name="scopusNames" id="' . $row["id"] . '" class="btn btn-info btn-sm scopusNames">Names</button>&nbsp;
//"<a href=\"./professor.php?id=$id\">$author</a>";
//<button type="button" name="publicatoons" id="' . $row["id"] . '" class="btn btn-primary btn-sm publications">Publications</button>&nbsp;'