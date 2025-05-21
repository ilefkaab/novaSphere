<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès refusé']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$app_id = intval($data['app_id']);
$user_id = intval($data['user_id']);
$action = $data['action']; // 'accept' ou 'reject'

if (!in_array($action, ['accept', 'reject'])) {
    echo json_encode(['error' => 'Action invalide']);
    exit;
}

$status = ($action === 'accept') ? 'accepted' : 'rejected';

// Mise à jour de la candidature
$stmt = $conn->prepare("UPDATE training_applications SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $app_id);
$stmt->execute();

// Si acceptée → changer le rôle du user
if ($action === 'accept') {
    $updateUser = $conn->prepare("UPDATE users SET role = 'employee' WHERE id = ?");
    $updateUser->bind_param("i", $user_id);
    $updateUser->execute();
    $updateUser->close();
}

echo json_encode(["success" => true]);
$stmt->close();
$conn->close();
?>
