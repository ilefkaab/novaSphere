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
$action = $data['action'];

$status = ($action === 'accept') ? 'accepted' : 'rejected';

// Mettre à jour le statut de l'application
$stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $app_id);
$stmt->execute();

// Si acceptée, changer le rôle
if ($action === 'accept') {
    $updateRole = $conn->prepare("UPDATE users SET role = 'emplyee' WHERE id = ?");
    $updateRole->bind_param("i", $user_id);
    $updateRole->execute();
}

echo json_encode(["success" => true]);
$conn->close();
?>
