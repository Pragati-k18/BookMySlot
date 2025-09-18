<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../backend/config/config.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $required = ['date', 'start_time', 'end_time', 'capacity'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Build query with flexible capacity (20% buffer)
    $minCapacity = max(1, $input['capacity'] * 0.8);
    $maxCapacity = $input['capacity'] * 1.2;

    $sql = "SELECT * FROM rooms 
            WHERE capacity BETWEEN ? AND ?
            AND available = 1";

    $params = [$minCapacity, $maxCapacity];
    $types = "ii";

    // Add equipment requirements
    if (!empty($input['requirements'])) {
        if (in_array('microphone', $input['requirements'])) {
            $sql .= " AND has_microphone = 1";
        }
        if (in_array('projector', $input['requirements'])) {
            $sql .= " AND has_projector = 1";
        }
    }

    // Check availability
    $sql .= " AND id NOT IN (
        SELECT room_id FROM bookings 
        WHERE date = ? 
        AND NOT (end_time <= ? OR start_time >= ?)
    ) ORDER BY capacity ASC
    LIMIT 5";  // Return top 5 closest matches

    array_push($params, $input['date'], $input['start_time'], $input['end_time']);
    $types .= "sss";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }

    if (empty($rooms)) {
        // Suggest alternatives when no exact matches
        $altSql = "SELECT * FROM rooms 
                  WHERE available = 1
                  AND capacity >= ?
                  AND id NOT IN (
                      SELECT room_id FROM bookings 
                      WHERE date = ? 
                      AND NOT (end_time <= ? OR start_time >= ?)
                  )
                  ORDER BY capacity ASC
                  LIMIT 3";

        $stmt = $conn->prepare($altSql);
        $stmt->bind_param("isss", $input['capacity'], $input['date'], $input['start_time'], $input['end_time']);
        $stmt->execute();
        $altResult = $stmt->get_result();

        $alternatives = [];
        while ($row = $altResult->fetch_assoc()) {
            $alternatives[] = $row;
        }

        echo json_encode([
            'success' => false,
            'message' => 'No exact matches found',
            'alternatives' => $alternatives
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'rooms' => $rooms
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>