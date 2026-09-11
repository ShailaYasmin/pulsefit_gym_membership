<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

$pageTitle       = 'Privacy Notice | PulseFit Gym';
$pageDescription = 'How PulseFit Gym collects, uses, stores and protects the personal information you share with us.';
$activePage      = '';
require __DIR__ . '/includes/header.php';
?>

  <section class="page-head-plain">
    <div class="container">
      <p class="eyebrow">Privacy</p>
      <h1>Privacy Notice</h1>
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= e(base_url('index.php')) ?>">Home</a>
        <span aria-hidden="true">/</span>
        <span>Privacy Notice</span>
      </nav>
    </div>
  </section>

  <section>
    <div class="container" style="max-width:760px;">
      <div class="card reveal in-view" style="padding:40px;">
        <p style="margin-bottom:18px;">This notice explains, in plain language, what personal information PulseFit Gym's website collects, why, how it's stored, and what control you have over it. This site is a student coursework project (ICT726, King's Own Institute) — no real members' data is processed, but it is built and documented as if it were a genuine studio, in line with the assignment's privacy and ethics requirements.</p>

        <h2 style="font-size:1.2rem;margin:28px 0 10px;">What we collect</h2>
        <ul style="color:var(--text-muted);padding-left:20px;line-height:1.9;">
          <li><strong>Account details</strong> — full name, email address, optional phone number, and a securely hashed password when you register.</li>
          <li><strong>Membership &amp; booking activity</strong> — the plan you choose and the classes you book, so we can show them back to you on your dashboard.</li>
          <li><strong>Contact form submissions</strong> — name, email, optional phone, and your message, when you use the Contact page.</li>
          <li><strong>Testimonials</strong> — the review text and star rating you choose to submit, linked to your account so we can moderate it.</li>
        </ul>

        <h2 style="font-size:1.2rem;margin:28px 0 10px;">How we use it</h2>
        <p>Your information is used only to operate the studio's booking system: to identify you when you log in, to show you your own bookings and membership, to respond to enquiries, and to display testimonials you've chosen to publish. We do not sell, rent, or share your information with third parties for marketing.</p>

        <h2 style="font-size:1.2rem;margin:28px 0 10px;">How it's protected</h2>
        <ul style="color:var(--text-muted);padding-left:20px;line-height:1.9;">
          <li>Passwords are never stored in plain text — they're hashed with PHP's <code>password_hash()</code> (bcrypt) before being saved.</li>
          <li>All database queries use parameterised prepared statements, which prevents SQL injection.</li>
          <li>Every form on this site is protected against cross-site request forgery (CSRF) with a per-session token.</li>
          <li>Access to member and admin areas is controlled by role-based checks on every page — a member cannot view another member's private data, and only admin accounts can access the back-office tools.</li>
          <li>The database connection uses a dedicated, least-privilege application account rather than a root/superuser account.</li>
        </ul>

        <h2 style="font-size:1.2rem;margin:28px 0 10px;">Your choices</h2>
        <p>You can review and update your profile details from your dashboard at any time. You can request that your account and associated data be deleted by contacting us — as this is a coursework demo, deletion requests can simply be made via the Contact page.</p>

        <h2 style="font-size:1.2rem;margin:28px 0 10px;">Cookies &amp; sessions</h2>
        <p>We use a single session cookie to keep you logged in. It's marked <code>HttpOnly</code> so it can't be read by page scripts, and it's cleared when you log out. We do not use third-party tracking or advertising cookies.</p>

        <h2 style="font-size:1.2rem;margin:28px 0 10px;">Contact</h2>
        <p>Questions about this notice or your data can be sent via the <a href="<?= e(base_url('contact.php')) ?>" style="color:var(--accent);font-weight:700;">Contact page</a>.</p>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
