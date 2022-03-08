<?PHP
/**
 * config.php
 *
 * @brief   Contains configuration settings for the database connection and arrays holding links to UI webpages.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

//// @cond
if( count(get_included_files()) == ((version_compare(PHP_VERSION, '5.0.0', '>='))?1:0) )
{
    header("Location: index.php");
    exit();
}

$host = '127.0.0.1';
$dbName = 'postgres';
$port = '5432';
$user = 'userbob';               // (admin uname : postgres          | user uname: userbob)
$pass = '-';    // (admin pw    : - | user pw   : -)

$dsn = "pgsql:host=$host;port=$port;dbname=$dbName";
$pdoString = $dsn . ";user=" . $user . ";password=" . $pass;

return [
    'database' => [
        'host' => $host,					// server address
        'user' => $user,					// username
        'password' => $pass,		        // password to connect to server
        'dbname' => $dbName,				// database to connect to
        'port' => $port
    ],

    'pdo_string' =>  $pdoString,

    'public_link_array' => [					// pages accessible before logging in
        "HOME" => "index.php"
    ],

    'member_link_array' => [					// pages accessible to logged-in users
        "HOME" => "index.php",
        "TOPICS" => "forumtopics.php",
        "MEMBER PAGE" => "memberpage.php",
    ]
];
//// @endcond