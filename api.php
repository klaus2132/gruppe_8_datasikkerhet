<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "admin", "password", "feedback_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM messages WHERE subject_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_GET['subject_id']);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);

$conn->close();
?>
