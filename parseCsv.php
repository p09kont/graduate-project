<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Parse CSV</title>
    </head>
    <body>
        <?php
        include("./Database.php");
        $conn = connectToDB();
        //$sql = "SELECT lastname FROM di_professors LIMIT 3";
        //Limit 3 for testing
        
        $sql = "SELECT lastname FROM di_professors";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<h2>" . $row["lastname"] . "</h2>";
                $lastname = $row["lastname"];
                //read csv file as a string
                $csv = file_get_contents("./csv/$lastname.csv", FILE_USE_INCLUDE_PATH);
                //convert string to array 
                $lines = explode("\n", $csv);
                $linesLength = count($lines);
                //echo $linesLength."<br>";
                //remove the last lement of lines if empty because causes problems to parsing
                if ($lines[$linesLength - 1] == "") {
                    array_pop($lines);
                }
                //remove the first element from the array. This element is the header of CSV file
                $head = str_getcsv(array_shift($lines), ",", '"');
                //echo $head[0] ."<br />";
                $head[0] = "Authors";
                echo "<br />";
                // count how many filds has the header of the file
                $headLength = count($head);
                echo "<br />";
            
                foreach ($lines as $key => $line) {
                    //$data[]= array_combine($head, str_getcsv($line));
                    //echo $i ." ". $key. " " . count((str_getcsv($line, ",", '"'))) . "<br/>";
                    if (count(str_getcsv($line, ",", '"')) == 45) {
                        //echo $line . "<br>";
                        $lineForFixing = $line;
                        //echo $lineForFixing."<br />";
                        $regexForDetectBrokenPart = '/\s"[^"].+?",/';
                        $regexForDetectTowExtraFilds = '/",{3}"/';
                        if (preg_match($regexForDetectBrokenPart, $lineForFixing, $match)) {
                            $brokenPart = $match[0];
                            $fixedPart = str_replace('"', '""', $brokenPart);
                            //echo $fixedPart."<br />";
                            $fixedLine = preg_replace($regexForDetectBrokenPart, $fixedPart, $lineForFixing);
                            //echo $fixedLine . "<br />";
                            $lines[$key] = $fixedLine;
                        } else if (preg_match($regexForDetectTowExtraFilds, $lineForFixing, $match)) {
                            $extraCommas = $match[0];
                            $oneComma = '","';
                            $fixedLine = preg_replace($regexForDetectTowExtraFilds, $oneComma, $lineForFixing);
                            $lines[$key] = $fixedLine;
                        }
                    }
                    if (count(str_getcsv($line, ",", '"')) == 44) {
                        $lineForFixing = $line;
                        //echo $lineForFixing."<br />";
                        $regexForDetectTowExtraFilds = '/",{2}"/';
                        if (preg_match($regexForDetectTowExtraFilds, $lineForFixing, $match)) {
                            $extraCommas = $match[0];
                            $oneComma = '","';
                            $fixedLine = preg_replace($regexForDetectTowExtraFilds, $oneComma, $lineForFixing);
                            $lines[$key] = $fixedLine;
                        }
                    }

                    if (count(str_getcsv($line, ",", '"')) == 42) {
                        $lineForFixing = $line;
                        //echo $lineForFixing."<br />";
                        $regexForDetectTowExtraFilds = '/",{6}"/';
                        if (preg_match($regexForDetectTowExtraFilds, $lineForFixing, $match)) {
                            $extraCommas = $match[0];
                            $oneComma = '",,,,,,,"';
                            $fixedLine = preg_replace($regexForDetectTowExtraFilds, $oneComma, $lineForFixing);
                            $lines[$key] = $fixedLine;
                        }
                    }                    
                }
                echo "<hr/>";

                //define an array that holds data
                $data = array();

                foreach ($lines as $line) {
                    $data[] = array_combine($head, str_getcsv($line));
                }
                
                foreach ($data as $key => $value) {
                    //defanie helpul variables
                    $title = $data[$key]["Title"];
                    $authors = $data[$key]["Authors"];
                    $year = $data[$key]["Year"];
                    $citedBy = $data[$key]["Cited by"];

                    $listOfAuthors = (explode(",", $authors));
                    $documentType = $data[$key]["Document Type"];
                    $source = $data[$key]["Source title"];
                    $pageStart = $data[$key]["Page start"];
                    $pageEnd = $data[$key]["Page end"];
                    $allIsbns = explode(";", $data[$key]["ISBN"]);
                    $isbn = $allIsbns[0];
                    $publisher = $data[$key]["Publisher"];
                    $volume = $data[$key]["Volume"];

                    $issue = $data[$key]["Issue"];
                    $confName = $data[$key]["Conference name"];
                    $confDate = $data[$key]["Conference date"];
                    $confLocation = $data[$key]["Conference location"];
                    $procName = $data[$key]["Source title"];
                    $procVolume = $data[$key]["Volume"];
                    
                    //Start inserting to DB
                    $hasInserted = publicationHasInserted($conn, $title);
                    if (!$hasInserted) {
                        switch ($documentType) {
                            case "Article":
                            case "Article in Press":
                            case "Review":
                                
                                $pub_id = insertIntoPublications($conn, $title, $year, $pageStart, $pageEnd, $citedBy);
                                if ($pub_id != null) {
                                    insertIntoPersons($conn, $listOfAuthors);
                                    insertIntoAuthorships($conn, $pub_id, $listOfAuthors);
                                    insertIntoJournals($conn, $source);
                                    insertIntoJournalArticles($conn, $pub_id, $volume, $issue, $source);
                                }
                                break;
                                
                            case "Conference Paper":

                                $pub_id = insertIntoPublications($conn, $title, $year, $pageStart, $pageEnd, $citedBy);
                                if ($pub_id != null) {
                                    insertIntoPersons($conn, $listOfAuthors);
                                    insertIntoAuthorships($conn, $pub_id, $listOfAuthors);
                                    if (empty($confName)) {
                                        insertIntoJournals($conn, $source);
                                        insertIntoJournalArticles($conn, $pub_id, $volume, $issue, $source);
                                    } else {
                                        insertIntoProccendings($conn, $procName);
                                        //echo "<br/> procs <br/>";
                                        insertIntoConferences($conn, $confName, $confDate, $confLocation, $procVolume, $procName);
                                        //echo " Confs <br/>";
                                        insertIntoConferenceArticles($conn, $pub_id, $confName, $confDate);
                                        //echo "Conf Artic<br/>";
                                    }
                                }
                                break;

                            case "Editorial":

                                if (!empty($pageStart)) {
                                    $pub_id = insertIntoPublications($conn, $title, $year, $pageStart, $pageEnd, $citedBy);
                                    if ($pub_id != null) {
                                        insertIntoPersons($conn, $listOfAuthors);
                                        insertIntoAuthorships($conn, $pub_id, $listOfAuthors);
                                        if (empty($confName)) {
                                            insertIntoJournals($conn, $source);
                                            insertIntoJournalArticles($conn, $pub_id, $volume, $issue, $source);
                                        } else {
                                            insertIntoProccendings($conn, $procName);
                                            //echo "<br/> procs <br/>";
                                            insertIntoConferences($conn, $confName, $confDate, $confLocation, $procVolume, $procName);
                                            //echo " Confs <br/>";
                                            insertIntoConferenceArticles($conn, $pub_id, $confName, $confDate);
                                            //echo "Conf Artic<br/>";
                                        }
                                    }
                                }
                                break;

                            case "Book":

                                $pub_id = insertIntoPublications($conn, $title, $year, $pageStart, $pageEnd, $citedBy);
                                if ($pub_id != null) {
                                    insertIntoPersons($conn, $listOfAuthors);
                                    insertIntoAuthorships($conn, $pub_id, $listOfAuthors);
                                    insertIntoBooks($conn, $pub_id, $isbn, $publisher);
                                }
                                break;

                            case "Book Chapter":

                                $pub_id = insertIntoPublications($conn, $title, $year, $pageStart, $pageEnd, $citedBy);
                                if ($pub_id != null) {
                                    insertIntoPersons($conn, $listOfAuthors);
                                    insertIntoAuthorships($conn, $pub_id, $listOfAuthors);
                                    inserIntoBookChapters($conn, $pub_id, $source, $isbn, $publisher);
                                }
                                break;

                            default:
                                break;
                        }
                    }
                    //echo "<hr/>";
                }
            }
        }
        mysqli_close($conn);
        ?>

    </body>
</html>
