<?php
/**
 * registervote.php
 *
 * @brief   Registers an upvote/downvote on a Post-message by updating the database.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

///@cond
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php'); // don't put anything before this (starts $SESSION)
header("Content-Type: application/json");

/** VARIABLES default values */
$errorMessage = "Invalid request.";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if ($_SESSION['loggedin'] && $data !== null) {
        $dbConnection = DatabaseConnection::getInstance();

        if (property_exists($data, 'postID') && property_exists($data, 'vote')) {
            $postID = $data->postID;
            $username = $_SESSION['uname'];
            $vote = $data->vote;
            $memberID = -1;

            //query to get the user's ID from their username
            $preparedQuery = "SELECT id FROM public.members WHERE username = :username";
            $variables = [':username' => $username];
            $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);

            // memberID retrieved successfully
            if ($result) {
                $memberID = $result->fetch(PDO::FETCH_OBJ)->id;

                // Query to insert the updated vote-count into the database
                $preparedQuery = "INSERT INTO public.post_votes (post_id, member_id, vote)
                    VALUES (:postID, :memberID, :vote)
                    ON CONFLICT (post_id, member_id)
                    DO UPDATE SET vote = :vote ";

                $variables = [':postID' => $postID, ':memberID' => $memberID, ':vote' => $vote];
                $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);

                // vote-count was updated successfully
                if ($result) {
                    $sumVote = Vote::getTotalVotesOnPost($postID);

                    $errorMessage = "none";
                    $response = array(
                        'postID' => $postID,
                        'vote' => $vote,
                        'sumVote' => $sumVote
                    );
                } else {
                    $errorMessage = "You can't vote on this post right now. Try again later.";
                    error_log("Database error, couldn't update vote-count");
                }
            } else {
                $errorMessage = "You can't vote on this post right now. Try again later.";
                error_log("Database error, couldn't retrieve memberID");
            }
        } else {
            error_log("POST fields missing");
        }
    } else {
        $response['errorMessage'] = "Invalid request.";
        error_log("POST data is null or non-logged-in user attempting to vote");
    }
} //else: will use default variables for response

/******************************************************************************
 * Response array
 ******************************************************************************/
$response['errorMessage'] = $errorMessage;
echo json_encode($response);
/// @endcond