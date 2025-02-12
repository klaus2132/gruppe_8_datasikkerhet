<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrering</title>
    <script>
        function toggleLecturerFields() {
            var role = document.querySelector('input[name="role"]:checked').value;
            var lecturerFields = document.getElementById('lecturerFields');
            if (role === 'lecturer') {
                lecturerFields.style.display = 'block';
            } else {
                lecturerFields.style.display = 'none';
            }
        }
    </script>
</head>
<body>

<h2>Registrer deg</h2>

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
        <label for="role">Velg rolle:</label>
        <input type="radio" id="student" name="role" value="student" checked onclick="toggleLecturerFields()"> Student
        <input type="radio" id="lecturer" name="role" value="lecturer" onclick="toggleLecturerFields()"> Foreleser
    </div>

    <!-- Feltene som vises kun for foreleser -->
    <div id="lecturerFields" style="display: none;">
        <div>
            <label for="subject_id">Fag-ID (Foreleserens fag):</label>
            <input type="number" id="subject_id" name="subject_id">
        </div>
        <div>
            <label for="pin_code">PIN-kode:</label>
            <input type="text" id="pin_code" name="pin_code">
        </div>
        <div>
            <label for="image_path">Bilde (URL til bilde):</label>
            <input type="text" id="image_path" name="image_path">
        </div>
    </div>

    <div>
        <label for="program">Studieretning (Program) / Fag (for foreleser):</label>
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
