<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Titan Court Tracker - Manage Statistics</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php require_once('components/header.php'); ?>

    <?php if( !empty($successMessage) ): ?>
        <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
        <script>
            // 3000 milliseconds = 3 seconds
            setTimeout(function() {
                window.location.href = 'statistics_manage.php'; 
            }, 2000);
        </script>
    <?php endif; ?>
    <?php if( !empty($errorMessage) ): ?>
        <div class="message error"><?php echo htmlspecialchars($errorMessage); ?></div>
    <?php endif; ?>
    

    <!-- Select Season -->
    <div class="drop-down-menu">
        <form method="GET" action="statistics_manage.php">
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

    <!-- CRUD GAMES -->
    <div class="card">
        <div class="card-header">
            <h2>Select Game</h2>
        </div>

        <?php if( !empty($gameListRows) ): ?>
            <form method="GET" action="statistics_manage.php" class="drop-down-menu">
            <input type="hidden" name="season" value="<?php echo htmlspecialchars($selectedSeason); ?>">
            <label for="game_id">Game:</label>
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
        <?php endif; ?>

        <!-- Box score table -->
        <div class="card">
            <div class="card-header">
                <h2>Game Box Score</h2>
            </div>

            <!-- Errors or empty data -->
            <?php if( !empty($gameStatsError) ): ?>
                <div class="message error"><?php echo htmlspecialchars($gameStatsError); ?></div>
            <?php elseif( $selectedGameID === 0 ): ?>
                <p class="no-data">Select a season and game above to manage statistics.</p>
            <?php elseif( empty($gameStatsRows) ): ?>
                <p class="no-data">No statistics recorded for this game yet. Use the form below to add some.</p>
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
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>

                    <?php foreach( $gameStatsRows as $row ): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['jersey_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['name_first'] . ' ' . $row['name_last']); ?></td>
                            <td><?php echo htmlspecialchars($row['position']); ?></td>
                            <td><?php echo $row['playing_time_min'] . ':' . str_pad($row['playing_time_sec'], 2, '0', STR_PAD_LEFT); ?></td>
                            <td><strong><?php echo $row['points']; ?></strong></td>
                            <td><?php echo $row['assists']; ?></td>
                            <td><?php echo $row['rebounds']; ?></td>
                            <td><?php echo $row['steals']; ?></td>
                            <td><?php echo $row['blocks']; ?></td>
                            <td><?php echo $row['turnovers']; ?></td>
                            <td><?php echo $row['fouls']; ?></td>
                            <td><?php echo $row['free_throw'] . '/' . $row['free_throw_attempts']; ?></td>
                            
                            <!-- EDIT -->
                            <td>
                                <form method="GET" action="statistics_manage.php">
                                    <input type="hidden" name="season" value="<?php echo htmlspecialchars($selectedSeason); ?>">
                                    <input type="hidden" name="game_id" value="<?php echo $selectedGameID; ?>">
                                    <input type="hidden" name="edit_stats" value="<?php echo $row['stats_id']; ?>">
                                    <input type="submit" value="Edit" class="btn-inline">
                                </form>
                            </td>

                            <!-- DELETE -->
                            <td>
                                <form method="POST" action="statistics_manage.php">
                                    <input type="hidden" name="action" value="delete_stats">
                                    <input type="hidden" name="stats_id" value="<?php echo $row['stats_id']; ?>">
                                    <input type="hidden" name="game_id" value="<?php echo $selectedGameID; ?>">
                                    <input type="hidden" name="season" value="<?php echo htmlspecialchars($selectedSeason); ?>">
                                    <input type="submit" value="Delete" class="btn-delete">
                                </form>
                            </td>
                        </tr>

                        <!-- Inline edit form rip -->
                        <?php if( isset($_GET['edit_stats']) && (int)$_GET['edit_stats'] === $row['stats_id'] ): ?>
                        <tr class="inline-form-row">
                            <td colspan="14">
                            <form method="POST" action="statistics_manage.php">
                                <input type="hidden" name="action" value="edit_stats">
                                <input type="hidden" name="stats_id" value="<?php echo $row['stats_id']; ?>">
                                <input type="hidden" name="game_id" value="<?php echo $selectedGameID; ?>">
                                <input type="hidden" name="season" value="<?php echo htmlspecialchars($selectedSeason); ?>">

                                <p class="section-title">
                                    Editing: <strong><?php echo htmlspecialchars($row['name_first'] . ' ' . $row['name_last']); ?></strong>
                                </p>

                                <!-- Form inside the table -->
                                <div class="inline-form-grid">
                                    <div>
                                        <label>Min Played (0-40)</label>
                                        <input type="number" name="playing_time_min" min="0" max="40"
                                            value="<?php echo $row['playing_time_min']; ?>">
                                    </div>
                                    <div>
                                        <label>Sec Played (0-59)</label>
                                        <input type="number" name="playing_time_sec" min="0" max="59"
                                            value="<?php echo $row['playing_time_sec']; ?>">
                                    </div>
                                    <div>
                                        <label>Points</label>
                                        <input type="number" name="points" min="0"
                                            value="<?php echo $row['points']; ?>">
                                    </div>
                                    <div>
                                        <label>Assists</label>
                                        <input type="number" name="assists" min="0"
                                            value="<?php echo $row['assists']; ?>">
                                    </div>
                                    <div>
                                        <label>Rebounds</label>
                                        <input type="number" name="rebounds" min="0"
                                            value="<?php echo $row['rebounds']; ?>">
                                    </div>
                                    <div>
                                        <label>Steals</label>
                                        <input type="number" name="steals" min="0"
                                            value="<?php echo $row['steals']; ?>">
                                    </div>
                                    <div>
                                        <label>Blocks</label>
                                        <input type="number" name="blocks" min="0"
                                            value="<?php echo $row['blocks']; ?>">
                                    </div>
                                    <div>
                                        <label>Turnovers</label>
                                        <input type="number" name="turnovers" min="0"
                                            value="<?php echo $row['turnovers']; ?>">
                                    </div>
                                    <div>
                                        <label>Fouls (0-5)</label>
                                        <input type="number" name="fouls" min="0" max="5"
                                            value="<?php echo $row['fouls']; ?>">
                                    </div>
                                    <div>
                                        <label>Free Throws Made</label>
                                        <input type="number" name="free_throw" min="0"
                                            value="<?php echo $row['free_throw']; ?>">
                                    </div>
                                    <div>
                                        <label>Free Throw Attempts</label>
                                        <input type="number" name="free_throw_attempts" min="0"
                                            value="<?php echo $row['free_throw_attempts']; ?>">
                                    </div>
                                </div>
                                <input type="submit" value="Save Changes" class="btn-save">
                            </form>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>


            <!-- Add new player stats -->
            <?php if( $selectedGameID > 0 ): ?>
                <h3 class="section-title">Add Player Statistics</h3>
                <form method="POST" action="statistics_manage.php">
                <input type="hidden" name="action"  value="add_stats">
                <input type="hidden" name="game_id" value="<?php echo $selectedGameID; ?>">
                <input type="hidden" name="season"  value="<?php echo htmlspecialchars($selectedSeason); ?>">

                <div class="inline-form-grid">
                    <div>
                        <!-- DROPDOWN MENU for choosing players -->
                        <label>Player *</label>
                        <select name="player_id">
                            <?php foreach( $allPlayers as $p ): ?>
                            <option value="<?php echo $p['ID']; ?>">
                                #<?php echo htmlspecialchars($p['jersey_number']); ?>
                                <?php echo htmlspecialchars($p['name_last'] . ', ' . $p['name_first']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Min Played (0-40)</label>
                        <input type="number" name="playing_time_min" min="0" max="40" value="0">
                    </div>
                    <div>
                        <label>Sec Played (0-59)</label>
                        <input type="number" name="playing_time_sec" min="0" max="59" value="0">
                    </div>
                    <div>
                        <label>Points</label>
                        <input type="number" name="points" min="0" value="0">
                    </div>
                    <div>
                        <label>Assists</label>
                        <input type="number" name="assists" min="0" value="0">
                    </div>
                    <div>
                        <label>Rebounds</label>
                        <input type="number" name="rebounds" min="0" value="0">
                    </div>
                    <div>
                        <label>Steals</label>
                        <input type="number" name="steals" min="0" value="0">
                    </div>
                    <div>
                        <label>Blocks</label>
                        <input type="number" name="blocks" min="0" value="0">
                    </div>
                    <div>
                        <label>Turnovers</label>
                        <input type="number" name="turnovers" min="0" value="0">
                    </div>
                    <div>
                        <label>Fouls (0-5)</label>
                        <input type="number" name="fouls" min="0" max="5" value="0">
                    </div>
                    <div>
                        <label>Free Throws Made</label>
                        <input type="number" name="free_throw" min="0" value="0">
                    </div>
                    <div>
                        <label>Free Throw Attempts</label>
                        <input type="number" name="free_throw_attempts" min="0" value="0">
                    </div>
                </div>
                <input type="submit" value="Add Statistics" class="btn-save">
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>