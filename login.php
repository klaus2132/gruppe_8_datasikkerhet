<?php
session_start(); // Start sessionen hvis vi trenger å bruke session variabler

// Hvis brukeren allerede er logget inn, kan du omdirigere dem til en annen side (for eksempel dashboard)
if (isset($_SESSION['email'])) {
    header("Location: dashboard.php");
    exit();
}

// Sjekk om skjemaet er sendt med POST-metoden
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Hent og trim data fra POST
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Sjekk at e-post og passord ikke er tomme
    if (empty($email) || empty($password)) {
        echo "E-post og passord er påkrevd.";
    } else {
        // Databaseforbindelse
        $servername = "localhost";
        $db_username = "admin"; // Juster om nødvendig
        $db_password = "admin"; // Juster om nødvendig
        $dbname = "prosjekt_db"; // Juster om nødvendig

        // Opprett tilkobling
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

        // Sjekk tilkoblingen
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // SQL-spørring for å finne brukeren med den oppgitte e-posten
        $sql = "SELECT * FROM students WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind parametere (s = string)
            $stmt->bind_param("s", $email);

            // Utfør spørringen
            $stmt->execute();
            $result = $stmt->get_result();

            // Sjekk om vi fant en bruker med den e-posten
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Sjekk om passordet er korrekt
                if (password_verify($password, $row['password'])) {
                    // Start en session og lagre brukerens e-post og navn
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['name'] = $row['name']; // Lagre navnet i sessionen

                    // Omdiriger til dashboard eller en annen beskyttet side
                    header("Location: dashboard.php");
                    exit();
                } else {
                    echo "Feil passord.";
                }
            } else {
                echo "Bruker ikke funnet.";
            }

            $stmt->close();
        }

        // Lukk tilkoblingen
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Innlogging</title>
</head>
<body>

<h2>Logg inn</h2>

<form action="login.php" method="POST">
    <div>
        <label for="email">E-post:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="password">Passord:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <input type="submit" value="Logg inn">
    </div>
</form>

<p>Har du ikke en konto? <a href="student_register.php">Registrer deg her</a></p>

<!-- Glemt passord lenke -->
<p><a href="forgot_password.php">Glemt passord?</a></p>

</body>
</html>
