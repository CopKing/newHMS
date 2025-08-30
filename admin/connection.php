<?php
ob_start(); // Start output buffering
session_start();
include('inc.functions.php');

// Database connection
$hostname = 'localhost';
$username = 'root'; 
$password = '';
$database = 'covid';

// Create connection
if($conn = mysqli_connect($hostname, $username, $password, $database)) {
    echo "$database connected successfully";
} else {
    echo "Connection failed: " . mysqli_connect_error();
}