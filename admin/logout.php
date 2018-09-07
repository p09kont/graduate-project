<?php

session_start();
unset($_SESSION["admin_session"]);
if(session_destroy()){
    header("Location: index.php");
}

