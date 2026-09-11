<?php

declare(strict_types=1);

// One-off fix: the original `mysql -u root < seed.sql` import used the CLI's
// default latin1 connection charset, which double-encoded the handful of
// rows containing an em/en dash. PDO's connection is correctly utf8mb4 (see
// includes/db.php), so writing the correct text back through it fixes the
// stored bytes. Usage: php sql/fix_encoding.php

require_once __DIR__ . '/../includes/db.php';

$fixes = [
    ['table' => 'plan_features', 'match' => ['plan_id' => 1, 'display_order' => 1], 'column' => 'feature_text', 'value' => 'Open-gym access (6am–11pm)'],
    ['table' => 'testimonials', 'match' => ['display_name' => 'Aisha K.'], 'column' => 'quote', 'value' => 'The 6am HIIT class is the only reason I\'m consistent anymore. Small group, loud music, zero ego — exactly what I needed.'],
    ['table' => 'testimonials', 'match' => ['display_name' => 'Tom H.'], 'column' => 'quote', 'value' => '24/7 access means I can train at 5am before work or 10pm after a late shift. Never used to prioritise fitness — now it\'s non-negotiable.'],
];

foreach ($fixes as $fix) {
    $whereParts = [];
    $params = [':new' => $fix['value']];
    foreach ($fix['match'] as $col => $val) {
        $whereParts[] = "$col = :$col";
        $params[":$col"] = $val;
    }
    $sql = "UPDATE {$fix['table']} SET {$fix['column']} = :new WHERE " . implode(' AND ', $whereParts);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo "  - fixed {$fix['table']}.{$fix['column']} (" . $stmt->rowCount() . " row)\n";
}

echo "Done.\n";
