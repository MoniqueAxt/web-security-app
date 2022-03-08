<?PHP
/**
 * login.php
 *
 * @brief   Logs a user in if the provided credentials are valid.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

///@cond
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php'); // don't put anything before this (starts $SESSION, no output)
header("Content-Type: application/json");

/** VARIABLES default values */
$errorMessage = "Invalid credentials. Please try again.";
$_SESSION['loggedin'] = false;
$usernameInput = "-";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $tokenReceived = property_exists($data, 'token');
    $validToken = !empty($_SESSION['login_token']) && $tokenReceived && hash_equals($_SESSION['login_token'], htmlspecialchars($data->token));

    /******************************************************************************
     * Login attempt
     ******************************************************************************/
    if ($validToken && $data !== null && property_exists($data, 'username') && property_exists($data, 'password')) {
        $usernameInput = htmlspecialchars($data->username);

        // username can't contain disallowed characters, max-length 20 characters
        if (preg_match("/^[a-zäöå\d_]{3,20}$/i", $usernameInput)) {
            $pwdInput = htmlspecialchars($data->password);  // password will be hashed
            //Get the memberID from the database matching the form input data
            $userID = Member::verifyLogin($usernameInput, $pwdInput);

            // verified: correct username & password
            if ($userID) {
                $_SESSION['loggedin'] = true;
                $_SESSION['uname'] = $usernameInput;
                $_SESSION['memberID'] = $userID;
                $errorMessage = "none";
            }
        }//else: will use default variables for response

    } else if (!$validToken) { // log warning, potential attack
        error_log(basename($_SERVER['PHP_SELF']) . ": User login error: Invalid token, potential CSRF");
    }
    else {    // probably a coding error on JS side
        error_log(basename($_SERVER['PHP_SELF']) . ": User login error: POST data null or missing POST fields");
    }

} //else: will use default variables for response

/******************************************************************************
 * Response array
 ******************************************************************************/
$response = array (
    'username' => $usernameInput,
    'loggedin' => $_SESSION['loggedin'],
    'errorMessage' => $errorMessage,
);
// customise the navigation links sent based on logged-in status
$config = Config::getInstance();

if ($_SESSION['loggedin']) {    // logged in as member
    $response['linkarray'] = $_SESSION['linkarray'] = $config->get('member_link_array');
} else {        // couldn't log in
    $response['linkarray'] = $_SESSION['linkarray'] = $config->get('public_link_array');
    $response['username'] = "-";
}

echo json_encode($response);
/// @endcond