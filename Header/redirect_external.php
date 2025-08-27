<?php

namespace crow\Header;

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