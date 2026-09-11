<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Access Denied | PulseFit Gym</title>
<meta name="robots" content="noindex">
<link rel="icon" type="image/svg+xml" href="<?= e(base_url('images/favicon.svg')) ?>">
<link rel="stylesheet" href="<?= e(base_url('css/style.css')) ?>">
</head>
<body>
<main id="main" style="min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:24px;">
  <div>
    <p class="eyebrow">403 — Access Denied</p>
    <h1>You don't have permission to view this page</h1>
    <p style="max-width:480px;margin:16px auto 32px;">This area is restricted to a different account role. If you think this is a mistake, contact the studio.</p>
    <a class="btn btn-primary" href="<?= e(base_url('index.php')) ?>">Back to Home</a>
  </div>
</main>
</body>
</html>
