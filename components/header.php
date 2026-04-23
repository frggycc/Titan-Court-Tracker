<header>
    <a href="landing.php"><h1>Titan Court Tracker</h1></a>
    <nav>
        <!-- EVERYONE HAS THIS -->
        <a href="landing.php"    class="<?php echo basename($_SERVER['PHP_SELF']) === 'landing.php'    ? 'active' : ''; ?>">Home</a>
        <a href="statistics.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'statistics.php' ? 'active' : ''; ?>">Statistics</a>
        <a href="account.php"    class="<?php echo basename($_SERVER['PHP_SELF']) === 'account.php'    ? 'active' : ''; ?>">Account</a>
        

        <!-- ONLY FOR MANAGERS -->
        <?php if( $role === 'Executive Manager' ): ?>
            <a href="manager.php" class="nav-manager <?php echo basename($_SERVER['PHP_SELF']) === 'manager.php' ? 'active' : ''; ?>">
                Manager
            </a>
        <?php endif; ?>

        <!-- LOGOUT -->
        <a href="logout.php" class="nav-logout">Logout</a>
    </nav>
</header>