<?php
declare(strict_types=1);

// ---- Database credentials -------------------------------------------------
// Live hosting (InfinityFree) credentials. Unlike the local dev setup, free
// shared hosts issue a single account-level MySQL user with full rights on
// every database under that account — there's no option to create a
// separate least-privilege app user the way includes/config.php does on
// the main/shaila_dynamic branches for local development.
define('DB_HOST', 'sql207.infinityfree.com');
define('DB_NAME', 'if0_42890673_pulsefit');
define('DB_USER', 'if0_42890673');
define('DB_PASS', 'rQGhA1ZqIB');

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
ini_set('display_errors', '1'); // TEMP for live debugging — set back to '0' before submission
ini_set('log_errors', '1');
