<?php
session_start(); // Start sessionen

// Hvis ikke innlogget, omdiriger til innloggingssiden
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db.php'); // Forbindelse til databasen

// Hent alle meldinger
$query = "SELECT * FROM messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Vis Meldinger</title>
</head>
<body>
    <h1>Alle Anonyme Meldinger</h1>
    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<p><strong>Sendt: </strong>" . $row['created_at'] . "<br><strong>Melding: </strong>" . htmlspecialchars($row['message']) . "</p>";
        }
    } else {
        echo "<p>Ingen meldinger funnet.</p>";
    }
    ?>
    <a href="logout.php">Logg ut</a>
</body>
</html>
