<?php
/**
 * deletePost.php
 *
 * @brief   Deletes a Post-message (made by a logged-in member) from the database
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

///@cond
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php'); // don't put anything before this (starts $SESSION)
header("Content-Type: application/json");

// user must be logged-in to delete one of their posts
if (!isset($_SESSION['memberID'])) {
    exit;
}

/** VARIABLES default values */
$errorMessage = "none";
$postID = null;
$content = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    // Search the database for the search term
    if (property_exists($data, 'username')) {
        $username = htmlspecialchars($data->username);
        $postAuthorID = Member::getMemberIDFromUsername($username);
        $loggedinUserID = $_SESSION['memberID'];

        // logged-in user can't delete someone else's post
        if ($postAuthorID != $loggedinUserID) {
            exit;
        }
        // logged-in user can delete their own post
        else if (property_exists($data, 'timestamp') && property_exists($data, 'content') && property_exists($data, 'voteCount')) {
            $timestamp = $data->timestamp;
            $content = $data->content;
            $voteCount = $data->voteCount;
            $postID = $data->postID;

            $variables = [
                ':memberID' => $postAuthorID,
                ':timestamp' => $timestamp,
                ':content' => $content,
                'postID' => $postID
            ];

            $preparedQuery = " DELETE FROM public.posts
                            WHERE member_id = :memberID
                            AND timestamp = :timestamp
                            AND content = :content
                            AND (
                                    SELECT COALESCE
                                    (                                  
                                        (SELECT SUM (vote) 
                                            FROM public.post_votes
                                            WHERE post_id = :postID
                                        ), 0
                                    )
                                ) = $voteCount
                           
        ";
            $dbConnection = DatabaseConnection::getInstance();
            $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);

            if (!$result || $result->rowCount() < 1) {
                $errorMessage = "Post could not be deleted.";
                error_log("Database post deletion failed.");
            }
        }
    }
}
/******************************************************************************
 * Response array
 ******************************************************************************/
$response = array(
    'errorMessage' => $errorMessage,
    'postID' => $postID,
    'content' => $content
);
echo json_encode($response);
/// @endcond