<?php
declare(strict_types=1);
// Expects $pageTitle, $pageDescription, $activePage to be set by the including page.
$pageTitle       = $pageTitle       ?? SITE_NAME;
$pageDescription = $pageDescription ?? 'PulseFit Gym is a boutique strength and conditioning studio offering modern equipment, expert coaching and flexible memberships.';
$activePage      = $activePage      ?? '';
$user            = current_user();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDescription) ?>">
<meta name="author" content="Shaila Yasmin Erin">
<link rel="canonical" href="<?= e(base_url($_SERVER['REQUEST_URI'] ?? '')) ?>">
<link rel="icon" type="image/svg+xml" href="<?= e(base_url('images/favicon.svg')) ?>">
<link rel="stylesheet" href="<?= e(base_url('css/style.css')) ?>">
</head>
<body>

<a class="skip-link" href="#main">Skip to main content</a>

<header class="site-header">
  <div class="container">
    <a href="<?= e(base_url('index.php')) ?>" class="brand" aria-label="PulseFit Gym home">
      <span class="brand-mark" aria-hidden="true">P</span>Pulse<span>Fit</span>
    </a>
    <nav class="main-nav" id="main-nav" aria-label="Primary">
      <ul class="nav-list">
        <li><a href="<?= e(base_url('index.php')) ?>" <?= $activePage === 'home' ? 'aria-current="page"' : '' ?>>Home</a></li>
        <li><a href="<?= e(base_url('about.php')) ?>" <?= $activePage === 'about' ? 'aria-current="page"' : '' ?>>About</a></li>
        <li><a href="<?= e(base_url('membership.php')) ?>" <?= $activePage === 'membership' ? 'aria-current="page"' : '' ?>>Membership</a></li>
        <li><a href="<?= e(base_url('gallery.php')) ?>" <?= $activePage === 'gallery' ? 'aria-current="page"' : '' ?>>Gallery</a></li>
        <li><a href="<?= e(base_url('testimonials.php')) ?>" <?= $activePage === 'testimonials' ? 'aria-current="page"' : '' ?>>Testimonials</a></li>
        <li><a href="<?= e(base_url('contact.php')) ?>" <?= $activePage === 'contact' ? 'aria-current="page"' : '' ?>>Contact</a></li>
        <?php if ($user): ?>
          <?php if ($user['role'] === 'admin'): ?>
            <li><a href="<?= e(base_url('admin/dashboard.php')) ?>" <?= $activePage === 'admin' ? 'aria-current="page"' : '' ?>>Admin</a></li>
          <?php else: ?>
            <li><a href="<?= e(base_url('member/dashboard.php')) ?>" <?= $activePage === 'dashboard' ? 'aria-current="page"' : '' ?>>My Account</a></li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
    </nav>
    <div class="header-cta">
      <?php if ($user): ?>
        <span style="color:var(--text-dim);font-size:0.85rem;margin-right:4px;" class="hide-mobile">Hi, <?= e(explode(' ', $user['full_name'])[0]) ?></span>
        <a href="<?= e(base_url('logout.php')) ?>" class="btn btn-ghost btn-sm">Log Out</a>
      <?php else: ?>
        <a href="<?= e(base_url('login.php')) ?>" class="btn btn-ghost btn-sm">Log In</a>
        <a href="<?= e(base_url('register.php')) ?>" class="btn btn-primary btn-sm">Join Now</a>
      <?php endif; ?>
      <button class="nav-toggle" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="main-nav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<?php
$flashSuccess = flash_get('success');
$flashError   = flash_get('error');
if ($flashSuccess || $flashError):
?>
<div class="container" style="padding-top:calc(var(--header-h) + 24px);">
  <?php if ($flashSuccess): ?>
    <div class="form-status show success" role="alert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><span><?= e($flashSuccess) ?></span></div>
  <?php endif; ?>
  <?php if ($flashError): ?>
    <div class="form-status show error" role="alert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span><?= e($flashError) ?></span></div>
  <?php endif; ?>
</div>
<?php endif; ?>

<main id="main">
