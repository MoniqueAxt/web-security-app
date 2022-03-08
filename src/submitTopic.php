<?php
/**
 * submitTopic.php
 *
 * @brief   Submits a Topic-message (by a logged-in user) to the database.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

///@cond
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php'); // don't put anything before this (starts $SESSION)
header("Content-Type: application/json");

/** VARIABLES default values */
$errorMessage = "Invalid request. Cannot submit the topic.";
$successfulAddTopic = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $memberID = $_SESSION['memberID'] ?? null;

    /********************************************************************************
     * Submit the post to the database
     ******************************************************************************/
    // user must be logged in to post a topic and POST data must have content
    if ($memberID === null || $data === null) {
        error_log("submitTopic error: memberID or POST data is null");

        $response['errorMessage'] = "Invalid request. Cannot submit the topic.";
        echo json_encode($response);
        exit;
    }
    // user is logged in and POST has valid Topic data
    else if (property_exists($data, 'topicTitle') && property_exists($data, 'topicContent')
        &&  !(strlen($data->topicContent) > MAX_TOPIC_LEN)
        && !(strlen($data->topicTitle) > MAX_TOPIC_TITLE_LEN)) {

        // RETURNS: id, title, content, timestamp, member_id
        $topicData = Topic::addTopicToDB($data);

        // Topic was submitted without any errors
        if ($topicData) {
            $errorMessage = "none";
            $successfulAddTopic = true;
            $topicURL = Topic::buildTopicsLinkUrls($topicData->id, $topicData->title);

            $response = array(
                'topicContent' => htmlspecialchars($topicData->content),
                'topicURL' => $topicURL,
                'topicID' => $topicData->id,
                'strlen' => strlen($topicData->content)
            );
        }
    } else if (!property_exists($data, 'topicTitle') || !property_exists($data, 'topicContent')) {
        error_log("submitTopic error: missing POST properties.");

    } else if (strlen($data->topicTitle) > MAX_TOPIC_TITLE_LEN){
        $errorMessage = "Title length cannot be longer than " . MAX_TOPIC_TITLE_LEN . " characters.";

    } else if(strlen($data->topicContent) > MAX_TOPIC_LEN) {
        $errorMessage = "Content length cannot be longer than " . MAX_TOPIC_LEN . " characters.";
    }
}
/******************************************************************************
 * Response array
 ******************************************************************************/
$response['errorMessage'] = $errorMessage;
$response['successfulAddTopic'] = $successfulAddTopic;

echo json_encode($response);
/// @endcond
