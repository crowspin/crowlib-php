<?

require_once "../f/example.php";

class crowAuth {
    public static function tryLogin($username, $password, $database_id = 0){
        #if you can't get the database, error out of session
        #query database for username
            #if query fails, error out of session, push note in log.
            #if not found, error out of session, push note in log.
        crowLib\example();
    }
}

/**
 * This refactor is going to be a bigger headache than I thought. I was going to go file by file, but
 * that was before I remembered how monolithic this library was. I'm already getting a headache, and I've
 * barely dipped my toes into it.
 * 
 * I think the best call is for me to go operation by operation instead and bring pieces in one at a time in
 * the appropriate manner. So then my plan is going to be to inject this app as a submodule of the task-tree
 * project first, and then build the website and the library at the same time.
 * 
 * The basic login process should be:
 *  Server:
 *      Start Session
 *      (?) Test for cert-login
 *      Display Login Screen
 * 
 *  User:
 *      Submit form containing
 *          Username
 *          Password
 *          (?) Toggle (Stay Logged In)
 * 
 *  Server:
 *      Identify presence of form data
 *      Test Form Data against database
 *      (?) Display 2FA Screen
 * 
 *  User:
 *      (?) Submit 2FA code
 * 
 *  Server:
 *      (?) Test 2FA code
 *      (?) Set (Stay Logged In) cookie
 *      Set user data in session memory
 * 
 *  Secure Credential Storage: first, test for Apache VirtualHost-set values, then check for an ini just outside the webroot.
 *  example ini:
 * 
        [database]
            host = localhost
            dbname = mydatabase
            username = myuser
            password = 'your_secure_password'
 *
        $configPath = __DIR__ . '/../config/db_config.ini'; // Adjust path as needed
        if (!file_exists($configPath)) {
            die("Configuration file not found.");
        }
        $dbSettings = parse_ini_file($configPath, true);
        if (!$dbSettings) {
            die("Failed to parse configuration file.");
        }
        $dbHost = $dbSettings['database']['host'];
        $dbName = $dbSettings['database']['dbname'];
        $dbUser = $dbSettings['database']['username'];
        $dbPass = $dbSettings['database']['password'];
 * 
        <VirtualHost *:80>
            ServerName yourdomain.com
            DocumentRoot /var/www/html

            # Define PHP values for database connection
            php_value mysql.default_user myusername
            php_value mysql.default_password mypassword
            php_value mysql.default_host localhost

            # Other configurations...
        </VirtualHost>
 * 
        // PHP will use the defaults set in Apache configuration
        $db = mysqli_connect();
        // Or explicitly:
        // $db = mysqli_connect(ini_get("mysql.default_user"), ini_get("mysql.default_password"), ini_get("mysql.default_host"));

        if (!$db) {
            die("Connection failed: " . mysqli_connect_error());
        }
 * 
 * Other thing I need to consider is login method. Will we do standard html forms and post/get type stuff, or will we do something new
 * and finally implement a JS XMLHttpRequest background communication system?
 * Will we send this project off with our notification system baked in, or save that off for speed's sake?
 * This is a good time to remember the note in task-tree's readme.
 */