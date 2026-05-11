<?php
    require_once('StartSession.php');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    if( !authenticatedUser() ){
        header('Location: login.php');
        exit;
    }

    // Only Users and Executive Managers can access this page
    if( $_SESSION['UserRole'] !== 'Users' && $_SESSION['UserRole'] !== 'Executive Manager' ){
        header('Location: statistics.php');
        exit;
    }

    $role     = $_SESSION['UserRole'];
    $userName = $_SESSION['UserName'];

    $seasons = [
        '2025-2026',
        '2024-2025', '2023-2024', '2022-2023', '2021-2022', '2020-2021',
        '2019-2020', '2018-2019', '2017-2018', '2016-2017', '2015-2016'
    ];

    $selectedSeason = isset($_GET['season']) && in_array($_GET['season'], $seasons)
                        ? $_GET['season'] : $seasons[0];
    $selectedGameID = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;

    $successMessage = "";
    $errorMessage   = "";

    //////////////////////////////////////////
    // GET game information
    //////////////////////////////////////////
    // Get game info for game dropdown list
    $gameListRows  = [];
    $gameListError = '';

    $stmt = $db->prepare(
        "SELECT g.ID, g.game_date, lt.team_name AS opponent, g.outcome
        FROM   Game g
        JOIN   LeagueTeam lt ON g.opponent_id = lt.ID
        WHERE  g.season_year = ?
        ORDER BY g.game_date ASC"
    );
    if( $stmt === FALSE ){
        $gameListError = "Game list query failed";
    }
    else{
        $stmt->bind_param('s', $selectedSeason);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($glID, $glDate, $glOpponent, $glOutcome);
        while( $stmt->fetch() )
        $gameListRows[] = [
            'ID'        => $glID,
            'game_date' => $glDate,
            'opponent'  => $glOpponent,
            'outcome'   => $glOutcome,
        ];
        $stmt->close();

        if( $selectedGameID === 0 && !empty($gameListRows) ){
            $selectedGameID = $gameListRows[0]['ID'];
        }
    }

    // Game game stat for table rows for each player
    $gameStatsRows  = [];
    $gameStatsError = "";

    if( $selectedGameID > 0 )
    {
        $gameStatsQuery ="
                        SELECT
                            gs.ID, 
                            p.ID AS player_id,
                            p.name_first, 
                            p.name_last, 
                            p.jersey_number, 
                            p.position,
                            gs.playing_time_min, 
                            gs.playing_time_sec,
                            gs.points, 
                            gs.assists, 
                            gs.rebounds,
                            gs.steals, 
                            gs.blocks, 
                            gs.turnovers,
                            gs.fouls, 
                            gs.free_throw, 
                            gs.free_throw_attempts
                        FROM GameStatistics gs
                        JOIN Player p ON gs.player_id = p.ID
                        WHERE gs.game_id = ?
                        ORDER BY gs.points DESC";
        $stmt = $db->prepare($gameStatsQuery);
        if( $stmt === FALSE ){
            $gameStatsError = "Box score query failed";
        }
        else{
            $stmt->bind_param('i', $selectedGameID);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result(
                $gsStatsID, 
                $gsPlayerID,
                $gsFirst, 
                $gsLast, 
                $gsJersey, 
                $gsPos,
                $gsMin, 
                $gsSec, 
                $gsPts, 
                $gsAst, 
                $gsReb,
                $gsStl, 
                $gsBlk, 
                $gsTo, 
                $gsFouls, 
                $gsFT, 
                $gsFTA
            );
            while( $stmt->fetch() ){
                $gameStatsRows[] = [
                    'stats_id' => $gsStatsID,
                    'player_id' => $gsPlayerID,
                    'name_first' => $gsFirst,
                    'name_last' => $gsLast,
                    'jersey_number' => $gsJersey,
                    'position' => $gsPos,
                    'playing_time_min' => $gsMin,
                    'playing_time_sec' => $gsSec,
                    'points' => $gsPts,
                    'assists' => $gsAst,
                    'rebounds' => $gsReb,
                    'steals' => $gsStl,
                    'blocks' => $gsBlk,
                    'turnovers' => $gsTo,
                    'fouls' => $gsFouls,
                    'free_throw' => $gsFT,
                    'free_throw_attempts' => $gsFTA,
                ];
            }
            $stmt->close();
        }
    }


    // Get all players to choose for dropdown list
    $allPlayers  = [];

    $allPlayerQuery = "
                    SELECT
                        ID,
                        name_first,
                        name_last,
                        jersey_number
                    FROM Player
                    ORDER BY name_last, name_first ASC";
    $stmt = $db->prepare($allPlayerQuery);

    if( $stmt !== FALSE ){
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($apID, $apFirst, $apLast, $apJersey);
        while( $stmt->fetch() ){
            $allPlayers[] = [
                'ID' => $apID,
                'name_first' => $apFirst,
                'name_last' => $apLast,
                'jersey_number' => $apJersey,
            ];
        }
        $stmt->close();
    }

    ////////////////////////////////
    // CUD Stats
    ////////////////////////////////
    // Add player stats
    if( isset($_POST['action']) && $_POST['action'] === 'add_stats' ){
        $gameID = (int)$_POST['game_id'];
        $playerID = (int)$_POST['player_id'];
        $minPlayed = (int)$_POST['playing_time_min'];
        $secPlayed = (int)$_POST['playing_time_sec'];
        $points = (int)$_POST['points'];
        $assists = (int)$_POST['assists'];
        $rebounds = (int)$_POST['rebounds'];
        $steals = (int)$_POST['steals'];
        $blocks = (int)$_POST['blocks'];
        $turnovers = (int)$_POST['turnovers'];
        $fouls = (int)$_POST['fouls'];
        $ft = (int)$_POST['free_throw'];
        $fta = (int)$_POST['free_throw_attempts'];

        if( $gameID <= 0 || $playerID <= 0 ){
            $errorMessage = "A valid game and player are required.";
        }
        else{
            $addGameQuery = "
                            INSERT INTO GameStatistics
                            SET
                                game_id              = ?,
                                player_id            = ?,
                                playing_time_min     = ?,
                                playing_time_sec     = ?,
                                points               = ?,
                                assists              = ?,
                                rebounds             = ?,
                                steals               = ?,
                                blocks               = ?,
                                turnovers            = ?,
                                fouls                = ?,
                                free_throw           = ?,
                                free_throw_attempts  = ?";

            $stmt = $db->prepare($addGameQuery);
            if( $stmt === FALSE ){
                $errorMessage = "Failed to add stats.";
            }
            else{
                $stmt->bind_param('iiiiiiiiiiiii',
                                    $gameID,
                                    $playerID, 
                                    $minPlayed, 
                                    $secPlayed,
                                    $points, 
                                    $assists, 
                                    $rebounds, 
                                    $steals,
                                    $blocks, 
                                    $turnovers, 
                                    $fouls, 
                                    $ft, 
                                    $fta
                                );
                $stmt->execute();
                if( $stmt->affected_rows === 1 ){
                    $successMessage = "Statistics added successfully.";
                }
                else{
                    $errorMessage = "Failed to add statistics.";
                }
                $stmt->close();
            }
        }

        // Keep selected game after adding
        $selectedGameID = $gameID;
    }


    // Edit game stats for a single player
    if( isset($_POST['action']) && $_POST['action'] === 'edit_stats' ){
        $statsID     = (int)$_POST['stats_id'];
        $gameID      = (int)$_POST['game_id'];
        $minPlayed   = (int)$_POST['playing_time_min'];
        $secPlayed   = (int)$_POST['playing_time_sec'];
        $points      = (int)$_POST['points'];
        $assists     = (int)$_POST['assists'];
        $rebounds    = (int)$_POST['rebounds'];
        $steals      = (int)$_POST['steals'];
        $blocks      = (int)$_POST['blocks'];
        $turnovers   = (int)$_POST['turnovers'];
        $fouls       = (int)$_POST['fouls'];
        $ft          = (int)$_POST['free_throw'];
        $fta         = (int)$_POST['free_throw_attempts'];

        if( $statsID <= 0 ){
            $errorMessage = "Invalid statistics record selected.";
        }
        else{
            $gameStatsQuery = "
                            UPDATE GameStatistics SET
                                playing_time_min    = ?,
                                playing_time_sec    = ?,
                                points              = ?,
                                assists             = ?,
                                rebounds            = ?,
                                steals              = ?,
                                blocks              = ?,
                                turnovers           = ?,
                                fouls               = ?,
                                free_throw          = ?,
                                free_throw_attempts = ?
                            WHERE ID = ?";
            $stmt = $db->prepare($gameStatsQuery);
            if( $stmt === FALSE ){
                $errorMessage = "Failed to update stats.";
            }
            else{
                $stmt->bind_param('iiiiiiiiiiii',
                                    $minPlayed,
                                    $secPlayed,
                                    $points, 
                                    $assists, 
                                    $rebounds, 
                                    $steals,
                                    $blocks, 
                                    $turnovers, 
                                    $fouls, 
                                    $ft, 
                                    $fta,
                                    $statsID
                                );
                $stmt->execute();
                if( $stmt->affected_rows >= 0 ){
                    $successMessage = "Statistics updated successfully.";
                }
                else{
                    $errorMessage = "Failed to update statistics.";
                }
                $stmt->close();
            }
        }
        $selectedGameID = $gameID;
    }


    // Delete game stat record for a player
    if( isset($_POST['action']) && $_POST['action'] === 'delete_stats' )
    {
        $statsID = (int)$_POST['stats_id'];
        $gameID  = (int)$_POST['game_id'];

        if( $statsID <= 0 ){
            $errorMessage = "Invalid statistics record selected.";
        }
        else{
            $stmt = $db->prepare("DELETE FROM GameStatistics WHERE ID = ?");
            if( $stmt === FALSE ){
                $errorMessage = "Failed to delete stats.";
            }
            else{
                $stmt->bind_param('i', $statsID);
                $stmt->execute();
                if( $stmt->affected_rows === 1 ){
                    $successMessage = "Statistics record deleted successfully.";
                }
                else{
                    $errorMessage = "Failed to delete statistics.";
                }
                $stmt->close();
            }
        }

        $selectedGameID = $gameID;
    }
    require_once('views/statistics_manage_view.php');
?>