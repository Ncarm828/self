
<?php
 
/**
 * Handling database connection
 *
 * 
 */
class DbConnect {
 
    private $conn;
 
    function __construct() {        
    }
 
    /**
     * Establishing database connection
     * @return database connection handler
     */
    function connect() {
        include_once dirname(__FILE__) . '/Config.php';
 
 
 			$this->conn = new MongoClient(DB_HOST);
 			
			//TODO handle connection errors
			
			
        // returing connection resource
        return $this->conn;
    }
 
}
 
?>