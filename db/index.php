<?php

require 'Slim/Slim/Slim.php';
require_once 'DbHandler.php';
require_once 'PassHash.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
	$error = false;
	$error_fields = "";
	$request_params = array();
	$request_params = $_REQUEST;
	// Handling PUT request params
	if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
		$app = \Slim\Slim::getInstance();
		parse_str($app -> request() -> getBody(), $request_params);
	}
	foreach ($required_fields as $field) {
		if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
			$error = true;
			$error_fields .= $field . ', ';
		}
	}

	if ($error) {
		// Required field(s) are missing or empty
		// echo error json and stop the app
		$response = array();
		$app = \Slim\Slim::getInstance();
		$response["error"] = true;
		$response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
		echoRespnse(400, $response);
		$app -> stop();
	}
}

/**
 * Validating user name
 * @param String $user the user name of the person
 */
function validateEmail($email) {
	$app = \Slim\Slim::getInstance();
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$response["error"] = true;
		$response["message"] = 'Email address is not valid';
		echoRespnse(400, $response);
		$app -> stop();
	}

}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response) {
	$app = \Slim\Slim::getInstance();
	// Http response code
	$app -> status($status_code);

	// setting response content type to json
	$app -> contentType('application/json');

	echo json_encode($response);
}

/**
 * get all user attributes for user name
 *
 * method GET
 * url /user
 */
$app -> get('/user/:id', function($id) {
	$response = array();

	// $response["message"] = 'asdf';

	$connection = new MongoClient();
	// connect to a remote host (default port: 27017)
	$db = $connection -> self_db;
	$collection = $db -> self_host;

	$nameQuery = array('user' => $id);
	$document = $collection -> findOne($nameQuery);

	//$document = $collection->findOne();
	$connection -> close();
	echoRespnse(200, $document);
});


/**
 * add a new app to contexts in db
 * 
 * method POST
 * url: /user/:id/plugin_register
 * parameters: app_name 
 */

$app ->post('/user/:id/plugin_register', function($id) use($app){
//TODO: this.
	
});



/**
 * update the activity and recalculate score
 * 
 * method POST
 * url: /user/:id/update
 * parameters: activity, tp, ti, api_key
 *
 */
 
$app ->post('/user/:id/update', function($id) use($app){
	
			$response = array();


	
	
	// reading post params
	$activity = $app -> request -> post('activity');
	$api_key = $app -> request -> post('api_key');
	$tp = $app -> request -> post('tp');
	$ti = $app -> request -> post('ti');
	(isset($tp)) ? $tp = time() : false;
	
	$db = new DbHandler();
	$user = $db ->getUser($id);
	
	 /**
	 * TODO: Verify API key before updating stats
	 */
	
	
/**
	 * if its a new day, move engagement to participation, and rebuild
	 * engagement column (not perfectly accurate if person doesnt log in all the time)
	 * actually maybe it is (it just biases that you can log maximum participation points at midnight)
	 * (accumilate every day)  
	 */ 
	 $olde = $user['e'];
	 $oldp = $user['p'];
	 $oldr = $user['r'];
	 $oldtp = $user['tp'];
	 date_default_timezone_set('America/New_York');
	 $ti =  $user['ti'];
	 $datei = getdate($ti);
	 $tn = time();
	 $daten =  getdate($tn);
	 $dateoldtp =getdate($oldtp);
	 
	 
	 $ediff = $activity - $olde; // only todays activity gets sent from android right now so thats E
	 
	 if ($ediff < 0){
	 	$olde = 0;  // this resets activity if its a new day and $activity is low
	 	$ediff = $activity;
		$r = 0;
		$tp_output = $ti;
		
	 }
	 
	 $e = $olde + $ediff;
	 


	 
	
	 $p = $oldp + $ediff; // p always accumilates with the difference, never resets
	
	
	if(isset($tp)){ // when the prompt time is transmitted, record the current steps for the day  - only done with a new tp 
		$r = $e; 
		$tp_output = $tp;
	}
	else if($daten['yday']> $dateoldtp['yday']){ 
		$r = 0;
		$tp_output = $ti;
		
	}
	// if its a new day, reinitialize r ... r just records activit
	// it is up to the plugin to compare it to whatever, so if app wants you to walk 500, then should do:
	// $e - $r as output  of responsivness 
	
	
	//$db ->  updateUser('mike', $p, $e, $r,$tp);
	
	$res = $db -> updateUser($id, (isset($p)) ? $p : null, (isset($e)) ? $e : null, (isset($r)) ? $r : null, (isset($tp_output)) ? $tp_output : null,(isset($ti)) ? $ti : null);
	
	//this is for debug purposes
	$user = $db ->getUser($id);
	$resp = ($user['e'] - $user['r']) /  (time() - $user['tp']) * 60*60;
	 
	$response["message"] = "participation =  " . $user['p'] . ', engagement ' .  $user['e']  . ' responsiveness ' .  $resp  . '/n';
	
	echoRespnse(200, $response);
	
	
	
	

	
	
	
});


$app -> post('/register', function() use ($app) {// WORKS
	include_once dirname(__FILE__) . '/Config.php';

	verifyRequiredParams(array('name', 'email', 'password'));

	$response = array();

	// reading post params
	$name = $app -> request -> post('name');
	$email = $app -> request -> post('email');
	$password = $app -> request -> post('password');

	// validating email address
	validateEmail($email);

	$db = new DbHandler();
	//handle create user in dbHandler
	$res = $db -> createUser($name, $email, $password);

	echoRespnse(200, $res);

});

/**
 * Listing all user names
 * method GET
 * returns json array with all user names
 * url /users
 */
$app -> get('/users', function() {
	$response = array();

	$db = new DbHandler();
	//handle create user in dbHandler
	$response = $db -> getAllUserNames();

	//respond with that jawn and we'll parse it later
	echoRespnse(200, $response);

	// echoRespnse(200, $response);

});

//TODO log in api

//TODO security
$app -> run();
?>