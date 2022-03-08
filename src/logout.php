<?php
/**
 * logout.php
 *
 * @brief   Logs a user out by destroying the $_SESSION and cookies.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

///@cond
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php'); // don't put anything before this (starts $SESSION)

// Unset all of the session variables.
$_SESSION = array();

// If killing the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
     );
}

// Finally, destroy the session.
session_destroy();

$logoutMsg = "You are logged out.";
$response = array
(
    'loggedin' => "false",
    'msg' => $logoutMsg
);

/******************************************************************************
 * Response array
 ******************************************************************************/
$config = Config::getInstance();
$response['linkarray'] =  $config->get('public_link_array');

echo json_encode($response);
/// @endcond
