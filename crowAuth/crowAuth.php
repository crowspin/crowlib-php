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
 */