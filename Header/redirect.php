<?php

namespace crow\Header;

require_once __DIR__ . "/../GLOBALS.php";

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
    header("Location: " . \crow\HOSTNAME . $path);
    exit();
}