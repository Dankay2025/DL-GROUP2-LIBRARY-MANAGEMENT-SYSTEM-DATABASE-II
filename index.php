<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/dashboard/index.php');
} else {
    header('Location: ' . APP_URL . '/auth/login.php');
}
exit;
