<footer class="site-footer">
  <div class="container">
    <div class="footer-top">
      <div class="footer-col footer-about">
        <a href="<?= e(base_url('index.php')) ?>" class="brand" aria-label="PulseFit Gym home">
          <span class="brand-mark" aria-hidden="true">P</span>Pulse<span>Fit</span>
        </a>
        <p>A boutique strength &amp; conditioning studio built for every fitness level — real equipment, real coaching, real results.</p>
        <div class="social-row">
          <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="PulseFit Gym on Facebook">FB</a>
          <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" aria-label="PulseFit Gym on Instagram">IG</a>
          <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" aria-label="PulseFit Gym on X">X</a>
          <a href="https://youtube.com" target="_blank" rel="noopener noreferrer" aria-label="PulseFit Gym on YouTube">YT</a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Quick Links</h4>
        <ul class="footer-links">
          <li><a href="<?= e(base_url('about.php')) ?>">About Us</a></li>
          <li><a href="<?= e(base_url('membership.php')) ?>">Membership Plans</a></li>
          <li><a href="<?= e(base_url('gallery.php')) ?>">Gallery</a></li>
          <li><a href="<?= e(base_url('testimonials.php')) ?>">Testimonials</a></li>
          <li><a href="<?= e(base_url('contact.php')) ?>">Contact</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Account</h4>
        <ul class="footer-links">
          <?php if (current_user()): ?>
            <li><a href="<?= e(base_url(has_role('admin') ? 'admin/dashboard.php' : 'member/dashboard.php')) ?>">My Account</a></li>
            <li><a href="<?= e(base_url('logout.php')) ?>">Log Out</a></li>
          <?php else: ?>
            <li><a href="<?= e(base_url('login.php')) ?>">Log In</a></li>
            <li><a href="<?= e(base_url('register.php')) ?>">Join Now</a></li>
          <?php endif; ?>
          <li><a href="<?= e(base_url('privacy.php')) ?>">Privacy Notice</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Opening Hours</h4>
        <ul class="footer-hours">
          <li><span>Mon – Fri</span><span>05:00–23:00</span></li>
          <li><span>Saturday</span><span>07:00–21:00</span></li>
          <li><span>Sunday</span><span>08:00–18:00</span></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> PulseFit Gym. All rights reserved. Designed &amp; developed by Shaila Yasmin Erin.</p>
      <div class="footer-bottom-links">
        <a href="<?= e(base_url('privacy.php')) ?>">Privacy Policy</a>
        <a href="<?= e(base_url('contact.php')) ?>">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>

<button class="back-to-top" aria-label="Back to top">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
</button>

<script src="<?= e(base_url('js/script.js')) ?>"></script>
</body>
</html>
