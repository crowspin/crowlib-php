<?php

namespace crow\login;

/**
* Scrubs login form values (Contract, Username), ensuring they don't have leading or trailing spaces, and don't contain any HTML or XSS nastiness.
* @param string $str The value to be cleaned.
*/
function scrub_TBX($str): string{
    return htmlentities(trim($str), ENT_QUOTES);
}