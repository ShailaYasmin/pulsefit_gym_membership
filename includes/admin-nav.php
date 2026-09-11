<?php
declare(strict_types=1);
// Expects $activeSub to be set by the including page.
$activeSub = $activeSub ?? '';
?>
<nav class="dashboard-nav" aria-label="Admin navigation">
  <a href="<?= e(base_url('admin/dashboard.php')) ?>" class="<?= $activeSub === 'dashboard' ? 'active' : '' ?>">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    Overview
  </a>
  <a href="<?= e(base_url('admin/plans.php')) ?>" class="<?= $activeSub === 'plans' ? 'active' : '' ?>">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V6z"/></svg>
    Membership Plans
  </a>
  <a href="<?= e(base_url('admin/classes.php')) ?>" class="<?= $activeSub === 'classes' ? 'active' : '' ?>">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/></svg>
    Class Schedule
  </a>
  <a href="<?= e(base_url('admin/members.php')) ?>" class="<?= $activeSub === 'members' ? 'active' : '' ?>">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 19c0-3 2.5-5.2 5.5-5.2s5.5 2.2 5.5 5.2"/><circle cx="17" cy="8.5" r="2.6"/><path d="M15.5 13.6c2.4.3 4 2.2 4 5.4"/></svg>
    Members
  </a>
  <a href="<?= e(base_url('admin/enquiries.php')) ?>" class="<?= $activeSub === 'enquiries' ? 'active' : '' ?>">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3 6 12 13 21 6"/></svg>
    Enquiries
  </a>
  <a href="<?= e(base_url('admin/testimonials.php')) ?>" class="<?= $activeSub === 'testimonials' ? 'active' : '' ?>">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    Testimonials
  </a>
</nav>
