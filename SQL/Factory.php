<?php

namespace crow\SQL;

require_once __DIR__ . "/../GLOBALS.php";
require_once __DIR__ . "/Connection.php";

/** A factory for SQLConnection objects. Offers functions for registering and deregistering credentials, and additional logic handling for verification of connection status. */
class Factory {
    /** @var Connection[] $_Inst Contains all SQLConnection instances. */
    private static array $_Inst;

    /** @var array[] $_creds Contains all SQLConnection credentials in same fashion as $_SESSION['SQL'], but without persistence. */
    private static array $_Creds;

    /** @var callable[] $_Logic contains callable function references which determine conneciton validity. */
    private static array $_Logic;

    /**
     * First, checks that credentials are stored in memory. If they aren't, but the $handle is "default", then attempts to
     * load them from outside the document root. If they still aren't available, returns false. Knowing that we have credentials, 
     * we then check if the Connection has already been made. If it has, then we test to ensure validity before returning it. If
     * it hasn't, then we make a new one using the available credentials.
     * @param mixed $handle Hashable used to refer to your Connection.
     * @return bool|Connection Either False, or a valid Connection.
     */
    public static function get($handle = "default"): false | Connection {
        $creds = Factory::findCredentials($handle);
        if (!$creds) {
            if ($handle != "default") return false;
            
            if ($creds = Factory::tryLoadingENVCreds()) 
                Factory::registerCredentials($handle, $creds, true);
            else if ($creds = Factory::tryLoadingINICreds())
                Factory::registerCredentials($handle, $creds, true);
            else return false;
        }

        if (isset(Factory::$_Inst[$handle])){
            if (!Factory::testLogic($handle)) return false;
            if (!Factory::ping($handle)) return false;
            return Factory::$_Inst[$handle];
        } else {
            $connection = new Connection($creds);
            if ($connection->conn_errno == 0){
                Factory::$_Inst[$handle] = $connection;
                return Factory::$_Inst[$handle];
            } else return false;
            
        }
    }

    /**
     * Stores SQL Connection credentials in memory.
     * @param mixed $handle Hashable you will use to reference these credentials and the Connection related the them.
     * @param string[] $credentials An array of four named values:  
     * > "hostname" => Host address of database connection,  
     * > "username" => Username,  
     * > "password" => Password,  
     * > "database" => Name of database
     * @param bool $storeInSession If true, credentials will be placed in session and made available across pageloads. False will store the credentials locally and they will be lost when execution ends.
     * @return string $handle
     */
    public static function registerCredentials($handle, $credentials, $storeInSession): string {
        if ($storeInSession) $_SESSION['SQL'][$handle] = $credentials;
        else Factory::$_Creds[$handle] = $credentials;
        return $handle;
    }

    /**
     * Looks in both possible locations for database credentials, and checks that all fields are not empty.
     * Though it should be avoided, if credentials exist in both positions, the non-session copy will be prioritized.
     * @param mixed $handle Hashable used to refer to your Connection.
     * @return array Array containing credentials for Connection
     */
    public static function findCredentials($handle): array {
        if (!empty(Factory::$_Creds[$handle])){
            $creds = Factory::$_Creds[$handle];
        } else if (!empty($_SESSION['SQL'][$handle])){
            $creds = $_SESSION['SQL'][$handle];
        } else return [];

        if (empty($creds['hostname'])
            || empty($creds['username'])
            || empty($creds['password'])
            || empty($creds['database'])
        ) return [];
        else return $creds;
    }

    /**
     * Calls `unset()` on storage locations for credentials.
     * @param mixed $handle Hashable used to refer to your Connection.
     * @return void
     */
    public static function clearCredentials($handle): void {
        unset($_SESSION['SQL'][$handle], Factory::$_Creds[$handle]);
    }

    /**
     * Stores a callable for custom logic to be applied to an `Factory::get` operation.  
     * This callable is not validated, only stored. It must return a boolean, or a value that can
     * be evaluated as such, and it must not need any arguments.  
     * The callable will be executed via. `call_user_func`, and if the return value is false, we will assume the
     * connection is invalid, causing `Factory::get` to return false without any further evaluation
     * of the connection itself. No changes will be made to the connection.
     * @param mixed $handle The hashable handle used to refer to your Connection.
     * @param callable $logic The callable object to be stored.
     * @return void
     */
    public static function registerLogic($handle, &$logic): void {
        Factory::$_Logic[$handle] = $logic;
    }

    /**
     * Executes `call_user_func` on stored logical function to determine arbitrary validity flag for Connection.
     * @param mixed $handle Hashable used to refer to your Connection.
     * @return bool True if 'connection is valid', False if logic was set and returns false.
     */
    public static function testLogic($handle): bool {
        if (isset(Factory::$_Logic[$handle])){
            return call_user_func(Factory::$_Logic[$handle]);
        } else return true;
    }

    /**
     * Clears your logic callable from memory.
     * @param mixed $handle Hashable used to refer to your Connection.
     * @return void
     */
    public static function unsetLogic($handle): void {
        unset(Factory::$_Logic[$handle]);
    }
    
    /**
     * Calls the Connection's ping() function. If the response is false, attempts to reset the connection before returning.
     * @param mixed $handle Handle of Connection being tested.
     * @return bool True only if the Connection is still good.
     */
    public static function ping($handle): bool {
        if (Factory::$_Inst[$handle]->ping()) return true;
        
        $creds = Factory::findCredentials($handle);
        if ($creds == []) return false;

        $conn = new Connection($creds);
        if ($conn->conn_errno == 0){
            Factory::$_Inst[$handle] = $conn;
            return true;
        } else return false;
    }

    /**
     * Closes and clears the Connection and it's associated logic from memory. Does not remove credentials.
     * @param mixed $handle
     * @return void
     */
    public static function destroy($handle): void {
        Factory::unsetLogic($handle);
        Factory::$_Inst[$handle]->close();
        unset(Factory::$_Inst[$handle]);
    }

    /**
     * Attempts to load database credentials from the INI file just outside the webroot.
     * @return array|false False if load fails or credentials incomplete, otherwise a string-keyed array of credentials.
     */
    private static function tryLoadingINICreds(): mixed {
        $configPath = \crow\DOCROOT . '/../defaultsql.ini';
        if (!file_exists($configPath)) {
            return false;
        }
        $dbSettings = parse_ini_file($configPath, true);
        if (
            !$dbSettings
            || !$dbSettings['default']
            || !$dbSettings['default']['hostname']
            || !$dbSettings['default']['username']
            || !$dbSettings['default']['password']
            || !$dbSettings['default']['database']
        ){
            return false;
        }

        return $dbSettings['default'];
    }

    /**
     * Attempts to load database credentials from environment variables. Likely set in webserver's \etc\environment or \etc\profile.d.
     * @return array|false False if environment variables are not set or empty, otherwise a string-keyed array of credentials.
     */
    private static function tryLoadingENVCreds(): mixed {
        $h = getenv(strtoupper(\crow\APPNAME) . '_DB_HOSTNAME');
        $u = getenv(strtoupper(\crow\APPNAME) . '_DB_USERNAME');
        $p = getenv(strtoupper(\crow\APPNAME) . '_DB_PASSWORD');
        $d = getenv(strtoupper(\crow\APPNAME) . '_DB_DATABASE');

        if (!$h || !$u || !$p || !$d) return false;
        else return ['hostname'=>$h, 'username'=>$u, 'password'=>$p, 'database'=>$d];
    }
}