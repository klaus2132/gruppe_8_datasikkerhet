<?php
session_start(); // Start sessionen for å få tilgang til brukerens innloggingsdata

// Hvis ikke innlogget, omdiriger til innloggingssiden
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include('db.php'); // Forbindelse til databasen

// Håndtering av innsending av melding
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = mysqli_real_escape_string($conn, $_POST['message']); // Beskytt melding mot SQL-injeksjon
    $recipient = mysqli_real_escape_string($conn, $_POST['recipient']); // Beskytt mottakerens navn mot SQL-injeksjon

    // Sett inn meldingen i databasen
    $query = "INSERT INTO messages (message, recipient_name) VALUES ('$message', '$recipient')";
    if (mysqli_query($conn, $query)) {
        echo "Meldingen ble sendt!";
    } else {
        echo "Noe gikk galt. Prøv igjen senere.";
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <title>Send Anonym Melding</title>
</head>
<body>
    <h1>Send Anonym Melding</h1>
    
    <!-- Skjema for å sende melding -->
    <form action="send_message.php" method="post">
        <div>
            <label for="recipient">Mottaker (Navn):</label>
            <input type="text" id="recipient" name="recipient" required>
        </div>
        <div>
            <label for="message">Melding:</label>
            <textarea id="message" name="message" rows="4" cols="50" placeholder="Skriv din melding her..." required></textarea>
        </div>
        <div>
            <input type="submit" value="Send Melding">
        </div>
    </form>

    <a href="logout.php">Logg ut</a>
</body>
</html>
