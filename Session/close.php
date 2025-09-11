<?php

namespace crow\Session;

require_once __DIR__ . "/../Header/redirect.php";

/**
 * Deletes all session variables, clears the session and 
 * cookies, then redirects user to login.
 * @return never
 */
function close($destination = "/login.php"): never {//! Had to add ".php" because there isn't a guaruntee a deployment of the library will use htaccess for extension stripping
    if (session_status() == PHP_SESSION_NONE){
        require_once __DIR__ . "/open.php";//! Want to do more conditional includes in the library where possible. Optimally, I want to avoid including anything unneccesary. Hence the separation of functions across files...
        \crow\Session\open();
    }
    $_SESSION = [];
    session_destroy();
    //! ... xAuth line rel. PHPEXP (value unclear)
    \crow\Header\redirect($destination);
}