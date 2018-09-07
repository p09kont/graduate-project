<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include("../Database.php");
include("../include/auxFunctions.php");
$connection = connectToDB();
$allPublications = "";

$booksIds = getArrayOfIdsForDocumentTypeDetection($connection, "books");
$chaptrersIds = getArrayOfIdsForDocumentTypeDetection($connection, "book_chapters");
$jouralArticlesIds = getArrayOfIdsForDocumentTypeDetection($connection, "journal_articles");
$admin = false;
$mutual = false;
if (isset($_GET["id"])) {
    //$admin = false;
    if (isset($_GET["a"])) {
        $admin = true;
    }

    $thisProfId = $_GET["id"];
    //$connection = connectToDB();
    if (isset($_GET["id2"])) {
        $mutual = true;
        //echo $_GET["affil"];
        $author2Id = $_GET["id2"];
        $allPublications = "SELECT t1.id, t1.title, t1.year, t1.cited_by, t1.page_start, t1.page_end, t2.authors, t2.prof_id, t2.peId
                        FROM
                          (SELECT p.id AS id, p.title, p.year, p.cited_by, p.page_start, p.page_end FROM publications p
                        INNER JOIN authorships a ON p.id = a.pub_id
                        INNER JOIN persons pe ON pe.id = a.pers_id
                        INNER JOIN di_professors d ON d.id = pe.prof_id
                        WHERE d.id =$thisProfId) AS t1
                        LEFT JOIN
                          (SELECT  p.id AS id, pe.name AS authors, pe.id AS peID, pe.prof_id  FROM  di_professors d 
                        RIGHT JOIN persons pe ON d.id = pe.id
                        RIGHT JOIN authorships a ON pe.id = a.pers_id 
                        RIGHT JOIN publications p ON a.pub_id = p.id) AS t2 ON t1.id = t2.id ";

        if (isset($_GET["affil"]) && $_GET["affil"] == 'true') {
            $allPublications .= "WHERE t2.prof_id = $author2Id";
            //echo "T";
        }
        if (isset($_GET["affil"]) && $_GET["affil"] == 'false') {
            $allPublications .= "WHERE t2.peId = $author2Id";
            //echo "F";
        }
    } else {
        $allPublications = "SELECT p.id, p.title, d.lastname, p.year, p.cited_by, p.page_start, p.page_end FROM publications p
                        INNER JOIN authorships a ON p.id = a.pub_id
                        INNER JOIN persons pe ON pe.id = a.pers_id
                        INNER JOIN di_professors d ON d.id = pe.prof_id
                        WHERE d.id = $thisProfId ";

        // An evazes edo to if(isset($year)) nomizo pos einai ok!
        if(isset($_GET["year"])){
            $year = $_GET["year"];
            $allPublications .= "AND p.year = $year";
        }
    }







    //$allPublications = "SELECT p.id, p.title, d.lastname, p.year, p.cited_by, p.page_start, p.page_end FROM publications p
    //                    INNER JOIN authorships a ON p.id = a.pub_id
    //                    INNER JOIN persons pe ON pe.id = a.pers_id
    //                    INNER JOIN di_professors d ON d.id = pe.prof_id
    //                    WHERE d.id = $thisProfId";
} else {
    $allPublications = "SELECT p.id, p.title, p.year, p.cited_by, p.page_start, p.page_end FROM publications p";
}

$result = mysqli_query($connection, $allPublications);
if (!$result) {
    echo mysqli_error($connection);
}

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $pubId = $row["id"];
    $subData = array();
    $subData["id"] = $pubId;
    $subData["title"] = $row["title"];
    $subData["year"] = $row["year"];
    $subData["cited_by"] = $row["cited_by"];
    $subData["page_start"] = is_null($row["page_start"]) ? "-" : $row["page_start"];
    $subData["page_end"] = is_null($row["page_end"]) ? "-" : $row["page_end"];
    $docType = "";
    if (in_array($pubId, $booksIds)) {
        $docType = "Book";
        $sql = "SELECT b.isbn AS isbn, b.publisher AS publisher FROM books b 
                     WHERE b.pub_id= $pubId";
        $query = mysqli_query($connection, $sql);
        $queryRow = mysqli_fetch_assoc($query);
        $subData["isbn"] = $queryRow["isbn"];
        $subData["publisher"] = $queryRow["publisher"];
    } elseif (in_array($pubId, $chaptrersIds)) {
        $docType = "Book Chapter";
        $sql = "SELECT bc.booktitle AS book, bc.bookisbn AS isbn, bc.bookpublisher AS publisher FROM book_chapters bc 
                     WHERE bc.pub_id= $pubId";
        $query = mysqli_query($connection, $sql);
        $queryRow = mysqli_fetch_assoc($query);
        $subData["book"] = $queryRow["book"];
        $subData["isbn"] = $queryRow["isbn"];
        $subData["publisher"] = $queryRow["publisher"];
    } elseif (in_array($pubId, $jouralArticlesIds)) {
        $docType = "Journal Article";
        $sql = "SELECT ja.volume, ja.issue, j.name AS journal FROM journal_articles ja 
                     INNER JOIN journals j ON j.id = ja.journal_id
                     WHERE ja.pub_id= $pubId";
        $query = mysqli_query($connection, $sql);
        $queryRow = mysqli_fetch_assoc($query);
        $subData["volume"] = is_null($queryRow["volume"]) ? "-" : $queryRow["volume"];
        $subData["issue"] = is_null($queryRow["issue"]) ? "-" : $queryRow["issue"];
        $subData["journal"] = $queryRow["journal"];
    } else {
        $docType = "Conference Article";
        $sql = "SELECT c.name AS conference, c.date, c.location, c.volume, pr.name AS proccendings FROM conference_articles ca
                    INNER JOIN conferences c ON c.id = ca.conf_id
                    INNER JOIN proccendings pr ON pr.id = c.proc_id
                    WHERE ca.pub_id = $pubId";
        $query = mysqli_query($connection, $sql);
        $queryRow = mysqli_fetch_assoc($query);
        $subData["volume"] = is_null($queryRow["volume"]) ? "-" : $queryRow["volume"];
        $subData["conference"] = $queryRow["conference"];
        $subData["date"] = is_null($queryRow["date"]) ? "-" : $queryRow["date"];
        $subData["location"] = is_null($queryRow["location"]) ? "-" : $queryRow["location"];
        $subData["proc"] = $queryRow["proccendings"];
    }
    $subData["type"] = $docType;
    if(isset($thisProfId)){
        $authors = findThisPubAuthors($connection, $pubId, $thisProfId, $admin, $mutual);
    }
    else{
        $authors = getAuthors($connection, $pubId);
    }
    



    $subData["authors"] = implode(", ", $authors);
    $subData["checkbox"] = '<input type="checkbox" name="publ_id[]" class="delete_publ" value="' . $pubId . '" /></td>';
    $data[] = $subData;
}
//$data = functionName($connection, $allPublications, $booksIds, $chaptrersIds, $jouralArticlesIds, $thisProfId, $admin);
$json_data = array("data" => $data);
echo json_encode($json_data);
mysqli_close($connection);
