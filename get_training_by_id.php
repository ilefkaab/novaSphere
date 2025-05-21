<?php

require_once 'connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'emplyee') {
   echo json_encode(['error' => 'Accès refusé']);
    http_response_code(403);
    exit();
}

$id = $_GET['id'] ?? '';

$stmt = $conn->prepare("SELECT id, title, description, start_date, end_date, created_at FROM trainings WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$training = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($training);

$stmt->close();
$conn->close();
?>
