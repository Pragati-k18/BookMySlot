<?php
// Load environment variables
$env_path = __DIR__.'/../../.env'; // Adjust path as needed

if (file_exists($env_path)) {
    $env_vars = parse_ini_file($env_path);
    foreach ($env_vars as $key => $value) {
        putenv("$key=$value");
    }
} else {
    die(json_encode(['error' => 'Missing .env file']));
}

// Database configuration
$host = "localhost";
$username = "";
$password = "";
$database = "bookmyslot";

// OpenRouter configuration
define('OPENROUTER_API_KEY', getenv('OPENROUTER_API_KEY'));

// Establish a connection to MySQL
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>
