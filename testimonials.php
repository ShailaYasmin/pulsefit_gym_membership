<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

$errors = [];
$oldQuote = '';
$oldRating = 5;

// ---- Handle a new testimonial submission (member/admin only) --------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testimonial'])) {
    require_login();

    if (!csrf_verify()) {
        $errors['form'] = 'Your session expired. Please try again.';
    } else {
        $oldQuote  = trim((string) ($_POST['quote'] ?? ''));
        $oldRating = (int) ($_POST['rating'] ?? 5);
        $user      = current_user();

        if ($oldQuote === '' || mb_strlen($oldQuote) < 15) {
            $errors['quote'] = 'Please share at least a sentence or two about your experience.';
        }
        if ($oldRating < 1 || $oldRating > 5) {
            $errors['rating'] = 'Please choose a rating between 1 and 5.';
        }

        if (empty($errors)) {
            $label = has_role('admin') ? 'PulseFit Team' : 'Member';
            $stmt = $pdo->prepare(
                'INSERT INTO testimonials (user_id, display_name, member_label, rating, quote, status) VALUES (?, ?, ?, ?, ?, "pending")'
            );
            $stmt->execute([$user['id'], $user['full_name'], $label, $oldRating, $oldQuote]);
            flash_set('success', 'Thanks for sharing! Your review is awaiting a quick approval from our team.');
            redirect('testimonials.php');
        }
    }
}

$testimonials = $pdo->query(
    "SELECT * FROM testimonials WHERE status = 'approved' ORDER BY created_at DESC LIMIT 6"
)->fetchAll();

$pageTitle       = 'Testimonials | PulseFit Gym';
$pageDescription = 'Real reviews from PulseFit Gym members — read what our community says about training, coaching and results.';
$activePage      = 'testimonials';
require __DIR__ . '/includes/header.php';
?>

  <!-- ============ PAGE BANNER ============ -->
  <section class="page-banner">
    <div class="hero-bg" style="background-image:url('<?= e(base_url('images/banner-testimonials.jpg')) ?>');" role="img" aria-label="Athlete performing an overhead barbell press, viewed from behind"></div>
    <div class="hero-overlay"></div>
    <div class="container">
      <p class="eyebrow">Testimonials</p>
      <h1>Stories from the PulseFit community</h1>
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= e(base_url('index.php')) ?>">Home</a>
        <span aria-hidden="true">/</span>
        <span>Testimonials</span>
      </nav>
    </div>
  </section>

  <!-- ============ TESTIMONIAL GRID (data-driven) ============ -->
  <section aria-labelledby="testimonials-heading">
    <div class="container">
      <div class="section-head center reveal">
        <p class="eyebrow">Member Reviews</p>
        <h2 id="testimonials-heading">Don't take our word for it</h2>
        <p>Every review below was submitted by a real logged-in account and approved by our team.</p>
      </div>

      <?php if (empty($testimonials)): ?>
        <p class="empty-state">No reviews yet — be the first to share your experience below.</p>
      <?php else: ?>
        <div class="grid grid-3">
          <?php foreach ($testimonials as $i => $t): ?>
            <div class="card testimonial-card reveal <?= $i > 0 ? 'reveal-delay-' . ($i % 3) : '' ?>">
              <div class="stars" aria-label="Rated <?= (int) $t['rating'] ?> out of 5 stars"><?= str_repeat('★', (int) $t['rating']) . str_repeat('☆', 5 - (int) $t['rating']) ?></div>
              <p class="testimonial-quote">"<?= e($t['quote']) ?>"</p>
              <div class="testimonial-person">
                <div style="width:52px;height:52px;border-radius:50%;background:var(--surface-2);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--accent);flex-shrink:0;"><?= e(mb_substr($t['display_name'], 0, 1)) ?></div>
                <div>
                  <strong><?= e($t['display_name']) ?></strong>
                  <span><?= e($t['member_label'] ?? 'Member') ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ============ SHARE YOUR EXPERIENCE ============ -->
  <section class="section-alt" id="share" aria-labelledby="share-heading">
    <div class="container">
      <div class="form-card reveal" style="max-width:640px;margin-inline:auto;">
        <p class="eyebrow">Share Your Experience</p>
        <h2 id="share-heading" style="font-size:1.6rem;margin-bottom:6px;">Leave a review</h2>

        <?php if (!is_logged_in()): ?>
          <p style="font-size:0.92rem;">
            <a href="<?= e(base_url('login.php')) ?>" style="color:var(--accent);font-weight:700;">Log in</a>
            or <a href="<?= e(base_url('register.php')) ?>" style="color:var(--accent);font-weight:700;">create a free account</a>
            to leave a review — this keeps testimonials genuine.
          </p>
        <?php else: ?>
          <p style="font-size:0.9rem;margin-bottom:22px;">Reviews are checked by our team before they go live.</p>

          <?php if (!empty($errors['form'])): ?>
            <div class="form-status show error"><span><?= e($errors['form']) ?></span></div>
          <?php endif; ?>

          <form method="post" novalidate action="<?= e(base_url('testimonials.php')) ?>#share">
            <?= csrf_field() ?>
            <input type="hidden" name="submit_testimonial" value="1">

            <div class="field">
              <label>Your Rating <span class="required">*</span></label>
              <div class="rating-input" role="radiogroup" aria-label="Star rating">
                <?php for ($r = 5; $r >= 1; $r--): ?>
                  <input type="radio" name="rating" id="star<?= $r ?>" value="<?= $r ?>" <?= $oldRating === $r ? 'checked' : '' ?>>
                  <label for="star<?= $r ?>" aria-label="<?= $r ?> star<?= $r > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
              </div>
              <?php if (isset($errors['rating'])): ?><p class="field-error" style="display:flex;"><?= e($errors['rating']) ?></p><?php endif; ?>
            </div>

            <div class="field <?= isset($errors['quote']) ? 'has-error' : '' ?>">
              <label for="quote">Your Review <span class="required">*</span></label>
              <textarea id="quote" name="quote" placeholder="Tell other members what training here has been like..." required minlength="15"><?= e($oldQuote) ?></textarea>
              <?php if (isset($errors['quote'])): ?><p class="field-error" style="display:flex;"><?= e($errors['quote']) ?></p><?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Submit Review</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- ============ FEATURED SUCCESS STORY ============ -->
  <section aria-labelledby="success-heading">
    <div class="container two-col">
      <div class="about-image-wrap reveal">
        <img src="<?= e(base_url('images/gallery-4.jpg')) ?>" alt="Black and white close-up portrait of a member focused during a heavy lift" loading="lazy" width="800" height="600">
        <div class="about-image-badge">
          <span class="stat-num">4mo</span>
          <span class="stat-label">Injury to full training</span>
        </div>
      </div>
      <div class="reveal reveal-delay-1">
        <p class="eyebrow">Success Story</p>
        <h2 id="success-heading">"They didn't just help me train around an injury — they helped me get stronger than before it."</h2>
        <p>When Daniel first came to PulseFit he'd been sidelined by a shoulder injury for six months and had lost most of his confidence under the bar. His coach rebuilt his entire program from the ground up, starting with mobility work and rebuilding load gradually over four months.</p>
        <p style="margin-top:14px;">Today Daniel trains five days a week and recently hit a new personal best on his overhead press — pain-free.</p>
        <div class="hero-actions" style="margin-top:30px;margin-bottom:0;">
          <a href="<?= e(base_url('contact.php')) ?>" class="btn btn-primary">Start Your Story</a>
        </div>
      </div>
    </div>
  </section>

  <!-- ============ CTA BAND ============ -->
  <section class="section-alt">
    <div class="container">
      <div class="cta-band reveal">
        <div class="hero-bg" style="background-image:url('<?= e(base_url('images/banner-about.jpg')) ?>');" role="img" aria-label="Close-up of an athlete gripping a barbell mid deadlift"></div>
        <div class="hero-overlay"></div>
        <h2>Ready to write your own review?</h2>
        <p>Join hundreds of members already training smarter at PulseFit.</p>
        <div class="hero-actions">
          <a href="<?= e(base_url('membership.php')) ?>" class="btn btn-primary">See Membership Plans</a>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
