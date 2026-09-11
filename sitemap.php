<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/xml; charset=utf-8');

// Public, indexable pages only — member/admin areas are excluded via robots.txt.
$pages = [
    ['path' => 'index.php',        'priority' => '1.0', 'changefreq' => 'weekly'],
    ['path' => 'membership.php',   'priority' => '0.9', 'changefreq' => 'weekly'],
    ['path' => 'about.php',        'priority' => '0.7', 'changefreq' => 'monthly'],
    ['path' => 'gallery.php',      'priority' => '0.6', 'changefreq' => 'monthly'],
    ['path' => 'testimonials.php', 'priority' => '0.6', 'changefreq' => 'weekly'],
    ['path' => 'contact.php',      'priority' => '0.8', 'changefreq' => 'monthly'],
    ['path' => 'privacy.php',      'priority' => '0.3', 'changefreq' => 'yearly'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $p): ?>
  <url>
    <loc><?= htmlspecialchars(base_url($p['path']), ENT_XML1) ?></loc>
    <changefreq><?= $p['changefreq'] ?></changefreq>
    <priority><?= $p['priority'] ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
