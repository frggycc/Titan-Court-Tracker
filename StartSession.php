<?php
    session_start();
    require_once('Adaptation.php');

    // Return true if current session = authenticated user
    function authenticatdUser(){
        global $DBPasswords;
        return isset($_SESSION['UserName']) && !empty($_SESSION['UserName']) &&
               isset($_SESSION['UserRole']) && !empty($_SESSION['UserRole']) &&
               isset($DBPasswords[$_SESSION['UserRole']]);
    }

    // Connect as authenticated user's DB role or the deafult "NO_ROLE"
    if (authenticatdUser()){
        $DBName = $_SESSION['UserRole'];
    }
    else{
        $DBName = NO_ROLE;
    }

    $DBPassword = $DBPasswords[$DBName];

    printf("Connecting to DB as '%s'/'%s'<br/>", $DBName, $DBPassword);
    $db = new mysqli(DATA_BASE_HOST, $DBName, $DBPassword, DATA_BASE_NAME);

    // Check if connection successful
    if ($db->connect_errno != 0){
        echo "Error: Failed to make a succesful MySQL connections: " . $db->connect_error . "</br>";
        return -1;
    }
?>