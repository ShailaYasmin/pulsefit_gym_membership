<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');
$currentAdmin = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $targetId = (int) ($_POST['user_id'] ?? 0);

    if ($targetId === $currentAdmin['id']) {
        flash_set('error', "You can't change your own role or status.");
        redirect('admin/members.php');
    }

    if (isset($_POST['new_role'])) {
        $newRole = (string) $_POST['new_role'];
        if (in_array($newRole, ['admin', 'member', 'normal'], true)) {
            $pdo->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([$newRole, $targetId]);
            flash_set('success', 'Role updated.');
        }
    } elseif (isset($_POST['new_status'])) {
        $newStatus = (string) $_POST['new_status'];
        if (in_array($newStatus, ['active', 'suspended'], true)) {
            $pdo->prepare('UPDATE users SET status = ? WHERE id = ?')->execute([$newStatus, $targetId]);
            flash_set('success', 'Status updated.');
        }
    }
    redirect('admin/members.php');
}

$users = $pdo->query(
    "SELECT u.*, mp.name AS plan_name
     FROM users u
     LEFT JOIN user_memberships um ON um.user_id = u.id AND um.status = 'active'
     LEFT JOIN membership_plans mp ON mp.id = um.plan_id
     ORDER BY u.created_at DESC"
)->fetchAll();

$pageTitle  = 'Manage Members | PulseFit Gym Admin';
$activePage = 'admin';
$activeSub  = 'members';
require __DIR__ . '/../includes/header.php';
?>

  <section class="dashboard-shell">
    <div class="container">
      <div class="section-head reveal in-view" style="margin-bottom:32px;">
        <p class="eyebrow">Admin</p>
        <h1 style="font-size:2rem;">Members</h1>
      </div>

      <div class="dashboard-grid">
        <?php require __DIR__ . '/../includes/admin-nav.php'; ?>

        <div>
          <div class="data-table-wrap">
            <table class="data-table">
              <thead><tr><th>Name</th><th>Email</th><th>Plan</th><th>Role</th><th>Status</th><th>Joined</th></tr></thead>
              <tbody>
                <?php foreach ($users as $u): ?>
                  <tr>
                    <td><?= e($u['full_name']) ?><?= $u['id'] === $currentAdmin['id'] ? ' <span class="status-badge status-admin">You</span>' : '' ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= e($u['plan_name'] ?? '—') ?></td>
                    <td>
                      <?php if ($u['id'] === $currentAdmin['id']): ?>
                        <span class="status-badge status-<?= e($u['role']) ?>"><?= e(ucfirst($u['role'])) ?></span>
                      <?php else: ?>
                        <form method="post" style="display:flex;gap:6px;align-items:center;">
                          <?= csrf_field() ?>
                          <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                          <select name="new_role" onchange="this.form.submit()" class="select-inline">
                            <option value="normal" <?= $u['role'] === 'normal' ? 'selected' : '' ?>>Normal</option>
                            <option value="member" <?= $u['role'] === 'member' ? 'selected' : '' ?>>Member</option>
                            <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                          </select>
                        </form>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($u['id'] === $currentAdmin['id']): ?>
                        <span class="status-badge status-active">Active</span>
                      <?php else: ?>
                        <form method="post">
                          <?= csrf_field() ?>
                          <input type="hidden" name="user_id" value="<?= (int) $u['id'] ?>">
                          <input type="hidden" name="new_status" value="<?= $u['status'] === 'active' ? 'suspended' : 'active' ?>">
                          <button type="submit" class="status-badge status-<?= e($u['status']) ?>" style="border:none;cursor:pointer;"><?= e(ucfirst($u['status'])) ?></button>
                        </form>
                      <?php endif; ?>
                    </td>
                    <td><?= e(format_date_nice(substr($u['created_at'], 0, 10))) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
