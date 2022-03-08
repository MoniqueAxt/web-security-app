<?php
/**
 * forumtopics.php
 *
 * @brief   UI HTML webpage that displays a list of Topics titles; contains the ability to search Topics and Posts and create/submit a new Topic.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

///@cond
include_once($_SERVER['DOCUMENT_ROOT'] . '/config/util.php'); // don't put anything before this (starts $SESSION)

$loggedin = $_SESSION['loggedin'] ?? null;
/** Re-direct browser if user is not logged-in*/
if (!$loggedin) {
    header("Location: index.php");
    exit;
}

/*******************************************************************************
 * HTML section starts here
 ******************************************************************************/
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include_once('includes/head.php'); ?>
    <script src="/js/topics.js"></script>
    <script src="/js/search.js"></script>
    <link type="text/css" rel="stylesheet" href="css/topic.css"/>
</head>
<body>
<header>
    <?php include_once('includes/header.php') ?>
</header>
<nav id="nav_links">
    <?php printLinks('forumtopics.php'); ?>
</nav>
<main>
    <aside id="login_aside">
        <?php include_once('includes/login_form.php'); ?>
    </aside>
    <section id="content_section">
        <input type="button" id="createNewTopicBtn" value="Create New Topic" >
        <h2>FORUM TOPICS</h2>

        <p id="messageToUser"></p>

        <form id="forumTopics">
            <fieldset id="search_fieldset">
                <legend><b>Search</b></legend>
                <label for="searchDropDownList">Search by: </label>
                <select id = "searchDropDownList">
                    <option value = "unspecified">none</option>
                    <option value = "keyword">keyword</option>
                    <option value = "username">username</option>
                </select>
                <input type="text" id="searchTextBox" name="searchTextBox" placeholder="e.g. Cars" maxlength="20"/>
                <input type="button" value="Search" id="searchBtn" name="searchBtn"/>
                <input type="button" value="Reset" id="resetSearchBtn" name="resetSearchBtn">
            </fieldset>

            <fieldset id="topics_fieldset">
                <legend><b>Topics</b></legend>
                <?php Topic::printTopicTitleLinks(); ?>
            </fieldset>

            <fieldset id="results_fieldset" style="display: none;"></fieldset>
        </form>

        <form method="post" action="src/submitTopic.php" id="submitNewTopicForm">
        <fieldset id="submitTopicFieldset">
            <!-- New topic submission -->
            <legend><b>Create new Topic</b></legend>
            <input type="text" id="submitTopicTitle" placeholder="Topic title" maxlength="50" required>
            <textarea id="submitTopicText" placeholder="Topic content" rows="15" cols="83" required></textarea>
            <br>
            <input type="button" value="Clear" id="resetSubmitTopicBtn" name="resetSubmitTopicBtn">
            <input type="button" id="submitTopicBtn" value="Submit" >
            <p id="charCount">0</p>
        </fieldset>
        </form>
    </section>
    <button id="navTopBtn" title="Go to top">Top</button>
</main>
<footer>
    <?php include_once('includes/footer.html'); ?>
</footer>
</body>
</html>
<?php /// @endcond ?>