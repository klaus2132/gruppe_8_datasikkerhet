<?php
session_start();
include('db.php'); // Forbindelse til databasen

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Hent brukerens informasjon basert på token
    if ($stmt = $conn->prepare("SELECT * FROM students WHERE reset_token = ? AND reset_token_expiry > NOW()")) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $new_password = trim($_POST['new_password']);
                $confirm_password = trim($_POST['confirm_password']);

                if ($new_password == $confirm_password) {
                    // Hash passordet og oppdater databasen
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    if ($update_stmt = $conn->prepare("UPDATE students SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?")) {
                        $update_stmt->bind_param("ss", $hashed_password, $token);
                        if ($update_stmt->execute()) {
                            // Etter passordendring, send brukeren tilbake til innloggingssiden
                            echo "Passordet ditt er endret. Du kan nå logge inn med det nye passordet.";
                            header("Location: login.php");
                            exit();
                        }
                    }
                } else {
                    echo "Passordene stemmer ikke overens.";
                }
            }
        } else {
            echo "Ugyldig eller utløpt lenke.";
        }
    }
} else {
    echo "Ingen token funnet.";
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

<form action="reset_password.php?token=<?php echo $token; ?>" method="POST">
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

</body>
</html>
