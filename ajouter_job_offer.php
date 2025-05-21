<?php
session_start();
require_once 'connect.php';

// Vérifier l'accès admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo "Accès refusé.";
    exit();
}

// Données du formulaire
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');

if (empty($title) || empty($description)) {
    echo "Tous les champs sont requis.";
    exit();
}

// Gérer l'image
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo "Erreur lors du téléchargement de l'image.";
    exit();
}

$imageTmp = $_FILES['image']['tmp_name'];
$imageName = basename($_FILES['image']['name']);
$imagePath = "../uploads/" . uniqid() . "_" . $imageName;

if (!move_uploaded_file($imageTmp, $imagePath)) {
    echo "Échec du téléchargement de l'image.";
    exit();
}

// Chemin à enregistrer dans la base (relatif au frontend)
$imageDBPath = str_replace('../', '', $imagePath);

// Insertion dans la base
$stmt = $conn->prepare("INSERT INTO joboffers (title, description, image) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $title, $description, $imageDBPath);

if ($stmt->execute()) {
    echo "✅ Offre ajoutée avec image.";
} else {
    echo "❌ Erreur : " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
