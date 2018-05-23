<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include("../Database.php");
if (isset($_GET["id"])) {
    $thisProfId = $_GET["id"];
    $dbc = connectToDB();
    $query = "SELECT year, SUM(cited_by) AS citations FROM publications p
            INNER JOIN authorships a ON a.pub_id = p.id
            INNER JOIN persons pe ON pe.id = a.pers_id
            INNER JOIN di_professors d ON d.id = pe.prof_id
            WHERE d.id = $thisProfId
            GROUP BY p.year";
    $result = mysqli_query($dbc, $query);
    mysqli_close($dbc);
    $table = array();
    $table["cols"] = array(
        array("id" => "", "label" => "Year", "type" => "string"/* was string but changed for testing */),
        array("id" => "", "label" => "citations", "type" => "number")
    );
    $rows = array();
    foreach ($result as $row) {
        $temp = array();
        $temp[] = array("v" => (string) /* was string but changed for testing like above */$row["year"]);
        $temp[] = array("v" => (int) $row["citations"]);
        $rows[] = array("c" => $temp);
    }
    $result->free();
    $table["rows"] = $rows;
    $jsonTable = json_encode($table, true);
    echo $jsonTable;
}
