<?php
// Pour afficher les erreurs PHP pendant le développement
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$host = "localhost";
$dbname = "ghr";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Récupérer les données du formulaire
$name = $_POST['firstname'] ?? '';
$lastname = $_POST['lastname'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirmpassword = $_POST['confirmpassword'] ?? '';
$phonenumber = $_POST['numTel'] ?? '';

// Vérifier si l'e-mail existe déjà (utiliser prepare pour éviter les erreurs)
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "❌ Cet e-mail est déjà utilisé.";
    $check->close();
    $conn->close();
    exit();
}
$check->close();

// Vérification des mots de passe
if ($password !== $confirmpassword) {
    echo "❌ Les mots de passe ne correspondent pas.";
    $conn->close();
    exit();
}

// Hachage du mot de passe
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insertion dans la base de données
$stmt = $conn->prepare("INSERT INTO users (name, lasname, email, password, phonenumeber, role) VALUES (?, ?, ?, ?, ?, 'visitor')");
$stmt->bind_param("sssss", $name, $lastname, $email, $hashed_password, $phonenumber);

if ($stmt->execute()) {
    echo "✅ Compte créé avec succès !";
} else {
    echo "❌ Erreur lors de l'inscription : " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
