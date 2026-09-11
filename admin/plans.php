<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth.php';

require_role('admin');

$errors = [];
$editingPlan = null;
$editingFeaturesText = '';

// ---- Handle delete ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_plan_id']) && csrf_verify()) {
    $id = (int) $_POST['delete_plan_id'];
    try {
        $pdo->prepare('DELETE FROM membership_plans WHERE id = ?')->execute([$id]);
        flash_set('success', 'Plan deleted.');
    } catch (PDOException $e) {
        flash_set('error', 'That plan cannot be deleted while members are subscribed to it.');
    }
    redirect('admin/plans.php');
}

// ---- Handle create/update ----------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_plan'])) {
    if (!csrf_verify()) {
        $errors['form'] = 'Your session expired. Please try again.';
    } else {
        $planId       = (int) ($_POST['plan_id'] ?? 0);
        $name         = trim((string) ($_POST['name'] ?? ''));
        $priceDollars = (string) ($_POST['price'] ?? '');
        $cycle        = trim((string) ($_POST['billing_cycle'] ?? 'month'));
        $description  = trim((string) ($_POST['description'] ?? ''));
        $isFeatured   = isset($_POST['is_featured']) ? 1 : 0;
        $displayOrder = (int) ($_POST['display_order'] ?? 0);
        $featuresText = (string) ($_POST['features_text'] ?? '');

        if ($name === '' || mb_strlen($name) < 2) {
            $errors['name'] = 'Please enter a plan name.';
        }
        if (!is_numeric($priceDollars) || (float) $priceDollars < 0) {
            $errors['price'] = 'Please enter a valid price.';
        }
        if ($cycle === '') {
            $errors['billing_cycle'] = 'Please enter a billing cycle (e.g. month).';
        }

        if (empty($errors)) {
            $priceCents = (int) round((float) $priceDollars * 100);
            $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));

            if ($planId > 0) {
                $pdo->prepare(
                    'UPDATE membership_plans SET name=?, slug=?, price_cents=?, billing_cycle=?, description=?, is_featured=?, display_order=? WHERE id=?'
                )->execute([$name, $slug, $priceCents, $cycle, $description, $isFeatured, $displayOrder, $planId]);
            } else {
                $pdo->prepare(
                    'INSERT INTO membership_plans (name, slug, price_cents, billing_cycle, description, is_featured, display_order) VALUES (?,?,?,?,?,?,?)'
                )->execute([$name, $slug, $priceCents, $cycle, $description, $isFeatured, $displayOrder]);
                $planId = (int) $pdo->lastInsertId();
            }

            // Replace the feature list: one line per feature, "-" prefix = not included.
            $pdo->prepare('DELETE FROM plan_features WHERE plan_id = ?')->execute([$planId]);
            $lines = array_filter(array_map('trim', explode("\n", $featuresText)), fn($l) => $l !== '');
            $order = 1;
            $insertFeature = $pdo->prepare('INSERT INTO plan_features (plan_id, feature_text, is_included, display_order) VALUES (?,?,?,?)');
            foreach ($lines as $line) {
                $included = 1;
                if (str_starts_with($line, '-')) {
                    $included = 0;
                    $line = trim(substr($line, 1));
                }
                if ($line !== '') {
                    $insertFeature->execute([$planId, $line, $included, $order++]);
                }
            }

            flash_set('success', 'Plan saved.');
            redirect('admin/plans.php');
        } else {
            $editingPlan = ['id' => $planId, 'name' => $name, 'price_cents' => (int) round((float) ($priceDollars ?: 0) * 100), 'billing_cycle' => $cycle, 'description' => $description, 'is_featured' => $isFeatured, 'display_order' => $displayOrder];
            $editingFeaturesText = $featuresText;
        }
    }
}

// ---- Load a plan for editing (GET ?edit=ID) --------------------------
if ($editingPlan === null && isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM membership_plans WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $editingPlan = $stmt->fetch() ?: null;

    if ($editingPlan) {
        $fStmt = $pdo->prepare('SELECT * FROM plan_features WHERE plan_id = ? ORDER BY display_order ASC');
        $fStmt->execute([$editingPlan['id']]);
        $lines = [];
        foreach ($fStmt->fetchAll() as $f) {
            $lines[] = ($f['is_included'] ? '' : '- ') . $f['feature_text'];
        }
        $editingFeaturesText = implode("\n", $lines);
    }
}

$plans = $pdo->query('SELECT * FROM membership_plans ORDER BY display_order ASC')->fetchAll();

$pageTitle  = 'Manage Plans | PulseFit Gym Admin';
$activePage = 'admin';
$activeSub  = 'plans';
require __DIR__ . '/../includes/header.php';
?>

  <section class="dashboard-shell">
    <div class="container">
      <div class="section-head reveal in-view" style="margin-bottom:32px;">
        <p class="eyebrow">Admin</p>
        <h1 style="font-size:2rem;">Membership Plans</h1>
      </div>

      <div class="dashboard-grid">
        <?php require __DIR__ . '/../includes/admin-nav.php'; ?>

        <div>
          <div class="form-card" style="margin-bottom:32px;">
            <h3 style="margin-bottom:20px;"><?= $editingPlan && !empty($editingPlan['id']) ? 'Edit Plan' : 'Add A New Plan' ?></h3>

            <?php if (!empty($errors['form'])): ?><div class="form-status show error"><span><?= e($errors['form']) ?></span></div><?php endif; ?>

            <form method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="save_plan" value="1">
              <input type="hidden" name="plan_id" value="<?= (int) ($editingPlan['id'] ?? 0) ?>">

              <div class="inline-form-grid">
                <div class="field <?= isset($errors['name']) ? 'has-error' : '' ?>">
                  <label for="name">Plan Name <span class="required">*</span></label>
                  <input type="text" id="name" name="name" value="<?= e($editingPlan['name'] ?? '') ?>" required>
                  <?php if (isset($errors['name'])): ?><p class="field-error" style="display:flex;"><?= e($errors['name']) ?></p><?php endif; ?>
                </div>
                <div class="field <?= isset($errors['price']) ? 'has-error' : '' ?>">
                  <label for="price">Price (AUD) <span class="required">*</span></label>
                  <input type="number" id="price" name="price" min="0" step="1" value="<?= isset($editingPlan['price_cents']) ? (int) round($editingPlan['price_cents'] / 100) : '' ?>" required>
                  <?php if (isset($errors['price'])): ?><p class="field-error" style="display:flex;"><?= e($errors['price']) ?></p><?php endif; ?>
                </div>
                <div class="field">
                  <label for="billing_cycle">Billing Cycle</label>
                  <input type="text" id="billing_cycle" name="billing_cycle" value="<?= e($editingPlan['billing_cycle'] ?? 'month') ?>">
                </div>
                <div class="field">
                  <label for="display_order">Display Order</label>
                  <input type="number" id="display_order" name="display_order" value="<?= (int) ($editingPlan['display_order'] ?? 0) ?>">
                </div>
              </div>

              <div class="field">
                <label for="description">Short Description</label>
                <input type="text" id="description" name="description" value="<?= e($editingPlan['description'] ?? '') ?>">
              </div>

              <div class="field">
                <label for="features_text">Features (one per line — start a line with "-" for an excluded feature)</label>
                <textarea id="features_text" name="features_text" rows="6"><?= e($editingFeaturesText) ?></textarea>
              </div>

              <label class="checkbox-field" style="margin-bottom:20px;">
                <input type="checkbox" name="is_featured" <?= !empty($editingPlan['is_featured']) ? 'checked' : '' ?>>
                <span>Mark as "Most Popular"</span>
              </label>

              <div style="display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary"><?= $editingPlan && !empty($editingPlan['id']) ? 'Update Plan' : 'Create Plan' ?></button>
                <?php if ($editingPlan && !empty($editingPlan['id'])): ?>
                  <a href="<?= e(base_url('admin/plans.php')) ?>" class="btn btn-ghost">Cancel</a>
                <?php endif; ?>
              </div>
            </form>
          </div>

          <h3 style="margin-bottom:16px;">All Plans</h3>
          <div class="data-table-wrap">
            <table class="data-table">
              <thead><tr><th>Name</th><th>Price</th><th>Featured</th><th>Order</th><th></th></tr></thead>
              <tbody>
                <?php foreach ($plans as $p): ?>
                  <tr>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e(format_price((int) $p['price_cents'])) ?> / <?= e($p['billing_cycle']) ?></td>
                    <td><?= $p['is_featured'] ? '<span class="status-badge status-active">Yes</span>' : '—' ?></td>
                    <td><?= (int) $p['display_order'] ?></td>
                    <td class="table-actions">
                      <a href="<?= e(base_url('admin/plans.php?edit=' . $p['id'])) ?>" class="btn btn-ghost btn-icon-sm">Edit</a>
                      <form method="post" onsubmit="return confirm('Delete this plan permanently?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="delete_plan_id" value="<?= (int) $p['id'] ?>">
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
