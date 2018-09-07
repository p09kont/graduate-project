<?php



if (isset($_GET["id"])) {
    $id = $_GET["id"];
    include("../Database.php");
    include("../include/auxFunctions.php");

    $conn = connectToDB();

    $sql = "SELECT p.title, p.cited_by FROM publications p 
                INNER JOIN authorships a ON p.id = a.pub_id
                INNER JOIN persons pe ON a.pers_id = pe.id
                INNER JOIN di_professors d ON pe.prof_id = d.id
                WHERE d.id = $id
                ORDER BY p.cited_by DESC";
    $result = mysqli_query($conn, $sql);

    

    $line1 = array();
    $citations = array();


    $paper = 1;

    while ($row = mysqli_fetch_array($result)) {
        $subdata = array();
        $subdata["paper"] = $paper;
        $subdata["citations"] = intval($row["cited_by"]);
        $subdata["title"] = $row["title"];
       
        $paper++;


        $line1[] = $subdata;
        $citations[] = $subdata["citations"];
       
    }

    sort($citations);
    
    $x = getProfessorsHIndex($citations);

    $hIndex = array();
    $hIndexCords = array();
    $hIndexCords["x"] = $x;
    $hIndexCords["y"] = $x;
    $hIndex[] = $hIndexCords;

    $toJSON = array("line1" => $line1, "hIndex" => $hIndex);
    echo json_encode($toJSON);
    mysqli_close($conn);
}

