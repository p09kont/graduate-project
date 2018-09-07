<?php

include("../Database.php");
$conn = connectToDB();
if (isset($_GET["id"])) {
    $id = $_GET["id"];

    $firstAndLastYear = "SELECT MIN(year) AS firstYear, MAX(year) AS lastYear FROM publications p
                                INNER JOIN authorships a ON p.id = a.pub_id
                                INNER JOIN persons pe ON pe.id = a.pers_id
                                INNER JOIN di_professors d ON d.id = pe.prof_id
                                WHERE d.id = $id";

    $numOfPapersPerYearSql = "SELECT  p.year, COUNT(p.id) AS numOfPapers FROM 
                                di_professors d 
                                INNER JOIN persons pe ON d.id = pe.prof_id
                                INNER JOIN authorships a ON pe.id = a.pers_id
                                INNER JOIN publications p ON a.pub_id = p.id
                                where d.id = $id
                                GROUP BY p.year
                                ORDER BY p.year ASC";
} else {
    
    $firstAndLastYear = "SELECT MIN(year) AS firstYear, MAX(year) AS lastYear FROM publications p";
    $numOfPapersPerYearSql = "SELECT  p.year, COUNT(p.id) AS numOfPapers FROM publications p
                                GROUP BY p.year
                                ORDER BY p.year ASC";
}

$runFirstLastYear = mysqli_query($conn, $firstAndLastYear);
$firstLastYearRow = mysqli_fetch_array($runFirstLastYear);
$firstYear = $firstLastYearRow["firstYear"];
$lastYear = $firstLastYearRow["lastYear"];


$numOfPapersPerYearResult = mysqli_query($conn, $numOfPapersPerYearSql);
$yearLineChartData = array();

while ($numOfPapersPerYearRow = mysqli_fetch_array($numOfPapersPerYearResult)) {
    $theYear = $numOfPapersPerYearRow["year"];
    $yearLineChartData["$theYear"] = $numOfPapersPerYearRow["numOfPapers"];
}

for ($i = $firstYear; $i <= $lastYear; $i++) {
    if (!array_key_exists($i, $yearLineChartData)) {
        $yearLineChartData["$i"] = 0;
    }
}
ksort($yearLineChartData);
$line = array();
foreach ($yearLineChartData as $key => $value) {
    //echo "$key $value <br>"; 
    $subdata = array();
    $subdata["year"] = intval($key);
    $subdata["publications"] = intval($value);
    $line[] = $subdata;
}
$toJSON = array("line" => $line);
echo json_encode($toJSON);
mysqli_close($conn);
