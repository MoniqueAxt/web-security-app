<?php
/**
 * searchmanagement.php
 *
 * @brief   Handles the search functionality: searches the database data.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

///@cond
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php'); // don't put anything before this (starts $SESSION)
header("Content-Type: application/json");

/** VARIABLES default values */
$errorMessage = "No search results. Try searching for something else.";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    // POST data exists
    if ($data !== null && property_exists($data, 'searchType') && property_exists($data, 'searchText')) {
        $response ['errorMessage'] = $errorMessage;
        $searchType = htmlspecialchars($data->searchType);         //options:  keyword | username
        $searchText = $data->searchText;

        /******************************************************************************************************
         * KEYWORD search
         *******************************************************************************************************/
        if ($searchType === "keyword") {
            $variables = [':searchText' => "%" . $searchText . "%"];

            $preparedQuery =
                'SELECT t.id as topic_id, t.title as display_title, null as is_post, null as post_id
   		           FROM public.topics t
		           WHERE LOWER(title) LIKE LOWER(:searchText) 
                   OR LOWER(content) LIKE LOWER(:searchText)
                UNION	
                SELECT p.topic_id, substr(p.content, 1, 50), (SELECT title FROM public.topics WHERE id = p.topic_id), p.id
		           FROM public.posts p
		           WHERE LOWER(content) LIKE LOWER(:searchText);';


            /******************************************************************************************************
             * USERNAME search
             *****************************************************************************************************/
        } else if ($searchType === "username") {
            $userID = Member::getMemberIDFromUsername($searchText);

            if ($userID) {  // the username searched for exists
                $variables = [':member_id' => $userID];

                // from topic:  topicID  |   title   | is_post (false)   | post_id (null)
                //  from post:  topicID  |  content  | topic_title       | post_id
                $preparedQuery =
                    'SELECT t.id as topic_id, t.title as display_title, null as is_post, null as post_id
                        FROM public.topics t
                        WHERE t.member_id = :member_id                    							
	                UNION												
                        SELECT p.topic_id, substring(p.content, 1, 50), (SELECT title FROM public.topics WHERE id = p.topic_id), p.id
	                    FROM public.posts p
	                    WHERE p.member_id = :member_id;';

            } else {  // the username searched for doesn't exist
                $response['errorMessage'] = "No search results. Try searching for something else.";
                echo json_encode($response);
                exit(0);
            }
            /******************************************************************************************************
             * UNDEFINED search
             *****************************************************************************************************/
        } else {    // default behaviour if the search-category received is neither 'keyword' nor 'username'
            $response['errorMessage'] = "No search results. Try searching for something else.";
            echo json_encode($response);
            exit(0);
        }

        /** SEND QUERY to database *********************************************************************/
        $dbConnection = DatabaseConnection::getInstance();
        $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);
        // query was successful and there are matching posts/topics for the search
        if ($result) {
            $searchResponse = $result->fetchAll(PDO::FETCH_ASSOC);

            if ($searchResponse /*&& $searchResponse->rowCount() > 0*/) {
                $response = array(
                    'data' => $searchResponse,
                    'searchType' => $searchType,
                );
                $errorMessage = "none";
            }
            //query was unsuccessful
        } else {
            error_log("SQL query unsuccessful");
        }
    } else {
        error_log("POST data is null or missing properties");
    }
} //else: will use default variables for response

/******************************************************************************
 * Response array
 ******************************************************************************/
$response['errorMessage'] = $errorMessage;
echo json_encode($response);
/// @endcond