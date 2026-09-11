<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

$pageTitle       = 'Gallery | PulseFit Gym';
$pageDescription = 'A look inside PulseFit Gym — training floor, group classes and members at work. Click any photo to view it larger.';
$activePage      = 'gallery';
require __DIR__ . '/includes/header.php';

$zoomIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="10.5" cy="10.5" r="6.5"/><line x1="10.5" y1="7.5" x2="10.5" y2="13.5"/><line x1="7.5" y1="10.5" x2="13.5" y2="10.5"/><line x1="15.5" y1="15.5" x2="20" y2="20"/></svg>';

$items = [
    ['img' => 'gallery-2', 'tall' => true,  'title' => 'Between Sets',        'desc' => 'Member Spotlight',   'alt' => 'Woman resting a battle rope towel on her shoulders between sets'],
    ['img' => 'gallery-1', 'tall' => false, 'title' => 'Dumbbell Row Session', 'desc' => 'Free Weights Area',  'alt' => 'Member performing a single-arm dumbbell row on a bench'],
    ['img' => 'gallery-3', 'tall' => false, 'title' => 'Cardio Track',         'desc' => 'Running Club',       'alt' => "Close-up of a runner's legs and shoes mid-stride outdoors"],
    ['img' => 'gallery-4', 'tall' => false, 'title' => 'Full Focus',           'desc' => 'Strength Floor',     'alt' => 'Black and white close-up portrait of a member focused during a lift'],
    ['img' => 'gallery-6', 'tall' => true,  'title' => 'Battle Ropes',         'desc' => 'Rooftop HIIT',       'alt' => 'Man performing battle rope waves during an outdoor rooftop HIIT session'],
    ['img' => 'gallery-5', 'tall' => false, 'title' => 'Core Conditioning',    'desc' => 'Group Class',        'alt' => 'Woman performing a sit-up during a core conditioning class'],
    ['img' => 'gallery-7', 'tall' => false, 'title' => 'Boxing Fit',           'desc' => 'Combat Class',       'alt' => 'Woman wearing boxing gloves training in the combat fitness class'],
    ['img' => 'gallery-8', 'tall' => false, 'title' => 'Mobility Class',       'desc' => 'Group Studio',       'alt' => 'Group fitness class stretching on mats in the studio'],
];
?>

  <!-- ============ PAGE BANNER ============ -->
  <section class="page-banner">
    <div class="hero-bg" style="background-image:url('<?= e(base_url('images/banner-gallery.jpg')) ?>');" role="img" aria-label="Wide view of the PulseFit dumbbell rack and open training floor"></div>
    <div class="hero-overlay"></div>
    <div class="container">
      <p class="eyebrow">Gallery</p>
      <h1>A look inside PulseFit</h1>
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= e(base_url('index.php')) ?>">Home</a>
        <span aria-hidden="true">/</span>
        <span>Gallery</span>
      </nav>
    </div>
  </section>

  <!-- ============ GALLERY GRID ============ -->
  <section aria-labelledby="gallery-heading">
    <div class="container">
      <div class="section-head center reveal">
        <p class="eyebrow">Photo Gallery</p>
        <h2 id="gallery-heading">Our floor, our classes, our people</h2>
        <p>Click any photo to view a larger version. Use the arrow keys or on-screen buttons to browse.</p>
      </div>

      <div class="gallery-grid reveal">
        <?php foreach ($items as $item): ?>
          <div class="gallery-item <?= $item['tall'] ? 'tall' : '' ?>" data-full="<?= e(base_url('images/' . $item['img'] . '-full.jpg')) ?>" data-title="<?= e($item['title']) ?>" data-desc="<?= e($item['desc']) ?>">
            <img src="<?= e(base_url('images/' . $item['img'] . '.jpg')) ?>" alt="<?= e($item['alt']) ?>" loading="lazy">
            <span class="gallery-zoom-icon" aria-hidden="true"><?= $zoomIcon ?></span>
            <span class="gallery-caption"><?= e($item['title']) ?><span><?= e($item['desc']) ?></span></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ============ CTA BAND ============ -->
  <section class="section-alt">
    <div class="container">
      <div class="cta-band reveal">
        <div class="hero-bg" style="background-image:url('<?= e(base_url('images/hero-home.jpg')) ?>');" role="img" aria-label="Member training on a mat inside PulseFit Gym"></div>
        <div class="hero-overlay"></div>
        <h2>Want to see it in person?</h2>
        <p>Come walk the floor, meet a coach and try a class before you commit.</p>
        <div class="hero-actions">
          <a href="<?= e(base_url('contact.php')) ?>" class="btn btn-primary">Book A Free Tour</a>
        </div>
      </div>
    </div>
  </section>

</main>

<!-- ============ LIGHTBOX ============ -->
<div class="lightbox" id="lightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Image viewer">
  <div class="lightbox-inner">
    <button class="lightbox-close" aria-label="Close image viewer">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <button class="lightbox-prev" aria-label="Previous image">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 6 9 12 15 18"/></svg>
    </button>
    <button class="lightbox-next" aria-label="Next image">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 6 15 12 9 18"/></svg>
    </button>
    <img src="" alt="">
    <p class="lightbox-caption"></p>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
