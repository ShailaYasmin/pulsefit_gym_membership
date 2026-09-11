<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $id = (int) ($_POST['testimonial_id'] ?? 0);
    if (isset($_POST['new_status']) && in_array($_POST['new_status'], ['approved', 'rejected', 'pending'], true)) {
        $pdo->prepare('UPDATE testimonials SET status = ? WHERE id = ?')->execute([$_POST['new_status'], $id]);
        flash_set('success', 'Review updated.');
    } elseif (isset($_POST['delete_testimonial'])) {
        $pdo->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$id]);
        flash_set('success', 'Review deleted.');
    }
    redirect('admin/testimonials.php');
}

$pending = $pdo->query("SELECT * FROM testimonials WHERE status = 'pending' ORDER BY created_at ASC")->fetchAll();
$others  = $pdo->query("SELECT * FROM testimonials WHERE status != 'pending' ORDER BY created_at DESC")->fetchAll();

$pageTitle  = 'Manage Testimonials | PulseFit Gym Admin';
$activePage = 'admin';
$activeSub  = 'testimonials';
require __DIR__ . '/../includes/header.php';
?>

  <section class="dashboard-shell">
    <div class="container">
      <div class="section-head reveal in-view" style="margin-bottom:32px;">
        <p class="eyebrow">Admin</p>
        <h1 style="font-size:2rem;">Testimonials</h1>
      </div>

      <div class="dashboard-grid">
        <?php require __DIR__ . '/../includes/admin-nav.php'; ?>

        <div>
          <h3 style="margin-bottom:16px;">Pending Approval (<?= count($pending) ?>)</h3>
          <?php if (empty($pending)): ?>
            <p class="empty-state" style="margin-bottom:32px;">Nothing waiting for review.</p>
          <?php else: ?>
            <?php foreach ($pending as $t): ?>
              <div class="card" style="margin-bottom:16px;">
                <div class="stars" aria-hidden="true"><?= str_repeat('★', (int) $t['rating']) ?></div>
                <p style="margin:8px 0 10px;">"<?= e($t['quote']) ?>"</p>
                <p style="font-size:0.85rem;color:var(--text-dim);margin-bottom:14px;"><?= e($t['display_name']) ?> · <?= e($t['member_label']) ?> · <?= e(format_date_nice(substr($t['created_at'], 0, 10))) ?></p>
                <div style="display:flex;gap:10px;">
                  <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="testimonial_id" value="<?= (int) $t['id'] ?>">
                    <input type="hidden" name="new_status" value="approved">
                    <button type="submit" class="btn btn-primary btn-icon-sm">Approve</button>
                  </form>
                  <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="testimonial_id" value="<?= (int) $t['id'] ?>">
                    <input type="hidden" name="new_status" value="rejected">
                    <button type="submit" class="btn btn-ghost btn-icon-sm">Reject</button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <h3 style="margin-bottom:16px;">All Other Reviews</h3>
          <?php if (empty($others)): ?>
            <p class="empty-state">None yet.</p>
          <?php else: ?>
            <div class="data-table-wrap">
              <table class="data-table">
                <thead><tr><th>Author</th><th>Rating</th><th>Status</th><th></th></tr></thead>
                <tbody>
                  <?php foreach ($others as $t): ?>
                    <tr>
                      <td><?= e($t['display_name']) ?></td>
                      <td><?= str_repeat('★', (int) $t['rating']) ?></td>
                      <td><span class="status-badge status-<?= e($t['status']) ?>"><?= e(ucfirst($t['status'])) ?></span></td>
                      <td class="table-actions">
                        <form method="post" onsubmit="return confirm('Delete this review permanently?');">
                          <?= csrf_field() ?>
                          <input type="hidden" name="testimonial_id" value="<?= (int) $t['id'] ?>">
                          <input type="hidden" name="delete_testimonial" value="1">
                          <button type="submit" class="btn btn-ghost btn-icon-sm" style="color:var(--accent-2);">Delete</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
