<?php
if( !defined('MANAGER_VIEW_LOADED') )
{
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Titan Court Tracker - Manager</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require_once('components/header.php'); ?>

    <div class="card">
        <div class="card-header">
            <h2>Users</h2>
        </div>

        <?php if( !empty($successMessage) ): ?>
            <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
 
        <?php if( !empty($errorMessage) ): ?>
            <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>


        <table>
            <tr>
                <th>Username</th>
                <th>Name</th>
                <th>Email</th>
                <th>Current Role</th>
                <th>Change Role</th>
                <th>Delete</th>
            </tr>
            <?php foreach($userRows as $row): ?>
                <tr <?php echo $row['username'] === $userName ? 'class="selected-row"' : ''; ?>>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars(trim(($row['name_first'] ?? '') . ' ' . ($row['name_last'] ?? ''))); ?></td>
                    <td><?php echo htmlspecialchars($row['email'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($row['role_name']); ?></td>

                    <!-- TODO: Change role name in table -->
                    <td>
                        <?php if($row['username'] !== $userName): ?>
                            <form method="POST" action="manager.php">
                                <input type="hidden" name="action" value="change_role">
                                <input type="hidden" name="target_username" value="<?php echo htmlspecialchars($row['username']); ?>">

                                <select name="new_role_id">
                                    <?php foreach($roleRows as $roleOption): ?>
                                        <option value="<?php echo $roleOption['ID']; ?>"
                                            <?php echo $roleOption['ID'] === $row['role_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($roleOption['role_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <input type="submit" value="Update" class="btn-inline">
                            </form>
                        <?php else: ?>
                            <span>You</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>