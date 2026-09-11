<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

logout_user();
session_start();
flash_set('success', "You've been logged out.");
redirect('index.php');
