<?php
/**
 * createAccount.php
 *
 * @brief   Creates a user account that is added to the database.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

/// @cond
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php'); // don't put anything before this (starts $SESSION)
header("Content-Type: application/json");

/** VARIABLES default values */
$successfulAddUser = false;
$errorMessage = "Could not create account.";
$usernameInput = "-";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    // Add a user to the database
    if ($data !== null && property_exists($data, 'newUsername')) {
        $usernameInput = htmlspecialchars($data->newUsername);
        //check the username doesn't contain disallowed characters
        if (!preg_match("/^[a-z\d_]{3,20}$/i", $usernameInput)) {
            $errorMessage = "Username can only contain letters, numbers and underscores and must be between 3-20 characters.";
        }
        // check the username doesn't already exist in the database
        else if (Member::usernameAlreadyExists($usernameInput)) {
            $errorMessage = "That username is already taken or not allowed. Try something else.";

            // the username does NOT already exist in the database
        } else if (Member::addUserToDB($usernameInput, htmlspecialchars($data->newPassword))){  // password will be hashed
            $successfulAddUser = true;
            $errorMessage = "none";
        }
    } else {
        error_log("Create user error: POST data null or missing fields");
    }
} //else: will use default variable values for response

/******************************************************************************
 * Response array
 ******************************************************************************/
$response = array(
    'successfulAddUser' => $successfulAddUser,
    'errorMessage' => $errorMessage
);
if (!$successfulAddUser) {
    $response['username'] = "-";
} else {
    $response['username'] = $usernameInput;
}

echo json_encode($response);
/// @endcond