<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

$memberCount      = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'member'")->fetchColumn();
$normalCount      = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'normal'")->fetchColumn();
$upcomingBookings = (int) $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'booked' AND booking_date >= CURDATE()")->fetchColumn();
$newEnquiries     = (int) $pdo->query("SELECT COUNT(*) FROM enquiries WHERE status = 'new'")->fetchColumn();
$pendingReviews   = (int) $pdo->query("SELECT COUNT(*) FROM testimonials WHERE status = 'pending'")->fetchColumn();

$recentEnquiries = $pdo->query('SELECT * FROM enquiries ORDER BY created_at DESC LIMIT 5')->fetchAll();

$pageTitle       = 'Admin Dashboard | PulseFit Gym';
$pageDescription = 'PulseFit Gym admin overview.';
$activePage      = 'admin';
$activeSub       = 'dashboard';
require __DIR__ . '/../includes/header.php';
?>

  <section class="dashboard-shell">
    <div class="container">
      <div class="section-head reveal in-view" style="margin-bottom:32px;">
        <p class="eyebrow">Admin</p>
        <h1 style="font-size:2rem;">Studio Overview</h1>
      </div>

      <div class="dashboard-grid">
        <?php require __DIR__ . '/../includes/admin-nav.php'; ?>

        <div>
          <div class="stat-card-row">
            <div class="stat-card"><span class="stat-num"><?= $memberCount ?></span><p class="stat-label">Active Members</p></div>
            <div class="stat-card"><span class="stat-num"><?= $normalCount ?></span><p class="stat-label">Registered Leads</p></div>
            <div class="stat-card"><span class="stat-num"><?= $upcomingBookings ?></span><p class="stat-label">Upcoming Bookings</p></div>
            <div class="stat-card"><span class="stat-num"><?= $newEnquiries ?></span><p class="stat-label">New Enquiries</p></div>
            <div class="stat-card"><span class="stat-num"><?= $pendingReviews ?></span><p class="stat-label">Reviews To Approve</p></div>
          </div>

          <div class="admin-toolbar">
            <h3 style="margin:0;">Recent Enquiries</h3>
            <a href="<?= e(base_url('admin/enquiries.php')) ?>" class="btn btn-ghost btn-sm">View All</a>
          </div>

          <?php if (empty($recentEnquiries)): ?>
            <p class="empty-state">No enquiries yet.</p>
          <?php else: ?>
            <div class="data-table-wrap">
              <table class="data-table">
                <thead><tr><th>Name</th><th>Subject</th><th>Received</th><th>Status</th></tr></thead>
                <tbody>
                  <?php foreach ($recentEnquiries as $en): ?>
                    <tr>
                      <td><?= e($en['name']) ?></td>
                      <td><?= e($en['subject']) ?></td>
                      <td><?= e(format_date_nice(substr($en['created_at'], 0, 10))) ?></td>
                      <td><span class="status-badge status-<?= e($en['status']) ?>"><?= e(ucfirst($en['status'])) ?></span></td>
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
