<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php');
/**
 * Class Topic
 *
 * @brief Represents users' Topic-messages stored in the database.
 * Handles Topic-related functionality: database queries and UI output of Topic data.
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

class Topic
{
    /** Retrieves all the data on a given Topic from the database
     * @param $topicID
     * @param $topicTitle
     * @return array|false returns an array containing the data of a specific Topic if successful, false if not
     */
    static public function getAllTopicData($topicID, $topicTitle)
    {
        $dbConnection = DatabaseConnection::getInstance();
        // check if the GET params match what is in the database
        if (self::topicExistsInDB($topicID, $topicTitle)) {

            // if so, get the all the topic data from the DB
            $preparedQuery = 'SELECT * FROM public.topics
                            WHERE id = :id
                            AND title = :title';
            $variables = [':id' => $topicID, ':title' => $topicTitle];

            $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);

            if ($result) {
                $data = $result->fetch(PDO::FETCH_ASSOC);

                if ($result->rowCount() > 0) {
                    return $data;
                }
                return false;
            }
        }
        return false;
    }


    /** Checks if a provided Topic exists in the database.
     * @param $topicID
     * @param $topicTitle
     * @return bool return true if the given topic-title and topic-id exists in the database, else false.
     */
    static private function topicExistsInDB($topicID, $topicTitle): bool
    {
        $dbConnection = DatabaseConnection::getInstance();
        // check topic exists in database
        $preparedExistsQuery = 'SELECT EXISTS
                                    (SELECT id FROM public.topics
                                        WHERE id = :topicID 
                                        AND title = :topicTitle)';

        $variablesExist = [':topicID' => $topicID, ':topicTitle' => $topicTitle];
        $resultExists = $dbConnection->connectAndSendPreparedQuery($preparedExistsQuery, $variablesExist);

        if ($resultExists)
            return true;

        return false;
    }


    /** Outputs (echos) HTML that displays a list of all the Topics' titles in the database.
     * The Topic-titles are hyperlinked that when clicked, open a new webpage.
     * If the database is not populated yet, this is indicated to the user.
     */
    static public function printTopicTitleLinks()
    {
        $database = DatabaseConnection::getInstance();

        $query = "SELECT id, title, timestamp FROM public.topics ORDER BY timestamp DESC ";
        $result = $database->connectAndSendQuery($query);

        $topics = array();
        if ($result) {
            $topics = pg_fetch_all($result); //PGSQL_ASSOC is default type
            pg_free_result($result);
        }

        if ($topics != null) {
            echo "<ul id='dynamicTopicsList'>";

            for ($i = 0; $i < count($topics); $i++) {
                $date = $topics[$i]['timestamp'];
                $title = $topics[$i]['title'];

                $id = $topics[$i]['id'];
                $timestamp = ($date);
                $url = self::buildTopicsLinkUrls($id, $title);

                if (strlen($title) > 50) {
                    $title = substr($title, 0, 50) . '...';
                }

                echo "<li><a href='" . htmlspecialchars($url) . "'>" . htmlspecialchars($title) . "</a>"
                    . "&nbsp;&nbsp;(" . htmlspecialchars($timestamp) . ")</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No topics yet!</p>";
        }
    }

    /** Generates a URL-encoded query string.
     * @param $id
     * @param $title
     * @return string
     */
    static function buildTopicsLinkUrls($id, $title): string
    {
        $data = array(
            'id' => $id,
            'topic' => $title
        );
        return 'topic.php?' . http_build_query($data);
    }

    /** Adds a Topic to the database and, if successful, returns the inserted Topic data.
     * @param $data
     * @return false|array false if unsuccessful, else returns the Topic data stored in the database.
     */
    public static function addTopicToDB($data)
    {
        // extract topic-data from POST content
        $topicTitle = $data->topicTitle;
        $topicContent = $data->topicContent;
        $timestamp = date('M j Y\, h:i:s');

        $preparedQuery = 'INSERT INTO public.topics(title, content, timestamp, member_id)
                        VALUES(:title, :content, :timestamp, :memberID)
                        RETURNING id, title, content, timestamp, member_id';

        $memberID = $_SESSION['memberID'];
        $variables = [':title' => $topicTitle, ':content' => $topicContent, ':memberID' => $memberID, ':timestamp' => $timestamp];
        $dbConnection = DatabaseConnection::getInstance();
        $result = $dbConnection->connectAndSendPreparedQuery($preparedQuery, $variables);

        // Topic was submitted without any errors; return data
        if ($result) {
            return $result->fetch(PDO::FETCH_OBJ);
        }

        error_log("submitTopic error: send query failed - sql or connection failed.");
        return false;
    }


}