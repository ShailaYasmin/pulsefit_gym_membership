<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $id = (int) ($_POST['enquiry_id'] ?? 0);
    if (isset($_POST['new_status']) && in_array($_POST['new_status'], ['new', 'read', 'replied'], true)) {
        $pdo->prepare('UPDATE enquiries SET status = ? WHERE id = ?')->execute([$_POST['new_status'], $id]);
        flash_set('success', 'Enquiry updated.');
    } elseif (isset($_POST['delete_enquiry'])) {
        $pdo->prepare('DELETE FROM enquiries WHERE id = ?')->execute([$id]);
        flash_set('success', 'Enquiry deleted.');
    }
    redirect('admin/enquiries.php');
}

$enquiries = $pdo->query('SELECT * FROM enquiries ORDER BY created_at DESC')->fetchAll();

$pageTitle  = 'Enquiries | PulseFit Gym Admin';
$activePage = 'admin';
$activeSub  = 'enquiries';
require __DIR__ . '/../includes/header.php';
?>

  <section class="dashboard-shell">
    <div class="container">
      <div class="section-head reveal in-view" style="margin-bottom:32px;">
        <p class="eyebrow">Admin</p>
        <h1 style="font-size:2rem;">Contact Enquiries</h1>
      </div>

      <div class="dashboard-grid">
        <?php require __DIR__ . '/../includes/admin-nav.php'; ?>

        <div>
          <?php if (empty($enquiries)): ?>
            <p class="empty-state">No enquiries submitted yet.</p>
          <?php else: ?>
            <?php foreach ($enquiries as $en): ?>
              <div class="card" style="margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:10px;">
                  <div>
                    <strong><?= e($en['name']) ?></strong>
                    <span style="color:var(--text-dim);font-size:0.85rem;margin-left:8px;"><?= e($en['email']) ?><?= $en['phone'] ? ' · ' . e($en['phone']) : '' ?></span>
                  </div>
                  <span class="status-badge status-<?= e($en['status']) ?>"><?= e(ucfirst($en['status'])) ?></span>
                </div>
                <p style="font-size:0.85rem;color:var(--accent);margin-bottom:6px;"><?= e(ucwords(str_replace('-', ' ', $en['subject']))) ?> · <?= e(format_date_nice(substr($en['created_at'], 0, 10))) ?></p>
                <p style="margin-bottom:16px;"><?= nl2br(e($en['message'])) ?></p>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                  <a href="mailto:<?= e($en['email']) ?>" class="btn btn-ghost btn-icon-sm">Reply by Email</a>
                  <form method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="enquiry_id" value="<?= (int) $en['id'] ?>">
                    <input type="hidden" name="new_status" value="<?= $en['status'] === 'new' ? 'read' : ($en['status'] === 'read' ? 'replied' : 'new') ?>">
                    <button type="submit" class="btn btn-ghost btn-icon-sm">Mark as <?= $en['status'] === 'new' ? 'Read' : ($en['status'] === 'read' ? 'Replied' : 'New') ?></button>
                  </form>
                  <form method="post" style="display:inline;" onsubmit="return confirm('Delete this enquiry?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="enquiry_id" value="<?= (int) $en['id'] ?>">
                    <input type="hidden" name="delete_enquiry" value="1">
                    <button type="submit" class="btn btn-ghost btn-icon-sm" style="color:var(--accent-2);">Delete</button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
