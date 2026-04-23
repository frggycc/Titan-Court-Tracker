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

    <!-- DISPLAY PLAYER STATS -->
    <div class="card">
        <div class="card-header">
            <h2>Season Averages</h2>
        </div>
        <table>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Position</th>
                <th>Class</th>
                <th>GP</th>
                <th>MIN</th>
                <th>PPG</th>
                <th>APG</th>
                <th>RPG</th>
                <th>SPG</th>
                <th>BPG</th>
                <th>TPG</th>
                <th>FT/FTA</th>
            </tr>

            <?php foreach( $playerRows as $row ): ?>
            <tr <?php echo $row['ID'] === $selectedPlayerID ? 'class="selected-row"' : ''; ?>>
                <!-- BASIC PLAYER INFORMATION -->
                <td><?php echo htmlspecialchars($row['jersey_number']); ?></td>
                <td>
                <a href="statistics.php?season=<?php echo urlencode($selectedSeason);
                    ?>&game_id=<?php echo $selectedGameID;
                    ?>&player_id=<?php echo $row['ID']; ?>">
                    <?php echo htmlspecialchars($row['name_last'] . ', ' . $row['name_first']); ?>
                </a>
                </td>
                <td><?php echo htmlspecialchars($row['position']); ?></td>
                <td><?php echo htmlspecialchars($row['class']);    ?></td>

                <!-- SHOW DASH FOR EMPTY STATS (= NO GAMES PLAYED) -->
                <?php if( $row['games_played'] == 0 ): ?>
                    <td>0</td>
                    <td class="no-data-cell">&mdash;</td>
                    <td class="no-data-cell">&mdash;</td>
                    <td class="no-data-cell">&mdash;</td>
                    <td class="no-data-cell">&mdash;</td>
                    <td class="no-data-cell">&mdash;</td>
                    <td class="no-data-cell">&mdash;</td>
                    <td class="no-data-cell">&mdash;</td>
                    <td class="no-data-cell">&mdash;</td>
                <?php else: ?>
                    <td><?php echo $row['games_played']; ?></td>
                    <td><?php echo $row['avg_min'] . ':' . str_pad($row['avg_sec'], 2, '0', STR_PAD_LEFT); ?></td>
                    <td><strong><?php echo $row['avg_points'];    ?></strong></td>
                    <td><?php echo $row['avg_assists'];   ?></td>
                    <td><?php echo $row['avg_rebounds'];  ?></td>
                    <td><?php echo $row['avg_steals'];    ?></td>
                    <td><?php echo $row['avg_blocks'];    ?></td>
                    <td><?php echo $row['avg_turnovers']; ?></td>
                    <td><?php echo $row['avg_ft'] . '/' . $row['avg_fta']; ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <!-- DISPLAY GAME BOX --> 
    
</body>
</html>