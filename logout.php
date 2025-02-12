<?php
session_start(); // Start sessionen

// Fjern alle session-variabler
session_unset();

// Ødelegg sessionen
session_destroy();

// Omdiriger brukeren til registreringssiden
header("Location: student_register.php");
exit();
?>
