<?php
include("../Database.php");
$connection = connectToDB();
$sql = "SELECT j.id, j.name, COUNT(ja.journal_id) AS articles FROM journals j 
        INNER JOIN journal_articles ja ON j.id = ja.journal_id
        GROUP BY j.name
        ORDER BY articles DESC";
$query = mysqli_query($connection, $sql);
$data = array();
while ($row = mysqli_fetch_array($query)) {
    $subData = array();
    $articles = $row["articles"];
    $id = $row["id"];
    $subData["id"] = $id;
    $subData["name"] = $row["name"];
    $subData["articles"] = "<a href=\"./journal.php?id=$id\" >$articles</a>";
    $data[] = $subData;
}
$dataToJSON = array("data" => $data);
echo json_encode($dataToJSON);
mysqli_close($connection);


