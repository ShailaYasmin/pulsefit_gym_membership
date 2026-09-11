<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

require_role('member', 'normal', 'admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    flash_set('error', 'Something went wrong. Please try again.');
    redirect('membership.php');
}

$planId = (int) ($_POST['plan_id'] ?? 0);
$stmt = $pdo->prepare('SELECT id, name FROM membership_plans WHERE id = ?');
$stmt->execute([$planId]);
$plan = $stmt->fetch();

if (!$plan) {
    flash_set('error', 'That plan could not be found.');
    redirect('membership.php');
}

$user = current_user();

// Close out any existing active membership, then start the new one.
$pdo->prepare('UPDATE user_memberships SET status = "cancelled", end_date = CURDATE() WHERE user_id = ? AND status = "active"')
    ->execute([$user['id']]);

$pdo->prepare('INSERT INTO user_memberships (user_id, plan_id, start_date, status) VALUES (?, ?, CURDATE(), "active")')
    ->execute([$user['id'], $planId]);

// A "normal" account becomes a full "member" once they hold an active plan.
if ($user['role'] === 'normal') {
    $pdo->prepare('UPDATE users SET role = "member" WHERE id = ?')->execute([$user['id']]);
    $_SESSION['user']['role'] = 'member';
}

flash_set('success', 'You are now on the ' . $plan['name'] . ' plan. Head to your dashboard to book a class.');
redirect('member/dashboard.php');
