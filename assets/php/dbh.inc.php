<?php

$serverHost = "localhost";
$dbName = "rabano.ovh";
$dbUser = "admin";
$dbPass = "XFRCHhhVvZ8WhNoPWHPxZWZm*";
$conn = new mysqli($serverHost, $dbUser, $dbPass);
$conn->select_db($dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

