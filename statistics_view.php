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
                <th>Name</th>
                <th>GP</th>
                <th>PPG</th>
                <th>APG</th>
                <th>RPG</th>
            </tr>

            <!-- SHOW DASH FOR EMPTY STATS (= NO GAMES PLAYED) -->
            <?php foreach( $playerRows as $row ): ?>
            <tr <?php echo $row['ID'] === $selectedPlayerID ? 'class="selected-row"' : ''; ?>>
                <td><?php echo htmlspecialchars($row['name_last'] . ', ' . $row['name_first']); ?></td>
                <?php if( $row['games_played'] == 0 ): ?>
                    <td>0</td>
                    <td class="no-data-cell">&mdash;</td>
                    <td class="no-data-cell">&mdash;</td>
                    <td class="no-data-cell">&mdash;</td>
                <?php else: ?>
                    <td><?php echo $row['games_played']; ?></td>
                    <td><strong><?php echo $row['avg_points'];    ?></strong></td>
                    <td><?php echo $row['avg_assists'];   ?></td>
                    <td><?php echo $row['avg_rebounds'];  ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>