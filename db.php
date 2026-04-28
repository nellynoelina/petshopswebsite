<?php
$host = "localhost";
$user = "root"; // or your username
$pass = "";     // your DB password
$db = "petshop";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
