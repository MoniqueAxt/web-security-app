<?php
/**
 * submitPost.php
 *
 * @brief   Submits a Post-message (by a logged-in user) to the database.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

///@cond
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php'); // don't put anything before this (starts $SESSION)
header("Content-Type: application/json");

/** VARIABLES default values */
$errorMessage = "Invalid request. Cannot submit the post.";
$successfulAddPost = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    // check all required data fields were received
    $memberID = !empty($_SESSION['memberID']) ? $_SESSION['memberID'] : false;
    $username = !empty($_SESSION['uname']) ? $_SESSION['uname'] : false;
    $topicID = !empty($_SESSION['topicID']) ? $_SESSION['topicID'] : false;

    /*****************************************************************************
     * Submit the post to the database
     ******************************************************************************/
    // user must be logged in to post a post and POST data must have content
    if (!$memberID || !$data || !$topicID || !$username) {
        error_log("submitPost error: one or more of these is empty: SESSION[memberID], SESSION[username], SESSION[topicID], POST data");
        
        $response['errorMessage'] = "Invalid request. Cannot submit the post.";
        echo json_encode($response);
        exit;

    } else if ($memberID !== Member::getMemberIDFromUsername(htmlspecialchars($username))) {
        error_log("submitPost: userID doesn't match username [" . $memberID . "/" . $username . "]");

        $response['errorMessage'] = "Invalid request. Cannot submit the post.";
        echo json_encode($response);
        exit;

        // data sent has post content
    } else if (property_exists($data, 'postContent') && !(strlen($data->postContent) > MAX_POST_LEN)) {

        // RETURNS: post_id, post_content, timestamp, topic_id member_id
        $postData = Post::addPostToDB($data);

        if (!$postData){
            $errorMessage = "Post could not be submitted. Try again later.";

        } else {
            $errorMessage = "none";
            $successfulAddPost = true;
            $response = array(
                'postID' => $postData->post_id,
                'postContent' => $postData->content,
                'timestamp' => $postData->timestamp,
                'topicID' => $postData->topic_id,
                'username' => $username
            );
        }
    } else if (!(property_exists($data, 'postContent'))) {
        error_log("submitPost error: missing POST properties.");

    } else if (strlen($data->postContent) > MAX_POST_LEN) {
        $errorMessage = "Post length cannot be longer than " . MAX_POST_LEN . " characters";
    }
} //else: will use default variables for response

/******************************************************************************
 * Response array
 ******************************************************************************/
$response['errorMessage'] = $errorMessage;
$response['successfulAddPost'] = $successfulAddPost;

echo json_encode($response);
/// @endcond
