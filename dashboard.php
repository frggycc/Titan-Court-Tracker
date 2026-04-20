<?php
    require_once('StartSession.php');

    // Redirect unauthorzed users to login page
    if(!authenticatedUser()){
        header('Location: login.php');
        exit;
    }

    // Set role and username
    $role = $_SESSION['UserRole'];
    $userName = $_SESSION['UserName'];

    require_once('dashboard_view.php');
?>