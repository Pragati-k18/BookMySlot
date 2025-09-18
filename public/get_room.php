<?php

header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../backend/config/config.php'); // Include database connection

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed!"]));
}

// Fetch available rooms
$sql = "SELECT id, name, capacity, has_projector, has_smart_board, has_microphone, floor FROM rooms";
$result = $conn->query($sql);

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

// If no rooms are available, return an error
if (empty($rooms)) {
    echo json_encode(["response" => "❌ No rooms available. Please add rooms first."]);
    exit;
}

// Convert room data to JSON (shortened for AI)
$roomData = array_map(function ($room) {
    return [
        "name" => $room["name"],
        "capacity" => $room["capacity"],
        "projector" => $room["has_projector"],
        "smart_board" => $room["has_smart_board"],
        "microphone" => $room["has_microphone"],
        "floor" => $room["floor"]
    ];
}, $rooms);

$roomDataJSON = json_encode($roomData, JSON_PRETTY_PRINT);

// 🛑 **DEBUG: Log Data to Check API Input**
file_put_contents("ai_debug.log", "Sent to AI:\n" . $roomDataJSON . "\n", FILE_APPEND);

// ✅ OpenRouter API Key (Replace this with your actual key)
$apiKey = "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";

// ✅ AI Prompt
$prompt = "Here is a list of available seminar rooms:\n$roomDataJSON\n\n" .
    "Based on capacity, projector, smart board, and microphone availability, " .
    "recommend the best room for a seminar or meeting.";

// ✅ OpenRouter API URL
$url = "https://openrouter.ai/api/v1/chat/completions";

// ✅ API Request Data
$data = [
    "model" => "openchat/openchat-7b",
    "messages" => [["role" => "user", "content" => $prompt]],
    "max_tokens" => 150
];

// Convert data to JSON
$jsonData = json_encode($data);

// ✅ cURL Setup
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $apiKey",
    "Content-Type: application/json"
]);

// ✅ Execute API call
$response = curl_exec($ch);

// ✅ Check for cURL errors
if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    echo json_encode(["response" => "❌ API request failed: " . $error_msg]);
    exit;
}

curl_close($ch);

// ✅ Decode AI response
$responseData = json_decode($response, true);
$aiResponse = $responseData['choices'][0]['message']['content'] ?? "No room recommended.";

// 🛑 **DEBUG: Log AI Response**
file_put_contents("ai_debug.log", "AI Response:\n" . $aiResponse . "\n", FILE_APPEND);

// ✅ Extract recommended room name using better pattern matching
$recommendedRoom = null;
foreach ($rooms as $room) {
    if (stripos($aiResponse, $room['name']) !== false) {
        $recommendedRoom = $room;
        break;
    }
}

// ✅ Final Response
if ($recommendedRoom) {
    echo json_encode([
        "room" => $recommendedRoom['name'],
        "capacity" => $recommendedRoom['capacity'] ?? "N/A",
        "floor" => $recommendedRoom['floor'] ?? "N/A",
        "projector" => $recommendedRoom['has_projector'] ? "Yes" : "No",
        "smart_board" => $recommendedRoom['has_smart_board'] ? "Yes" : "No",
        "microphone" => $recommendedRoom['has_microphone'] ? "Yes" : "No"
    ]);
} else {
    echo json_encode(["response" => "❌ No suitable rooms found."]);
}

$conn->close();
?>
