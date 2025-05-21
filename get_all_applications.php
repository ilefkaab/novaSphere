<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès refusé']);
    exit();
}
$sql ="SELECT a.id, a.status, a.user_id, u.name as user_name, j.title as job_title
        FROM applications a
        JOIN users u ON a.user_id = u.id
        JOIN joboffers j ON a.job_offer_id = j.id";

$result = $conn->query($sql);
$applications = [];

while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}

echo json_encode($applications);
$conn->close();
?>
