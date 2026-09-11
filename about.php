<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

$trainers = $pdo->query('SELECT * FROM trainers ORDER BY display_order ASC')->fetchAll();

$trainerSocialIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none"/></svg>';
$mailIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3 6 12 13 21 6"/></svg>';

$pageTitle       = 'About Us | PulseFit Gym';
$pageDescription = 'Learn the story behind PulseFit Gym, our mission and values, and meet the certified coaches behind every session.';
$activePage      = 'about';
require __DIR__ . '/includes/header.php';
?>

  <!-- ============ PAGE BANNER ============ -->
  <section class="page-banner">
    <div class="hero-bg" style="background-image:url('<?= e(base_url('images/banner-about.jpg')) ?>');" role="img" aria-label="Close-up of an athlete gripping a barbell mid deadlift"></div>
    <div class="hero-overlay"></div>
    <div class="container">
      <p class="eyebrow">About PulseFit</p>
      <h1>Built by coaches, not marketers</h1>
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= e(base_url('index.php')) ?>">Home</a>
        <span aria-hidden="true">/</span>
        <span>About</span>
      </nav>
    </div>
  </section>

  <!-- ============ OUR STORY ============ -->
  <section aria-labelledby="story-heading">
    <div class="container two-col">
      <div class="about-image-wrap reveal">
        <img src="<?= e(base_url('images/facility.jpg')) ?>" alt="Bright, modern PulseFit Gym training floor with dumbbell racks and cardio machines" loading="lazy" width="1000" height="700">
        <div class="about-image-badge">
          <span class="stat-num">2016</span>
          <span class="stat-label">Founded by Shaila Yasmin Erin</span>
        </div>
      </div>
      <div class="reveal reveal-delay-1">
        <p class="eyebrow">Our Story</p>
        <h2 id="story-heading">From one small studio to a full training community</h2>
        <p>PulseFit Gym was founded in 2016 by Shaila Yasmin Erin, who started with a single 200m² studio, six squat racks and a big idea: fitness should feel personal, not intimidating. Nine years later, under her direction, we've grown into a full strength-and-conditioning facility — but that founding idea — coaching people, not just counting reps — still drives everything we do.</p>
        <p style="margin-top:16px;">Today our floor blends free weights, functional turf and a dedicated studio for group classes, all designed around one principle: every rep should have a purpose.</p>
      </div>
    </div>
  </section>

  <!-- ============ MISSION / VISION ============ -->
  <section class="section-alt" aria-labelledby="mission-heading">
    <div class="container two-col">
      <div class="reveal">
        <p class="eyebrow">What Drives Us</p>
        <h2 id="mission-heading">Our mission, vision &amp; values</h2>
        <p>Three ideas guide every class we run and every program we write.</p>
        <ul class="mission-list">
          <li>
            <div class="feature-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.4" fill="currentColor" stroke="none"/></svg>
            </div>
            <div>
              <strong>Mission</strong>
              <p>Make expert-level coaching and a genuinely supportive training environment accessible to everyone, regardless of experience or background.</p>
            </div>
          </li>
          <li>
            <div class="feature-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V6z"/></svg>
            </div>
            <div>
              <strong>Vision</strong>
              <p>Be the strength studio our members can't imagine their week without — where they invest in themselves, not just work out.</p>
            </div>
          </li>
          <li>
            <div class="feature-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 19c0-3 2.5-5.2 5.5-5.2s5.5 2.2 5.5 5.2"/><circle cx="17" cy="8.5" r="2.6"/><path d="M15.5 13.6c2.4.3 4 2.2 4 5.4"/></svg>
            </div>
            <div>
              <strong>Values</strong>
              <p>Consistency over intensity, real coaching over guesswork, and a community that celebrates every personal best, big or small.</p>
            </div>
          </li>
        </ul>
      </div>
      <div class="about-image-wrap reveal reveal-delay-1">
        <img src="<?= e(base_url('images/about-story.jpg')) ?>" alt="Coach spotting a member during a heavy bench press set" loading="lazy" width="900" height="1350">
      </div>
    </div>
  </section>

  <!-- ============ TEAM (data-driven from `trainers` table) ============ -->
  <section aria-labelledby="team-heading">
    <div class="container">
      <div class="section-head center reveal">
        <p class="eyebrow">Meet The Team</p>
        <h2 id="team-heading">Coaches who know your name, not just your membership number</h2>
        <p>Every trainer at PulseFit is certified, insured and genuinely invested in your progress.</p>
      </div>
      <div class="grid grid-3">
        <?php foreach ($trainers as $i => $trainer): ?>
          <div class="card trainer-card reveal <?= $i > 0 ? 'reveal-delay-' . $i : '' ?>">
            <div class="trainer-avatar">
              <img src="<?= e(base_url($trainer['photo_path'])) ?>" alt="Portrait of <?= e($trainer['full_name']) ?>, <?= e($trainer['role_title']) ?> at PulseFit Gym" loading="lazy" width="128" height="128">
            </div>
            <h3><?= e($trainer['full_name']) ?></h3>
            <p class="trainer-role"><?= e($trainer['role_title']) ?></p>
            <p><?= e($trainer['bio']) ?></p>
            <div class="trainer-socials">
              <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="<?= e($trainer['full_name']) ?> on Instagram"><?= $trainerSocialIcon ?></a>
              <a href="mailto:<?= e(strtolower(explode(' ', $trainer['full_name'])[0])) ?>@pulsefitgym.example" aria-label="Email <?= e($trainer['full_name']) ?>"><?= $mailIcon ?></a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============ CTA BAND ============ -->
  <section class="section-alt">
    <div class="container">
      <div class="cta-band reveal">
        <div class="hero-bg" style="background-image:url('<?= e(base_url('images/banner-testimonials.jpg')) ?>');" role="img" aria-label="Athlete performing an overhead barbell press"></div>
        <div class="hero-overlay"></div>
        <h2>Come train with a team that's invested in you</h2>
        <p>Book a free tour of the facility and meet the coaches before you commit to anything.</p>
        <div class="hero-actions">
          <a href="<?= e(base_url('contact.php')) ?>" class="btn btn-primary">Book A Free Tour</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
