<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

$errors = [];
$old = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];
$submitted = false;

$user = current_user();
if ($user && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $old['name']  = $user['full_name'];
    $old['email'] = $user['email'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $errors['form'] = 'Your session expired. Please refresh and try again.';
    } else {
        $old['name']    = trim((string) ($_POST['name'] ?? ''));
        $old['email']   = trim((string) ($_POST['email'] ?? ''));
        $old['phone']   = trim((string) ($_POST['phone'] ?? ''));
        $old['subject'] = trim((string) ($_POST['subject'] ?? ''));
        $old['message'] = trim((string) ($_POST['message'] ?? ''));

        $validSubjects = ['membership', 'personal-training', 'tour', 'feedback'];

        if ($old['name'] === '' || mb_strlen($old['name']) < 2) {
            $errors['name'] = 'Please enter your full name.';
        }
        if ($old['email'] === '' || !filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }
        if ($old['phone'] !== '' && !preg_match('/^[0-9+()\-\s]{7,20}$/', $old['phone'])) {
            $errors['phone'] = 'Please enter a valid phone number.';
        }
        if (!in_array($old['subject'], $validSubjects, true)) {
            $errors['subject'] = 'Please select an enquiry type.';
        }
        if ($old['message'] === '' || mb_strlen($old['message']) < 10) {
            $errors['message'] = 'Message should be at least 10 characters.';
        }
        if (empty($_POST['consent'])) {
            $errors['consent'] = 'Please agree before sending your message.';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare(
                'INSERT INTO enquiries (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $old['name'],
                $old['email'],
                $old['phone'] !== '' ? $old['phone'] : null,
                $old['subject'],
                $old['message'],
            ]);
            $submitted = true;
            $old = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];
        }
    }
}

$pageTitle       = 'Contact Us | PulseFit Gym';
$pageDescription = 'Get in touch with PulseFit Gym — visit the studio, call, email or send us a message using the contact form below.';
$activePage      = 'contact';
require __DIR__ . '/includes/header.php';
?>

  <!-- ============ PAGE BANNER ============ -->
  <section class="page-banner">
    <div class="hero-bg" style="background-image:url('<?= e(base_url('images/banner-contact.jpg')) ?>');" role="img" aria-label="Woman performing core exercises on a mat in a bright, modern gym"></div>
    <div class="hero-overlay"></div>
    <div class="container">
      <p class="eyebrow">Contact</p>
      <h1>Let's talk about your goals</h1>
      <p style="max-width:480px;margin-top:14px;">Questions about membership, classes or a facility tour? Reach out below.</p>
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= e(base_url('index.php')) ?>">Home</a>
        <span aria-hidden="true">/</span>
        <span>Contact</span>
      </nav>
    </div>
  </section>

  <!-- ============ CONTACT ============ -->
  <section aria-labelledby="contact-heading">
    <div class="container two-col">

      <!-- Contact info -->
      <div class="reveal">
        <p class="eyebrow">Get In Touch</p>
        <h2 id="contact-heading">Visit, call or send a message</h2>
        <p>Our front desk team can answer questions about membership, classes and facility tours.</p>

        <ul class="contact-info-list" style="margin-top:32px;">
          <li class="contact-info-item">
            <div class="feature-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-7.2 7-12a7 7 0 0 0-14 0c0 4.8 7 12 7 12z"/><circle cx="12" cy="9" r="2.4"/></svg>
            </div>
            <div>
              <h3>Studio Address</h3>
              <p>88 Ironclad Lane, Sydney NSW 2000, Australia</p>
            </div>
          </li>
          <li class="contact-info-item">
            <div class="feature-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h4l2 5-2.5 1.5a12 12 0 0 0 6 6L15 14l5 2v4c0 1-1 2-2 2C10.5 22 2 13.5 2 6c0-1 1-2 2-2z"/></svg>
            </div>
            <div>
              <h3>Phone</h3>
              <p><a href="tel:+61212345678">(02) 1234 5678</a></p>
            </div>
          </li>
          <li class="contact-info-item">
            <div class="feature-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3 6 12 13 21 6"/></svg>
            </div>
            <div>
              <h3>Email</h3>
              <p><a href="mailto:hello@pulsefitgym.example">hello@pulsefitgym.example</a></p>
            </div>
          </li>
          <li class="contact-info-item">
            <div class="feature-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg>
            </div>
            <div>
              <h3>Studio Hours</h3>
              <p>Mon–Fri 5am–11pm · Sat 7am–9pm · Sun 8am–6pm</p>
            </div>
          </li>
        </ul>

        <h3 style="font-size:0.95rem;text-transform:uppercase;letter-spacing:.08em;color:var(--text-dim);margin-bottom:14px;">Follow Us</h3>
        <div class="social-row">
          <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="PulseFit Gym on Facebook">FB</a>
          <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="PulseFit Gym on Instagram">IG</a>
          <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" aria-label="PulseFit Gym on X">X</a>
          <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="PulseFit Gym on YouTube">YT</a>
        </div>

        <div class="map-embed reveal">
          <iframe
            src="https://www.google.com/maps?q=Sydney%20CBD%2C%20NSW%2C%20Australia&output=embed"
            title="Map showing the approximate location of PulseFit Gym in Sydney CBD"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>

      <!-- Contact form (server-side validated + stored in the enquiries table) -->
      <div class="reveal reveal-delay-1">
        <div class="form-card">
          <h3 style="margin-bottom:6px;">Send us a message</h3>
          <p style="font-size:0.9rem;margin-bottom:26px;">Fields marked <span class="required">*</span> are required.</p>

          <?php if ($submitted): ?>
            <div class="form-status show success" role="alert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg><span>Thanks! Your message has been sent — our team will reply within 1 business day.</span></div>
          <?php elseif (!empty($errors['form'])): ?>
            <div class="form-status show error" role="alert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span><?= e($errors['form']) ?></span></div>
          <?php elseif (!empty($errors)): ?>
            <div class="form-status show error" role="alert"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span>Please fix the highlighted fields and try again.</span></div>
          <?php endif; ?>

          <form method="post" novalidate action="<?= e(base_url('contact.php')) ?>">
            <?= csrf_field() ?>
            <div class="form-row">
              <div class="field <?= isset($errors['name']) ? 'has-error' : '' ?>">
                <label for="name">Full Name <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?= e($old['name']) ?>" placeholder="Jordan Smith" required minlength="2" autocomplete="name">
                <?php if (isset($errors['name'])): ?><p class="field-error" style="display:flex;"><?= e($errors['name']) ?></p><?php endif; ?>
              </div>
              <div class="field <?= isset($errors['email']) ? 'has-error' : '' ?>">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" placeholder="jordan@email.com" required autocomplete="email">
                <?php if (isset($errors['email'])): ?><p class="field-error" style="display:flex;"><?= e($errors['email']) ?></p><?php endif; ?>
              </div>
            </div>

            <div class="form-row">
              <div class="field <?= isset($errors['phone']) ? 'has-error' : '' ?>">
                <label for="phone">Phone Number <span class="field-hint" style="display:inline;">(optional)</span></label>
                <input type="tel" id="phone" name="phone" value="<?= e($old['phone']) ?>" placeholder="04XX XXX XXX" autocomplete="tel">
                <?php if (isset($errors['phone'])): ?><p class="field-error" style="display:flex;"><?= e($errors['phone']) ?></p><?php endif; ?>
              </div>
              <div class="field <?= isset($errors['subject']) ? 'has-error' : '' ?>">
                <label for="subject">Enquiry Type <span class="required">*</span></label>
                <select id="subject" name="subject" required>
                  <option value="" <?= $old['subject'] === '' ? 'selected' : '' ?> disabled>Select an option</option>
                  <option value="membership" <?= $old['subject'] === 'membership' ? 'selected' : '' ?>>General Membership</option>
                  <option value="personal-training" <?= $old['subject'] === 'personal-training' ? 'selected' : '' ?>>Personal Training</option>
                  <option value="tour" <?= $old['subject'] === 'tour' ? 'selected' : '' ?>>Book a Facility Tour</option>
                  <option value="feedback" <?= $old['subject'] === 'feedback' ? 'selected' : '' ?>>Feedback / Other</option>
                </select>
                <?php if (isset($errors['subject'])): ?><p class="field-error" style="display:flex;"><?= e($errors['subject']) ?></p><?php endif; ?>
              </div>
            </div>

            <div class="field <?= isset($errors['message']) ? 'has-error' : '' ?>">
              <label for="message">Message <span class="required">*</span></label>
              <textarea id="message" name="message" placeholder="Tell us a little about your goals..." required minlength="10"><?= e($old['message']) ?></textarea>
              <?php if (isset($errors['message'])): ?><p class="field-error" style="display:flex;"><?= e($errors['message']) ?></p><?php endif; ?>
            </div>

            <div class="field <?= isset($errors['consent']) ? 'has-error' : '' ?>">
              <label class="checkbox-field" for="consent">
                <input type="checkbox" id="consent" name="consent">
                <span>I agree to be contacted by PulseFit Gym regarding my enquiry. <span class="required">*</span></span>
              </label>
              <?php if (isset($errors['consent'])): ?><p class="field-error" style="display:flex;"><?= e($errors['consent']) ?></p><?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
