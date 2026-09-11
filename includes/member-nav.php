<?php
declare(strict_types=1);
// Expects $activeSub to be set by the including page: dashboard|bookings|profile
$activeSub = $activeSub ?? '';
?>
<nav class="dashboard-nav" aria-label="Account navigation">
  <a href="<?= e(base_url('member/dashboard.php')) ?>" class="<?= $activeSub === 'dashboard' ? 'active' : '' ?>">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    Dashboard
  </a>
  <a href="<?= e(base_url('member/my-bookings.php')) ?>" class="<?= $activeSub === 'bookings' ? 'active' : '' ?>">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="7" y1="2.5" x2="7" y2="6.5"/><line x1="17" y1="2.5" x2="17" y2="6.5"/></svg>
    My Bookings
  </a>
  <a href="<?= e(base_url('member/profile.php')) ?>" class="<?= $activeSub === 'profile' ? 'active' : '' ?>">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.5 3.5-7 8-7s8 2.5 8 7"/></svg>
    Edit Profile
  </a>
  <a href="<?= e(base_url('membership.php')) ?>">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l7 3v6c0 4.5-3 8-7 9-4-1-7-4.5-7-9V6z"/></svg>
    Membership Plans
  </a>
</nav>
