<?php
    define('DATA_BASE_NAME', 'TitanCourtTracker');
    define('DATA_BASE_HOST', '127.0.0.1');

    // Default role to use when not logged in, such as to register a new user or to get end-user credentials
    define('NO_ROLE', 'Executive Manager');

    $DBPasswords = [
        'Observer'          => 'TCT_Observer!',
        'Users'             => 'TCT_Users!',
        'Executive Manager' => 'TCT_Manager!'
    ];
?>