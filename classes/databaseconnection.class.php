<?php
include_once ($_SERVER['DOCUMENT_ROOT'].'/config/util.php');
/**
 * Class DatabaseConnection
 *
 * @brief   Singleton class that handles the database connection and queries.
 * @see     config for configuration settings used
 *
 * @author  Monique Axt <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
 */

class DatabaseConnection
{
    private string $connectionString;
    // don't specify type - Typed static property DatabaseConnection::$instance must not be accessed before initialization
    private static $instance;
    private PDO $pdo;

    /** DatabaseConnection constructor.
     *
     */
    private function __construct() {
        $config = config::getInstance();
        try {
            $this->connectionString = $config->getDbDns();
        } catch (Exception $e) {
            error_log($e->getMessage());
            die();
        }
        try {
            $this->pdo = new PDO($config->getPDOString());
        } catch (Exception $e) {
            error_log($e->getMessage());
            die();
        }
    }

    /** Get an instance of the singleton class
     * @return DatabaseConnection instance
     */
    public static function getInstance(): DatabaseConnection {
        if (self::$instance == null){
            self::$instance = new DatabaseConnection();
        }
        return self::$instance;
    }

    /** Connects to the database and sends a
     * query using the connection info specified in config.php
     * @param $query
     * @return bool|resource returns false if unsuccessful, resource if successful
     */
    public function connectAndSendQuery($query) {
        $connection = pg_connect($this->connectionString);

        if(!$connection) {
            error_log("Can't connect to the database! Error:" . pg_last_error($connection));
            pg_close();
            return false;
        }
        else {
            $result = pg_query($connection, $query);

            if (!$result) {
                error_log("Invalid SQL query! Error:" . pg_last_error($connection));
                pg_close();
                return false;
            }
            else {
                pg_close();
                return $result;
            }
        }
    }

    /** Sends a prepared query to the database.
     * @param $preparedQuery
     * @param array $variableArray of key-value pairs for variables
     * @return bool | PDOStatement false if query was unsuccessful, else return response from the database
     */
    public function connectAndSendPreparedQuery($preparedQuery, array $variableArray) {
        $statement = $this->pdo->prepare($preparedQuery);
        $bindSuccess = false;

        if ($statement) {                   // query was prepared successfully
            foreach ($variableArray as $key => $value) {
                $bindSuccess = $statement->bindValue($key, $value);

                if ($bindSuccess == false)  //stop loop if any binds are unsuccessful
                    break;
            }

            if ($bindSuccess) {             // variables were bound successfully
                $successfulExecute = $statement->execute();

                if ($successfulExecute) {   // query was executed successfully
                    return $statement;

                } else {
                   error_log("SQL didn't execute. Error:" . $this->pdo->errorCode());
                    return false;
                }

            } else {
                error_log("SQL couldn't bind. Error:" . $this->pdo->errorCode());
                return false;
            }
        } else {
            error_log("SQL query not prepared successfully. Error:" . $this->pdo->errorCode());
            return false;
        }
    }

}