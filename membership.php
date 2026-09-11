<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

// ---- Plans + their features (data-driven pricing table) -------------------
$plans = $pdo->query('SELECT * FROM membership_plans ORDER BY display_order ASC')->fetchAll();
$featureStmt = $pdo->prepare('SELECT * FROM plan_features WHERE plan_id = ? ORDER BY display_order ASC');

$user = current_user();
$myActivePlanId = null;
if ($user) {
    $stmt = $pdo->prepare('SELECT plan_id FROM user_memberships WHERE user_id = ? AND status = "active" ORDER BY id DESC LIMIT 1');
    $stmt->execute([$user['id']]);
    $myActivePlanId = $stmt->fetchColumn() ?: null;
}

// ---- Weekly schedule, pivoted into a Time × Day grid -----------------------
$classesRaw = $pdo->query(
    "SELECT c.*, t.full_name AS trainer_name
     FROM classes c
     LEFT JOIN trainers t ON t.id = c.trainer_id
     ORDER BY c.start_time ASC"
)->fetchAll();

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$grid = [];
foreach ($classesRaw as $c) {
    $grid[$c['start_time']][$c['day_of_week']] = $c;
}
ksort($grid);

$pageTitle       = 'Membership Plans | PulseFit Gym';
$pageDescription = 'Compare PulseFit Gym membership plans, browse our weekly class schedule and book a class online.';
$activePage      = 'membership';
require __DIR__ . '/includes/header.php';
?>

  <!-- ============ PAGE BANNER ============ -->
  <section class="page-banner">
    <div class="hero-bg" style="background-image:url('<?= e(base_url('images/banner-membership.jpg')) ?>');" role="img" aria-label="Close-up of weight plates and a barbell on a gym floor"></div>
    <div class="hero-overlay"></div>
    <div class="container">
      <p class="eyebrow">Membership</p>
      <h1>Plans built around how you train</h1>
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= e(base_url('index.php')) ?>">Home</a>
        <span aria-hidden="true">/</span>
        <span>Membership</span>
      </nav>
    </div>
  </section>

  <!-- ============ PRICING (data-driven) ============ -->
  <section aria-labelledby="pricing-heading">
    <div class="container">
      <div class="section-head center reveal">
        <p class="eyebrow">Choose Your Plan</p>
        <h2 id="pricing-heading">No lock-in contracts. Cancel anytime.</h2>
        <p>Every membership includes access to our member app, free parking and induction session.</p>
      </div>
      <div class="grid grid-3">
        <?php foreach ($plans as $i => $plan): ?>
          <?php
          $featureStmt->execute([$plan['id']]);
          $features = $featureStmt->fetchAll();
          $isMine   = $myActivePlanId && (int) $myActivePlanId === (int) $plan['id'];
          ?>
          <div class="plan-card <?= $plan['is_featured'] ? 'featured' : '' ?> reveal <?= $i > 0 ? 'reveal-delay-' . $i : '' ?>">
            <?php if ($plan['is_featured']): ?><span class="plan-badge">Most Popular</span><?php endif; ?>
            <p class="plan-name"><?= e($plan['name']) ?></p>
            <div class="plan-price"><span class="amount"><?= e(format_price((int) $plan['price_cents'])) ?></span><span class="cycle">/ <?= e($plan['billing_cycle']) ?></span></div>
            <p class="plan-desc"><?= e($plan['description']) ?></p>
            <ul class="plan-features">
              <?php foreach ($features as $f): ?>
                <li class="<?= $f['is_included'] ? '' : 'unavailable' ?>">
                  <?php if ($f['is_included']): ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  <?php else: ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  <?php endif; ?>
                  <?= e($f['feature_text']) ?>
                </li>
              <?php endforeach; ?>
            </ul>
            <?php if ($isMine): ?>
              <span class="btn btn-ghost btn-block" style="pointer-events:none;">Your Current Plan</span>
            <?php elseif (has_role('member', 'normal')): ?>
              <form method="post" action="<?= e(base_url('select-plan.php')) ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="plan_id" value="<?= (int) $plan['id'] ?>">
                <button type="submit" class="btn <?= $plan['is_featured'] ? 'btn-primary' : 'btn-ghost' ?> btn-block"><?= $myActivePlanId ? 'Switch To This Plan' : 'Choose ' . e($plan['name']) ?></button>
              </form>
            <?php else: ?>
              <a href="<?= e(base_url('register.php')) ?>" class="btn <?= $plan['is_featured'] ? 'btn-primary' : 'btn-ghost' ?> btn-block">Choose <?= e($plan['name']) ?></a>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============ CLASS SCHEDULE (data-driven) ============ -->
  <section class="section-alt" aria-labelledby="schedule-heading">
    <div class="container">
      <div class="section-head center reveal">
        <p class="eyebrow">Class Schedule</p>
        <h2 id="schedule-heading">Weekly timetable</h2>
        <p><?= has_role('member', 'admin') ? 'Click any class to book your spot.' : 'Log in as a member to book a class online.' ?></p>
      </div>
      <div class="schedule-table-wrap reveal">
        <table class="schedule-table">
          <caption class="sr-only">Weekly class schedule showing class name by day and time</caption>
          <thead>
            <tr>
              <th scope="col">Time</th>
              <?php foreach ($days as $day): ?><th scope="col"><?= e($day) ?></th><?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($grid as $time => $row): ?>
              <tr>
                <th scope="row"><?= e(format_time($time)) ?></th>
                <?php foreach ($days as $day): ?>
                  <td>
                    <?php if (isset($row[$day])): ?>
                      <?php $c = $row[$day]; ?>
                      <?php if (has_role('member', 'admin')): ?>
                        <a href="<?= e(base_url('member/book-class.php?class_id=' . $c['id'])) ?>"><?= e($c['name']) ?></a>
                      <?php else: ?>
                        <?= e($c['name']) ?>
                      <?php endif; ?>
                      <?php if ($c['is_new']): ?><span class="tag">New</span><?php endif; ?>
                    <?php else: ?>
                      —
                    <?php endif; ?>
                  </td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- ============ ADD-ONS ============ -->
  <section aria-labelledby="addons-heading">
    <div class="container">
      <div class="section-head center reveal">
        <p class="eyebrow">Add-Ons</p>
        <h2 id="addons-heading">Level up any plan</h2>
        <p>Pair these with any membership tier — ask a coach about bundle pricing.</p>
      </div>
      <div class="grid grid-4">
        <div class="card reveal">
          <div class="feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 19c0-3 2.5-5.2 5.5-5.2s5.5 2.2 5.5 5.2"/><circle cx="17" cy="8.5" r="2.6"/><path d="M15.5 13.6c2.4.3 4 2.2 4 5.4"/></svg></div>
          <h3>PT Packs</h3>
          <p>5 or 10-session personal training packs with any coach on our floor.</p>
        </div>
        <div class="card reveal reveal-delay-1">
          <div class="feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 4C10 4 4 10 4 18c0 .6 0 1 .1 1.5C13 19 20 12 20 4z"/><path d="M5 19 14 10"/></svg></div>
          <h3>Nutrition Coaching</h3>
          <p>Monthly check-ins and a custom macro plan built around your training.</p>
        </div>
        <div class="card reveal reveal-delay-2">
          <div class="feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg></div>
          <h3>Recovery Zone</h3>
          <p>Sauna, foam-rolling bay and stretch area — day pass or unlimited add-on.</p>
        </div>
        <div class="card reveal reveal-delay-3">
          <div class="feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V6z"/></svg></div>
          <h3>Kids Club</h3>
          <p>Supervised play area so parents can train without the childcare juggle.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ FAQ ============ -->
  <section class="section-alt" aria-labelledby="faq-heading">
    <div class="container">
      <div class="section-head center reveal">
        <p class="eyebrow">FAQ</p>
        <h2 id="faq-heading">Membership questions, answered</h2>
      </div>
      <div class="grid" style="max-width:820px;margin-inline:auto;gap:16px;">
        <details class="card reveal">
          <summary style="cursor:pointer;font-weight:700;">Do I need to sign a long-term contract?</summary>
          <p style="margin-top:12px;">No — all plans are month-to-month with no lock-in. Cancel anytime with 7 days' notice.</p>
        </details>
        <details class="card reveal reveal-delay-1">
          <summary style="cursor:pointer;font-weight:700;">Can I freeze my membership?</summary>
          <p style="margin-top:12px;">Yes, freezes are available for up to 60 days per year for medical or travel reasons.</p>
        </details>
        <details class="card reveal reveal-delay-2">
          <summary style="cursor:pointer;font-weight:700;">Is there a joining fee?</summary>
          <p style="margin-top:12px;">No joining or cancellation fees on any plan — the price you see is the price you pay.</p>
        </details>
        <details class="card reveal reveal-delay-3">
          <summary style="cursor:pointer;font-weight:700;">Can I upgrade or downgrade my plan?</summary>
          <p style="margin-top:12px;">Absolutely — changes take effect immediately from your account dashboard.</p>
        </details>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
