<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Titan Court Tracker</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Titan Court Tracker</h1>
            <h2>CSUF's Backetball Management</h2>

            <?php if(!empty($loginError)):?>
                <div class="message error"><?php echo htmlspecialchars($loginError);?></div>
            <?php endif;?>
            <?php if( !empty($registerSuccess) ): ?>
                <div class="message success"><?php echo htmlspecialchars($registerSuccess); ?></div>
            <?php endif; ?>

            <!-- LOGIN FORM -->
            <div class="form-section">
                <form method="POST" action="login.php">
                    <input type="hidden" name="action" value="login">
                    
                    <label>Username</label>
                    <input type="text" name="username" maxlength="50" required>
                    <label>Password</label>
                    <input type="password" name="password" maxlength="255" required>
                    
                    <input type="submit" value="Login">
                </form>
            </div>
        </div>
    </div>
</body>
</html>