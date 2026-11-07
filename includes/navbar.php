<aside class="sidebar">
    <a class="brand" href="<?= APP_URL ?>/dashboard/index.php">
        <img class="sidebar-logo" src="<?= APP_URL ?>/assets/images/cuu-logo.png" alt="Cavendish University Uganda logo">
        <span><strong>CUU LIBRARY</strong><small>Management Portal</small></span>
    </a>

    <div class="nav-caption">MAIN MENU</div>
    <nav class="side-nav" aria-label="Main navigation">
        <a class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= APP_URL ?>/dashboard/index.php">▦ <span>Dashboard</span></a>
        <a class="nav-link <?= ($activePage ?? '') === 'books' ? 'active' : '' ?>" href="<?= APP_URL ?>/books/index.php">▤ <span>Books</span></a>
        <a class="nav-link <?= ($activePage ?? '') === 'members' ? 'active' : '' ?>" href="<?= APP_URL ?>/members/index.php">♙ <span>Members</span></a>
        <a class="nav-link <?= ($activePage ?? '') === 'loans' ? 'active' : '' ?>" href="<?= APP_URL ?>/loans/index.php">⇄ <span>Loans</span></a>
        <a class="nav-link <?= ($activePage ?? '') === 'authors' ? 'active' : '' ?>" href="<?= APP_URL ?>/authors/index.php">✎ <span>Authors</span></a>
        <a class="nav-link <?= ($activePage ?? '') === 'categories' ? 'active' : '' ?>" href="<?= APP_URL ?>/categories/index.php">▧ <span>Categories</span></a>
    </nav>

    <div class="sidebar-bottom">
        <div class="group-tag">DATABASE II · DL GROUP TWO</div>
        <a class="nav-link logout-link" href="<?= APP_URL ?>/auth/logout.php">↪ <span>Log out</span></a>
    </div>
</aside>
