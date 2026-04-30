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

    define('MANAGER_VIEW_LOADED', true);
    require_once('views/manager_view.php');
 
?>
 
