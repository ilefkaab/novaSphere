<?php
require_once 'connect.php';
session_start();

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'emplyee') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès refusé']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupère les crédits de congé
$stmt = $conn->prepare("SELECT leave_days FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$data = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($data);

$stmt->close();
$conn->close();
?>
