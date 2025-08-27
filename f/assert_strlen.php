<?php

namespace crow;

/**
 * Confirms string length is between `$min` and `$max`, **inclusive**.
 * @param string $string The string to have it's length checked.
 * @param int $min The minimum length of the string
 * @param int $max The maximum length of the string
 */
function assert_strlen($string, $min = 0, $max = 0): bool {
    $stlen = strlen($string);
    if ($stlen > $max || $stlen < $min) return false;
    else return true;
}