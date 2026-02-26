<?php
// includes/db.php

// MySQL server settings — adjust as needed
$dbHost   = '127.0.0.1';
$dbPort   = 3306;
$dbUser   = 'root';
$dbPass   = '';
$dbName   = 'fitflex_db';

// Create the connection
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

// Check for errors
if ($conn->connect_error) {
    die('DB connection failed: ' . $conn->connect_error);
}

// Ensure UTF-8 encoding
$conn->set_charset('utf8mb4');
