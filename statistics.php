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

    $selectedSeason   = isset($_GET['season'])    && in_array($_GET['season'], $seasons)
                      ? $_GET['season'] : $seasons[0];
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
                            JOIN GameStatistics gs ON p.ID       = gs.player_id
                            JOIN Game           g  ON gs.game_id = g.ID
                            WHERE g.season_year = ?
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

    // Getting list of all games for a specific year
    $gameListRows  = [];
    $gameListError = '';
    
    $gameListQuery = "SELECT 
                        g.ID, 
                        g.game_date, 
                        lt.team_name AS opponent, 
                        g.outcome
                      FROM  Game g
                      JOIN  LeagueTeam lt ON g.opponent_id = lt.ID
                      WHERE g.season_year = ?
                      ORDER BY g.game_date ASC";
    $stmt = $db->prepare($gameListQuery);
    if( $stmt === FALSE ){
        $gameListError = 'Game list query failed: ' . $db->error;
    }
    else{
        $stmt->bind_param('s', $selectedSeason);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($glID, $glDate, $glOpponent, $glOutcome);
        while( $stmt->fetch() ){
            $gameListRows[] = ['ID'=>$glID, 'game_date'=>$glDate, 'opponent'=>$glOpponent, 'outcome'=>$glOutcome];
        }
        $stmt->close();
    
        if( $selectedGameID === 0 && !empty($gameListRows) ){
            $selectedGameID = $gameListRows[0]['ID'];
        }   
    }
    
    
    // Box scores for Game_ID chosen via dropdown
    $gameInfo       = [];
    $gameStatsRows  = [];
    $gameStatsError = '';
    
    if( $selectedGameID > 0 )
    {
        $gameBoxQuery = "SELECT 
                            g.game_date, 
                            g.game_time, 
                            g.location, 
                            g.home_or_away,
                            g.outcome, 
                            g.csuf_score, 
                            g.opp_score,
                            lt.team_name AS opponent
                         FROM  Game g
                         JOIN  LeagueTeam lt ON g.opponent_id = lt.ID
                         WHERE g.ID = ?
                         LIMIT 1";
        $stmt = $db->prepare($gameBoxQuery);
        if( $stmt === FALSE ){
            $gameStatsError = 'Game info query failed: ' . $db->error;
        }

        else{
            $stmt->bind_param('i', $selectedGameID);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($giDate, $giTime, $giLoc, $giHA, $giOutcome, $giCsuf, $giOpp, $giOpponent);
            if( $stmt->fetch() )
                $gameInfo = [
                    'game_date'    => $giDate,    
                    'game_time'    => $giTime,
                    'location'     => $giLoc,     
                    'home_or_away' => $giHA,
                    'outcome'      => $giOutcome, 
                    'csuf_score'   => $giCsuf,
                    'opp_score'    => $giOpp,     
                    'opponent'     => $giOpponent,
                ];
            $stmt->close();
        }
    
        $stmt = $db->prepare("SELECT p.name_first, p.name_last, p.jersey_number, p.position,
                                    gs.playing_time_min, gs.playing_time_sec,
                                    gs.points, gs.assists, gs.rebounds,
                                    gs.steals, gs.blocks, gs.turnovers,
                                    gs.fouls, gs.free_throw, gs.free_throw_attempts
                            FROM  GameStatistics gs
                            JOIN  Player p ON gs.player_id = p.ID
                            WHERE gs.game_id = ?
                            ORDER BY gs.points DESC");
        if( $stmt === FALSE ){
            $gameStatsError = 'Box score query failed: ' . $db->error;
        }

        else{
            $stmt->bind_param('i', $selectedGameID);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result(
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
            while( $stmt->fetch() )
                $gameStatsRows[] = [
                    'name_first'          => $gsFirst,  
                    'name_last'           => $gsLast,
                    'jersey_number'       => $gsJersey, 
                    'position'            => $gsPos,
                    'playing_time_min'    => $gsMin,    
                    'playing_time_sec'    => $gsSec,
                    'points'              => $gsPts,    
                    'assists'             => $gsAst,
                    'rebounds'            => $gsReb,    
                    'steals'              => $gsStl,
                    'blocks'              => $gsBlk,    
                    'turnovers'           => $gsTo,
                    'fouls'               => $gsFouls,  
                    'free_throw'          => $gsFT,
                    'free_throw_attempts' => $gsFTA,
                ];
            $stmt->close();
        }
    }

    require_once('views/statistics_view.php');
?>
 
