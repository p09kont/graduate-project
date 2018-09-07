<?php

include("../Database.php");
$conn = connectToDB();

$sql = "SELECT id, firstname, lastname, property FROM di_professors";
$result = mysqli_query($conn, $sql);

$nodes = array();
$links = array();
$ids = array();

//mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_array($result)) {
    $subdata = array();
    $id = $row["id"];
    
    $subdata["id"] = $id;
    $subdata["lastname"] = $row["lastname"];

    $commonPublicationPerAuthor = "SELECT  t2.authors, t2.prof_id, COUNT(t2.prof_id) AS numOfPublications
		FROM
  		(SELECT p.id AS id, p.title AS title FROM publications p
			INNER JOIN authorships a ON p.id = a.pub_id
		INNER JOIN persons pe ON pe.id = a.pers_id
		INNER JOIN di_professors d ON d.id = pe.prof_id
		WHERE d.id =$id) AS t1
		LEFT JOIN
  		(SELECT  p.id AS id, p.title AS title, pe.name AS authors, pe.prof_id  FROM  di_professors d 
		RIGHT JOIN persons pe ON d.id = pe.id
		RIGHT JOIN authorships a ON pe.id = a.pers_id 
		RIGHT JOIN publications p ON a.pub_id = p.id) AS t2 ON t1.id = t2.id
		WHERE (t2.prof_id IS NOT NULL AND t2.prof_id <> $id)
		GROUP BY t2.prof_id";

    $commonResult = mysqli_query($conn, $commonPublicationPerAuthor);
    $subdata["influence"] = mysqli_num_rows($commonResult);
    $ids[] = $id;
    while ($row1 = mysqli_fetch_array($commonResult)) {
        $linkDatails = array();
        if (!in_array($row1["prof_id"], $ids)) {
            $linkDatails["source"] = $id; 
            $linkDatails["target"] = $row1["prof_id"]; 
            $linkDatails["weight"] = intval($row1["numOfPublications"]);
            $links[] = $linkDatails;
        }
    }
    $nodes[] = $subdata;
}
$toJSON = array("nodes" => $nodes, "links" => $links);
echo json_encode($toJSON);
mysqli_close($conn);

