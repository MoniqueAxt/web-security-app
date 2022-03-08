<?php
/**
 * util.php
 *
 * @brief   Utility file for autoloading classes, SESSION start, and functions to reduce code repetition.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */


session_name('id');
session_set_cookie_params([
    'lifetime' => 1800, // 30 minutes
    'path' => '/',
    'domain' => 'coolforums.test',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
include_once('globals.php');

/** Autoload functions for classes stored in directory /classes.
 * All classes must be saved in lower case to work and end with class.php
 * @param $class
 */
function my_autoloader($class) {
    $classfilename = strtolower($class);
    include($_SERVER['DOCUMENT_ROOT'].'/classes/'  . $classfilename . '.class.php');
}
spl_autoload_register('my_autoloader');


/** Checks whether the data provided is a number 0-9
 * @param $dataToValidate
 * @return bool
 */
function isNumber($dataToValidate): bool
{
    return preg_match('/^[0-9][0-9]*$/', $dataToValidate);
}

/** Prints the sidebar navigation links for each page.
 * The link arrays are set in session by login.php
 * @param $activePage
 */
function printLinks($activePage)
{
    $linkarray = isset($_SESSION['linkarray']);
    if ($linkarray) {
        foreach ($_SESSION['linkarray'] as $name => $link) {
            if ($link === $activePage)
                echo "<a href='" . htmlspecialchars($link) . " ' class='active'>" . htmlspecialchars($name) . "</a>";
            else
                echo "<a href='" . htmlspecialchars($link) . " '>" . htmlspecialchars($name) . "</a>";
        }
    }
}

/** Checks the config class for debug status.
 * If true, debug information is shown.
 */
$config = config::getInstance();
//$debug = config::getInstance();

if ($config->isDebugMode()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}

if (!isset($_SESSION['linkarray'])) {
    try {
        $_SESSION['linkarray'] = $config->get('public_link_array');
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}


