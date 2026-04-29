<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Titan Court Tracker - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php require_once('components/header.php'); ?>

    <!-- TEAM INFO CARD HERE -->
    
    <!-- SEASON BAR GOES HERE -->
    <div class="drop-down-menu">
      <form method="GET" action="statistics.php">
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
                <td><?php echo htmlspecialchars($row['jersey_number']); ?></td>
                <td><?php echo htmlspecialchars($row['name_last'] . ', ' . $row['name_first']); ?></td>
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
    <div class="card">
        <div class="card-header">
            <h2>Game Box Scores</h2>
        </div>
    
        <?php if( !empty($gameListError) ): ?>
            <div class="message error"><?php echo htmlspecialchars($gameListError); ?></div>
    
        <?php elseif( empty($gameListRows) ): ?>
            <p>No games found for <?php echo htmlspecialchars($selectedSeason); ?>.</p>
    
        <?php else: ?>
    
            <!-- Game selector -->
            <form method="GET" action="statistics.php" class="drop-down-menu">
                <input type="hidden" name="season"    value="<?php echo htmlspecialchars($selectedSeason); ?>">
                <input type="hidden" name="player_id" value="<?php echo $selectedPlayerID; ?>">
                <label for="game_id">Select Game:</label>
                <select name="game_id" id="game_id" onchange="this.form.submit()">
                    <?php foreach( $gameListRows as $g ): ?>
                    <option value="<?php echo $g['ID']; ?>"
                        <?php echo $g['ID'] === $selectedGameID ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(
                        $g['game_date'] . ' vs ' . $g['opponent'] . ' (' . $g['outcome'] . ')'
                        ); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <noscript><input type="submit" value="Go"></noscript>
            </form>
    
            <!-- Box score table -->
            <?php if( !empty($gameStatsError) ): ?>
            <div class="message error"><?php echo htmlspecialchars($gameStatsError); ?></div>
    
            <?php elseif( empty($gameStatsRows) ): ?>
            <p>No player statistics recorded for this game yet.</p>
    
            <?php else: ?>
                <table>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Pos</th>
                        <th>MIN</th>
                        <th>PTS</th>
                        <th>AST</th>
                        <th>REB</th>
                        <th>STL</th>
                        <th>BLK</th>
                        <th>TO</th>
                        <th>FLS</th>
                        <th>FT/FTA</th>
                    </tr>
                    <?php foreach( $gameStatsRows as $row ): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['jersey_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['name_first'] . ' ' . $row['name_last']); ?></td>
                            <td><?php echo htmlspecialchars($row['position']); ?></td>
                            <td><?php echo $row['playing_time_min'] . ':' . str_pad($row['playing_time_sec'], 2, '0', STR_PAD_LEFT); ?></td>
                            <td><strong><?php echo $row['points'];    ?></strong></td>
                            <td><?php echo $row['assists'];   ?></td>
                            <td><?php echo $row['rebounds'];  ?></td>
                            <td><?php echo $row['steals'];    ?></td>
                            <td><?php echo $row['blocks'];    ?></td>
                            <td><?php echo $row['turnovers']; ?></td>
                            <td><?php echo $row['fouls'];     ?></td>
                            <td><?php echo $row['free_throw'] . '/' . $row['free_throw_attempts']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    
</body>
</html>