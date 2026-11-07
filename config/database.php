<?php
declare(strict_types=1);

/*
 * One database connection for the entire application.
 * XAMPP defaults: username=root, password empty.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'Cavendish University Library Management System');
define('APP_URL', '/library-system'); // Change only if your project folder has another name.
define('APP_ROOT', dirname(__DIR__));

$dbHost = 'localhost';
$dbName = 'library_db';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    error_log('Library database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('The system is temporarily unable to connect to its database. Please contact the administrator.');
}
