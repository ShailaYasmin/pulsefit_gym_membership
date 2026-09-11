<?php
/**
 * process_contact.php
 * ---------------------------------------------------
 * Handles submission of the Contact Us form.
 * - Validates all fields server-side (never trust client-side only)
 * - Uses a prepared statement to prevent SQL injection
 * - Stores the enquiry in MySQL
 * - Redirects back to contact.php with a status flag
 *
 * Individual contribution: Trishna
 * Feature: Contact form backend, error handling & validation,
 *          secure data handling (privacy/security requirement)
 * ---------------------------------------------------
 */

session_start();
require_once "db_connect.php";

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact.php");
    exit;
}

$errors = [];

// --- Collect & sanitise input ---
$full_name = trim($_POST["name"] ?? "");
$email     = trim($_POST["email"] ?? "");
$phone     = trim($_POST["phone"] ?? "");
$subject   = trim($_POST["subject"] ?? "");
$message   = trim($_POST["message"] ?? "");
$consent   = isset($_POST["consent"]) ? 1 : 0;

$allowed_subjects = ["membership", "personal-training", "tour", "feedback"];

// --- Server-side validation (mirrors the client-side rules, but authoritative) ---
if (strlen($full_name) < 2) {
    $errors[] = "Please enter your full name (at least 2 characters).";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

if ($phone !== "" && !preg_match('/^[0-9+()\s-]{6,20}$/', $phone)) {
    $errors[] = "Please enter a valid phone number.";
}

if (!in_array($subject, $allowed_subjects, true)) {
    $errors[] = "Please select a valid enquiry type.";
}

if (strlen($message) < 10) {
    $errors[] = "Message should be at least 10 characters.";
}

if ($consent !== 1) {
    $errors[] = "You must agree to be contacted before sending your message.";
}

// --- If validation failed, send the visitor back with error details ---
if (!empty($errors)) {
    $_SESSION["contact_errors"] = $errors;
    $_SESSION["old_input"] = compact("full_name", "email", "phone", "subject", "message");
    header("Location: contact.php?status=error");
    exit;
}

// --- Insert into the database using a prepared statement ---
try {
    $stmt = $pdo->prepare(
        "INSERT INTO contact_enquiries
            (full_name, email, phone, enquiry_type, message, consent_given)
         VALUES
            (:full_name, :email, :phone, :enquiry_type, :message, :consent_given)"
    );

    $stmt->execute([
        ":full_name"     => $full_name,
        ":email"         => $email,
        ":phone"         => $phone !== "" ? $phone : null,
        ":enquiry_type"  => $subject,
        ":message"       => $message,
        ":consent_given" => $consent,
    ]);

    header("Location: contact.php?status=success");
    exit;

} catch (PDOException $e) {
    error_log("Insert failed: " . $e->getMessage());
    $_SESSION["contact_errors"] = ["Something went wrong sending your message. Please try again."];
    header("Location: contact.php?status=error");
    exit;
}
