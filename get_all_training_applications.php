<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès refusé']);
    exit();
}

$sql = "SELECT ta.id, ta.status, ta.user_id, u.name as user_name, t.title as training_title
        FROM training_applications ta
        JOIN users u ON ta.user_id = u.id
        JOIN trainings t ON ta.training_id = t.id";

$result = $conn->query($sql);
$applications = [];

while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}

echo json_encode($applications);
$conn->close();
?>
