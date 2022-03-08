<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php');
/**
 * Class Vote
 *
 * @brief Represents upvotes and downvotes on a Post
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

class Vote
{
    /** Retrieves a vote done by a specific user on a specific Post, if the user has voted on this post.
     * This can be used to check whether a user has voted (up/downvote) on specific post.
     * @param $loggedinUserID
     * @param $postID
     * @return int|null return of 1 indicates an upvote, -1 indicates downvote; null returned if query was unsuccessful
     */
    static public function getVoteOnPostByMember($loggedinUserID, $postID): ?int
    {
        $preparedQuery = "SELECT vote FROM public.post_votes                                
                            WHERE member_id = :memberID
                            AND post_id = :postID";

        $variables = [':memberID' => $loggedinUserID, ':postID' => $postID];
        $dbConnection = DatabaseConnection::getInstance();
        $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);

        $vote = 0;
        if ($result) {
            $temp = $result->fetch(PDO::FETCH_OBJ);
            if ($temp) {
                $vote = $temp->vote;
            }
        }
        if ($vote) {
            return $vote;
        }
        return null;
    }

    /** Retrieves the total vote-count on a specific Post.
     * @param $postID
     * @return int
     */
    static public function getTotalVotesOnPost($postID): int
    {
        $preparedQuery = "SELECT COALESCE 
                            (                                  
                                (SELECT SUM (vote) 
                                    FROM public.post_votes
                                    WHERE post_id = :post_id
                                ), 0
                            ) as sum";

        $variables = [':post_id' => $postID];

        $dbConnection = DatabaseConnection::getInstance();
        $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);

        if ($result) {
            $temp = $result->fetch(PDO::FETCH_OBJ);

            if ($temp && $temp->sum !== NULL) {
                return $temp->sum;
            }
        }
        return 0;
    }


}