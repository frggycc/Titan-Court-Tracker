<?php
    require_once('StartSession.php');

    // // Comment out to see errors
    // ini_set('display_errors', 1);
    // error_reporting(E_ALL);

    if( !authenticatedUser() ){
        header('Location: login.php');
        exit;
    }
    if( $_SESSION['UserRole'] !== 'Executive Manager' ){
        header('Location: landing.php');
        exit;
    }

    $role     = $_SESSION['UserRole'];
    $userName = $_SESSION['UserName'];

    // Query all users and their user information
    $userRows = [];
    $userError = "";

    $userQuery = "SELECT
                ul.username,
                ul.name_first,
                ul.name_last,
                ul.email,
                ul.role,
                r.role_name
              FROM UserLogin ul
              JOIN Roles r ON ul.role = r.ID
              ORDER BY r.ID ASC, ul.username ASC";

    $stmt = $db->prepare($userQuery);
    if ($stmt === FALSE){
        $userError = "Could not load users.";
    }
    else{
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result(
            $uUsername, 
            $uFirst, 
            $uLast, 
            $uEmail,
            $uRoleID, 
            $uRoleName
        );

        while($stmt->fetch()){
            $userRows[] = [
                'username'   => $uUsername,
                'name_first' => $uFirst,
                'name_last'  => $uLast,
                'email'      => $uEmail,
                'role_id'    => $uRoleID,
                'role_name'  => $uRoleName,
            ];
        }
        $stmt->close();
    }

    define('MANAGER_VIEW_LOADED', true);
    require_once('views/manager_view.php');
 
?>
 
