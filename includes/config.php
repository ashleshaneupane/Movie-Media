<?php
date_default_timezone_set("Asia/Kathmandu");

$host = "localhost";
$username = "root";
$password = "";
$database = "MovieMedia";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

?>