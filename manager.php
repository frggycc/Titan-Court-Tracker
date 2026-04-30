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
    $errorMessage = "";
    $successMessage = "";

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

    // All roles for the dropdown menu
    $roleRows = [];
    $rolesError = "";
    
    $roleQuery = "SELECT ID, role_name
                  FROM Roles
                  ORDER BY ID ASC";
    $stmt = $db->prepare($roleQuery);
    
    if( $stmt === FALSE ){
        $rolesError = "Could not load roles";
    }
    else{
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($rID, $rName);

        while( $stmt->fetch() ){
            $roleRows[] = [
                'ID' => $rID, 
                'role_name' => $rName
            ];
        }
        $stmt->close();
    }


    // Change a user's role
    if( isset($_POST['action']) && $_POST['action'] === 'change_role'){
        $targetUser = trim($_POST['target_username']);
        $newRoleID = (int)$_POST['new_role_id'];

        // MANAGER CANNOT CHANGE OWN ROLE
        if($targetUser === $userName){
            $errorMessage = "You cannot change your own role.";
        }
        else if(empty($targetUser) || $newRoleID <= 0){
            $errorMessage = "Invalid user or role.";
        }
        else{
            $updateQuery = "UPDATE UserLogin
                            SET role = ?
                            WHERE username = ?";

            $stmt = $db->prepare($updateQuery);
            if($stmt === FALSE){
                $errorMessage = "Role update failed.";
            }
            else{
                $stmt->bind_param('is', $newRoleID, $targetUser);
                $stmt->execute();

                if($stmt->affected_rows === 1){
                    $successMessage = "Role updated successfully.";
                }
                else{
                    $errorMessage = "Role update failed.";
                }

                $stmt->close();
            }
        }
    }

    define('MANAGER_VIEW_LOADED', true);
    require_once('views/manager_view.php');
 
?>
 
