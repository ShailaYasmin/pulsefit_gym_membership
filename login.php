<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect(has_role('admin') ? 'admin/dashboard.php' : 'member/dashboard.php');
}

$errors = [];
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors['form'] = 'Your session expired. Please try again.';
    } else {
        $oldEmail = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($oldEmail === '' || $password === '') {
            $errors['form'] = 'Please enter your email and password.';
        } else {
            $stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role, status FROM users WHERE email = ?');
            $stmt->execute([$oldEmail]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                // Deliberately generic — never reveal whether the email exists.
                $errors['form'] = 'Invalid email or password.';
            } elseif ($user['status'] !== 'active') {
                $errors['form'] = 'This account has been suspended. Please contact the studio.';
            } else {
                login_user($user);
                flash_set('success', 'Welcome back, ' . explode(' ', $user['full_name'])[0] . '!');
                redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'member/dashboard.php');
            }
        }
    }
}

$pageTitle       = 'Log In | PulseFit Gym';
$pageDescription = 'Log in to your PulseFit Gym account to manage bookings, your membership and your profile.';
$activePage      = '';
require __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
  <div class="container">
    <div class="auth-card">
      <div class="form-card reveal in-view">
        <p class="eyebrow">Welcome Back</p>
        <h1 style="font-size:1.9rem;margin-bottom:6px;">Log in to your account</h1>
        <p style="font-size:0.9rem;margin-bottom:26px;">New to PulseFit? <a href="<?= e(base_url('register.php')) ?>" style="color:var(--accent);font-weight:700;">Create an account</a>.</p>

        <?php if (!empty($errors['form'])): ?>
          <div class="form-status show error"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span><?= e($errors['form']) ?></span></div>
        <?php endif; ?>

        <form method="post" novalidate action="<?= e(base_url('login.php')) ?>">
          <?= csrf_field() ?>

          <div class="field">
            <label for="email">Email Address <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="<?= e($oldEmail) ?>" placeholder="you@example.com" required autocomplete="email">
          </div>

          <div class="field">
            <label for="password">Password <span class="required">*</span></label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
          </div>

          <button type="submit" class="btn btn-primary btn-block">Log In</button>
        </form>

        <p style="margin-top:24px;font-size:0.78rem;color:var(--text-dim);text-align:center;">
          Demo accounts — Admin: admin@pulsefitgym.example / Admin123!<br>
          Member: member@pulsefitgym.example / Member123!
        </p>
      </div>
    </div>
  </div>
</section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
