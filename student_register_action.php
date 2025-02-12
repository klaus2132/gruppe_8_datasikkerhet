<?php
// Start session hvis du skal bruke sessionvariabler
session_start();

// Initialiserer feilmelding
$error_message = "";

// Sjekk om skjemaet er sendt med POST-metoden
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Hent og trim data fra POST
    $name        = trim($_POST['name']);
    $email       = trim($_POST['email']);
    $role        = $_POST['role']; // Rolle: student eller foreleser
    $program     = trim($_POST['program']);
    $cohort_year = trim($_POST['cohort_year']);
    $raw_password = trim($_POST['password']);

    // Felter for foreleser
    $subject_id  = isset($_POST['subject_id']) ? trim($_POST['subject_id']) : null;
    $pin_code    = isset($_POST['pin_code']) ? trim($_POST['pin_code']) : null;
    $image_path  = isset($_POST['image_path']) ? trim($_POST['image_path']) : null;

    // Enkel validering (utvid gjerne validering ved behov)
    if (empty($name) || empty($email) || empty($program) || empty($cohort_year) || empty($raw_password)) {
        $error_message = "Alle feltene må fylles ut.";
    }

    if (empty($error_message)) {
        // Databaseforbindelse
        $servername = "localhost";
        $db_username = "admin";
        $db_password = "admin";
        $dbname = "prosjekt_db";

        // Opprett tilkobling
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

        // Sjekk tilkoblingen
        if ($conn->connect_error) {
            $error_message = "Connection failed: " . $conn->connect_error;
        }

        // Sjekk om e-posten allerede finnes i databasen
        $sql_check_email = "SELECT * FROM students WHERE email = ? UNION SELECT * FROM lecturers WHERE email = ?";
        if ($stmt_check = $conn->prepare($sql_check_email)) {
            $stmt_check->bind_param("ss", $email, $email);
            $stmt_check->execute();
            $stmt_check->store_result();

            // Hvis e-posten allerede finnes, vis en feilmelding
            if ($stmt_check->num_rows > 0) {
                $error_message = "Denne e-posten er allerede registrert. Vennligst bruk en annen e-post.";
            }

            $stmt_check->close();
        }

        // Hvis ingen feil ble funnet, registrer brukeren
        if (empty($error_message)) {
            $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);
            $created_at = date("Y-m-d H:i:s");

            if ($role === 'student') {
                // Studentregistrering
                $student_id = mt_rand(100000, 999999);
                $sql = "INSERT INTO students (student_id, name, email, program, cohort_year, password, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";

                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("isssiss", $student_id, $name, $email, $program, $cohort_year, $hashed_password, $created_at);
                    if ($stmt->execute()) {
                        header("Location: login.php");
                        exit();
                    } else {
                        $error_message = "Feil under registreringen: " . $stmt->error;
                    }
                    $stmt->close();
                }
            } elseif ($role === 'lecturer') {
                // Foreleserregistrering
                $lecturer_id = mt_rand(100000, 999999);
                $sql = "INSERT INTO lecturers (lecturer_id, name, email, subject_id, pin_code, password, image_path, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("ississss", $lecturer_id, $name, $email, $subject_id, $pin_code, $hashed_password, $image_path, $created_at);
                    if ($stmt->execute()) {
                        header("Location: login.php");
                        exit();
                    } else {
                        $error_message = "Feil under registreringen: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        }

        $conn->close();
    }
}

?>

<!-- Hvis det er feil, vis en feilmelding -->
<?php if (!empty($error_message)): ?>
    <script>
        alert("<?php echo $error_message; ?>");
    </script>
<?php endif; ?>


<!-- Registreringsskjema -->
<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrering</title>
    <script>
        // Hvis det finnes en feilmelding, vis en alert box
        <?php if (!empty($error_message)): ?>
            alert("<?php echo $error_message; ?>");
        <?php endif; ?>
    </script>
</head>
<body>

<form method="POST" action="student_register_action.php">
    <div>
        <label for="name">Navn:</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div>
        <label for="email">E-post:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="program">Studieretning (Program):</label>
        <input type="text" id="program" name="program" required>
    </div>
    <div>
        <label for="cohort_year">Studiekull (År):</label>
        <input type="number" id="cohort_year" name="cohort_year" required>
    </div>
    <div>
        <label for="password">Passord:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <input type="submit" value="Registrer">
    </div>
</form>

</body>
</html>
