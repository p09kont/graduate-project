<?php
include("../Database.php");

if (isset($_POST["id"])) {
    $thisProfId = $_POST["id"];
    //echo $id;
    $connection = connectToDB();
    // AFFILIATED CO-AUTHORS QUERY
    $affiliatedCoAuthorsSQL = "SELECT  t2.prof_id, t3.lastname, COUNT(t2.prof_id) AS plithos
                                FROM
                                  (SELECT p.id AS id, p.title AS title FROM publications p
                                INNER JOIN authorships a ON p.id = a.pub_id
                                INNER JOIN persons pe ON pe.id = a.pers_id
                                INNER JOIN di_professors d ON d.id = pe.prof_id
                                WHERE d.id = $thisProfId) AS t1
                                LEFT JOIN
                                  (SELECT  p.id AS id, p.title AS title, pe.name AS authors, pe.prof_id  FROM  di_professors d 
                                RIGHT JOIN persons pe ON d.id = pe.id
                                RIGHT JOIN authorships a ON pe.id = a.pers_id 
                                RIGHT JOIN publications p ON a.pub_id = p.id) AS t2 ON t1.id = t2.id
                                INNER JOIN (SELECT id AS id, lastname FROM di_professors) AS t3 ON t3.id =t2.prof_id 
                                WHERE (t2.prof_id IS NOT NULL AND t2.prof_id <> $thisProfId)
                                GROUP BY t2.prof_id";
    $result = mysqli_query($connection, $affiliatedCoAuthorsSQL);
    
    // Non Affil co - authors qurry
    
    $nonAffiliatedCoAuthorsSQL = "SELECT   t2.authors, COUNT(t2.authors) AS plithos, t2.prof_id, t2.peID
                FROM
                  (SELECT p.id AS id, p.title AS title FROM publications p
                INNER JOIN authorships a ON p.id = a.pub_id
                INNER JOIN persons pe ON pe.id = a.pers_id
                INNER JOIN di_professors d ON d.id = pe.prof_id
                WHERE d.id =$thisProfId) AS t1
                LEFT JOIN
                  (SELECT  p.id AS id, p.title AS title, pe.id AS peID, pe.name AS authors, pe.prof_id  FROM  di_professors d 
                RIGHT JOIN persons pe ON d.id = pe.id
                RIGHT JOIN authorships a ON pe.id = a.pers_id 
                RIGHT JOIN publications p ON a.pub_id = p.id) AS t2 ON t1.id = t2.id
                WHERE t2.prof_id IS NULL
                GROUP BY t2.authors
                ORDER BY plithos DESC";
    $nonAffilResult= mysqli_query($connection, $nonAffiliatedCoAuthorsSQL);
    
    
    $data = array();
    while ($row = mysqli_fetch_array($result)) {
        $subData = array();
        $profId = $row["prof_id"];
        $lastname = $row["lastname"];
        $plithos = $row["plithos"];
        //$subData["id"] = $row["prof_id"];
        $subData["name"] = "<a href=\"./professor.php?id=$profId\">$lastname</a>";
        $subData["plithos"] = "<a href=\"./affilMutual.php?id1=$thisProfId&id2=$profId\" >$plithos</a>";
        $data[] = $subData;
    }
    
    while ($nonAffilRow = mysqli_fetch_array($nonAffilResult)) {
        $subData = array();
        $persId = $nonAffilRow["peID"];
        $name = $nonAffilRow["authors"];
        $plithos = $nonAffilRow["plithos"];
        $subData["name"] = $name;
        $subData["plithos"] = "<a href=\"./noAffilMutual.php?id1=$thisProfId&id2=$persId\" >$plithos</a>";
        $data[] = $subData;
    }
    $dataToJSON = array("data" => $data);
    echo json_encode($dataToJSON);
    mysqli_close($connection);
    
}

