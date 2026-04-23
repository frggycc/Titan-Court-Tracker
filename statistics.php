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
                            COUNT(gs.ID)     AS games_played,
                            AVG(gs.points)   AS avg_points,
                            AVG(gs.assists)  AS avg_assists,
                            AVG(gs.rebounds) AS avg_rebounds
                         FROM Player p
                         LEFT JOIN GameStatistics gs ON p.ID = gs.player_id
                         LEFT JOIN Game g            ON gs.game_id = g.ID AND g.season_year = ?
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
            $psGames, 
            $psAvgPts, 
            $psAvgAst, 
            $psAvgReb,
        );
        while( $stmt->fetch() ){
            $playerRows[] = [
                'ID'            => $psID,
                'name_first'    => $psFirst,
                'name_last'     => $psLast,
                'games_played'  => $psGames,
                'avg_points'    => round($psAvgPts, 1),
                'avg_assists'   => round($psAvgAst, 1),
                'avg_rebounds'  => round($psAvgReb, 1),
            ];
        }
        
        $stmt->close();
    }

    require_once('statistics_view.php');
 
?>
 
