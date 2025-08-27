<?php

namespace crow;

require_once __DIR__ . "GLOBALS.php";

/**
 * If crowLib's debug mode flag is set true, then enables (highly insecure) transmission
 * of error data to end user. Enforces HTTPS, session 'strict mode', and secure session
 * cookies. Then starts session using options from crowLib's `GLOBALS.php`.
 * @return bool Returns `True` if a session was successfully started or if session was started previously, otherwise `False`.
 */
function openSession(): bool {
    if (DEBUG_MODE){
        ini_set('display_errors', 1); 
        error_reporting(E_ALL|E_STRICT);
    }

    if (empty($_SERVER['HTTPS'])) redirect();
    else header("Strict-Transport-Security: max-age=16070400; includeSubDomains;");

    if (session_status() != PHP_SESSION_NONE) return true;

    ini_set("session.use_strict_mode", "1");
    ini_set("session.cookie_secure", "1");
    return session_start(SESSION_OPTIONS);
}

/**
 * Deletes all session variables, clears the session and 
 * cookies, then redirects user to login.
 * @return never
 */
function closeSession($destination = "/login"): never {
    $_SESSION = [];
    session_destroy();
    //! ... xAuth line rel. PHPEXP (value unclear)
    redirect($destination);
}

/**
 * A shorthand so I can forget about needing to exit() after a header() redirect.
 * Supplied path MUST use a leading slash unless intent is to redirect to root index.
 * @param string $path The URI where users should be redirected to.
 * @return never
 */
function redirect($path = ""): never {
    //! xPHP::log("-> `$uri`");
    if ($path != "/" && $path[0] != '/'){
        $path = "";
        //! log misuse
    }
    header("Location: " . HOSTNAME . $path);
    exit();
}

/**
 * An alternate shorthand so I can forget about needing to exit() after a header() redirect.
 * We have this second redirect option because the first was adjusted to accept local paths while assuming use of `crow\HOSTNAME`.
 * @param string $uri The URI where users should be redirected to.
 * @return never
 */
function redirect_external($url): never {
    //! xPHP::log("-> `$uri`");
    header("Location: $url");
    exit();
}