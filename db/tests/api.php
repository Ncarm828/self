<?php
 

require 'Slim/Slim/Slim.php';
 
\Slim\Slim::registerAutoloader();
 
$app = new \Slim\Slim();



/**
 * Validating user name
 * @param String $user the user name of the person
 */
function validateUser($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
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
    $app->status($status_code);
 
    // setting response content type to json
    $app->contentType('application/json');
 
    echo json_encode($response);
}



/**
 * Listing all tasks of particual user
 * method GET
 * url /tasks          
 */
$app->get('/user/:id', function($id) {
            $response = array();
			
			
			$connection = new MongoClient(); // connect to a remote host (default port: 27017)
			$db = $connection->self_db;
			$collection = $db->self_host;
			$document = $collection->findOne();
			$connection->close();
			
           // array_push($response["tasks"], $document);
           
 
            echoRespnse(200, $document);
			
            echoRespnse(200, $id);
        });
 
 
$app->run();
 
 

 
 
 ?>