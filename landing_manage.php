<?php
    require_once('StartSession.php');

    if( !authenticatedUser() ){
        header('Location: login.php');
        exit;
    }

    // Only Users and Executive Managers can access this page
    if( $_SESSION['UserRole'] !== 'Users' && $_SESSION['UserRole'] !== 'Executive Manager' ){
        header('Location: landing.php');
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

    $successMessage = '';
    $errorMessage   = '';

    // Get players for edit/display
    $playerRows = [];
    $playerError = '';

    $playerQuery = "SELECT
                        ID,
                        name_first,
                        name_last,
                        jersery_number,
                        position,
                        class
                    FROM Player
                    ORDER BY jersery_number ASC";
    
    $stmt = $db->prepare($playerQuery);
    if ($stmt === FALSE){
        $playerError = "Player query failed.";
    }
    else{
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result(
            $pID,
            $pFirst,
            $pLast,
            $pJersery,
            $pPosition,
            $pClass
        );
        while($stmt->fetch()){
            $playerRows[] = [
                'ID' => $pID,
                'name_first' => $pFirst,
                'name_last' => $pLast,
                'jersey_number' => $pJersery,
                'position' => $pPosition,
                'class' => $pClass,
            ];
        }

        $stmt->close();
    }

    // Get League Team 
    $leagueRows  = [];
    $leagueError = '';
    
    $teamsQuery = "SELECT ID, team_name, head_coach, conference, city, state
                   FROM   LeagueTeam
                   ORDER BY is_csuf DESC, team_name ASC";
    $stmt = $db->prepare($teamsQuery);
    if( $stmt === FALSE ){
        $leagueError = 'League query failed: ' . $db->error;
    }
    else{
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($lID, $lTeamName, $lHeadCoach, $lConference, $lCity, $lState);
        while( $stmt->fetch() )
        $leagueRows[] = [
            'ID'         => $lID,   'team_name'  => $lTeamName,
            'head_coach' => $lHeadCoach, 'conference' => $lConference,
            'city'       => $lCity, 'state'      => $lState,
        ];
        $stmt->close();
    }

    // Add League Team
    if( isset($_POST['action']) && $_POST['action'] === 'add_league' )
    {
        $teamName   = trim( preg_replace("/\t|\R/", ' ', $_POST['team_name'])   );
        $headCoach  = trim( preg_replace("/\t|\R/", ' ', $_POST['head_coach'])  );
        $conference = trim( preg_replace("/\t|\R/", ' ', $_POST['conference'])  );
        $city       = trim( preg_replace("/\t|\R/", ' ', $_POST['city'])        );
        $state      = trim( preg_replace("/\t|\R/", ' ', $_POST['state'])       );

        if( empty($teamName) ){
            $errorMessage = "Team name is required.";
        }
        else{
            $addTeamQuery = "INSERT INTO LeagueTeam 
                             SET
                                team_name  = ?,
                                head_coach = ?,
                                conference = ?,
                                city       = ?,
                                state      = ?,
                                is_csuf    = FALSE";
            $stmt = $db->prepare($addTeamQuery);

            if( $stmt === FALSE ){
                $errorMessage = "Failed to add team.";
            }
            else{
                $stmt->bind_param('sssss', $teamName, $headCoach, $conference, $city, $state);
                $stmt->execute();
                if( $stmt->affected_rows === 1 ){
                    $successMessage = "Team '$teamName' added successfully.";
                }
                else{
                    $errorMessage = "Failed to add team.";
                }
                $stmt->close();
            }
        }
    }

    // Delete League Team
    if( isset($_POST['action']) && $_POST['action'] === 'delete_league' ){
        $teamID = (int)$_POST['team_id'];
        if( $teamID <= 0 ){
            $errorMessage = "Invalid team selected.";
        }
        else{
            $deleteTeamQuery = "DELETE FROM LeagueTeam WHERE ID = ? AND is_csuf = FALSE";
            $stmt = $db->prepare($deleteTeamQuery);
            if( $stmt === FALSE ){
                $errorMessage = "Failed to delete team.";
            }
            else{
                $stmt->bind_param('i', $teamID);
                $stmt->execute();
                if( $stmt->affected_rows === 1 ) {
                    $successMessage = "Team deleted successfully.";
                }
                else {
                    $errorMessage = "Failed to delete team.";
                }
                $stmt->close();
            }
        }
    }
    require_once('views/landing_manage_view.php');
?>