<?php
    require_once('StartSession.php');

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

    // Get game schedule
    $scheduleRows  = [];
    $scheduleError = '';

    $scheduleQuery = "SELECT
                    g.ID,
                    g.game_date,
                    g.game_time,
                    g.location,
                    g.home_or_away,
                    g.game_type,
                    g.outcome,
                    g.csuf_score,
                    g.opp_score,
                    lt.team_name AS opponent,
                    s.name_first AS coach_first,
                    s.name_last  AS coach_last
                    FROM  Game g
                    JOIN  LeagueTeam lt ON g.opponent_id = lt.ID
                    JOIN  Coach      c  ON g.`coach`     = c.ID
                    JOIN  Staff      s  ON c.staff_id    = s.ID
                    WHERE g.season_year = ?
                    ORDER BY g.game_date ASC";


    if( ($stmt = $db->prepare($scheduleQuery)) === FALSE ){
        $scheduleError = "Schedule query failed: " . $db->error;
    }
    else{
        $stmt->bind_param('s', $selectedSeason);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result(
            $sID,
            $sDate,
            $sTime,
            $sLocation,
            $sHomeAway,
            $sType,
            $sOutcome,
            $sCsufScore,
            $sOppScore,
            $sOpponent,
            $sCoachFirst,
            $sCoachLast
        );
        while( $stmt->fetch() ){
            $scheduleRows[] = [
                'ID'           => $sID,
                'game_date'    => $sDate,
                'game_time'    => $sTime,
                'location'     => $sLocation,
                'home_or_away' => $sHomeAway,
                'game_type'    => $sType,
                'outcome'      => $sOutcome,
                'csuf_score'   => $sCsufScore,
                'opp_score'    => $sOppScore,
                'opponent'     => $sOpponent,
                'coach_first'  => $sCoachFirst,
                'coach_last'   => $sCoachLast,
            ];
        }
        $stmt->close();
    }

    // Get CSUF Rosters
    $rosterRows  = [];
    $rosterError = '';

    $scheduleQuery = "SELECT DISTINCT
                        p.ID,
                        p.name_first,
                        p.name_last,
                        p.jersey_number,
                        p.position,
                        p.class
                      FROM Player p
                      JOIN GameStatistics gs ON p.ID = gs.player_id
                      JOIN Game           g  ON gs.game_id = g.ID
                      WHERE g.season_year = ?
                      ORDER BY p.jersey_number ASC";


    if( ($stmt = $db->prepare($scheduleQuery)) === FALSE ){
        $scheduleError = 'Schedule query failed: ' . $db->error;
    }
    else{
        $stmt->bind_param('s', $selectedSeason);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result(
            $rID,
            $rFirst,
            $rLast,
            $rJersey,
            $rPosition,
            $rClass
        );
        while( $stmt->fetch() ){
            $rosterRows[] = [
                'ID'            => $rID,
                'name_first'    => $rFirst,
                'name_last'     => $rLast,
                'jersey_number' => $rJersey,
                'position'      => $rPosition,
                'class'         => $rClass,
            ];
        }
        $stmt->close();
    }

    // Get League Teams
    $leagueRows  = [];
    $leagueError = '';

    $scheduleQuery = "SELECT DISTINCT
                        ID,
                        team_name,
                        head_coach,
                        conference,
                        city,
                        state
                      FROM LeagueTeam
                      ORDER BY is_csuf DESC, team_name ASC";


    if( ($stmt = $db->prepare($scheduleQuery)) === FALSE ){
        $scheduleError = 'Schedule query failed: ' . $db->error;
    }
    else{
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result(
            $lID, 
            $lTeamName, 
            $lHeadCoach, 
            $lConference, 
            $lCity, 
            $lState
        );
        while( $stmt->fetch() ){
            $leagueRows[] = [
                'ID'         => $lID,
                'team_name'  => $lTeamName,
                'head_coach' => $lHeadCoach,
                'conference' => $lConference,
                'city'       => $lCity,
                'state'      => $lState,
            ];
        }
        $stmt->close();
    }

    require_once('views/landing_view.php');
?>