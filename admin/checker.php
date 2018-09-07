<?php

header('Content-Type: applicatio/json');
$file = str_replace(".", "", $_GET["file"]);
$file = "tmp/" . $file . ".txt";
if (file_exists($file)) {
    $text = file_get_contents($file);
    echo $text;
    $obj = json_decode($text);
    if ($obj->percent == 100) {
        unlink($file);
    }
    
} else {
    echo json_encode(
            array(
                "check" => null,
                "percent" => null,
                "message" => null,
                "inserted" => null,
                "updated" => null
    ));
}

