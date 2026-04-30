<?php
    require_once('StartSession.php');

    // // Comment out to see errors
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    if( !authenticatedUser() ){
        header('Location: login.php');
        exit;
    }

    $role     = $_SESSION['UserRole'];
    $userName = $_SESSION['UserName'];

    $userInfo = [];
    $userInfoError = "";

    // Get the user info for whoever is currently logged in based on username
    $query = "SELECT
                ul.name_first,
                ul.name_last,
                ul.email,
                ul.last_login,
                ul.ts,
                r.role_name
                
              FROM UserLogin ul
              JOIN Roles r ON ul.role = r.ID
              WHERE ul.username = ?
              LIMIT 1";
    
    $stmt = $db->prepare($query);

    if($stmt === FALSE){
        $userInfoError = "Couldn't load account info.";
    }

    else{
        $stmt->bind_param('s', $userName);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result(
            $uFirst, 
            $uLast,
            $uEmail,
            $uLastLogin,
            $uCreated,
            $uRoleName);
        if( $stmt->fetch()){
            $userInfo = [
                'name_first' => $uFirst,
                'name_last'  => $uLast,
                'email'      => $uEmail,
                'last_login' => $uLastLogin,
                'created'    => $uCreated,
                'role_name'  => $uRoleName,
            ];
        }
        $stmt->close();
    }


    // Change the password after confirming user information
    $errorMessage = "";
    $successMsg = "";

    if(isset($_POST['action']) && $_POST['action'] === 'change_password'){
        $currentPass = trim($_POST['current_password']);
        $newPass     = trim($_POST['new_password']);
        $confirmPass = trim($_POST['confirm_password']);

        // Check if all fields are filled
        if(empty($currentPass) || empty($newPass) || empty($confirmPass)){
            $errorMessage = "All fields are required.";
        }

        // Check if new password and confirmation passwords match
        else if ($newPass !== $confirmPass){
            $errorMessage = "New passwords do not match.";
        }

        // Go to database to check password hash -> Change password
        else{
            $query = "SELECT password
                      FROM UserLogin
                      WHERE username = ?";
            $stmt = $db->prepare($query);

            if($stmt === FALSE){
                $errorMessage = "Could not verify current password.";
            }
            else{
                $stmt->bind_param('s', $userName);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($passwordHash);
                $stmt->fetch();
                $stmt->close();

                // Verify current password hash matches hash in DB
                if(!password_verify($currentPass, $passwordHash)){
                    $errorMessage = "Current password is incorrect.";
                }

                // Password can now be updated
                else{
                    $query = "UPDATE UserLogin
                              SET password = ?
                              WHERE username = ?";
                    $stmt = $db->prepare($query);
                    
                    if($stmt === FALSE){
                        $errorMessage = "Password failed to update.";
                    }
                    else{
                        $newHash = password_hash($newPass, PASSWORD_DEFAULT);
                        $stmt->bind_param('ss', $newHash, $userName);
                        $stmt->execute();

                        if( $stmt->affected_rows === 1 ){
                            $successMsg = "Password updated successfully.";
                        }
                        else{
                            $errorMessage = "Password update failed.";
                        }

                        $stmt->close();
                    }
                }
            }
        }
    }

    require_once('views/account_view.php');
 
?>
 
