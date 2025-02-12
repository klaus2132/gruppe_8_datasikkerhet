<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (empty($email)) {
        echo "E-post er påkrevd.";
    } else {
        // Databaseforbindelse
        $servername = "localhost";
        $db_username = "admin";
        $db_password = "admin";
        $dbname = "prosjekt_db";

        // Opprett tilkobling
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Sjekk om e-posten finnes i databasen
        $sql = "SELECT * FROM students WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                // Generer en tilbakestillingslenke (kan være en token for ekstra sikkerhet)
                $reset_link = "http://yourdomain.com/reset_password.php?email=" . urlencode($email);

                // Send e-post med PHPMailer
                require 'vendor/autoload.php';  // Path to the autoload file from Composer

                $mail = new PHPMailer\PHPMailer\PHPMailer();
                $mail->isSMTP();
                $mail->Host = 'smtp.mailtrap.io';  // For Mailtrap SMTP (juster etter behov)
                $mail->SMTPAuth = true;
                $mail->Username = 'your_username';  // Erstatt med din SMTP-brukernavn
                $mail->Password = 'your_password';  // Erstatt med ditt SMTP-passord
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('noreply@yourdomain.com', 'Nettsted');
                $mail->addAddress($email);
                $mail->Subject = 'Tilbakestilling av passord';
                $mail->Body    = "Klikk på følgende lenke for å tilbakestille passordet ditt: " . $reset_link;

                if ($mail->send()) {
                    echo "En e-post med instruksjoner er sendt.";
                } else {
                    echo "Feil med å sende e-post. Vennligst prøv igjen.";
                }
            } else {
                echo "E-posten er ikke registrert.";
            }

            $stmt->close();
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glemt Passord</title>
</head>
<body>

<h2>Glemt Passord</h2>

<form action="forgot_password.php" method="POST">
    <div>
        <label for="email">E-postadresse:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <input type="submit" value="Send E-post for Tilbakestilling">
    </div>
</form>

</body>
</html>
