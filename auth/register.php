<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$error = '';
$success = '';
$fullName = trim((string)($_POST['full_name'] ?? ''));
$studentId = trim((string)($_POST['student_id'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = 'Your session could not be verified. Please refresh and try again.';
    } elseif ($fullName === '' || mb_strlen($fullName) > 150 || !preg_match('/^\d{3}-\d{3}$/', $studentId) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150 || strlen($phone) > 30) {
        $error = 'Please enter a valid full name, six-digit student ID in the format 000-000, email address and phone number.';
    } else {
        $check = $pdo->prepare('SELECT member_id FROM members WHERE email = :email OR student_id = :student_id LIMIT 1');
        $check->execute(['email' => $email, 'student_id' => $studentId]);
        if ($check->fetch()) {
            $error = 'This email address or student ID is already registered. Please contact the library desk if you need help.';
        } else {
            $pdo->beginTransaction();
            try {
                $memberNumber = 'CUU-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
                $insert = $pdo->prepare('INSERT INTO members (member_number, student_id, full_name, email, phone, status, date_joined) VALUES (:number, :student_id, :name, :email, :phone, \'active\', CURRENT_DATE)');
                $insert->execute(['number' => $memberNumber, 'student_id' => $studentId, 'name' => $fullName, 'email' => $email, 'phone' => $phone !== '' ? $phone : null]);
                $pdo->commit();
                $success = 'Registration received. Your library membership number is ' . $memberNumber . '. Please keep it for your records.';
                $fullName = $studentId = $email = $phone = '';
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                error_log('Library member registration failed: ' . $exception->getMessage());
                $error = 'We could not complete your registration. Please try again or contact the library desk.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Member registration | CUU Library</title><link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css"></head>
<body class="login-body"><main class="login-layout">
<section class="login-visual"><div class="login-photo-wall" aria-hidden="true"><img src="<?= APP_URL ?>/assets/images/campus-building.jpg" alt=""><img src="<?= APP_URL ?>/assets/images/students-campus.jpg" alt=""><img src="<?= APP_URL ?>/assets/images/library-interior.jpg" alt=""></div><div class="login-visual-overlay"></div><div class="login-brand"><img class="cuu-logo-login" src="<?= APP_URL ?>/assets/images/cuu-logo.png" alt="Cavendish University Uganda logo"><div><strong>CAVENDISH UNIVERSITY UGANDA</strong><small>LIBRARY MANAGEMENT SYSTEM</small></div></div><div class="login-message"><span class="eyebrow">KNOWLEDGE · ACCESS · EXCELLENCE</span><h1>Join the university library.</h1><p>Register for library membership to access the catalogue and borrowing services.</p></div><div class="visual-credit">DATABASE II · DL GROUP TWO</div></section>
<section class="login-panel"><div class="login-card"><div class="mobile-brand"><img class="cuu-logo-mobile" src="<?= APP_URL ?>/assets/images/cuu-logo.png" alt="Cavendish University Uganda logo"><strong>CUU LIBRARY</strong></div><span class="eyebrow">LIBRARY MEMBERSHIP</span><h2>Create your membership</h2><p class="muted">Enter your details below. Fields marked required must be completed.</p>
<?php if ($error !== ''): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
<?php if ($success !== ''): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
<form method="post" action="" autocomplete="on"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label for="full_name">Full name</label><input id="full_name" name="full_name" type="text" maxlength="150" required placeholder="Enter your full name" value="<?= e($fullName) ?>"><label for="student_id">Student ID number</label><input id="student_id" name="student_id" type="text" inputmode="numeric" pattern="[0-9]{3}-[0-9]{3}" maxlength="7" required placeholder="252-304" value="<?= e($studentId) ?>"><label for="email">Email address</label><input id="email" name="email" type="email" maxlength="150" required placeholder="name@example.com" value="<?= e($email) ?>"><label for="phone">Telephone number (optional)</label><input id="phone" name="phone" type="tel" maxlength="30" placeholder="Enter your phone number" value="<?= e($phone) ?>"><button class="btn btn-primary btn-full" type="submit">Submit registration <span>→</span></button></form>
<div class="login-register"><a href="<?= APP_URL ?>/auth/login.php">Return to staff sign in</a></div><div class="login-footer">Cavendish University Uganda · DL Group Two</div></div></section></main><script src="<?= APP_URL ?>/assets/js/app.js"></script></body></html>
