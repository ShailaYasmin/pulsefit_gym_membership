<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';

require_login();
$user = current_user();

// Current active membership (if any)
$stmt = $pdo->prepare(
    'SELECT um.*, mp.name AS plan_name, mp.price_cents, mp.billing_cycle
     FROM user_memberships um
     JOIN membership_plans mp ON mp.id = um.plan_id
     WHERE um.user_id = ? AND um.status = "active"
     ORDER BY um.id DESC LIMIT 1'
);
$stmt->execute([$user['id']]);
$activePlan = $stmt->fetch();

// Upcoming bookings
$stmt = $pdo->prepare(
    'SELECT b.*, c.name AS class_name, c.start_time, c.end_time, t.full_name AS trainer_name
     FROM bookings b
     JOIN classes c ON c.id = b.class_id
     LEFT JOIN trainers t ON t.id = c.trainer_id
     WHERE b.user_id = ? AND b.status = "booked" AND b.booking_date >= CURDATE()
     ORDER BY b.booking_date ASC, c.start_time ASC
     LIMIT 5'
);
$stmt->execute([$user['id']]);
$upcoming = $stmt->fetchAll();

// Stat counts
$s = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE user_id = ? AND status = "attended"');
$s->execute([$user['id']]);
$attendedCount = (int) $s->fetchColumn();

$pageTitle       = 'My Dashboard | PulseFit Gym';
$pageDescription = 'Manage your PulseFit Gym membership and class bookings.';
$activePage      = 'dashboard';
$activeSub       = 'dashboard';
require __DIR__ . '/../includes/header.php';
?>

  <section class="dashboard-shell">
    <div class="container">
      <div class="section-head reveal in-view" style="margin-bottom:32px;">
        <p class="eyebrow">My Account</p>
        <h1 style="font-size:2rem;">Welcome back, <?= e(explode(' ', $user['full_name'])[0]) ?></h1>
      </div>

      <div class="dashboard-grid">
        <?php require __DIR__ . '/../includes/member-nav.php'; ?>

        <div>
          <div class="stat-card-row">
            <div class="stat-card">
              <span class="stat-num"><?= e($activePlan['plan_name'] ?? '—') ?></span>
              <p class="stat-label">Current Plan</p>
            </div>
            <div class="stat-card">
              <span class="stat-num"><?= count($upcoming) ?></span>
              <p class="stat-label">Upcoming Classes</p>
            </div>
            <div class="stat-card">
              <span class="stat-num"><?= $attendedCount ?></span>
              <p class="stat-label">Classes Attended</p>
            </div>
          </div>

          <?php if (!$activePlan): ?>
            <div class="card" style="margin-bottom:32px;">
              <h3>You don't have an active membership yet</h3>
              <p style="margin:10px 0 20px;">Choose a plan to unlock class bookings and member pricing.</p>
              <a href="<?= e(base_url('membership.php')) ?>" class="btn btn-primary">View Membership Plans</a>
            </div>
          <?php else: ?>
            <div class="card" style="margin-bottom:32px;">
              <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div>
                  <h3><?= e($activePlan['plan_name']) ?> Plan</h3>
                  <p style="margin-top:6px;"><?= e(format_price((int) $activePlan['price_cents'])) ?> / <?= e($activePlan['billing_cycle']) ?> · Member since <?= e(format_date_nice($activePlan['start_date'])) ?></p>
                </div>
                <a href="<?= e(base_url('membership.php')) ?>" class="btn btn-ghost btn-sm">Change Plan</a>
              </div>
            </div>
          <?php endif; ?>

          <div class="admin-toolbar">
            <h3 style="margin:0;">Upcoming Bookings</h3>
            <a href="<?= e(base_url('member/my-bookings.php')) ?>" class="btn btn-ghost btn-sm">View All</a>
          </div>

          <?php if (empty($upcoming)): ?>
            <div class="empty-state">
              <p>No upcoming classes booked yet.</p>
              <a href="<?= e(base_url('membership.php')) ?>" class="btn btn-primary btn-sm" style="margin-top:14px;">Browse The Schedule</a>
            </div>
          <?php else: ?>
            <div class="data-table-wrap">
              <table class="data-table">
                <thead>
                  <tr><th>Class</th><th>Date</th><th>Time</th><th>Trainer</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($upcoming as $b): ?>
                    <tr>
                      <td><?= e($b['class_name']) ?></td>
                      <td><?= e(format_date_nice($b['booking_date'])) ?></td>
                      <td><?= e(format_time($b['start_time'])) ?></td>
                      <td><?= e($b['trainer_name'] ?? '—') ?></td>
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
