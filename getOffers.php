<?php
    require_once 'connect.php';
    session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'visitor') {
   echo json_encode(['error' => 'Accès refusé']);
    http_response_code(403);
    exit();
}

    //recuperer les offres 

    $sql="SELECT id, title,description, creationdate, image FROM joboffers ORDER BY creationdate DESC";
    $result = $conn->query($sql);

    $offers = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $offers[] = $row;
        }
    }

    $conn->close();

    header('content-Type: application/json');
    echo json_encode($offers);
?>