<?php
    require_once('StartSession.php');

    // Go to dashboard if already logged in
    if( authenticatedUser() ){
        header('Location: dashboard.php');
        exit;
    }

    $loginError      = '';
    $registerSuccess = isset($_GET['registered']) ? 'Account created successfully. You can now log in.' : '';

    if( isset($_POST['action']) && $_POST['action'] === 'login' ){

        // Sanitize user input
        $userName = strtolower( trim( preg_replace("/\t|\R/", ' ', $_POST['username']) ) );
        $password = trim( $_POST['password'] );

        if( empty($userName) || empty($password) ){
            $loginError = 'Login attempt failed.';
        }
        else{
            $query = "SELECT Roles.role_name, UserLogin.password
                      FROM UserLogin, Roles
                      WHERE UserLogin.username = ?  AND
                            UserLogin.role     = Roles.ID";

            if( ($stmt = $db->prepare($query)) === FALSE ){
                $loginError = "Login attempt failed.";
            }
            else if( ($stmt->bind_param('s', $userName)) === FALSE ){
                $loginError = "Login attempt failed.";
            }
            else if( !($stmt->execute() && $stmt->store_result() && $stmt->num_rows === 1) ){
                $loginError = "Login attempt failed.";
            }
            else if( ($stmt->bind_result($roleName, $PWHash)) === FALSE ){
                $loginError = "Login attempt failed.";
            }
            else if( ($stmt->fetch()) === FALSE ){
                $loginError = "Login attempt failed.";
            }
            else if( !password_verify($password, $PWHash) ){
                $loginError = "Login attempt failed.";
            }
            else{
                // Login successful; Set session variables
                $_SESSION['UserName'] = $userName;
                $_SESSION['UserRole'] = $roleName;

                $stmt->close();

                // Update last_login 
                $updateQuery = "UPDATE UserLogin
                                SET    last_login = NOW()
                                WHERE  username   = ?";

                if( ($updateStmt = $db->prepare($updateQuery)) !== FALSE ){
                    $updateStmt->bind_param('s', $userName);
                    $updateStmt->execute();
                    $updateStmt->close();
                }

                // Insert login into LoginHistory table
                $idQuery = "SELECT ID 
                            FROM UserLogin 
                            WHERE username = ?";

                if( ($idStmt = $db->prepare($idQuery)) !== FALSE ){
                    $idStmt->bind_param('s', $userName);
                    $idStmt->execute();
                    $idStmt->store_result();
                    $idStmt->bind_result($userID);
                    $idStmt->fetch();
                    $idStmt->close();

                    $histQuery = "INSERT INTO LoginHistory (user_id, login_time) 
                                  VALUES (?, NOW())";

                    if( ($histStmt = $db->prepare($histQuery)) !== FALSE ){
                        $histStmt->bind_param('i', $userID);
                        $histStmt->execute();
                        $histStmt->close();
                    }
                }

                header('Location: landing.php');
                exit;
            }

            if( isset($stmt) ) $stmt->close();
        }
    }

    require_once('login_view.php');
?>