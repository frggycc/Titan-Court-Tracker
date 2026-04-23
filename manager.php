<?php
    require_once('StartSession.php');

    // // Comment out to see errors
    // ini_set('display_errors', 1);
    // error_reporting(E_ALL);

    if( !authenticatedUser() ){
        header('Location: login.php');
        exit;
    }

    $role     = $_SESSION['UserRole'];
    $userName = $_SESSION['UserName'];

    require_once('manager_view.php');
 
?>
 
