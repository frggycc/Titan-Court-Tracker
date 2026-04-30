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
            <h2>Create a New Account</h2>

            <?php if(!empty($registerError)):?>
                <div class="message error"><?php echo htmlspecialchars($registerError);?></div>
            <?php endif;?>

            <!-- REGISTER FORM -->
            <div class="form-section">
                <form method="POST" action="register.php">
                    <input type="hidden" name="action" value="register">

                    <label>First Name</label>
                    <input type="text" name="first_name" maxlength="100">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" maxlength="100" required>
                    <label>Email</label>
                    <input type="email" name="email" maxlength="250">
                    <label>Username * (min 3 characters)</label>
                    <input type="text" name="username" maxlength="50" required>
                    <label>Password *</label>
                    <input type="password" name="password" maxlength="255" required>
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" maxlength="255" required>
            
                    <input type="submit" value="Create Account">
                </form>

                <div class="redirect-link">
                    <a href="login.php">Login here</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>