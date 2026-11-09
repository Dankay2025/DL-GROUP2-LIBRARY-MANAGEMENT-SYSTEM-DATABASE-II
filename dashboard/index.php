<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth_check.php';

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

$stats = [
    'books' => (int)$pdo->query('SELECT COUNT(*) FROM books')->fetchColumn(),
    'members' => (int)$pdo->query('SELECT COUNT(*) FROM members')->fetchColumn(),
    'active_loans' => (int)$pdo->query('SELECT COUNT(*) FROM loans WHERE date_returned IS NULL')->fetchColumn(),
    'overdue' => (int)$pdo->query('SELECT COUNT(*) FROM loans WHERE date_returned IS NULL AND due_date < CURRENT_DATE')->fetchColumn(),
];

$recentStmt = $pdo->query(
    'SELECT l.loan_id, m.full_name, b.title, l.date_issued, l.due_date, l.date_returned
     FROM loans l
     JOIN members m ON m.member_id = l.member_id
     JOIN books b ON b.book_id = l.book_id
     ORDER BY l.loan_id DESC
     LIMIT 6'
);
$recentLoans = $recentStmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<main class="main-content">
    <header class="topbar">
        <div><span class="eyebrow">LIBRARY OPERATIONS</span><h1>Dashboard</h1></div>
        <div class="user-chip"><span class="user-avatar"><?= e(strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1))) ?></span>
            <span><strong><?= e($_SESSION['full_name'] ?? 'User') ?></strong><small><?= e(ucfirst($_SESSION['role'] ?? '')) ?></small></span>
        </div>
    </header>

    <section class="welcome-panel">
        <div><span class="eyebrow">CAVENDISH UNIVERSITY UGANDA</span>
            <h2>Good day, <?= e($_SESSION['full_name'] ?? 'Librarian') ?>.</h2>
            <p>Here is an overview of the library records and borrowing activity.</p>
        </div>
        <div class="welcome-symbol">▤</div>
    </section>

    <section class="stats-grid">
        <article class="stat-card"><span class="stat-label">REGISTERED BOOK TITLES</span><strong><?= number_format($stats['books']) ?></strong><span class="stat-foot">Book records in the catalogue</span></article>
        <article class="stat-card"><span class="stat-label">LIBRARY MEMBERS</span><strong><?= number_format($stats['members']) ?></strong><span class="stat-foot">Registered members</span></article>
        <article class="stat-card"><span class="stat-label">ACTIVE LOANS</span><strong><?= number_format($stats['active_loans']) ?></strong><span class="stat-foot">Books not yet returned</span></article>
        <article class="stat-card stat-card-alert"><span class="stat-label">OVERDUE LOANS</span><strong><?= number_format($stats['overdue']) ?></strong><span class="stat-foot">Past their due date</span></article>
    </section>

    <section class="content-card">
        <div class="section-heading"><div><span class="eyebrow">LATEST ACTIVITY</span><h2>Recent loans</h2></div>
            <a class="text-link" href="<?= APP_URL ?>/loans/index.php">View all loans →</a>
        </div>
        <?php if (!$recentLoans): ?>
            <div class="empty-state">No loan records are available yet.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Member</th><th>Book</th><th>Issue date</th><th>Due date</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentLoans as $loan): ?>
                        <?php
                        $status = $loan['date_returned'] !== null ? 'Returned'
                            : ($loan['due_date'] < date('Y-m-d') ? 'Overdue' : 'On loan');
                        ?>
                        <tr>
                            <td><?= e($loan['full_name']) ?></td>
                            <td><?= e($loan['title']) ?></td>
                            <td><?= e($loan['date_issued']) ?></td>
                            <td><?= e($loan['due_date']) ?></td>
                            <td><span class="status status-<?= strtolower(str_replace(' ', '-', $status)) ?>"><?= e($status) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
    <section class="campus-gallery">
        <div class="section-heading"><div><span class="eyebrow">OUR LEARNING COMMUNITY</span><h2>Cavendish University Uganda</h2></div><span class="gallery-caption">Campus · Students · Library</span></div>
        <div class="gallery-grid">
            <figure class="gallery-card"><img src="<?= APP_URL ?>/assets/images/campus-building.jpg" alt="Cavendish University Uganda campus building"><figcaption><strong>Our campus</strong><span>A place to learn, connect and grow</span></figcaption></figure>
            <figure class="gallery-card"><img src="<?= APP_URL ?>/assets/images/students-campus.jpg" alt="Cavendish University Uganda students"><figcaption><strong>Our students</strong><span>A vibrant learning community</span></figcaption></figure>
            <figure class="gallery-card"><img src="<?= APP_URL ?>/assets/images/library-reading-room.jpg" alt="Library reading room"><figcaption><strong>Library resources</strong><span>Space for research and study</span></figcaption></figure>
        </div>
    </section>
    <div class="development-note">Books, members, loans, authors and categories registers are connected to the library database.</div>
    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</main>
