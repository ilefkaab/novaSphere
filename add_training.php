<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo "⛔ Accès refusé.";
    exit;
}

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

if (empty($title) || empty($description) || empty($start_date) || empty($end_date)) {
    echo "❗ Veuillez remplir tous les champs.";
    exit;
}

$stmt = $conn->prepare("INSERT INTO trainings (title, description, start_date, end_date, created_at) VALUES (?, ?, ?, ?, NOW())");
$stmt->bind_param("ssss", $title, $description, $start_date, $end_date);

if ($stmt->execute()) {
    echo "✅ Formation ajoutée avec succès.";
} else {
    echo "❌ Erreur : " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
