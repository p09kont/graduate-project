<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function connectToDB() {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "research_db_test";
    $connection = mysqli_connect($host, $username, $password, $database);
    if (mysqli_connect_errno()) {
        echo "Αποτυχία σύνδεσης με την Bάση Δεδομένων" . mysqli_connect_error() . "<br/>";
    }
    mysqli_set_charset($connection, "utf8");
    return $connection;
}

function dbc() {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "research_db_test";
    $connection = mysqli_connect($host, $username, $password, $database);
    if (mysqli_connect_errno()) {
        echo "Αποτυχία σύνδεσης με την Bάση Δεδομένων" . mysqli_connect_error() . "<br/>";
    }
    mysqli_set_charset($connection, "utf8");
    return $connection;
}

function publicationHasInserted($link, $title) {
    $theTitle = mysqli_real_escape_string($link, trim($title));
    $select = "SELECT id FROM publications WHERE title = '$theTitle'";
    $result = mysqli_query($link, $select);
    
    if(mysqli_num_rows($result) > 0){
        return true;
    }
    else{
        return false;
    }
}

function selectIdFromPublications($link, $title) {
    $theTitle = mysqli_real_escape_string($link, trim($title));
    $select = "SELECT id FROM publications WHERE title = '$theTitle'";
    $result = mysqli_query($link, $select);
    return mysqli_num_rows($result);
}

function insertIntoPersons($link, $listOfAuthors) {
    foreach ($listOfAuthors as $a) {
        $trimAuthor = trim($a);
        $author = mysqli_real_escape_string($link, $trimAuthor);
        $select = "SELECT id FROM persons WHERE name = '$author'";
        $result = mysqli_query($link, $select);
        if (mysqli_num_rows($result) == 0) {
            $ionianId = selectProfId($link, $author);
            $prof_id = !is_null($ionianId) ? $ionianId : "NULL";
            //echo $prof_id;
            $insertPerson = "INSERT INTO persons (name, prof_id) VALUES ('$author' , $prof_id)";
            $r = mysqli_query($link, $insertPerson);
            if (!$r) {
                echo mysqli_error($link);
            }
        }
    }
}

function selectProfId($link, $name) {
    $select = "SELECT prof_id FROM prof_names WHERE name = '$name' ";
    $result = mysqli_query($link, $select);
    if (mysqli_num_rows($result) == 0) {
        return null;
    } else {
        $row = mysqli_fetch_assoc($result);
        $id = $row["prof_id"];
        return $id;
    }
}

function insertIntoPublications($link, $title, $year, $pageStart, $pageEnd, $citedBy) {
    $theTitle = mysqli_real_escape_string($link, trim($title));
    $theYear = mysqli_real_escape_string($link, trim($year));
    $firstPage = mysqli_real_escape_string($link, trim($pageStart));
    $lastPage = mysqli_real_escape_string($link, trim($pageEnd));
    $cited = mysqli_real_escape_string($link, trim($citedBy));
    $citations = empty($cited) ? 0 : $cited;
    $page_start = empty($firstPage) ? "NULL" : "'$firstPage'";
    $page_end = empty($lastPage) ? "NULL" : "'$lastPage'";
    //echo $citations;
    //$select = "SELECT id FROM publications WHERE title = '$theTitle'";
    //$result = mysqli_query($link, $select);
    //if(mysqli_num_rows($result) == 0){
    $insertPublications = "INSERT INTO publications (title, year, page_start, page_end, cited_by)"
            . "VALUES ('$theTitle', '$theYear', $page_start, $page_end, $citations)";
    if (mysqli_query($link, $insertPublications)) {
        $last_id = mysqli_insert_id($link);
        //echo $last_id;
        return $last_id;
    } else {
        echo mysqli_error($link);
        return null;
    }
    //}
}

function insertIntoBooks($link, $id, $isbn, $publisher) {
    $theIsbn = mysqli_real_escape_string($link, trim($isbn));
    $thePublisher = mysqli_real_escape_string($link, trim($publisher));
    $ISBN = empty($theIsbn) ? "NULL" : "'$theIsbn'";
    $insertBook = "INSERT INTO books (pub_id, isbn, publisher) "
            . "VALUES ($id, $ISBN, '$thePublisher')";
    $result = mysqli_query($link, $insertBook);
    if (!$result) {
        echo mysqli_error($link);
    }
}

function insertIntoAuthorships($link, $pub_id, $listOfAuthors) {
    foreach ($listOfAuthors as $a) {
        $trimAuthor = trim($a);
        $author = mysqli_real_escape_string($link, $trimAuthor);
        $select = "SELECT id FROM persons WHERE name = '$author'";
        $result = mysqli_query($link, $select);
        $row = mysqli_fetch_assoc($result);
        $pers_id = $row["id"];
        $insertAuthorship = "INSERT INTO authorships (pub_id, pers_id) "
                . "VALUES($pub_id, $pers_id)";
        $r = mysqli_query($link, $insertAuthorship);
        if (!$r) {
            echo mysqli_error($link);
        }
    }
}

function inserIntoBookChapters($link, $pub_id, $bookTitle, $bookIsbn, $bookPublisher) {
    $theBookTitle = mysqli_real_escape_string($link, trim($bookTitle));
    $theBookIsbn = mysqli_real_escape_string($link, trim($bookIsbn));
    $theBookPublisher = mysqli_real_escape_string($link, trim($bookPublisher));
    $bookISBN = empty($theBookIsbn) ? "NULL" : "'$theBookIsbn'";
    $insertBook = "INSERT INTO book_chapters (pub_id, booktitle, bookisbn, bookpublisher) "
            . "VALUES ($pub_id, '$theBookTitle', $bookISBN , '$theBookPublisher')";
    $result = mysqli_query($link, $insertBook);
    if (!$result) {
        echo mysqli_error($link);
    }
}

function insertIntoJournalArticles($link, $pub_id, $volume, $issue, $journalName) {
    $theVolume = mysqli_real_escape_string($link, trim($volume));
    $theIssue = mysqli_real_escape_string($link, trim($issue));
    $articleVolume = empty($theVolume) ? "NULL" : "'$theVolume'";
    $articleIssue = empty($theIssue) ? "NULL" : "'$theIssue'";
    $select = "SELECT id FROM journals WHERE name = '$journalName'";
    $result = mysqli_query($link, $select);
    $row = mysqli_fetch_assoc($result);
    $journal_id = $row["id"];
    $insertJournalArticle = "INSERT INTO journal_articles (pub_id, volume, issue, journal_id) "
            . "VALUES ($pub_id, $articleVolume, $articleIssue, $journal_id)";
    $r = mysqli_query($link, $insertJournalArticle);
    if (!$r) {
        echo mysqli_error($link);
    }
}

/* function journalHasInserted($link, $name) {
  $select = "SELECT id FROM journals WHERE name = '$name'";
  $result = mysqli_query($link, $select);
  if (mysqli_num_rows($result) == 0) {
  return false;
  }
  else{
  return true;
  }
  } */

function insertIntoJournals($link, $name) {
    $theName = mysqli_real_escape_string($link, trim($name));
    $select = "SELECT id FROM journals WHERE name = '$theName'";
    $result = mysqli_query($link, $select);
    if (mysqli_num_rows($result) == 0) {
        $insertJournal = "INSERT INTO journals (name) VALUES ('$theName')";
        $r = mysqli_query($link, $insertJournal);
        if (!$r) {
            echo mysqli_error($link);
        }
    }
}

function insertIntoProccendings($link, $name) {
    $theName = mysqli_real_escape_string($link, trim($name));
    $select = "SELECT id FROM proccendings WHERE name = '$theName'";
    $result = mysqli_query($link, $select);
    if (mysqli_num_rows($result) == 0) {
        $insertProc = "INSERT INTO proccendings (name) VALUES ('$theName')";
        $r = mysqli_query($link, $insertProc);
        if (!$r) {
            echo mysqli_error($link);
        }
    }
}

function insertIntoConferences($link, $name, $date, $location, $volume, $procName) {
    $theName = mysqli_real_escape_string($link, trim($name));
    $theDate = mysqli_real_escape_string($link, trim($date));
    $theLocation = mysqli_real_escape_string($link, trim($location));
    $theVolume = mysqli_real_escape_string($link, trim($volume));
    $theProcName = mysqli_real_escape_string($link, trim($procName));
    $confDate = empty($theDate) ? "NULL" : "'$theDate'";
    $confLocation = empty($theLocation) ? "NULL" : "'$theLocation'";
    $procVolume = empty($theVolume) ? "NULL" : "'$theVolume'";
    $selectConf = "SELECT id FROM conferences WHERE name = '$theName' AND date = '$theDate'";
    $resultConf = mysqli_query($link, $selectConf);
    if (mysqli_num_rows($resultConf) == 0) {
        //echo "<h3>$theName</h3>";
        $select = "SELECT id FROM proccendings WHERE name = '$theProcName'";
        $result = mysqli_query($link, $select);
        $row = mysqli_fetch_assoc($result);
        $proc_id = $row["id"];
        //echo $row["id"] . "<br/>";
        $insertConference = "INSERT INTO conferences (name, date, location, proc_id, volume) "
                . "VALUES ('$theName', $confDate, $confLocation, $proc_id, $procVolume)";
        $r = mysqli_query($link, $insertConference);
        if (!$r) {
            echo mysqli_error($link);
        }
    }
}

function insertIntoConferenceArticles($link, $pub_id, $confName, $confDate) {
    $theConfName = mysqli_real_escape_string($link, trim($confName));
    $theConfDate = mysqli_real_escape_string($link, trim($confDate));
    $date = empty($theConfDate) ? "NULL" : "'$theConfDate'";
    //$theConfLocation = empty($confLocation) ? "NULL" : "'$confLocation'";
    $select = "SELECT id FROM conferences WHERE name = '$theConfName' AND date = $date";
    $result = mysqli_query($link, $select);
    if (!$result) {
        echo mysqli_error($link);
    } else {
        $row = mysqli_fetch_assoc($result);
        $conf_id = $row["id"];
        $insertConferenceArticle = "INSERT INTO conference_articles (pub_id, conf_id) "
                . "VALUES ($pub_id, $conf_id)";
        $r = mysqli_query($link, $insertConferenceArticle);
        if (!$r) {
            echo mysqli_error($link);
        }
    }
}

function isJournalArticle($link, $id) {
    $select = "SELECT pub_id FROM journal_articles WHERE pub_id = $id";
    $result = mysqli_query($link, $select);
    if(mysqli_num_rows($result) > 0 ){
        return true;
    }
    else{
        return false;
    }
}

function updatePersonIfIsProfessorAndHasNullProfidInPersonsTable($link, $listOfAuthors) {
    foreach ($listOfAuthors as $a) {
        $trimAuthor = trim($a);
        $author = mysqli_real_escape_string($link, $trimAuthor);
        $select = "SELECT id, prof_id FROM persons WHERE name = '$author'";
        $result = mysqli_query($link, $select);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $id = $row["id"];
            $profId = $row["prof_id"];
            //if (is_null($profId)) {
            //    echo "NULL <br>";
            //}
            //else{
            //    echo "NOT NULL <br>";
            //}
            $ionianId = selectProfId($link, $author);
            //echo $author." ". $ionianId ."<br>";
            if((!is_null($ionianId)) && ($ionianId != $profId)) {
                //echo "OK <br>";
                $update = "UPDATE persons SET prof_id = $ionianId WHERE id = $id";
                if(!mysqli_query($link, $update)){
                     echo mysqli_error($link);
                }
            }
        }
    }
}

function updateCitations($link, $title, $citedBy) {
    $theTitle = mysqli_real_escape_string($link, trim($title));
    $select = "SELECT id, cited_by FROM publications WHERE title = '$theTitle'";
    $result = mysqli_query($link, $select);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result);
        if ($row[cited_by] < $citedBy) {
            $id = $row["id"];
            $update = "UPDATE publications SET cited_by = $citedBy WHERE id = $id";
            if (mysqli_query($link, $update)) {
                return true;
            } else {
                echo mysqli_error($link);
                return false;
            }
        } else {
            return false;
        }
    }
}
