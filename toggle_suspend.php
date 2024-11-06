<?php
require 'db.php';
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'];

$sql = "UPDATE users SET status = IF(status = 'active', 'suspended', 'active') WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $userId]);

// Get new status for response
$stmt = $conn->prepare("SELECT status FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$newStatus = $stmt->fetchColumn();

echo json_encode(['success' => true, 'newStatus' => $newStatus]);
