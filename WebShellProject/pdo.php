<?php

session_start();

$servername = "localhost";
$uname = "mhuy";
$pass = "se150815@fpt.edu.vn";
$dbname = "webshell";

$pdo = new PDO(
    "mysql:host=$servername;
        dbname=$dbname",
    $uname,
    $pass,
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($pdo) {
    $_SESSION['connect'] = "Connect successfully";
} else {
    $_SESSION['connect'] = "Connect failed";
}
// print($_SESSION['connect']);