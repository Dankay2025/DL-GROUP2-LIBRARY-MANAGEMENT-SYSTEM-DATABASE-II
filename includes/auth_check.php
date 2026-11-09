<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

if (empty($_SESSION['user_id'])) {
    set_flash('warning', 'Please log in to access the system.');
    header('Location: ' . APP_URL . '/auth/login.php');
    exit;
}

/* Recheck account status so a deactivated account cannot continue using an old session. */
$stmt = $pdo->prepare('SELECT is_active FROM users WHERE user_id = :user_id LIMIT 1');
$stmt->execute(['user_id' => (int)$_SESSION['user_id']]);
$account = $stmt->fetch();

if (!$account || (int)$account['is_active'] !== 1) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    header('Location: ' . APP_URL . '/auth/login.php');
    exit;
}
