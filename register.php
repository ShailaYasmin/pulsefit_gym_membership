<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect(has_role('admin') ? 'admin/dashboard.php' : 'member/dashboard.php');
}

$errors = [];
$old    = ['full_name' => '', 'email' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors['form'] = 'Your session expired. Please try again.';
    } else {
        $old['full_name'] = trim((string) ($_POST['full_name'] ?? ''));
        $old['email']     = trim((string) ($_POST['email'] ?? ''));
        $old['phone']     = trim((string) ($_POST['phone'] ?? ''));
        $password         = (string) ($_POST['password'] ?? '');
        $passwordConfirm  = (string) ($_POST['password_confirm'] ?? '');

        if ($old['full_name'] === '' || mb_strlen($old['full_name']) < 2) {
            $errors['full_name'] = 'Please enter your full name.';
        }

        if ($old['email'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($old['phone'] !== '' && !preg_match('/^[0-9+()\-\s]{7,20}$/', $old['phone'])) {
            $errors['phone'] = 'Please enter a valid phone number.';
        }

        if (mb_strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Password must be at least 8 characters and include a letter and a number.';
        }

        if ($password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Passwords do not match.';
        }

        if (empty($_POST['terms'])) {
            $errors['terms'] = 'You must agree to the privacy notice to create an account.';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$old['email']]);
            if ($stmt->fetch()) {
                $errors['email'] = 'An account with this email already exists.';
            }
        }

        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                'INSERT INTO users (full_name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $old['full_name'],
                $old['email'],
                $hash,
                $old['phone'] !== '' ? $old['phone'] : null,
                'normal',
            ]);

            $newUserId = (int) $pdo->lastInsertId();
            $stmt = $pdo->prepare('SELECT id, full_name, email, role FROM users WHERE id = ?');
            $stmt->execute([$newUserId]);
            $newUser = $stmt->fetch();

            login_user($newUser);
            flash_set('success', 'Welcome to PulseFit, ' . explode(' ', $newUser['full_name'])[0] . '! Choose a membership plan to get started.');
            redirect('membership.php');
        }
    }
}

$pageTitle       = 'Create an Account | PulseFit Gym';
$pageDescription = 'Register for a free PulseFit Gym account to book classes, track your membership and manage your profile.';
$activePage      = '';
require __DIR__ . '/includes/header.php';
?>

<section class="auth-page">
  <div class="container">
    <div class="auth-card">
      <div class="form-card reveal in-view">
        <p class="eyebrow">Join PulseFit</p>
        <h1 style="font-size:1.9rem;margin-bottom:6px;">Create your account</h1>
        <p style="font-size:0.9rem;margin-bottom:26px;">Already a member? <a href="<?= e(base_url('login.php')) ?>" style="color:var(--accent);font-weight:700;">Log in instead</a>.</p>

        <?php if (!empty($errors['form'])): ?>
          <div class="form-status show error"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span><?= e($errors['form']) ?></span></div>
        <?php endif; ?>

        <form method="post" novalidate action="<?= e(base_url('register.php')) ?>">
          <?= csrf_field() ?>

          <div class="field <?= isset($errors['full_name']) ? 'has-error' : '' ?>">
            <label for="full_name">Full Name <span class="required">*</span></label>
            <input type="text" id="full_name" name="full_name" value="<?= e($old['full_name']) ?>" required minlength="2" autocomplete="name">
            <?php if (isset($errors['full_name'])): ?><p class="field-error" style="display:flex;"><?= e($errors['full_name']) ?></p><?php endif; ?>
          </div>

          <div class="field <?= isset($errors['email']) ? 'has-error' : '' ?>">
            <label for="email">Email Address <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" required autocomplete="email">
            <?php if (isset($errors['email'])): ?><p class="field-error" style="display:flex;"><?= e($errors['email']) ?></p><?php endif; ?>
          </div>

          <div class="field <?= isset($errors['phone']) ? 'has-error' : '' ?>">
            <label for="phone">Phone <span class="field-hint" style="display:inline;">(optional)</span></label>
            <input type="tel" id="phone" name="phone" value="<?= e($old['phone']) ?>" autocomplete="tel">
            <?php if (isset($errors['phone'])): ?><p class="field-error" style="display:flex;"><?= e($errors['phone']) ?></p><?php endif; ?>
          </div>

          <div class="field <?= isset($errors['password']) ? 'has-error' : '' ?>">
            <label for="password">Password <span class="required">*</span></label>
            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
            <p class="field-hint">At least 8 characters, with a letter and a number.</p>
            <?php if (isset($errors['password'])): ?><p class="field-error" style="display:flex;"><?= e($errors['password']) ?></p><?php endif; ?>
          </div>

          <div class="field <?= isset($errors['password_confirm']) ? 'has-error' : '' ?>">
            <label for="password_confirm">Confirm Password <span class="required">*</span></label>
            <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password">
            <?php if (isset($errors['password_confirm'])): ?><p class="field-error" style="display:flex;"><?= e($errors['password_confirm']) ?></p><?php endif; ?>
          </div>

          <div class="field <?= isset($errors['terms']) ? 'has-error' : '' ?>">
            <label class="checkbox-field" for="terms">
              <input type="checkbox" id="terms" name="terms" required>
              <span>I agree to the <a href="<?= e(base_url('privacy.php')) ?>" style="color:var(--accent);">privacy notice</a> and terms of use. <span class="required">*</span></span>
            </label>
            <?php if (isset($errors['terms'])): ?><p class="field-error" style="display:flex;"><?= e($errors['terms']) ?></p><?php endif; ?>
          </div>

          <button type="submit" class="btn btn-primary btn-block">Create Account</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
