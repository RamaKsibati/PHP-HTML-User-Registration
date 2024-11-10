<?php
require 'db.php';

// Get the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;

if ($userId) {
    // Toggle user status
    $sql = "UPDATE users SET status = IF(status = 'active', 'suspended', 'active') WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $userId]);

    // Get the new status after update
    $stmt = $conn->prepare("SELECT status FROM users WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $newStatus = $stmt->fetchColumn();

    // Return the new status as JSON
    echo json_encode(['success' => true, 'newStatus' => $newStatus]);
} else {
    // Return an error if no user ID was provided
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
}

