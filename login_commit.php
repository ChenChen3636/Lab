<?php
ini_set("display_errors", "1");
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/vendor/autoload.php';


$collection = (new MongoDB\Client)->logindb->account;

$id = $_POST['name'];
$pw = $_POST['password'];

$document = $collection->findone(['username' => $id]);

//var_dump($document["username"]);

if ($id == $document["username"] && $pw == $document["password"] && $id != "" && $pw != "") {
    $_SESSION['name'] = 'true';
    setcookie("hi", $id, "time()+3600");
    header('Location: flashball.php');
} else {
    $_SESSION['name'] = "";
}

if ($_SESSION['name'] == "") {

    header('Location: index.html');
}

   // var_dump($_SESSION);
