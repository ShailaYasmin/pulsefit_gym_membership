<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

// ---- Live counts for the stats strip (data-driven, not hard-coded) --------
$memberCount = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role IN ('member','admin')")->fetchColumn();
$classCount  = (int) $pdo->query('SELECT COUNT(*) FROM classes')->fetchColumn();
$coachCount  = (int) $pdo->query('SELECT COUNT(*) FROM trainers')->fetchColumn();

// ---- The 3 lowest display_order plans for the homepage preview ------------
$plans = $pdo->query('SELECT * FROM membership_plans ORDER BY display_order ASC LIMIT 3')->fetchAll();
$planFeatureStmt = $pdo->prepare(
    'SELECT feature_text FROM plan_features WHERE plan_id = ? AND is_included = 1 ORDER BY display_order ASC LIMIT 2'
);

$pageTitle       = 'PulseFit Gym | Train Hard. Live Stronger.';
$pageDescription = 'PulseFit Gym is a boutique strength and conditioning studio offering modern equipment, expert coaching and flexible gym memberships in Sydney. Join today for a free trial class.';
$activePage      = 'home';
require __DIR__ . '/includes/header.php';
?>

  <!-- ============ HERO ============ -->
  <section class="hero">
    <div class="hero-bg" style="background-image:url('<?= e(base_url('images/hero-home.jpg')) ?>');" role="img" aria-label="Woman performing core exercises on a mat in a bright, modern gym"></div>
    <div class="hero-overlay"></div>
    <div class="container">
      <div class="hero-content">
        <p class="eyebrow">Welcome to PulseFit Gym</p>
        <h1>Train Hard.<br>Live <span class="text-gradient">Stronger.</span></h1>
        <p class="lead">PulseFit Gym is a boutique strength and conditioning studio in the heart of the city, blending modern equipment, expert coaching and a supportive community to help every member train smarter, move better and reach personal bests — no matter where they're starting from.</p>
        <div class="hero-actions">
          <a href="<?= e(base_url('membership.php')) ?>" class="btn btn-primary">Join Now</a>
          <a href="<?= e(base_url('about.php')) ?>" class="btn btn-ghost">Explore The Studio</a>
        </div>
        <div class="hero-stats">
          <div>
            <div class="stat-num"><?= $memberCount ?>+</div>
            <div class="stat-label">Active Members</div>
          </div>
          <div>
            <div class="stat-num"><?= $coachCount ?></div>
            <div class="stat-label">Expert Trainers</div>
          </div>
          <div>
            <div class="stat-num">24/7</div>
            <div class="stat-label">Studio Access</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ STATS STRIP ============ -->
  <section class="stats-strip" aria-label="PulseFit Gym in numbers">
    <div class="container">
      <div class="grid">
        <div class="stat-block reveal">
          <span class="stat-num"><span data-count="<?= $memberCount ?>" data-suffix="+">0</span></span>
          <p class="stat-label">Active Members</p>
        </div>
        <div class="stat-block reveal reveal-delay-1">
          <span class="stat-num"><span data-count="<?= $classCount ?>" data-suffix="+">0</span></span>
          <p class="stat-label">Weekly Classes</p>
        </div>
        <div class="stat-block reveal reveal-delay-2">
          <span class="stat-num"><span data-count="<?= $coachCount ?>" data-suffix="+">0</span></span>
          <p class="stat-label">Certified Coaches</p>
        </div>
        <div class="stat-block reveal reveal-delay-3">
          <span class="stat-num"><span data-count="9" data-suffix=" yrs">0</span></span>
          <p class="stat-label">Serving The Community</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ FEATURES ============ -->
  <section aria-labelledby="features-heading">
    <div class="container">
      <div class="section-head center reveal">
        <p class="eyebrow">Why PulseFit</p>
        <h2 id="features-heading">Everything you need to reach your goals</h2>
        <p>From strength racks to recovery zones, every corner of our studio is designed around one goal — your progress.</p>
      </div>
      <div class="grid grid-4">
        <div class="card reveal">
          <div class="feature-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="9" width="4" height="6" rx="1"/><rect x="19" y="9" width="4" height="6" rx="1"/><rect x="6" y="7" width="2" height="10" rx="0.5"/><rect x="16" y="7" width="2" height="10" rx="0.5"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
          </div>
          <h3>Modern Equipment</h3>
          <p>Premium free weights, plate-loaded machines and functional turf, regularly serviced and upgraded.</p>
        </div>
        <div class="card reveal reveal-delay-1">
          <div class="feature-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 19c0-3 2.5-5.2 5.5-5.2s5.5 2.2 5.5 5.2"/><circle cx="17" cy="8.5" r="2.6"/><path d="M15.5 13.6c2.4.3 4 2.2 4 5.4"/></svg>
          </div>
          <h3>Expert Coaches</h3>
          <p>Certified trainers who build programs around your goals, not the other way around.</p>
        </div>
        <div class="card reveal reveal-delay-2">
          <div class="feature-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg>
          </div>
          <h3>Flexible Hours</h3>
          <p>Open early, open late — with 24/7 keycard access for our Elite members.</p>
        </div>
        <div class="card reveal reveal-delay-3">
          <div class="feature-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 4C10 4 4 10 4 18c0 .6 0 1 .1 1.5C13 19 20 12 20 4z"/><path d="M5 19 14 10"/></svg>
          </div>
          <h3>Nutrition Guidance</h3>
          <p>Personalised meal frameworks that pair with your training block, not generic diet sheets.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ PROGRAMS PREVIEW ============ -->
  <section class="section-alt" id="programs" aria-labelledby="programs-heading">
    <div class="container">
      <div class="section-head center reveal">
        <p class="eyebrow">Our Programs</p>
        <h2 id="programs-heading">Find the training style that fits you</h2>
        <p>Every plan includes access to open-gym hours, group classes and our member app.</p>
      </div>
      <div class="grid grid-3">
        <article class="card reveal">
          <div class="feature-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="9" width="4" height="6" rx="1"/><rect x="19" y="9" width="4" height="6" rx="1"/><rect x="6" y="7" width="2" height="10" rx="0.5"/><rect x="16" y="7" width="2" height="10" rx="0.5"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
          </div>
          <h3>Strength Training</h3>
          <p>Barbell-focused programming to build raw power, structured around progressive overload.</p>
        </article>
        <article class="card reveal reveal-delay-1">
          <div class="feature-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.4" fill="currentColor" stroke="none"/></svg>
          </div>
          <h3>HIIT &amp; Cardio</h3>
          <p>High-energy interval classes that torch calories and build conditioning fast.</p>
        </article>
        <article class="card reveal reveal-delay-2">
          <div class="feature-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V6z"/></svg>
          </div>
          <h3>1-to-1 Coaching</h3>
          <p>Private sessions with a dedicated coach for form correction and accountability.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- ============ ABOUT TEASER ============ -->
  <section aria-labelledby="about-teaser-heading">
    <div class="container two-col">
      <div class="about-image-wrap reveal">
        <img src="<?= e(base_url('images/about-story.jpg')) ?>" alt="Coach spotting a member during a bench press at PulseFit Gym" loading="lazy" width="900" height="1350">
        <div class="about-image-badge">
          <span class="stat-num">9+</span>
          <span class="stat-label">Years shaping stronger stories</span>
        </div>
      </div>
      <div class="reveal reveal-delay-1">
        <p class="eyebrow">About PulseFit</p>
        <h2 id="about-teaser-heading">More than a gym — it's a community</h2>
        <p>What started as a single studio has grown into a home for anyone chasing a stronger, healthier version of themselves. Our coaches combine sports-science programming with genuine encouragement, so every session moves you forward.</p>
        <div class="badge-row">
          <span class="badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Certified trainers</span>
          <span class="badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Clean, modern facility</span>
          <span class="badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Judgement-free zone</span>
        </div>
        <div class="hero-actions" style="margin-top:34px;margin-bottom:0;">
          <a href="<?= e(base_url('about.php')) ?>" class="btn btn-primary">Learn Our Story</a>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ MEMBERSHIP PREVIEW (data-driven) ============ -->
  <section class="section-alt" aria-labelledby="plans-heading">
    <div class="container">
      <div class="section-head center reveal">
        <p class="eyebrow">Membership</p>
        <h2 id="plans-heading">Simple plans, real value</h2>
        <p>Pick a plan and change it anytime — no lock-in contracts.</p>
      </div>
      <div class="grid grid-3">
        <?php foreach ($plans as $i => $plan): ?>
          <?php
          $planFeatureStmt->execute([$plan['id']]);
          $topFeatures = $planFeatureStmt->fetchAll(PDO::FETCH_COLUMN);
          ?>
          <div class="plan-card <?= $plan['is_featured'] ? 'featured' : '' ?> reveal <?= $i > 0 ? 'reveal-delay-' . $i : '' ?>">
            <?php if ($plan['is_featured']): ?><span class="plan-badge">Most Popular</span><?php endif; ?>
            <p class="plan-name"><?= e($plan['name']) ?></p>
            <div class="plan-price"><span class="amount"><?= e(format_price((int) $plan['price_cents'])) ?></span><span class="cycle">/ <?= e($plan['billing_cycle']) ?></span></div>
            <p class="plan-desc"><?= e($plan['description']) ?></p>
            <ul class="plan-features">
              <?php foreach ($topFeatures as $feature): ?>
                <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg><?= e($feature) ?></li>
              <?php endforeach; ?>
            </ul>
            <a href="<?= e(base_url('membership.php')) ?>" class="btn <?= $plan['is_featured'] ? 'btn-primary' : 'btn-ghost' ?> btn-block">View Details</a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============ CTA BAND ============ -->
  <section>
    <div class="container">
      <div class="cta-band reveal">
        <div class="hero-bg" style="background-image:url('<?= e(base_url('images/banner-gallery.jpg')) ?>');" role="img" aria-label="Rows of dumbbells in the PulseFit weights area"></div>
        <div class="hero-overlay"></div>
        <h2>Your first class is on us</h2>
        <p>Book a free trial session and see why members stick around for years, not weeks.</p>
        <div class="hero-actions">
          <a href="<?= e(base_url('contact.php')) ?>" class="btn btn-primary">Book Free Trial</a>
          <a href="<?= e(base_url('membership.php')) ?>" class="btn btn-ghost">See Membership Plans</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
