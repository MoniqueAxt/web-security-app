<?php
/**
 * Class Member
 *
 * @brief   Handles functionality related to the data of members in the database
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

class Member {
    /** Checks if a specific username already exists in the database
     * @param $usernameToAdd
     * @return bool
     */
    public static function usernameAlreadyExists($usernameToAdd): bool
    {
        $queryUserExists = "SELECT EXISTS (SELECT 1 FROM public.members WHERE username = :usernameToAdd)";
        $variables = [':usernameToAdd' => $usernameToAdd];

        $dbConnection = DatabaseConnection::getInstance();
        $result = $dbConnection->connectAndSendPreparedQuery($queryUserExists, $variables);

        // query successful
        if ($result) {
            $existsFetch = $result->fetch(PDO::FETCH_OBJ);
            // username already exists in the database
            if ($existsFetch->exists === true) {
                return true;
            }
            // username is NOT already in the database
            else {
                return false;
            }
        }
        error_log("SQL query was unsuccessful");
        return true;    // query unsuccessful so assume user already exists
    }

    /** Attempts to add a user to the database.
     * @param $usernameToAdd
     * @param $newPasswordToAdd
     * @return bool true if user added to the database, else false
     */
    public static function addUserToDB($usernameToAdd, $newPasswordToAdd): bool
    {
        // create a hash of the user's password
        $hashedPassword = password_hash($newPasswordToAdd, PASSWORD_DEFAULT);

        $preparedQuery = " INSERT INTO public.members(username, password)
                                        VALUES(:usernameToAdd, :hashedPassword)";
        $variables = ['usernameToAdd' => $usernameToAdd, 'hashedPassword' => $hashedPassword];

        // send complete query
        $dbConnection = DatabaseConnection::getInstance();
        $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);

        if ($result) {
            return true;    // user added

        } else {
            error_log("SQL query was unsuccessful");
        }
        return false;   // user not added
    }


    /** Verifies the username and password of a user against the database data
     * and returns the user's userID
     * @param $username_input
     * @param $password_input
     * @return int | bool userID if log-in was verified, false if not
     */
    static public function verifyLogin($username_input, $password_input)
    {
        $connection =  DatabaseConnection::getInstance();
        $preparedQuery = "SELECT * FROM public.members WHERE username = :username";
        $variables = [':username' => $username_input];
        $result  = $connection->connectAndSendPreparedQuery($preparedQuery, $variables);

        if ($result) {	// successful query
            $userFromDB = $result->fetch(PDO::FETCH_OBJ);

            //  member with the provided username exists
            if ($userFromDB) {

                // password provided is correct
                if(self::verifyPassword($username_input, $password_input)){
                    return $userFromDB->id;
                }

                // member with provided username does NOT exist
            } else if ($userFromDB == null) {
                return false;
            }
        } else	// unsuccessful query
            error_log("SQL query was unsuccessful");
            return false;
    }


    /** Checks that the provided password of a username matches the one in the database
     * @param $username_input
     * @param $password_input
     * @return bool|null : true if pw was verified, false if not; null if query was unsuccessful
     */
    static private function verifyPassword($username_input, $password_input): ?bool
    {
        $connection = DatabaseConnection::getInstance();

        // get the hash stored in the database
        $queryHash = "SELECT password FROM public.members WHERE username = :username";
        $variables = [':username' => $username_input];
        $resultHash = $connection->connectAndSendPreparedQuery($queryHash, $variables);

        if ($resultHash) {
            $hash = $resultHash->fetch(PDO::FETCH_OBJ);
            // returns true if pw was verified, false if not
            if ($hash) {
                return password_verify($password_input, $hash->password);
            }
        }
        error_log("SQL query was unsuccessful");
        return null;
    }


    /** Retrieves a username corresponding to the given memberID
     * @param $memberID
     * @return int|bool
     */
    static public function getMemberUsernameFromID($memberID)
    {
        $dbConnection = DatabaseConnection::getInstance();

        $preparedQuery = 'SELECT username FROM public.members WHERE id = :id';
        $variables = [':id' => $memberID];
        $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);

        if ($result) {
            $postUsername = $result->fetch(PDO::FETCH_OBJ);
            // returns the username
            if ($postUsername) {
                return $postUsername->username;
            }
            else {
                error_log("Couldn't get data from SQL query result");
                return false;
            }
        }
        error_log("SQL query was unsuccessful");
        return false;
    }

    /** Retrieves a user's ID from the database based on the given username
     * @param $username
     * @return bool | int id returned if query is successful, false if not
     */
    static public function getMemberIDFromUsername($username)
    {
        $dbConnection = DatabaseConnection::getInstance();

        $preparedQuery = 'SELECT id FROM public.members WHERE username = :username';
        $variables = [':username' => $username];
        $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);

        if ($result) {
            $resultID = $result->fetch(PDO::FETCH_OBJ);
            //member with provided username exists, return their ID
            if ($resultID) {
                return $resultID->id;
            }
            else {
                error_log("Couldn't get data from SQL query result");
                return false;
            }
        }
        error_log("SQL query was unsuccessful");
        return false;
    }
}
