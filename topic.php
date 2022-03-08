<?php
/**
 * topic.php
 *
 * @brief   UI HTML webpage that displays a specific Topic and any user Post s submitted in response.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

///@cond
include_once($_SERVER['DOCUMENT_ROOT'] . '/config/util.php'); // don't put anything before this (starts $SESSION)
$loggedin = $_SESSION['loggedin'] ?? null;

/** Re-direct browser if user is not logged-in*/
if (!$loggedin || count(get_included_files()) == ((version_compare(PHP_VERSION, '5.0.0', '>='))?1:0)) {
    header("Location: index.php");
    exit;
} else {
    // VARIABLES
    //  $postToken = hash_hmac('sha256', 'post.php', $_SESSION['internal_token']);

    $errorMsg = "";
    $topicDataFromDB = false;

    // GET PARAMETERS
    $getParamsExist = isset($_GET['id']) && isset($_GET['topic']);

    // GET topicID can only be a number
    if ($getParamsExist && isNumber($_GET['id'])) {
        // set the topicID in session for each topic's page, to submit posts
        $_SESSION['topicID'] = $_GET['id'];

        // QUERY to check if topic exists in DB and get its data
        // will equal false if no data was returned
        $topicDataFromDB = Topic::getAllTopicData($_GET['id'], ($_GET['topic']));
    }
    // GET params don't exist or were modified to invalid params
    if (!$getParamsExist || !$topicDataFromDB) {
        $errorMsg = "Sorry, couldn't load content. Page not found.";
    }
}

/*******************************************************************************
 * HTML section starts here
 ******************************************************************************/
?>
<!DOCTYPE html>
<html lang="sv-SE">
<head>
    <?php include_once('includes/head.php'); ?>
    <script src="/js/posts.js"></script>
    <link type="text/css" rel="stylesheet" href="css/topicposts.css"/>
</head>
<body>
<header>
    <?php include_once 'includes/header.php' ?>
</header>
<nav>
    <?php printLinks('topic.php'); ?>
</nav>
<main>
    <aside>
        <?php include_once('includes/login_form.php'); ?>
    </aside>
    <section>
        <p id="messageToUser" class="messageToUser"> </p>
        <?php if (!$topicDataFromDB): ?>
            <div id="error_main">
                <div class="fof">
                    <h2>Page not found</h2>
                    <h3>Error 404</h3>
                    <p>
                        You may want to head back to the topics list.
                        If you think something is broken, report a problem.
                    </p>
                    <a class="error_link" href="https://coolforums.test/forumtopics.php"> Back to topics</a>
                    <a class="error_link" href="mailto:webmaster@coolforums.test" >Report a problem</a>
                </div>
            </div>
        <?php  endif; ?>

        <div id="topicContent" class="topic_content"
            <?php
            // hide this HTML section if no topic data was returned
            if (!$topicDataFromDB) {
                echo 'style="display: none"';
            } ?>
        >
            <div id="topicHeading">
                <h2><?php if ($topicDataFromDB){
                        echo htmlspecialchars($topicDataFromDB['title']);
                    } ?>
                </h2>
                <p>
                    <?php if ($topicDataFromDB) {
                        $username = Member::getMemberUsernameFromID($topicDataFromDB['member_id']);
                        $timestamp  = ($topicDataFromDB['timestamp']);

                        echo "by <b>" . htmlspecialchars($username) . "</b>  (" . htmlspecialchars($timestamp) . ")";
                    } ?>
                </p>
                <p>
                    <?php if ($topicDataFromDB) {
                        $content = nl2br(htmlspecialchars($topicDataFromDB['content']));
                        echo $content;
                    } ?>
                </p>
            </div>
            <?php
            if ($topicDataFromDB) {

                // query the database to get all posts on the topic
                $postData = Post::getAllPostDataAboutTopic($topicDataFromDB['id']);

                if (!$postData) {
                    $errorMsg = "No posts on this topic yet.";
                } else {
                    // Print the content and data of each post on the topic
                    Post::printAllPostsAboutTopic($postData);
                }
            }
            ?>
        </div>
        <form method="post" action="src/submitPost.php" id="submitNewPost"
            <?php
            if (!$topicDataFromDB) {
                // hide the submit form if no topic data is returned
                echo 'style="display: none"';
            } ?>
        >
            <fieldset id="submitPostFieldset">
                <!-- New post submission -->
                <textarea id="submitPostText" placeholder="Enter reply." rows="3" cols="83"></textarea>
                <input type="button" id="submitPostBtn" value="Post" >
                <p id="charCount">0</p>
            </fieldset>
        </form>
    </section>
    <button id="navTopBtn" title="Go to top">Top</button>
</main>

</body>
</html>
<?php /// @endcond ?>