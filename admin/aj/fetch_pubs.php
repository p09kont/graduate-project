<?php

include("../../Database.php");
include("../../include/auxFunctions.php");
if (isset($_GET["id"])) {
    $thisProfId = $_GET["id"];
    $connection = connectToDB();
    //$connection = dbc();

    $col = array(
        1 => 'title',
        2 => 'year',
        4 => 'cited_by'
    );

    $booksIds = getArrayOfIdsForDocumentTypeDetection($connection, "books");
    $chaptrersIds = getArrayOfIdsForDocumentTypeDetection($connection, "book_chapters");
    $jouralArticlesIds = getArrayOfIdsForDocumentTypeDetection($connection, "journal_articles");







    $allPublications = "SELECT p.id, p.title, d.lastname, p.year, p.cited_by, p.page_start, p.page_end FROM publications p
                        INNER JOIN authorships a ON p.id = a.pub_id
                        INNER JOIN persons pe ON pe.id = a.pers_id
                        INNER JOIN di_professors d ON d.id = pe.prof_id
                        WHERE d.id = '$thisProfId'";
    $result = mysqli_query($connection, $allPublications);
    $totalData = mysqli_num_rows($result);
    //$totalFilter = $totalData;

    if (!empty($_POST["search"]["value"])) {
        $allPublications .= " AND (p.title LIKE '%" . $_POST["search"]["value"] . "%' ";
        $allPublications .= " OR p.year LIKE '%" . $_POST["search"]["value"] . "%' ";
        $allPublications .= " OR p.cited_by LIKE '%" . $_POST["search"]["value"] . "%' )";

    }
    
    $searchResult = mysqli_query($connection, $allPublications);
    
    if(!$searchResult){
    echo mysqli_error($connection);
    }
    
    $totalFilter = mysqli_num_rows($searchResult);

    $allPublications .= " ORDER BY " . $col[$_POST["order"][0]["column"]] . " " . $_POST["order"][0]["dir"] . " LIMIT " .
            $_POST["start"] . " ," . $_POST["length"] . " ";

    $finalResult = mysqli_query($connection, $allPublications);
    if (!$finalResult) {
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

        $authors = findThisPubAuthors($connection, $pubId, $thisProfId);



        $subData["authors"] = implode(", ", $authors);
        $data[] = $subData;
    }
    $json_data = array(
        "drawn"             => intval($_POST["draw"]),
        "recordsTotal"      => intval($totalData),
        "recordsFiltered"   => intval($totalFilter),
        "data"              => $data);
    echo json_encode($json_data);
    mysqli_close($connection);
}

