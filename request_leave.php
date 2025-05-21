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
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$reason = $_POST['reason'] ?? '';

if (!$start_date || !$end_date || !$reason) {
    http_response_code(400);
    echo json_encode(['error' => 'Champs manquants']);
    exit();
}
$stmt = $conn->prepare("INSERT INTO leave_requests (user_id, start_date, end_date, reason, status) VALUES (?, ?, ?, ?, 'pending')");
$stmt->bind_param("isss", $user_id, $start_date, $end_date, $reason);

if ($stmt->execute()) {
    echo json_encode(['success' => 'Demande envoyée avec succès']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l\'enregistrement']);
}

$stmt->close();
$conn->close();
?>
