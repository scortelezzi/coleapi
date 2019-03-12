<?php
$servername = "localhost";
$dbname = "colegio";
$username = "colegio";
$password = "relojito";

$conn = new mysqli($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>