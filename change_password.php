<?php
session_start(); // Start sessionen

// Sjekk om brukeren er logget inn
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include('db.php'); // Forbindelse til databasen

// Håndter passordendringen når skjemaet er sendt
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Valider at nytt passord og bekreftelse er like
    if ($new_password !== $confirm_password) {
        echo "<script>alert('Passordene stemmer ikke overens.');</script>";
    } else {
        // Hent brukerens informasjon fra databasen
        $email = $_SESSION['email'];

        // Sjekk om brukeren eksisterer i databasen
        if ($stmt = $conn->prepare("SELECT * FROM students WHERE email = ?")) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            // Sjekk om brukeren ble funnet
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Sjekk om det gamle passordet er riktig
                if (password_verify($current_password, $row['password'])) {
                    // Hash det nye passordet
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    // Oppdater passordet i databasen
                    if ($update_stmt = $conn->prepare("UPDATE students SET password = ? WHERE email = ?")) {
                        $update_stmt->bind_param("ss", $hashed_password, $email);
                        if ($update_stmt->execute()) {
                            // Etter vellykket passordendring, logg ut og send til login-siden
                            session_unset();  // Fjern sessiondata
                            session_destroy(); // Ødelegg sessionen
                            header("Location: login.php"); // Send brukeren til login-siden
                            exit();
                        } else {
                            echo "<script>alert('Noe gikk galt. Vennligst prøv igjen senere.');</script>";
                        }
                    }
                } else {
                    echo "<script>alert('Feil nåværende passord.');</script>";
                }
            } else {
                echo "<script>alert('Brukeren ble ikke funnet.');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endre Passord</title>
</head>
<body>

<h2>Endre Passord</h2>

<form action="change_password.php" method="POST">
    <div>
        <label for="current_password">Nåværende Passord:</label>
        <input type="password" id="current_password" name="current_password" required>
    </div>
    <div>
        <label for="new_password">Nytt Passord:</label>
        <input type="password" id="new_password" name="new_password" required>
    </div>
    <div>
        <label for="confirm_password">Bekreft Nytt Passord:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
    </div>
    <div>
        <input type="submit" value="Endre Passord">
    </div>
</form>

<a href="dashboard.php">Tilbake til Dashboard</a>

</body>
</html>
