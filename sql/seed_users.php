<?php

declare(strict_types=1);

// One-off CLI script: creates demo accounts with properly hashed passwords,
// plus a sample active membership and a couple of bookings for the demo
// member so the dashboards have real data on first run.
// Usage: php sql/seed_users.php

require_once __DIR__ . '/../includes/db.php';

function upsertUser(PDO $pdo, string $name, string $email, string $password, string $role): int
{
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $existing = $stmt->fetch();
    if ($existing) {
        echo "  - $email already exists, skipping.\n";
        return (int) $existing['id'];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([$name, $email, $hash, $role]);
    $id = (int) $pdo->lastInsertId();
    echo "  - created $email (role: $role)\n";
    return $id;
}

echo "Seeding demo accounts...\n";

$adminId  = upsertUser($pdo, 'Shaila Erin', 'admin@pulsefitgym.example', 'Admin123!', 'admin');
$memberId = upsertUser($pdo, 'Jamie Lee', 'member@pulsefitgym.example', 'Member123!', 'member');
$normalId = upsertUser($pdo, 'Alex Chen', 'alex@pulsefitgym.example', 'Normal123!', 'normal');

// Give the demo member an active Standard membership, if they don't have one yet.
$stmt = $pdo->prepare('SELECT id FROM user_memberships WHERE user_id = ? AND status = "active"');
$stmt->execute([$memberId]);
if (!$stmt->fetch()) {
    $planStmt = $pdo->prepare('SELECT id FROM membership_plans WHERE slug = "standard"');
    $planStmt->execute();
    $planId = $planStmt->fetchColumn();

    $pdo->prepare('INSERT INTO user_memberships (user_id, plan_id, start_date, status) VALUES (?, ?, CURDATE(), "active")')
        ->execute([$memberId, $planId]);
    echo "  - gave member@pulsefitgym.example an active Standard membership\n";
}

// Book the demo member into a couple of upcoming classes.
$classStmt = $pdo->query('SELECT id, day_of_week FROM classes LIMIT 2');
$classes = $classStmt->fetchAll();
foreach ($classes as $i => $class) {
    $date = date('Y-m-d', strtotime('+' . ($i + 1) . ' week'));
    $exists = $pdo->prepare('SELECT id FROM bookings WHERE user_id = ? AND class_id = ? AND booking_date = ?');
    $exists->execute([$memberId, $class['id'], $date]);
    if (!$exists->fetch()) {
        $pdo->prepare('INSERT INTO bookings (user_id, class_id, booking_date) VALUES (?, ?, ?)')
            ->execute([$memberId, $class['id'], $date]);
        echo "  - booked demo member into class #{$class['id']} on $date\n";
    }
}

echo "Done.\n";
