<?php
    require_once('StartSession.php');

    // Go to dashboard if already session
    if (authenticatdUser()){
        header('Location: Dashboard.php');
        exit;
    }
    
    // For frontend
    $loginError = '';

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
    if(($stmt->bind_result($roleName, $PWStored)) === FALSE){
        $loginError = "Error: failed to bind query results to local variables: ". $db->error . "<br/>";
    }
    if(($stmt->fetch()) === FALSE){
        $loginError = "Error: failed to fetch query results: ". $db->error . "<br/>";
    }

    // Now validating password/usernames

?>