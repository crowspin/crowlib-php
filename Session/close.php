<?php

namespace crow\Session;

require_once __DIR__ . "/../Header/redirect.php";

/**
 * Deletes all session variables, clears the session and 
 * cookies, then redirects user to login.
 * @return never
 */
function close($destination = "/login"): never {
    $_SESSION = [];
    session_destroy();
    //! ... xAuth line rel. PHPEXP (value unclear)
    \crow\Header\redirect($destination);
}