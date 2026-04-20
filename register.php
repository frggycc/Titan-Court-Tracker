<?php
    require_once('StartSession.php');

    // Go to dashboard if already logged in
    if( authenticatedUser() ){
        header('Location: dashboard.php');
        exit;
    }

    $registerError   = '';
    $registerSuccess = '';

    if( isset($_POST['action']) && $_POST['action'] === 'register' ){

        // Sanitize user input
        $firstName = trim( preg_replace("/\t|\R/", ' ', $_POST['first_name']) );
        $lastName  = trim( preg_replace("/\t|\R/", ' ', $_POST['last_name']) );
        $email     = trim( preg_replace("/\t|\R/", ' ', $_POST['email']) );
        $userName  = strtolower( trim( preg_replace("/\t|\R/", ' ', $_POST['username']) ) );
        $password  = trim( $_POST['password'] );
        $confirm   = trim( $_POST['confirm_password'] );

        // Checking validity of input
        $valid = TRUE;

        if(empty($lastName) || empty($userName) || empty($password) || empty($confirm)){
            $registerError = "Last name, username, and password are required.";
            $valid = FALSE;
        }
        else if(strlen($userName) < 3){
            $registerError = "Username length must be at least 3 characters.";
            $valid = FALSE;
        }
        else if($password !== $confirm){
            $registerError = "Passwords do not match.";
            $valid = FALSE;
        }

        if($valid){
            $query = "INSERT INTO UserLogin
                      SET name_first = ?,
                          name_last  = ?,
                          email      = ?,
                          username   = ?,
                          password   = ?";
            

            if( ($stmt = $db->prepare($query)) === FALSE ){
                $registerError = "Registration failed. Please try again.";
            }
            else{
                // Hash the password before storing
                if( ($stmt->bind_param('sssss',
                    $firstName,
                    $lastName,
                    $email,
                    $userName,
                    password_hash($password, PASSWORD_DEFAULT)
                    )) === FALSE ){
                    $registerError = "Registration failed. Please try again.";
                }
                // Registration worked; redirect to login
                else if( $stmt->execute() && $stmt->affected_rows === 1 ){    
                    header('Location: index.php?registered=1');
                    exit;
                }
                else{
                    $registerError = "Username already exists.";
                }
        
                $stmt->close();
            }
        }
    }
    // Load frontend
    require_once('register_view.php');
?>