<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';

require_login();
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {
    if (!csrf_verify()) {
        flash_set('error', 'Your session expired. Please try again.');
    } else {
        $bookingId = (int) $_POST['cancel_booking_id'];
        // The WHERE user_id = ? clause is the access-control check: you can only
        // ever cancel a row that belongs to your own account.
        $stmt = $pdo->prepare('UPDATE bookings SET status = "cancelled" WHERE id = ? AND user_id = ? AND status = "booked"');
        $stmt->execute([$bookingId, $user['id']]);
        flash_set('success', $stmt->rowCount() > 0 ? 'Booking cancelled.' : 'That booking could not be found.');
    }
    redirect('member/my-bookings.php');
}

$stmt = $pdo->prepare(
    'SELECT b.*, c.name AS class_name, c.start_time, c.end_time, t.full_name AS trainer_name
     FROM bookings b
     JOIN classes c ON c.id = b.class_id
     LEFT JOIN trainers t ON t.id = c.trainer_id
     WHERE b.user_id = ?
     ORDER BY b.booking_date DESC, c.start_time DESC'
);
$stmt->execute([$user['id']]);
$all = $stmt->fetchAll();

$today = date('Y-m-d');
$upcoming = array_filter($all, fn($b) => $b['status'] === 'booked' && $b['booking_date'] >= $today);
$history  = array_filter($all, fn($b) => !($b['status'] === 'booked' && $b['booking_date'] >= $today));

$pageTitle       = 'My Bookings | PulseFit Gym';
$pageDescription = 'View and manage your PulseFit Gym class bookings.';
$activePage      = 'dashboard';
$activeSub       = 'bookings';
require __DIR__ . '/../includes/header.php';
?>

  <section class="dashboard-shell">
    <div class="container">
      <div class="section-head reveal in-view" style="margin-bottom:32px;">
        <p class="eyebrow">My Account</p>
        <h1 style="font-size:2rem;">My Bookings</h1>
      </div>

      <div class="dashboard-grid">
        <?php require __DIR__ . '/../includes/member-nav.php'; ?>

        <div>
          <h3 style="margin-bottom:16px;">Upcoming</h3>
          <?php if (empty($upcoming)): ?>
            <div class="empty-state" style="margin-bottom:32px;">
              <p>No upcoming classes booked.</p>
              <a href="<?= e(base_url('membership.php')) ?>" class="btn btn-primary btn-sm" style="margin-top:14px;">Browse The Schedule</a>
            </div>
          <?php else: ?>
            <div class="data-table-wrap" style="margin-bottom:32px;">
              <table class="data-table">
                <thead><tr><th>Class</th><th>Date</th><th>Time</th><th>Trainer</th><th></th></tr></thead>
                <tbody>
                  <?php foreach ($upcoming as $b): ?>
                    <tr>
                      <td><?= e($b['class_name']) ?></td>
                      <td><?= e(format_date_nice($b['booking_date'])) ?></td>
                      <td><?= e(format_time($b['start_time'])) ?></td>
                      <td><?= e($b['trainer_name'] ?? '—') ?></td>
                      <td>
                        <form method="post" onsubmit="return confirm('Cancel this booking?');">
                          <?= csrf_field() ?>
                          <input type="hidden" name="cancel_booking_id" value="<?= (int) $b['id'] ?>">
                          <button type="submit" class="btn btn-ghost btn-icon-sm">Cancel</button>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>

          <h3 style="margin-bottom:16px;">History</h3>
          <?php if (empty($history)): ?>
            <p class="empty-state">Nothing here yet.</p>
          <?php else: ?>
            <div class="data-table-wrap">
              <table class="data-table">
                <thead><tr><th>Class</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                  <?php foreach ($history as $b): ?>
                    <tr>
                      <td><?= e($b['class_name']) ?></td>
                      <td><?= e(format_date_nice($b['booking_date'])) ?></td>
                      <td><span class="status-badge status-<?= e($b['status']) ?>"><?= e(ucfirst($b['status'])) ?></span></td>
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
