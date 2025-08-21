<?php
$host = "localhost";
$user = "root";   // adjust if needed
$pass = "";       // adjust if needed
$db   = "ASMS";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
