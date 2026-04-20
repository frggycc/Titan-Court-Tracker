<?php
    require_once('StartSession.php');

    // Go to dashboard if already session
    if (authenticatdUser()){
        header('Location: Dashboard.php');
        exit;
    }
    
    // For frontend and testing
    $loginError = '';
    $registerError = '';
    $registerSuccess = '';

    // Sanitize; all lower and no trailing spaces
    $userName = strtolower( trim( preg_replace("/\t|\R/", ' ', $_POST['username'])));
    $password = trim( $_POST['password']);

    // If if one field not filled
    if( empty($userName) || empty($password)){
        $loginError = "Login attempt failed.";
    }

    // Go through various error checks; Eventually change all $loginError to 'Login attempt failed."
    $query = "SELECT Roles.role_name, UserLogin.password 
              FROM UserLogin, Roles 
              WHERE UserLogin.username = ? AND UserLogin.role = Roles.ID";
    
    if(($stmt = $db->prepare($query)) === FALSE){
        $loginError = "Error: failed to prepare query: ". $db->error . "<br/>";
    }
    if(($stmt->bind_param('s', $userName)) === FALSE){
        $loginError = "Error: failed to bind query parameters to query: ". $db->error . "<br/>";
    }
    // Execute stored correctly but no instance found of username
    if(!($stmt->execute() && $stmt->store_result() && $stmt->num_rows === 1)){
        $loginError = "Failure: existing user '$userName' not found<br/>";
    }
    // See if can bind results to local variables
    if(($stmt->bind_result($roleName, $PWHash)) === FALSE){
        $loginError = "Error: failed to bind query results to local variables: ". $db->error . "<br/>";
    }
    if(($stmt->fetch()) === FALSE){
        $loginError = "Error: failed to fetch query results: ". $db->error . "<br/>";
    }

    // Now validating password/usernames
    $stmt->fetch();
    if(!password_verify($password, $PWHash)){
        $loginError = "Incorrect credentials.";
    }
    else{
        // Session assignments
        $_SESSION['UserName'] = $userName;
        $_SESSION['UserRole'] = $roleName;

        $stmt->close();

        /*******************************
         * TODO: Update UserLogin table
         * ****************************/
    }

    // Register accounts; Sanitize information
    $firstName = trim(preg_replace("/\t|\R/", '', $_POST['first_name']));
    $lastName  = trim(preg_replace("/\t|\R/", '', $_POST['last_name']));
    $email     = trim(preg_replace("/\t|\R/", '', $_POST['email']));
    $username  = strtolower(trim(preg_replace("/\t|\R/", '', $_POST['username'])));
    $password  = trim($_POST['password']);
    $confirm   = trim($_POST['confirm_password']);

    // Confirm that user input valid and all required parts of form are filled
    $valid = TRUE;

    if(empty($lastName) || empty($userName) || empty($password) || empty($confirm)){
        $registerError = "Last name, username, and password are required.";
        $valid = FALSE;
    }
    else if(strlen($userName) < 3){
        $registerError = "Username must be at least 3 character.";
        $valid = FALSE;
    }
    else if($password !== $confirm){
        $registerError = "Passwords do not match.";
        $valid = FALSE;
    }

    // Register user if all entries valid
    if($valid){
        $query = "INSERT INTO UserLogin
                  SET name_first = ?,
                      name_last  = ?,
                      email      = ?,
                      username   = ?,
                      password   = ?";
        
    }

    // Go through various query error checks; Eventually change all $registerError to 'Registration failed."
    if(($stmt = $db->prepare($query)) === FALSE){
        $registerError = "Error: Failed to prepare query.";
    }
    if(($stmt->bind_param('sssss', $firstName, $lastName, $eMail, $userName, password_hash($password, PASSWORD_DEFAULT))) === FALSE ){
        $registerError = "Error: Failed to bind query parameters to query";
    }

    // No query errors; REgister OR user already exists
    if($stmt->execute() && $stmt->affected_rows === 1){
        $registerSuccess = "Account created successfully. You may now log in.";
    }
    else{
        $registerError = "Username already exists. Choose another username.";
    }

    $stmt->close();

    /**************************************
     * TODO: Complete simple frontend page
     *************************************/
    require_once('index_view.php');
?>