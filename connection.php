<?php
ob_start();
session_start();

include 'functions.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "covid";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>