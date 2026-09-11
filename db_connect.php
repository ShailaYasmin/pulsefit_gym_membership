<?php
/**
 * db_connect.php
 * ---------------------------------------------------
 * Establishes a single reusable PDO connection to the
 * PulseFit Gym MySQL database.
 *
 * Individual contribution: Trishna
 * Feature: Contact form backend (DB connectivity)
 * ---------------------------------------------------
 */

// --- Database configuration ---
// Update these values to match your local XAMPP/WAMP MySQL setup.
$db_host = "localhost";
$db_name = "pulsefit_gym";
$db_user = "root";
$db_pass = "";          // default XAMPP password is empty
$db_charset = "utf8mb4";

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$db_charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions on error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // return rows as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                   // use real prepared statements
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Never expose raw DB errors to the user (privacy/security requirement).
    error_log("Database connection failed: " . $e->getMessage());
    die("Sorry, something went wrong on our end. Please try again later.");
}
