<?php

/**
 * do all CRUD
 *
 *
 */

class DbHandler {

	private $conn;

	function __construct() {
		require_once dirname(__FILE__) . '/DbConnect.php';
		// opening db connection
		$db = new DbConnect();
		$this -> conn = $db -> connect();
	}

	/**
	 * get all user names
	 * returns with a json array of all user names from db
	 * no ids or nothing
	 */
	public function getAllUserNames() {
		$response = array();

		$col = $this -> conn -> self_db -> self_host;
		// build php mongo find query, empty array means find all
		// the fields means return only user and not _id
		$query = array();
		$fields = array('user' => 1, '_id' => 0);

		$cursor = $col -> find($query, $fields);

		//stuff it all into an array
		$response = (iterator_to_array($cursor));

		$this -> conn -> close();

		return $response;
	}

	/* ------------- `users` table method ------------------ */
	
	
	/**
	 * Checking for duplicate user by email address
	 * @param String $user user name
	 * @return array of user fields
	 */
	public function getUser($name) {

	//TODO: validate that user exists... later

		//select the collection - mongclient.db.collection
		$col = $this -> conn -> self_db -> self_host;

		//build query and find the name in db
		$nameQuery = array('user' => $name);
		$user = $col -> findOne($nameQuery);

		//close
		$this -> conn -> close();

		return $user;
	}
	

	/**
	 * Creating new user
	 * @param String $name User full name
	 * @param String $email User login email id
	 * @param String $password User login password
	 */
	public function createUser($name, $email, $password) {
		require_once 'PassHash.php';
		$response = array();

		// First check if user already existed in db

		if (!$this -> isUserExists($name)) {
			// Generating password hash
			$password_hash = PassHash::hash($password);

			// Generating API key
			$api_key = $this -> generateApiKey();

			// insert query

			//prepare document
			$doc = array("user" => $name, "email" => $email, "password" => $password_hash, "api_key" => $api_key, "p" => 0, "e" => 0, "r" => 0, "ti" => 0, "tp" => 0, "contexts" => array("game", "calendar"));

			//select the collection - mongclient.db.collection
			$col = $this -> conn -> self_db -> self_host;
			// insert document
			$result = $col -> insert($doc);
			$this -> conn -> close();

			// Check for successful insertion
			if ($result) {
				// User successfully inserted
				$response["message"] = "USER_CREATED_SUCCESSFULLY";
			} else {
				// Failed to create user
				$response["message"] = "USER_CREATE_FAILED";
			}

		} else {
			// User with same email already existed in the db
			$response["message"] = "USER_ALREADY_EXISTED";
		}

		return $response;
	}



	/**
	 * update user
	 * @param String $name User full name
	 * @param int $p participation
	 * @param int $e engagement score
	 * @param int $r responsiveness score
	 * @param time $tp the time prompted for activity
	 * @param time $ti the time started
	 */
	public function updateUser($name, $p, $e, $r, $tp, $ti) {

		$response = array();

		// First check if user exists in db

		if ($this -> isUserExists($name)) {
			
			// update query
			
			$newdata = array();
			//prepare new data
			(isset($p)) ? $newdata["p"] = $p : false ;
			(isset($e)) ? $newdata["e"] = $e : false ;
			(isset($r)) ? $newdata["r"] = $r : false ;
			(isset($tp)) ? $newdata["tp"] = $tp : false ;
			(isset($ti)) ? $newdata["ti"] = $ti : false ;
			
			$query['$set'] = $newdata; // change the above fields
			
			
			
			//$newdata = array("p" => $p, "e" => $e, "r" => $r, "ti" => $ti, "tp" => $ti);

			
			//select the collection - mongclient.db.collection
			$col = $this -> conn -> self_db -> self_host;
			// insert document
			$result = $col -> update(array("user" => $name), $query);
			
			$this -> conn -> close();

			// Check for successful insertion
			if ($result) {
				// User successfully inserted
				$response["message"] = "USER_UPDATED_SUCCESSFULLY";
			} else {
				// Failed to create user
				$response["message"] = "USER_UPDATE_FAILED";
			}

		} else {
			// User with this name does not exist
			$response["message"] = "USER_NOT_EXIST";
		}

		return $response;
	}







	/**
	 * Checking for duplicate user by email address
	 * @param String $email email to check in db
	 * @return boolean
	 */
	private function isUserExists($name) {

		//select the collection - mongclient.db.collection
		$col = $this -> conn -> self_db -> self_host;

		//build query and find the name in db
		$nameQuery = array('user' => $name);
		$cursor = $col -> find($nameQuery);

		//close
		$this -> conn -> close();

		return $cursor -> count() > 0;
	}


	/**
	 * Fetching user api key
	 * @param String $user_id user id primary key in user table
	 */
	public function getApiKeyById($user_id) {
		$stmt = $this -> conn -> prepare("SELECT api_key FROM users WHERE id = ?");
		$stmt -> bind_param("i", $user_id);
		if ($stmt -> execute()) {
			$api_key = $stmt -> get_result() -> fetch_assoc();
			$stmt -> close();
			return $api_key;
		} else {
			return NULL;
		}
	}

	

	
	/**
	 * Generating random Unique MD5 String for user Api key
	 */
	private function generateApiKey() {
		return md5(uniqid(rand(), true));
	}


 /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($name, $password) {
        // fetching user by email
        
      	 $user = $this->getUser($name);
		 
		 $password_hash = $user['password'];


 
            if (PassHash::check_password($password_hash, $password)) {
                // User password is correct
                return TRUE;
            } 
        return FALSE;
    }
	
}
?>