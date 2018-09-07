<?php

session_start();
include("../Database.php");
$dbc = connectToDB();
if (isset($_POST["btn-login"])) {
    $username = mysqli_real_escape_string($dbc, trim($_POST["username"]));
    $password = mysqli_real_escape_string($dbc, trim($_POST["password"]));

    $sql = "SELECT * FROM administrator WHERE username = '$username'";
    $result = mysqli_query($dbc, $sql);


    $row = mysqli_fetch_assoc($result);
    if ($row["password"] == $password) {
        echo "OK";
        $_SESSION["admin_session"] = $row["id"];
    } else {
        echo "username or password does not exist.";
    }
}
