<?php

function get_image_name($link, $user_id){
    $query = "SELECT image FROM di_professors WHERE id = $user_id";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($result);
    return $row["image"];
            
}

function get_csv_file_name($link, $user_id) {
    $query = "SELECT csv FROM di_professors WHERE id = $user_id";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($result);
    return $row["csv"];
}

function upload_image($lastId) {
    $extension = explode(".",$_FILES["user_image"]["name"]);
    $new_name = $lastId.".".$extension[1];
    $destination = '../../resources/prof_photos/'.$new_name;
    move_uploaded_file($_FILES["user_image"]["tmp_name"], $destination);
    return $new_name;
}

function upload_csv($lastId) {
    $extension = explode(".", $_FILES["user_csv"]["name"]);
    $new_name = $lastId.".".$extension[1];
    $destination = '../../resources/csv/'.$new_name;
    move_uploaded_file($_FILES["user_csv"]["tmp_name"], $destination);
    return $new_name;
}