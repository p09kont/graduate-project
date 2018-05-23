<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include("../Database.php");
$connection = connectToDB();
/*$request = mysqli_real_escape_string($connection, $_POST["query"]);
//$author = $_GET["author"];
$sql = "SELECT name FROM persons WHERE name LIKE '%$request%' ORDER BY name LIMIT 10";
$result = mysqli_query($connection, $sql);
$data = array();
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row["name"];
    }
    echo json_encode($data);
}*/

$request = mysqli_real_escape_string($connection, $_GET["term"]);
//$author = $_GET["term"];
$sql = "SELECT name FROM persons WHERE name LIKE '%$request%' ORDER BY name LIMIT 10";
$result = mysqli_query($connection, $sql);
$data = array();
while ($row = mysqli_fetch_array($result)) {
    $data[] = $row["name"];
}

echo json_encode($data);
mysqli_close($connection);
