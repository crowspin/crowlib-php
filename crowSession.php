<?php

namespace crow;

/**
 * If crowLib's debug mode flag is set true, then enables (highly insecure) transmission
 * of error data to end user. Enforces HTTPS, session 'strict mode', and secure session
 * cookies. Then starts session using options from crowLib's `GLOBALS.php`.
 * @return bool Returns `True` if a session was successfully started, otherwise `False`.
 */
function openSession(): bool {
    if (CROWLIB_DEBUG_MODE){
        ini_set('display_errors', 1); 
        error_reporting(E_ALL|E_STRICT);
    }

    if (empty($_SERVER['HTTPS'])) xPHP::redirect(AppData::$_['ROOT_URL']);
    else header("Strict-Transport-Security: max-age=16070400; includeSubDomains;");

    if (session_status() != PHP_SESSION_NONE) return;

    ini_set("session.use_strict_mode", "1");
    ini_set("session.cookie_secure", "1");
    return session_start(AppData::$_['xAuth']['SESSION_OPTIONS']);
}

/**
 * Summary of crow\closeSession
 * @return void
 */
function closeSession(): void {

}