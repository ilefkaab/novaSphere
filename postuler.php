<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'visitor') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès refusé']);
    exit();
}

header('Content-Type: application/json');

// Lire les données du formulaire
$jobId = intval($_POST['jobId'] ?? 0);
$user_id = $_SESSION['user_id'];
$message = trim($_POST['message'] ?? '');

if ($jobId === 0 ) {
    echo json_encode(["error" => "job Champs obligatoires manquants."]);
    exit;
}
if ($user_id === 0) {
    echo json_encode(["error" => "user Champs obligatoires manquants."]);
    exit;
}
if (empty($message)) {
    echo json_encode(["error" => "message Champs obligatoires manquants."]);
    exit;
}

// Gérer l'upload de fichier
$cvPath = '';
if (isset($_FILES['cv_url']) && $_FILES['cv_url']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir);
    $filename = basename($_FILES['cv_url']['name']);
    $targetPath = $uploadDir . time() . '_' . $filename;
    move_uploaded_file($_FILES['cv_url']['tmp_name'], $targetPath);
    $cvPath = $targetPath;
} else {
    echo json_encode(["error" => "Erreur de téléchargement du CV."]);
    exit;
}

// Vérifier si déjà postulé
$check = $conn->prepare("SELECT id FROM applications WHERE job_offer_id = ? AND user_id = ?");
$check->bind_param("ii", $jobId, $user_id);
$check->execute();
$res = $check->get_result();
if ($res->num_rows > 0) {
    echo json_encode(["error" => "Vous avez déjà postulé à cette offre."]);
    exit;
}

// Insérer la candidature
$stmt = $conn->prepare("INSERT INTO applications (job_offer_id, user_id, motivation_letter, cv_link) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $jobId, $user_id, $message, $cvPath);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Erreur lors de l'enregistrement."]);
}

$stmt->close();
$conn->close();
