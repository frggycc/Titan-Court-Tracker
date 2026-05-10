<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Titan Court Tracker - Manage</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php require_once('components/header.php'); ?>

    <?php if( !empty($successMessage) ): ?>
        <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
        <script>
            // 3000 milliseconds = 3 seconds
            setTimeout(function() {
                window.location.href = 'landing_manage.php'; 
            }, 2000);
        </script>
    <?php endif; ?>
    <?php if( !empty($errorMessage) ): ?>
        <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>
 
    <!-- Select Season -->
    <div class="drop-down-menu">
        <form method="GET" action="landing_manage.php">
            <label for="season">Season:</label>
            <select name="season" id="season" onchange="this.form.submit()">
            <?php foreach( $seasons as $s ): ?>
                <option value="<?php echo $s; ?>"
                <?php echo $s === $selectedSeason ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($s); ?>
                </option>
            <?php endforeach; ?>
            </select>
            <noscript><input type="submit" value="Go"></noscript>
        </form>
    </div>

    <!-- VIEW LEAGUE TEAMS -->
    <div class="card">
        <div class="card-header">
            <h2>League Teams</h2>
        </div>
 
        <!-- League table -->
        <?php if( !empty($leagueError) ): ?>
            <div class="message error"><?php echo htmlspecialchars($leagueError); ?></div>
        <?php elseif( empty($leagueRows) ): ?>
            <p class="no-data">No league teams found.</p>
        <?php else: ?>
            <table>
            <tr>
                <th>Team Name</th>
                <th>Head Coach</th>
                <th>Conference</th>
                <th>City, State</th>
                <th>Delete</th>
            </tr>
            <?php foreach( $leagueRows as $row ): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['team_name']);  ?></td>
                    <td><?php echo htmlspecialchars($row['head_coach']); ?></td>
                    <td><?php echo htmlspecialchars($row['conference']); ?></td>
                    <td><?php echo htmlspecialchars($row['city'] . ', ' . $row['state']); ?></td>
        
                    <!-- Delete -->
                    <td>
                    <form method="POST" action="landing_manage.php">
                        <input type="hidden" name="action"  value="delete_league">
                        <input type="hidden" name="team_id" value="<?php echo $row['ID']; ?>">
                        <input type="hidden" name="season"  value="<?php echo htmlspecialchars($selectedSeason); ?>">
                        <input type="submit" value="Delete" class="btn-delete">
                    </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </table>
        <?php endif; ?>
 
        <!-- Add League Team form -->
        <h3>Add New Team</h3>
        <form method="POST" action="landing_manage.php">
        <input type="hidden" name="action" value="add_league">
        <input type="hidden" name="season" value="<?php echo htmlspecialchars($selectedSeason); ?>">
            <div>
                <label>Team Name *</label>
                <input type="text" name="team_name" maxlength="100" required>
            </div>
            <div>
                <label>Head Coach</label>
                <input type="text" name="head_coach" maxlength="100">
            </div>
            <div>
                <label>Conference</label>
                <input type="text" name="conference" maxlength="100">
            </div>
            <div>
                <label>City</label>
                <input type="text" name="city" maxlength="100">
            </div>
            <div>
                <label>State</label>
                <input type="text" name="state" maxlength="100">
            </div>
        </div>
        <input type="submit" value="Add Team">
        </form>
    </div>
</body>
</html>