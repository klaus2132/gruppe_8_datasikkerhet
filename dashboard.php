<?php
session_start();

// Sjekk om brukeren er logget inn
if (!isset($_SESSION['email'])) {
    header("Location: login.php");  // Hvis ikke, send til login-siden
    exit();
}

echo "Velkommen, " . $_SESSION['name'] . "!"; // Vis velkomstmelding med brukerens navn
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Velkommen til ditt dashboard!</h1>

    <!-- Send melding knapp -->
    <form action="send_message.php" method="get">
        <input type="submit" value="Send Melding">
    </form>

    <!-- Endre passord knapp -->
    <form action="change_password.php" method="get">
        <input type="submit" value="Endre Passord">
    </form>

    <!-- Logout-knapp -->
    <form action="logout.php" method="post">
        <input type="submit" value="Logg ut">
    </form>

</body>
</html>
