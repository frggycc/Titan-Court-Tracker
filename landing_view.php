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
        <div class="card-header">
            <h2>Game Schedule</h2>
        </div>
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
    <div class="card">
        <div class="card-header">
            <h2>CSUF Roster</h2>
        </div>
        <table>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Position</th>
                <th>Class</th>
            </tr>
            <?php foreach( $rosterRows as $row ): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['jersey_number']);                       ?></td>
                <td><?php echo htmlspecialchars($row['name_first'] . ' ' . $row['name_last']);?></td>
                <td><?php echo htmlspecialchars($row['position']);                            ?></td>
                <td><?php echo htmlspecialchars($row['class']);                               ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- LEAGUE TEAMS -->
    <div class="card">
        <div class="card-header">
            <h2>Big West Conference Teams</h2>
        </div>
        <table>
            <tr>
                <th>Team Name</th>
                <th>Head Coach</th>
                <th>Conference</th>
                <th>Home</th>
            </tr>
            <?php foreach( $leagueRows as $row ): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['team_name']);                       ?></td>
                <td><?php echo htmlspecialchars($row['head_coach']);?></td>
                <td><?php echo htmlspecialchars($row['conference']);                            ?></td>
                <td><?php echo htmlspecialchars($row['city'] . ', ' . $row['state']);  ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>