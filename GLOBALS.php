<?php
//Set true for debug output, logging. Insecure, not for deployment!
const CROWLIB_DEBUG_MODE = true;

/**
 * Storage for predefined application data, like name and version.
 * HOSTNAME uses no trailing slash, DOCROOT is alias for $_SERVER['DOCUMENT_ROOT']
 * APPNAME and VERSION relate to the app this will be used in (for logging I think.)
 */
class AppData {
    public static string $HOSTNAME = ""; //No trailing slash!
    public static string $DOCROOT = $_SERVER['DOCUMENT_ROOT'];
    public static string $APPNAME = "";
    public static string $VERSION = "";
}

/**
 * Storage for application configuration data.
 * Has only one member `$_`, which is an array.
 * The intention is that `core` should be filled with all possible config keys, and 
 * should be observed as the 'default' settings, to be copied/referenced/
 * compared against.
 */
class DefaultConfig { public static array $_ = array(); }
    DefaultConfig::$_['core'] = [
        'timezone' => 'America/Vancouver'
    ];

/**
 * Storage for Error Messages.
 * Has only one member `$_`, which is an array. It is keyed using integers, and holds
 * error messages for the application. The intention is that 0-99 are to be reserved
 * for use only by CrowLib, and the application may use any code outside that scope.
 */
class ErrorMsg { public static array $_ = array(); }
    ErrorMsg::$_ = [
    //      0-9 => Basic Login Errors
         0 => "There was an error while fetching your account data, please try again.",
         1 => "There was a problem reading your login credentials. Please try again.",
         2 => "Your username and password did not match our records. Please try again.",
    //      10-19 => PHP Session Expiry Errors
        10 => "Your session was about to time out, so we saved your data and have logged you out. Please sign in again!",
    //      20-29 => SLI Auto-Relog Errors  
        20 => "SLI: Session has timed out. You have been logged out.",
        21 => "SLI: A communication error occurred while preparing your new access code. You have been logged out.",
    //      30-39 => iPage errors
        30 => "You don't seem to be logged in. Please refresh the page and log in again!",
        31 => "You don't have permission to view this page!",
    //      40-49 => Contract Settings Errors
        40 => "The specified contract ID was not found!"
    ];
