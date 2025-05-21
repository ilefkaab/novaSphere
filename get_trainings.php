<?php

require_once 'connect.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'emplyee') {
   echo json_encode(['error' => 'Accès refusé']);
    http_response_code(403);
    exit();
}

$sql = "SELECT id, title, description, start_date, end_date, created_at FROM trainings";
$result = $conn->query($sql);

$trainings = [];

while ($row = $result->fetch_assoc()) {
    $trainings[] = $row;
}

header('Content-Type: application/json');
echo json_encode($trainings);

$conn->close();
?>