<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';

require_login();
$user = current_user();

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user['id']]);
$dbUser = $stmt->fetch();

$profileErrors = [];
$passwordErrors = [];
$old = ['full_name' => $dbUser['full_name'], 'email' => $dbUser['email'], 'phone' => $dbUser['phone'] ?? ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!csrf_verify()) {
        $profileErrors['form'] = 'Your session expired. Please try again.';
    } else {
        $old['full_name'] = trim((string) ($_POST['full_name'] ?? ''));
        $old['email']     = trim((string) ($_POST['email'] ?? ''));
        $old['phone']     = trim((string) ($_POST['phone'] ?? ''));

        if ($old['full_name'] === '' || mb_strlen($old['full_name']) < 2) {
            $profileErrors['full_name'] = 'Please enter your full name.';
        }
        if ($old['email'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $profileErrors['email'] = 'Please enter a valid email address.';
        }
        if ($old['phone'] !== '' && !preg_match('/^[0-9+()\-\s]{7,20}$/', $old['phone'])) {
            $profileErrors['phone'] = 'Please enter a valid phone number.';
        }

        if (empty($profileErrors)) {
            $dupStmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
            $dupStmt->execute([$old['email'], $user['id']]);
            if ($dupStmt->fetch()) {
                $profileErrors['email'] = 'That email is already in use by another account.';
            }
        }

        if (empty($profileErrors)) {
            $pdo->prepare('UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?')
                ->execute([$old['full_name'], $old['email'], $old['phone'] !== '' ? $old['phone'] : null, $user['id']]);
            $_SESSION['user']['full_name'] = $old['full_name'];
            $_SESSION['user']['email']     = $old['email'];
            flash_set('success', 'Profile updated.');
            redirect('member/profile.php');
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!csrf_verify()) {
        $passwordErrors['form'] = 'Your session expired. Please try again.';
    } else {
        $current = (string) ($_POST['current_password'] ?? '');
        $new     = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['new_password_confirm'] ?? '');

        if (!password_verify($current, $dbUser['password_hash'])) {
            $passwordErrors['current_password'] = 'Current password is incorrect.';
        }
        if (mb_strlen($new) < 8 || !preg_match('/[A-Za-z]/', $new) || !preg_match('/[0-9]/', $new)) {
            $passwordErrors['new_password'] = 'New password must be at least 8 characters and include a letter and a number.';
        }
        if ($new !== $confirm) {
            $passwordErrors['new_password_confirm'] = 'Passwords do not match.';
        }

        if (empty($passwordErrors)) {
            $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                ->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);
            flash_set('success', 'Password changed.');
            redirect('member/profile.php');
        }
    }
}

$pageTitle       = 'Edit Profile | PulseFit Gym';
$pageDescription = 'Update your PulseFit Gym account details.';
$activePage      = 'dashboard';
$activeSub       = 'profile';
require __DIR__ . '/../includes/header.php';
?>

  <section class="dashboard-shell">
    <div class="container">
      <div class="section-head reveal in-view" style="margin-bottom:32px;">
        <p class="eyebrow">My Account</p>
        <h1 style="font-size:2rem;">Edit Profile</h1>
      </div>

      <div class="dashboard-grid">
        <?php require __DIR__ . '/../includes/member-nav.php'; ?>

        <div>
          <div class="form-card" style="margin-bottom:28px;">
            <h3 style="margin-bottom:20px;">Profile Details</h3>

            <?php if (!empty($profileErrors['form'])): ?>
              <div class="form-status show error"><span><?= e($profileErrors['form']) ?></span></div>
            <?php endif; ?>

            <form method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="update_profile" value="1">

              <div class="field <?= isset($profileErrors['full_name']) ? 'has-error' : '' ?>">
                <label for="full_name">Full Name <span class="required">*</span></label>
                <input type="text" id="full_name" name="full_name" value="<?= e($old['full_name']) ?>" required minlength="2">
                <?php if (isset($profileErrors['full_name'])): ?><p class="field-error" style="display:flex;"><?= e($profileErrors['full_name']) ?></p><?php endif; ?>
              </div>

              <div class="field <?= isset($profileErrors['email']) ? 'has-error' : '' ?>">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" required>
                <?php if (isset($profileErrors['email'])): ?><p class="field-error" style="display:flex;"><?= e($profileErrors['email']) ?></p><?php endif; ?>
              </div>

              <div class="field <?= isset($profileErrors['phone']) ? 'has-error' : '' ?>">
                <label for="phone">Phone <span class="field-hint" style="display:inline;">(optional)</span></label>
                <input type="tel" id="phone" name="phone" value="<?= e($old['phone']) ?>">
                <?php if (isset($profileErrors['phone'])): ?><p class="field-error" style="display:flex;"><?= e($profileErrors['phone']) ?></p><?php endif; ?>
              </div>

              <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
          </div>

          <div class="form-card">
            <h3 style="margin-bottom:20px;">Change Password</h3>

            <?php if (!empty($passwordErrors['form'])): ?>
              <div class="form-status show error"><span><?= e($passwordErrors['form']) ?></span></div>
            <?php endif; ?>

            <form method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="change_password" value="1">

              <div class="field <?= isset($passwordErrors['current_password']) ? 'has-error' : '' ?>">
                <label for="current_password">Current Password <span class="required">*</span></label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                <?php if (isset($passwordErrors['current_password'])): ?><p class="field-error" style="display:flex;"><?= e($passwordErrors['current_password']) ?></p><?php endif; ?>
              </div>

              <div class="field <?= isset($passwordErrors['new_password']) ? 'has-error' : '' ?>">
                <label for="new_password">New Password <span class="required">*</span></label>
                <input type="password" id="new_password" name="new_password" required minlength="8" autocomplete="new-password">
                <?php if (isset($passwordErrors['new_password'])): ?><p class="field-error" style="display:flex;"><?= e($passwordErrors['new_password']) ?></p><?php endif; ?>
              </div>

              <div class="field <?= isset($passwordErrors['new_password_confirm']) ? 'has-error' : '' ?>">
                <label for="new_password_confirm">Confirm New Password <span class="required">*</span></label>
                <input type="password" id="new_password_confirm" name="new_password_confirm" required autocomplete="new-password">
                <?php if (isset($passwordErrors['new_password_confirm'])): ?><p class="field-error" style="display:flex;"><?= e($passwordErrors['new_password_confirm']) ?></p><?php endif; ?>
              </div>

              <button type="submit" class="btn btn-ghost">Change Password</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
