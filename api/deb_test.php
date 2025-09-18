<?php
require __DIR__.'/../backend/config/config.php';
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: ".$conn->connect_error);
}

echo "Connected successfully. Server version: ".$conn->server_version;