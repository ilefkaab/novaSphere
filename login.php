<?php
session_start();

require_once 'connect.php';

// Récupérer les données
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Requête pour trouver l'utilisateur
$stmt = $conn->prepare("SELECT id, name, role, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Vérification du mot de passe
    if (password_verify($password, $user['password'])) {
        // Authentification réussie
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        // Redirection selon le rôle
        if ($user['role'] === 'visitor') {
            header("Location: ../Frontend/visitorDashboard.html");
        } elseif ($user['role'] === 'emplyee') {
            header("Location: ../Frontend/employeeDashbord.html");
        } elseif ($user['role'] === 'admin') {
            header("Location: ../Frontend/adminDashboard.html");
        } else {
            echo "Rôle inconnu.";
        }
    } else {
        echo "❌ Mot de passe incorrect.";
    }
} else {
    echo "❌ Email non trouvé.";
}

$stmt->close();
$conn->close();

session_start();
?>