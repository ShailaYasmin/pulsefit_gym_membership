<?php
declare(strict_types=1);

// ---- Database credentials -------------------------------------------------
// The app connects as a dedicated least-privilege MySQL user (SELECT/INSERT/
// UPDATE/DELETE only on this one database), never as root.
define('DB_HOST', 'localhost');
define('DB_NAME', 'pulsefit_gym');
define('DB_USER', 'pulsefit_app');
define('DB_PASS', 'PulseFit_App_2026!');

// ---- Site-wide constants ---------------------------------------------------
define('SITE_NAME', 'PulseFit Gym');
define('SITE_TAGLINE', 'Train Hard. Live Stronger.');

// Built from the current request so the same code works unmodified on the
// PHP dev server, on localhost, and once deployed to free hosting.
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
define('SITE_URL', $scheme . '://' . $host);

date_default_timezone_set('Australia/Sydney');

// Errors are logged, never displayed to visitors (a stack trace can leak
// file paths and query structure, which is a real information-disclosure risk).
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
