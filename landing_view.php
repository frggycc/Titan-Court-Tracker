<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Titan Court Tracker - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php require_once('components/header.php'); ?>

    <!-- SEASON BAR GOES HERE -->
  
    <!-- DISPLAY GAME SCHEDULE -->
    <div class="card">
        <h2>Game Schedule</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Opponent</th>
                <th>Location</th>
                <th>Home/Away</th>
                <th>Type</th>
                <th>Score</th>
                <th>Outcome</th>
                <th>Coach</th>
            </tr>
            <?php foreach( $scheduleRows as $row ): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['game_date']);                               ?></td>
                <td><?php echo htmlspecialchars(substr($row['game_time'], 0, 5));                 ?></td>
                <td><?php echo htmlspecialchars($row['opponent']);                                ?></td>
                <td><?php echo htmlspecialchars($row['location']);                                ?></td>
                <td><?php echo htmlspecialchars($row['home_or_away']);                            ?></td>
                <td><?php echo htmlspecialchars($row['game_type']);                               ?></td>
                <td>
                    <?php if( $row['outcome'] === 'TBD' ): ?>
                        &mdash;
                    <?php else: ?>
                        <?php echo (int)$row['csuf_score'] . ' - ' . (int)$row['opp_score']; ?>
                    <?php endif; ?>
                </td>
                <td class="outcome-<?php echo strtolower($row['outcome']); ?>">
                    <?php echo htmlspecialchars($row['outcome']); ?>
                </td>
                <td><?php echo htmlspecialchars($row['coach_first'] . ' ' . $row['coach_last']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- DISPLAY CSUF ROSTER -->
    <!-- LEAGUE TEAMS -->
</body>
</html>