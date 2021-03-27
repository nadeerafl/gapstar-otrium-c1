<?php
namespace App\DB;
include_once './src/Config/Config.php';
/*
* Mysql database class - only one connection alowed
*/
class Database {
	private $connection;
	private static $instance; //The single instance
	private $host       = HOST;
	private $userName   = USER;
	private $password   = PASSWORD;
	private $database   = DB;

	/**
	* Get an instance of the Database
	* @return Database
	*/
	public static function getInstance() : Database
    {
		if(!self::$instance) { // If no instance then make one
			self::$instance = new self();
		}
		return self::$instance;
	}

	// Constructor
	private function __construct() 
    {
        $connection_string  = "mysql:dbname=" .  $this->database . ";host=" .  $this->host;
        $this->connection   = new \PDO($connection_string,  $this->userName, $this->password);

	}

	// Magic method clone is empty to prevent duplication of connection
	private function __clone() { }

	// Get mysqli connection
    /**
     * Get connection string
     * 
     * @return  \PDO     Conncetion
     */
	public function getConnection() : \PDO
    {
		return $this->connection;
	}
}
?>