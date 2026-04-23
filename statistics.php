<?php
    require_once('StartSession.php');

    // // Comment out to see errors
    // ini_set('display_errors', 1);
    // error_reporting(E_ALL);

    if( !authenticatedUser() ){
        header('Location: login.php');
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
                      ? $_GET['season']
                      : $seasons[0];
    $selectedGameID   = isset($_GET['game_id'])   ? (int)$_GET['game_id']   : 0;
    $selectedPlayerID = isset($_GET['player_id']) ? (int)$_GET['player_id'] : 0;

    
    // PLayer lists with their seasion averages (only from one season)
    $playerRows       = [];
    $playerStatsError = '';

    $playerStatsQuery = "SELECT
                            p.ID,
                            p.name_first,
                            p.name_last,
                            p.jersey_number,
                            p.position,
                            p.class,
                            COUNT(gs.ID)                AS games_played,
                            AVG(gs.playing_time_min)    AS avg_min,
                            AVG(gs.playing_time_sec)    AS avg_sec,
                            AVG(gs.points)              AS avg_points,
                            AVG(gs.assists)             AS avg_assists,
                            AVG(gs.rebounds)            AS avg_rebounds,
                            AVG(gs.steals)              AS avg_steals,
                            AVG(gs.blocks)              AS avg_blocks,
                            AVG(gs.turnovers)           AS avg_turnovers,
                            AVG(gs.free_throw)          AS avg_ft,
                            AVG(gs.free_throw_attempts) AS avg_fta
                            FROM  Player p
                            LEFT JOIN GameStatistics gs ON p.ID       = gs.player_id
                            LEFT JOIN Game           g  ON gs.game_id = g.ID
                                                        AND g.season_year = ?
                            GROUP BY p.ID
                            ORDER BY p.name_last, p.name_first";

    if(($stmt = $db->prepare($playerStatsQuery)) === FALSE){
        $playerStatsError = "Player statistics query failed: " . $db->error;
    }
    else{
        $stmt->bind_param('s', $selectedSeason);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result(
            $psID, 
            $psFirst, 
            $psLast, 
            $psJersey, 
            $psPos, 
            $psClass,
            $psGames, 
            $psAvgMin, 
            $psAvgSec,
            $psAvgPts, 
            $psAvgAst, 
            $psAvgReb,
            $psAvgStl, 
            $psAvgBlk, 
            $psAvgTo,
            $psAvgFT, 
            $psAvgFTA
        );

        while( $stmt->fetch() ){
            $playerRows[] = [
                'ID'            => $psID,
                'name_first'    => $psFirst,
                'name_last'     => $psLast,
                'jersey_number' => $psJersey,
                'position'      => $psPos,
                'class'         => $psClass,
                'games_played'  => $psGames,
                'avg_min'       => round($psAvgMin),
                'avg_sec'       => round($psAvgSec),
                'avg_points'    => round($psAvgPts, 1),
                'avg_assists'   => round($psAvgAst, 1),
                'avg_rebounds'  => round($psAvgReb, 1),
                'avg_steals'    => round($psAvgStl, 1),
                'avg_blocks'    => round($psAvgBlk, 1),
                'avg_turnovers' => round($psAvgTo,  1),
                'avg_ft'        => round($psAvgFT,  1),
                'avg_fta'       => round($psAvgFTA, 1),
            ];
        }
        
        $stmt->close();
    }

    require_once('statistics_view.php');
?>
 
