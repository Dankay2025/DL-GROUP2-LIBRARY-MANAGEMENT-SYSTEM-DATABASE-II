<?php
declare(strict_types=1);

/** Escape values before displaying database/user content in HTML. */
function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Store a one-time message for the next page request. */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Retrieve and clear a one-time message. */
function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

/** Generate or return a session-bound CSRF token. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Validate a submitted CSRF token using a timing-safe comparison. */
function verify_csrf_token(?string $token): bool
{
    return isset($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}


function redirect(string $path): never { header('Location: ' . APP_URL . $path); exit; }
function post_only(): void { if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed.'); } }
function require_csrf(): void { if (!verify_csrf_token($_POST['csrf_token'] ?? null)) { set_flash('error', 'Your session expired. Please try again.'); redirect('/dashboard/index.php'); } }
function flash_html(): void { $f=get_flash(); if($f){ echo '<div class="alert '.e($f['type']).'">'.e($f['message']).'</div>'; } }
function current_user_id(): int { return (int)($_SESSION['user_id'] ?? 0); }
