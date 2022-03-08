<?php
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
    header("Location: custom_404.html");
    exit();
}

$browserTitle = "Cool Forums!";
$logo = "<img src='../img/logo.png' alt='Cool Forms' class='logo'/>";

const MAX_POST_LEN = 1500;
const MAX_TOPIC_LEN = 3000;
const MAX_TOPIC_TITLE_LEN = 50;

//// @endcond