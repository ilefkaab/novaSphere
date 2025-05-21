<?php
require_once 'connect.php';
 session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'visitor') {
   echo json_encode(['error' => 'Accès refusé']);
    http_response_code(403);
    exit();
}
header('Content-Type: application/json');

$id = intval($_GET['id']);

$sql = "SELECT * FROM joboffers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(["error" => "Offre non trouvée"]);
}

$stmt->close();
$conn->close();
