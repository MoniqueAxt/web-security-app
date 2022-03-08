<?php
/**
 * Class config
 *
 * @brief   Singleton class to get and use configuration settings that are set in /config/config.php
 *
 * @author  Monique Axt  <>
 * @note    Projekt, Mjukvarusäkerhet
 * @file
*/

class config {
    private $data;
    // don't specify type - Typed static property config::$instance must not be accessed before initialization
    private static $instance;

    /** Set debug mode to true/false (changes php.ini file).
     * For development set true, for production set false */
    private bool $debug = true;

    /** config constructor.
     * initialised with data from config.php
     */
    private function __construct() {
        $this->data = include($_SERVER['DOCUMENT_ROOT'].'/config/config.php');
    }

    /** Get an instance of the singleton class
     * @return config instance
     */
    public static function getInstance(): config
    {
        if (self::$instance == null) {
            self::$instance = new config();
        }
        return self::$instance;
    }

    /** Creates a PDO connection string
     * @return string containing PDO connection data
     * @throws Exception
     */
    public function getPDOString(): string
    {
        if (!isset($this->data['pdo_string'])) {
            throw new Exception("PDO connection info not set/found in config file");
        }

        return $this->data['pdo_string'];
    }

    /** Creates a database connection string
     * @return string
     * @throws Exception
     */
    public function getDbDns(): string
    {
        if (!isset($this->data['database'])) {
            throw new Exception("Database connection info not in config");
        }

        $dbConnString = "";
        foreach($this->data['database'] as $key => $value) {
            $dbConnString .=  $key . "=" . $value . " ";
        }

        return $dbConnString;
    }

    /** Get data set in the config array (originally from config.php)
     * by key value, if the key for the data exists.
     * @param $key
     * @return mixed data corresponding to the key provided
     * @throws Exception
     */
    public function get($key) {
        if (!isset($this->data[$key])) {
            throw new Exception("Key $key not in config.");
        }
        return $this->data[$key];
    }

    /** used to check if debug mode is set
     * @return boolean
     */
    public function isDebugMode(): bool
    {
        return $this->debug;
    }
}