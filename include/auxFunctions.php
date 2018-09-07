<?php

function findThisPubAuthors($link, $pubId, $profId, $adminRequest = false, $mutual = false) {
    $thisPubAuthors = "SELECT pe.name AS authors, pe.prof_id FROM persons pe 
                            INNER JOIN authorships a ON pe.id = a.pers_id 
                            INNER JOIN publications p ON a.pub_id = p.id
                            WHERE p.id = $pubId";
    $thisAuthorsResult = mysqli_query($link, $thisPubAuthors);
    $authors = array();
    while ($rowAuth = mysqli_fetch_assoc($thisAuthorsResult)) {
        $author = $rowAuth["authors"];
        $authID = $rowAuth["prof_id"];
        //echo $rowAuth["prof_id"]."<br>";
        //var_dump($rowAuth["prof_id"]);
        if (!$mutual) {
            if ($authID == $profId) {
                $authors[] = "<b>$author</b>";
            } else {
                if ($authID != null) {
                    $sql = "SELECT id FROM di_professors WHERE id = $authID";
                    $r = mysqli_query($link, $sql);
                    $sqlRow = mysqli_fetch_assoc($r);
                    $id = $sqlRow["id"];
                    if ($adminRequest) {
                        $authors[] = "<a href=\"publications.php?id=$id\">$author</a>";
                    } else {
                        $authors[] = "<a href=\"./professor.php?id=$id\">$author</a>";
                    }
                } else {
                    $authors[] = $author;
                }
            }
        }
        else{
            if ($authID != null) {
                    $sql = "SELECT id FROM di_professors WHERE id = $authID";
                    $r = mysqli_query($link, $sql);
                    $sqlRow = mysqli_fetch_assoc($r);
                    $id = $sqlRow["id"];                  
                        $authors[] = "<a href=\"./professor.php?id=$id\">$author</a>";
                } else {
                    $authors[] = $author;
                }
        }
    }
    return $authors;
}

function getAuthors($link, $pubId){
    $thisPubAuthors = "SELECT pe.name AS authors, pe.prof_id FROM persons pe 
                            INNER JOIN authorships a ON pe.id = a.pers_id 
                            INNER JOIN publications p ON a.pub_id = p.id
                            WHERE p.id = $pubId";
    $thisAuthorsResult = mysqli_query($link, $thisPubAuthors);
    $authors = array();
     while ($rowAuth = mysqli_fetch_assoc($thisAuthorsResult)){
        $author = $rowAuth["authors"];
        $authID = $rowAuth["prof_id"];
        if ($authID != null) {
                    $sql = "SELECT id FROM di_professors WHERE id = $authID";
                    $r = mysqli_query($link, $sql);
                    $sqlRow = mysqli_fetch_assoc($r);
                    $id = $sqlRow["id"];                  
                        $authors[] = "<a href=\"./professor.php?id=$id\">$author</a>";
                } else {
                    $authors[] = $author;
                }
     }
     return $authors;
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

function getThisProfId($link, $lastname) {
    $findThisProfId = "SELECT id FROM di_professors WHERE lastname = '$lastname'";
    $findRes = mysqli_query($link, $findThisProfId);
    $res = mysqli_fetch_assoc($findRes);
    $id = $res["id"];
    return $id;
}

function getProfessorsHIndex($arrayOfDocumentsCitations) {
    // $arrayOfDocumentsCitations mast be in ASCENDING order
    sort($arrayOfDocumentsCitations);
    $hIndex = 0;
    $length = count($arrayOfDocumentsCitations);
    for ($i = 0; $i < $length; $i++) {
        $smaller = min($arrayOfDocumentsCitations[$i], $length - $i);
        $hIndex = max($hIndex, $smaller);
    }
    return $hIndex;
}




