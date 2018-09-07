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
    
    
   
    
      
   $allBookChapters = "SELECT p.id, p.title, p.year, p.cited_by, p.page_start, p.page_end, bc.booktitle AS book, bc.bookisbn AS isbn, bc.bookpublisher AS publisher 
                        FROM publications p
                        INNER JOIN authorships a ON p.id = a.pub_id
                        INNER JOIN persons pe ON pe.id = a.pers_id
                        INNER JOIN di_professors d ON d.id = pe.prof_id
                        INNER JOIN book_chapters bc ON bc.pub_id = p.id
                        WHERE d.id = $thisProfId";
    
    
}
 else {
    $allBookChapters= "SELECT p.id, p.title, p.year, p.cited_by, p.page_start, p.page_end, bc.booktitle AS book, bc.bookisbn AS isbn, bc.bookpublisher AS publisher 
                        FROM publications p
                        INNER JOIN book_chapters bc ON bc.pub_id = p.id";
}
$result = mysqli_query($connection, $allBookChapters);

    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $pubId = $row["id"];
        $subData = array();
        $subData["id"] = $pubId;
        $subData["title"] = $row["title"];
        $subData["year"] = $row["year"];
        $subData["cited_by"] = $row["cited_by"];
        $subData["page_start"] = is_null($row["page_start"]) ? "-": $row["page_start"];
        $subData["page_end"] = is_null($row["page_end"]) ? "-": $row["page_end"];
        $subData["book"] = $row["book"];
        $subData["isbn"] = $row["isbn"];
        $subData["publisher"] = $row["publisher"];
        
        if(isset($thisProfId)){
            $authors = findThisPubAuthors($connection, $pubId, $thisProfId);
        }
        else{
            $authors = getAuthors($connection, $pubId);
        }
        
        

        $subData["authors"] = implode(", ", $authors);
        $data[] = $subData;
    }
    $json_data = array("data" => $data);
    echo json_encode($json_data);
    mysqli_close($connection);