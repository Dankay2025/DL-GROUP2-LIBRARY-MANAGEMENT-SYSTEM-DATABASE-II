<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/dashboard/index.php');
    exit;
}

$error = '';
$flash = get_flash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your session could not be verified. Refresh the page and try again.';
    } else {
        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $error = 'Enter both your username and password.';
        } elseif (strlen($username) > 60 || strlen($password) > 4096) {
            $error = 'The supplied login details are not valid.';
        } else {
            $stmt = $pdo->prepare(
                'SELECT user_id, username, password_hash, full_name, role, is_active
                 FROM users
                 WHERE username = :username
                 LIMIT 1'
            );
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && (int)$user['is_active'] === 1 && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                unset($_SESSION['csrf_token']);

                header('Location: ' . APP_URL . '/dashboard/index.php');
                exit;
            }

            $error = 'Invalid username or password, or the account is inactive.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in | CUU Library</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body class="login-body">
    <main class="login-layout">
        <section class="login-visual">
            <div class="login-photo-wall" aria-hidden="true"><img src="<?= APP_URL ?>/assets/images/campus-building.jpg" alt=""><img src="<?= APP_URL ?>/assets/images/students-campus.jpg" alt=""><img src="<?= APP_URL ?>/assets/images/library-interior.jpg" alt=""></div>
            <div class="login-visual-overlay"></div>
            <div class="login-brand">
                <img class="cuu-logo-login" src="<?= APP_URL ?>/assets/images/cuu-logo.png" alt="Cavendish University Uganda logo">
                <div><strong>CAVENDISH UNIVERSITY UGANDA</strong><small>LIBRARY MANAGEMENT SYSTEM</small></div>
            </div>
            <div class="login-message">
                <span class="eyebrow">KNOWLEDGE · ACCESS · EXCELLENCE</span>
                <h1>Welcome to your<br>University Library.</h1>
                <p>A central place to manage books, members, borrowing and returns.</p>
            </div>
            <div class="visual-credit">DATABASE II · DL GROUP TWO</div>
        </section>

        <section class="login-panel">
            <div class="login-card">
                <div class="mobile-brand"><img class="cuu-logo-mobile" src="<?= APP_URL ?>/assets/images/cuu-logo.png" alt="Cavendish University Uganda logo"><strong>CUU LIBRARY</strong></div>
                <span class="eyebrow">LIBRARIAN ACCESS</span>
                <h2>Sign in to continue</h2>
                <p class="muted">Enter your authorised librarian account details.</p>

                <?php if ($flash): ?>
                    <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
                <?php endif; ?>
                <?php if ($error !== ''): ?>
                    <div class="alert alert-error"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post" action="" autocomplete="on">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" maxlength="60"
                           placeholder="Enter your username" autocomplete="username" required
                           value="<?= e($_POST['username'] ?? '') ?>">

                    <label for="password">Password</label>
                    <div class="password-wrap">
                        <input id="password" name="password" type="password"
                               placeholder="Enter your password" autocomplete="current-password" required>
                        <button class="password-toggle" type="button" data-toggle-password="password"
                                aria-label="Show password" aria-pressed="false">Show</button>
                    </div>

                    <button class="btn btn-primary btn-full" type="submit">Sign in <span>→</span></button>
                </form>
                <div class="login-note">Staff access is restricted to authorised administrators and librarians.</div>
                <div class="login-register">New to the library? <a href="<?= APP_URL ?>/auth/register.php">Register as a library member</a></div>
                <div class="login-footer">Cavendish University Uganda · DL Group Two</div>
            </div>
        </section>
    </main>
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
