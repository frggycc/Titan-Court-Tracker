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
        <h2>MY ACCOUNT</h2>
        <?php if(!empty($userInfoError)): ?>
            <div class="message error"><?php echo htmlspecialchars($userInfoError)?></div>
        <?php else: ?>
            <span>Username</span>
            <span><?php echo htmlspecialchars($userName) ?></span>

            <span>Name</span>
            <span><?php echo htmlspecialchars( trim(($userInfo['name_first'] ?? '') . ' ' . ($userInfo['name_last'] ?? ''))); ?></span>

            <span>Member Since</span>
            <span><?php echo htmlspecialchars($userInfo['created'] ?? '-'); ?></span>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Change Password</h2>

        <?php if( !empty($successMessage) ): ?>
            <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
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