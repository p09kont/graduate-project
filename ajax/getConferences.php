<?php

include("../Database.php");
$connection = connectToDB();
$sql="SELECT c.id, c.name, c.date, c.location, c.proc_id, c.volume, COUNT(ca.conf_id) AS articles FROM conferences c 
        INNER JOIN conference_articles ca ON c.id = ca.conf_id
        GROUP BY c.name
        ORDER BY articles DESC";
$query = mysqli_query($connection, $sql);
$data = array();
while ($row = mysqli_fetch_array($query)) {
    $subData = array();
    $id = $row["id"];
    $numberOfarticles = $row["articles"]; 
    $subData["id"] = $id;
    $subData["name"] = $row["name"];
    $subData["date"] = is_null($row["date"]) ? "-" : $row["date"];
    $subData["location"] = is_null($row["location"]) ? "-" : $row["location"];
    $subData["proc_id"] = $row["proc_id"];
    $subData["volume"] = is_null($row["volume"]) ? "-" : $row["volume"];
    $subData["articles"] = "<a href=\"./conference.php?id=$id\" >$numberOfarticles</a>";
    $data[] = $subData;
}
$dataToJSON = array("data" => $data);
echo json_encode($dataToJSON);
mysqli_close($connection);


