<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

$errors = [];
$editingClass = null;
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_class_id']) && csrf_verify()) {
    $id = (int) $_POST['delete_class_id'];
    try {
        $pdo->prepare('DELETE FROM classes WHERE id = ?')->execute([$id]);
        flash_set('success', 'Class removed from the schedule.');
    } catch (PDOException $e) {
        flash_set('error', 'This class has existing bookings and cannot be deleted. Remove or wait for those bookings to clear first.');
    }
    redirect('admin/classes.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_class'])) {
    if (!csrf_verify()) {
        $errors['form'] = 'Your session expired. Please try again.';
    } else {
        $classId   = (int) ($_POST['class_id'] ?? 0);
        $name      = trim((string) ($_POST['name'] ?? ''));
        $day       = (string) ($_POST['day_of_week'] ?? '');
        $startTime = (string) ($_POST['start_time'] ?? '');
        $endTime   = (string) ($_POST['end_time'] ?? '');
        $trainerId = (int) ($_POST['trainer_id'] ?? 0) ?: null;
        $capacity  = (int) ($_POST['capacity'] ?? 20);
        $isNew     = isset($_POST['is_new']) ? 1 : 0;

        if ($name === '') {
            $errors['name'] = 'Please enter a class name.';
        }
        if (!in_array($day, $days, true)) {
            $errors['day_of_week'] = 'Please choose a day.';
        }
        if (!$startTime || !$endTime || $startTime >= $endTime) {
            $errors['start_time'] = 'End time must be after start time.';
        }
        if ($capacity < 1) {
            $errors['capacity'] = 'Capacity must be at least 1.';
        }

        if (empty($errors)) {
            if ($classId > 0) {
                $pdo->prepare(
                    'UPDATE classes SET name=?, day_of_week=?, start_time=?, end_time=?, trainer_id=?, capacity=?, is_new=? WHERE id=?'
                )->execute([$name, $day, $startTime, $endTime, $trainerId, $capacity, $isNew, $classId]);
            } else {
                $pdo->prepare(
                    'INSERT INTO classes (name, day_of_week, start_time, end_time, trainer_id, capacity, is_new) VALUES (?,?,?,?,?,?,?)'
                )->execute([$name, $day, $startTime, $endTime, $trainerId, $capacity, $isNew]);
            }
            flash_set('success', 'Class saved.');
            redirect('admin/classes.php');
        } else {
            $editingClass = compact('classId', 'name', 'day', 'startTime', 'endTime', 'trainerId', 'capacity', 'isNew');
        }
    }
}

if ($editingClass === null && isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM classes WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $row = $stmt->fetch();
    if ($row) {
        $editingClass = [
            'classId' => $row['id'], 'name' => $row['name'], 'day' => $row['day_of_week'],
            'startTime' => substr($row['start_time'], 0, 5), 'endTime' => substr($row['end_time'], 0, 5),
            'trainerId' => $row['trainer_id'], 'capacity' => $row['capacity'], 'isNew' => $row['is_new'],
        ];
    }
}

$trainers = $pdo->query('SELECT id, full_name FROM trainers ORDER BY display_order ASC')->fetchAll();
$classes  = $pdo->query(
    "SELECT c.*, t.full_name AS trainer_name FROM classes c LEFT JOIN trainers t ON t.id = c.trainer_id
     ORDER BY FIELD(c.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), c.start_time"
)->fetchAll();

$pageTitle  = 'Manage Classes | PulseFit Gym Admin';
$activePage = 'admin';
$activeSub  = 'classes';
require __DIR__ . '/../includes/header.php';
?>

  <section class="dashboard-shell">
    <div class="container">
      <div class="section-head reveal in-view" style="margin-bottom:32px;">
        <p class="eyebrow">Admin</p>
        <h1 style="font-size:2rem;">Class Schedule</h1>
      </div>

      <div class="dashboard-grid">
        <?php require __DIR__ . '/../includes/admin-nav.php'; ?>

        <div>
          <div class="form-card" style="margin-bottom:32px;">
            <h3 style="margin-bottom:20px;"><?= $editingClass ? 'Edit Class' : 'Add A New Class' ?></h3>

            <?php if (!empty($errors['form'])): ?><div class="form-status show error"><span><?= e($errors['form']) ?></span></div><?php endif; ?>

            <form method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="save_class" value="1">
              <input type="hidden" name="class_id" value="<?= (int) ($editingClass['classId'] ?? 0) ?>">

              <div class="inline-form-grid">
                <div class="field <?= isset($errors['name']) ? 'has-error' : '' ?>">
                  <label for="name">Class Name <span class="required">*</span></label>
                  <input type="text" id="name" name="name" value="<?= e($editingClass['name'] ?? '') ?>" placeholder="e.g. HIIT Blast" required>
                  <?php if (isset($errors['name'])): ?><p class="field-error" style="display:flex;"><?= e($errors['name']) ?></p><?php endif; ?>
                </div>
                <div class="field <?= isset($errors['day_of_week']) ? 'has-error' : '' ?>">
                  <label for="day_of_week">Day <span class="required">*</span></label>
                  <select id="day_of_week" name="day_of_week" required>
                    <?php foreach ($days as $d): ?>
                      <option value="<?= e($d) ?>" <?= ($editingClass['day'] ?? '') === $d ? 'selected' : '' ?>><?= e($d) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="field <?= isset($errors['start_time']) ? 'has-error' : '' ?>">
                  <label for="start_time">Start Time <span class="required">*</span></label>
                  <input type="time" id="start_time" name="start_time" value="<?= e($editingClass['startTime'] ?? '06:00') ?>" required>
                  <?php if (isset($errors['start_time'])): ?><p class="field-error" style="display:flex;"><?= e($errors['start_time']) ?></p><?php endif; ?>
                </div>
                <div class="field">
                  <label for="end_time">End Time <span class="required">*</span></label>
                  <input type="time" id="end_time" name="end_time" value="<?= e($editingClass['endTime'] ?? '06:45') ?>" required>
                </div>
                <div class="field">
                  <label for="trainer_id">Trainer</label>
                  <select id="trainer_id" name="trainer_id">
                    <option value="">— None —</option>
                    <?php foreach ($trainers as $t): ?>
                      <option value="<?= (int) $t['id'] ?>" <?= (int) ($editingClass['trainerId'] ?? 0) === (int) $t['id'] ? 'selected' : '' ?>><?= e($t['full_name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="field <?= isset($errors['capacity']) ? 'has-error' : '' ?>">
                  <label for="capacity">Capacity <span class="required">*</span></label>
                  <input type="number" id="capacity" name="capacity" min="1" placeholder="20" value="<?= (int) ($editingClass['capacity'] ?? 20) ?>" required>
                </div>
              </div>

              <label class="checkbox-field" style="margin-bottom:20px;">
                <input type="checkbox" name="is_new" <?= !empty($editingClass['isNew']) ? 'checked' : '' ?>>
                <span>Mark with a "New" tag</span>
              </label>

              <div style="display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary"><?= $editingClass ? 'Update Class' : 'Create Class' ?></button>
                <?php if ($editingClass): ?><a href="<?= e(base_url('admin/classes.php')) ?>" class="btn btn-ghost">Cancel</a><?php endif; ?>
              </div>
            </form>
          </div>

          <h3 style="margin-bottom:16px;">Weekly Schedule</h3>
          <div class="data-table-wrap">
            <table class="data-table">
              <thead><tr><th>Class</th><th>Day</th><th>Time</th><th>Trainer</th><th>Capacity</th><th></th></tr></thead>
              <tbody>
                <?php foreach ($classes as $c): ?>
                  <tr>
                    <td><?= e($c['name']) ?> <?php if ($c['is_new']): ?><span class="tag">New</span><?php endif; ?></td>
                    <td><?= e($c['day_of_week']) ?></td>
                    <td><?= e(format_time($c['start_time'])) ?>–<?= e(format_time($c['end_time'])) ?></td>
                    <td><?= e($c['trainer_name'] ?? '—') ?></td>
                    <td><?= (int) $c['capacity'] ?></td>
                    <td class="table-actions">
                      <a href="<?= e(base_url('admin/classes.php?edit=' . $c['id'])) ?>" class="btn btn-ghost btn-icon-sm">Edit</a>
                      <form method="post" onsubmit="return confirm('Delete this class?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="delete_class_id" value="<?= (int) $c['id'] ?>">
                        <button type="submit" class="btn btn-ghost btn-icon-sm" style="color:var(--accent-2);">Delete</button>
                      </form>
                    </td>
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
