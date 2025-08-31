<?php

namespace crow\Auth;

function tryLogin($username, $password, $database_id = 0){
    #if you can't get the database, error out of session
    #query database for username
        #if query fails, error out of session, push note in log.
        #if not found, error out of session, push note in log.
}


/**
 * The login process should be:
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
 * Want to add environment variable support
 * Want to add "Remember me" that will stow an SLI-like cookie with uuid to compare against database in place of alternate 2FA authenticator
 * Thinking about a table builder class, had something like that in the big-kid project. Not sure how serious that needs to be given that I could just have a script with a collection of 'queries' (statements.)
 * Want to go around and fix case for all classes, functions, and variables. I like pascal for classes, camel for functions, and snake for variables, and then constants are just all-caps.
 */