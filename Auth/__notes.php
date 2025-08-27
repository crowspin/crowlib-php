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
 */