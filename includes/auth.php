<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']);
}

/** True if the logged-in user has any of the given roles. */
function has_role(string ...$roles): bool
{
    $user = current_user();
    return $user !== null && in_array($user['role'], $roles, true);
}

/** Call at the top of any page that requires a signed-in user of any role. */
function require_login(): void
{
    if (!is_logged_in()) {
        flash_set('error', 'Please log in to continue.');
        redirect('login.php');
    }
}

/** Call at the top of any page restricted to specific roles (e.g. require_role('admin')). */
function require_role(string ...$roles): void
{
    require_login();
    if (!has_role(...$roles)) {
        http_response_code(403);
        require __DIR__ . '/../403.php';
        exit;
    }
}

/** Regenerates the session ID (prevents session fixation) and stores the safe user fields. */
function login_user(array $userRow): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id'        => (int) $userRow['id'],
        'full_name' => $userRow['full_name'],
        'email'     => $userRow['email'],
        'role'      => $userRow['role'],
    ];
}

function logout_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
