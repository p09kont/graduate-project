<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include("../Database.php");
include("../include/auxFunctions.php");
$connection = connectToDB();
if (isset($_GET["id"])) {
    $thisProfId = $_GET["id"];

    $allJournalArticles = "SELECT p.id, p.title, p.year, p.cited_by, p.page_start, p.page_end, ja.volume, ja.issue, j.name AS journal 
                        FROM publications p
                        INNER JOIN authorships a ON p.id = a.pub_id
                        INNER JOIN persons pe ON pe.id = a.pers_id
                        INNER JOIN di_professors d ON d.id = pe.prof_id
                        INNER JOIN journal_articles ja ON ja.pub_id = p.id
                        INNER JOIN journals j ON j.id = ja.journal_id
                        WHERE d.id = $thisProfId";
} else {
    if (isset($_POST["journalId"])) {
        $thisJournalId = $_POST["journalId"];
        $allJournalArticles = "SELECT p.id, p.title, p.year, p.page_start, p.page_end, p.cited_by, ja.volume, ja.issue, j.name AS journal
                                FROM publications p 
                                INNER JOIN journal_articles ja on ja.pub_id = p.id
                                INNER JOIN journals j ON j.id = ja.journal_id
                                WHERE ja.journal_id = $thisJournalId";
        
    } else {
        
        $allJournalArticles = "SELECT p.id, p.title, p.year, p.cited_by, p.page_start, p.page_end, ja.volume, ja.issue, j.name AS journal 
                        FROM publications p
                        INNER JOIN journal_articles ja ON ja.pub_id = p.id
                        INNER JOIN journals j ON j.id = ja.journal_id";
    }
}




$result = mysqli_query($connection, $allJournalArticles);

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
    $subData["volume"] = is_null($row["volume"]) ? "-" : $row["volume"];
    $subData["issue"] = is_null($row["issue"]) ? "-" : $row["issue"];
    $subData["journal"] = $row["journal"];

    if (isset($thisProfId)) {
        $authors = findThisPubAuthors($connection, $pubId, $thisProfId);
    } else {
        $authors = getAuthors($connection, $pubId);
    }


    $subData["authors"] = implode(", ", $authors);
    $data[] = $subData;
}
$json_data = array("data" => $data);
echo json_encode($json_data);
mysqli_close($connection);

