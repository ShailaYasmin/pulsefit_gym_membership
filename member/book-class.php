<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';

require_login();
$user = current_user();

$classId = (int) ($_GET['class_id'] ?? $_POST['class_id'] ?? 0);
$stmt = $pdo->prepare(
    'SELECT c.*, t.full_name AS trainer_name
     FROM classes c LEFT JOIN trainers t ON t.id = c.trainer_id
     WHERE c.id = ?'
);
$stmt->execute([$classId]);
$class = $stmt->fetch();

if (!$class) {
    flash_set('error', 'That class could not be found.');
    redirect('membership.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!has_role('member', 'admin')) {
        flash_set('error', 'You need an active membership to book classes.');
        redirect('membership.php');
    }

    if (!csrf_verify()) {
        $errors['form'] = 'Your session expired. Please try again.';
    } else {
        $bookingDate = (string) ($_POST['booking_date'] ?? '');
        $validDates  = upcoming_dates_for_weekday($class['day_of_week'], 6);

        if (!in_array($bookingDate, $validDates, true)) {
            $errors['form'] = 'Please choose one of the available dates.';
        } else {
            $countStmt = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE class_id = ? AND booking_date = ? AND status = "booked"');
            $countStmt->execute([$classId, $bookingDate]);
            $spotsTaken = (int) $countStmt->fetchColumn();

            if ($spotsTaken >= (int) $class['capacity']) {
                $errors['form'] = 'This class is fully booked on that date. Please choose another date.';
            } else {
                $existsStmt = $pdo->prepare('SELECT id FROM bookings WHERE user_id = ? AND class_id = ? AND booking_date = ? AND status = "booked"');
                $existsStmt->execute([$user['id'], $classId, $bookingDate]);
                if ($existsStmt->fetch()) {
                    $errors['form'] = "You've already booked this class on that date.";
                } else {
                    $pdo->prepare('INSERT INTO bookings (user_id, class_id, booking_date) VALUES (?, ?, ?)')
                        ->execute([$user['id'], $classId, $bookingDate]);
                    flash_set('success', 'Booked! ' . $class['name'] . ' on ' . format_date_nice($bookingDate) . '.');
                    redirect('member/my-bookings.php');
                }
            }
        }
    }
}

$upcomingDates = upcoming_dates_for_weekday($class['day_of_week'], 6);

$pageTitle       = 'Book ' . $class['name'] . ' | PulseFit Gym';
$pageDescription = 'Book your spot in ' . $class['name'] . ' at PulseFit Gym.';
$activePage      = 'dashboard';
require __DIR__ . '/../includes/header.php';
?>

  <section class="auth-page">
    <div class="container">
      <div class="auth-card">
        <div class="form-card reveal in-view">
          <p class="eyebrow">Book A Class</p>
          <h1 style="font-size:1.8rem;margin-bottom:6px;"><?= e($class['name']) ?></h1>
          <p style="font-size:0.9rem;margin-bottom:26px;">
            Every <?= e($class['day_of_week']) ?>, <?= e(format_time($class['start_time'])) ?>–<?= e(format_time($class['end_time'])) ?>
            <?php if ($class['trainer_name']): ?> · with <?= e($class['trainer_name']) ?><?php endif; ?>
          </p>

          <?php if (!has_role('member', 'admin')): ?>
            <div class="form-status show error"><span>You need an active membership to book classes.</span></div>
            <a href="<?= e(base_url('membership.php')) ?>" class="btn btn-primary btn-block" style="margin-top:16px;">Choose A Membership Plan</a>
          <?php else: ?>
            <?php if (!empty($errors['form'])): ?>
              <div class="form-status show error"><span><?= e($errors['form']) ?></span></div>
            <?php endif; ?>

            <form method="post" action="<?= e(base_url('member/book-class.php')) ?>">
              <?= csrf_field() ?>
              <input type="hidden" name="class_id" value="<?= (int) $class['id'] ?>">

              <div class="field">
                <label for="booking_date">Choose a date <span class="required">*</span></label>
                <select id="booking_date" name="booking_date" required>
                  <?php foreach ($upcomingDates as $d): ?>
                    <option value="<?= e($d) ?>"><?= e(format_date_nice($d)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <button type="submit" class="btn btn-primary btn-block">Confirm Booking</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
