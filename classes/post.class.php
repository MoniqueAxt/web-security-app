<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php');
/**
 * Class Post
 *
 * @brief Represents users' Post-messages stored in the database.
 * Handles Post-related functionality: database queries and UI output of Post data.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

class Post
{
    /** Retrieves all the post-replies on a given Topic from the database
     * @see Topic
     * @param $topicID
     * @return array|false returns an array of all posts if successful, false if not
     */
    static public function getAllPostDataAboutTopic($topicID){
        $preparedQuery = 'SELECT * FROM public.posts
                            WHERE topic_id = :id
                            ORDER BY timestamp';

        $dbConnection = DatabaseConnection::getInstance();
        $variables = [':id' => $topicID];
        $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);

        if ($result){
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /** Responsible for printing an HTML representation of the post-message data provided,
     * including up/downvote elements.
     * @param $postData
     */
    static public function printAllPostsAboutTopic($postData) {
        // Print content and data of each post (corresponds to a row in the DB)
        for ($i = 0; $i < count($postData); $i++) {

            // get the member_id of each post
            $postUsername = Member::getMemberUsernameFromID($postData[$i]['member_id']);

            if ($postUsername) {    // successfully query
                $postID = $postData[$i]['id'];

                echo "<fieldset class='$postID' name='$postID' id='$postID'>";
                // get the post content, author, timestamp
                $postContent = nl2br(htmlspecialchars($postData[$i]['content']));
                $postUser = htmlspecialchars($postUsername);
                $timestamp = htmlspecialchars($postData[$i]['timestamp']);

                echo "<legend class='postUser'>$postUser</legend>";
                echo "<div class='metaContainer'>";
                echo "<p class='postTS' id='timestamp_$postID'>$timestamp</p>";

                // get and display the correct icon for upvotes/downvotes
                $loggedinUserID = $_SESSION['memberID'];
                $vote = Vote::getVoteOnPostByMember($loggedinUserID, $postID);
                $voteCount = Vote::getTotalVotesOnPost($postID);

                // default up/downvote colours
                $upSrc = '/img/up_grey.png';
                $downSrc = '/img/down_grey.png';

                // logged-in user has voted on this post
                if ($vote){
                    // upvote on this post by logged-in user
                    if ($vote === 1) {
                        $upSrc ='/img/up_green.png';
                        $downSrc = '/img/down_grey.png';
                    }
                    // downvote on this post by logged-in user
                    else if ($vote === -1) {
                        $upSrc ='/img/up_grey.png';
                        $downSrc = '/img/down_red.png';
                    }
                }

                echo "<span class='voteContainer'>";
                echo "<p id='voteCount_$postID' class='voteCount'>($voteCount)</p>";
                echo "<img src='$upSrc' alt='up' class='voting' id='up_$postID'/>";
                echo "<img src='$downSrc' alt='down' class='voting' id='down_$postID'/>";
                echo "</span>";

                echo "</div>";
                echo "<p class='postContent' id='content_$postID'>$postContent</p>";

                // if the logged-in user is the post's author, add a delete button
                if ($loggedinUserID === $postData[$i]['member_id']) {
                    echo "<input id='delete_$postID' type='button' class='deleteBtn' value='Delete'>";
                }
                echo '</fieldset>';
            }
        }
    }

    /** Adds a user post to the database and if successful, returns the inserted post data.
     * @param $data
     * @return false|array false if unsuccessful, else returns the Post data in the database.
     */
    public static function addPostToDB($data)
    {
        // extract post-data from POST content & set local variables
        $postContent = $data->postContent;
        $timestamp = date('M j Y\, h:i:s');
        $memberID = $_SESSION['memberID'];
        $topicID = $_SESSION['topicID'];

        $preparedQuery = 'INSERT INTO public.posts(member_id, content, timestamp, topic_id)
                        VALUES(:memberID, :content, :time, :topicID)
                        RETURNING id as post_id, content, timestamp, topic_id';

        $variables = [':memberID' => $memberID, ':content' => $postContent, ':topicID' => $topicID, ':time' => $timestamp];

        $dbConnection = DatabaseConnection::getInstance();
        $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);

        if ($result) {
            return $result->fetch(PDO::FETCH_OBJ);
        }

        error_log("SQl query unsuccessful - sql or connection failed.");
        return false;
    }
}