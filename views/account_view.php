<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Titan Court Tracker - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require_once('components/header.php'); ?>

    <div class="card">
        <div class="card-header">
            <h2>MY ACCOUNT</h2>
        </div>
        <?php if(!empty($userInfoError)): ?>
            <div class="message error"><?php echo htmlspecialchars($userInfoError)?></div>
        <?php else: ?>
            <div class="account-info-grid">
                <div class="account-info-section">
                    <span>Username: </span>
                    <span><?php echo htmlspecialchars($userName) ?></span>
                </div>
                <div class="account-info-section">
                    <span>Name: </span>
                    <span><?php echo htmlspecialchars( trim(($userInfo['name_first'] ?? '') . ' ' . ($userInfo['name_last'] ?? ''))); ?></span>
                </div>
                <div class="account-info-section">
                    <span>Member Since: </span>
                    <span><?php echo htmlspecialchars($userInfo['created'] ?? '-'); ?></span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Change Password</h2>
        </div>

        <?php if( !empty($successMessage) ): ?>
            <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
            <script>
                // 3000 milliseconds = 3 seconds
                setTimeout(function() {
                    window.location.href = 'account.php'; 
                }, 2000);
            </script>
        <?php endif; ?>
    
        <?php if( !empty($errorMessage) ): ?>
            <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>


        <form method="POST" action="account.php">
            <input type="hidden" name="action" value="change_password">

            <label>Current Password*</label>
            <input type="password" name="current_password" required>

            <label>New Password*</label>
            <input type="password" name="new_password" required>

            <label>Confirm Password*</label>
            <input type="password" name="confirm_password" required>

            <input type="submit" value="Update Password">
        </form>
    </div>
</body>
</html>