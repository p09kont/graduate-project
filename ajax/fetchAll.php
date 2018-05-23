<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include("../Database.php");
include("../include/auxFunctions.php");
if (isset($_GET["id"])) {
    $thisProfId = $_GET["id"];
    $connection = connectToDB();

    $booksIds = getArrayOfIdsForDocumentTypeDetection($connection, "books");
    $chaptrersIds = getArrayOfIdsForDocumentTypeDetection($connection, "book_chapters");
    $jouralArticlesIds = getArrayOfIdsForDocumentTypeDetection($connection, "journal_articles");



    
    


    $allPublications = "SELECT p.id, p.title, d.lastname, p.year, p.cited_by FROM publications p
                        INNER JOIN authorships a ON p.id = a.pub_id
                        INNER JOIN persons pe ON pe.id = a.pers_id
                        INNER JOIN di_professors d ON d.id = pe.prof_id
                        WHERE d.id = '$thisProfId'";
    $result = mysqli_query($connection, $allPublications);

    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $pubId = $row["id"];
        $subData = array();
        $subData["id"] = $pubId;
        $subData["title"] = $row["title"];
        $subData["year"] = $row["year"];
        $subData["cited_by"] = $row["cited_by"];
        $docType = "";
        if (in_array($pubId, $booksIds)) {
            $docType = "Book";
        } elseif (in_array($pubId, $chaptrersIds)) {
            $docType = "Book Chapter";
        } elseif (in_array($pubId, $jouralArticlesIds)) {
            $docType = "Journal Article";
        } else {
            $docType = "Conference Article";
        }
        $subData["type"] = $docType;
        
        $authors = findThisPubAuthors($connection, $pubId, $thisProfId);



        $subData["authors"] = implode(", ", $authors);
        $data[] = $subData;
    }
    $json_data = array("data" => $data);
    echo json_encode($json_data);
    mysqli_close($connection);

    
}

function getArrayOfIdsForDocumentTypeDetection($link, $table) {
    $sql = "SELECT pub_id FROM $table";
    $query = mysqli_query($link, $sql);
    $ids = array();
    while ($row = mysqli_fetch_array($query)) {
        $ids[] = $row["pub_id"];
    }
    return $ids;
}
