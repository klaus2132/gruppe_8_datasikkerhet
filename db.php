<?php
$servername = "localhost";
$username = "admin";
$password = "admin";
$dbname = "prosjekt_db";

// Opprett tilkobling
$conn = new mysqli($servername, $username, $password, $dbname);

// Sjekk tilkoblingen
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
