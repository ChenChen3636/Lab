<?php
    session_start();
    $auth = $_SESSION["name"];
    if($auth !== "true"){
        header('Location: index.html');
    }
    else{
        
    }
    ?>