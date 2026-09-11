<?php
declare(strict_types=1);

/** Build an absolute URL from a root-relative path, e.g. base_url('member/dashboard.php'). */
function base_url(string $path = ''): string
{
    return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

/** Shorthand for htmlspecialchars() so every echoed value is escaped by default. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): bool
{
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

function flash_set(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function flash_get(string $key): ?string
{
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function format_price(int $cents): string
{
    return '$' . number_format($cents / 100, 0);
}

function format_time(string $time): string
{
    return date('g:i A', strtotime($time));
}

function format_date_nice(string $date): string
{
    return date('D, j M Y', strtotime($date));
}

/** The next $count calendar dates (Y-m-d) that fall on the given weekday name, starting today. */
function upcoming_dates_for_weekday(string $weekday, int $count = 4): array
{
    $dates = [];
    $date = new DateTime('today');
    while (count($dates) < $count) {
        if ($date->format('l') === $weekday) {
            $dates[] = $date->format('Y-m-d');
        }
        $date->modify('+1 day');
    }
    return $dates;
}
