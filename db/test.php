<?php

require 'Slim/Slim/Slim.php';
require_once 'DbHandler.php';
require_once 'PassHash.php';
\Slim\Slim::registerAutoloader();

//$app = new \Slim\Slim();

$db = new DbHandler();
$user = $db ->getUser('mike');

//print_r($user['p']);

//print_r($user);

// $db -> updateUser('mike', 888,null,null,null, null);

$user = $db ->getUser('mike');
print_r($user);
$activity = 1071;
//$tp = time();


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
		$tp = $ti;
		
	 }
	 
	 $e = $olde + $ediff;
	 

	 
	
	 $p = $oldp + $ediff; // p always accumilates with the difference, never resets
	
	
	if(isset($tp)){ // when the prompt time is transmitted, record the current steps for the day  - only done with a new tp 
		$r = $e; 
	}
	else if($daten['yday']> $dateoldtp['yday']){ 
		$r = 0;
		$tp = $ti;
		
		//$db ->  updateUser('mike',null, null, $r, $tp);
	}
	// if its a new day, reinitialize r ... r just records activit
	// it is up to the plugin to compare it to whatever, so if app wants you to walk 500, then should do:
	// $e - $r as output  of responsivness 
	
	
	$db ->  updateUser('mike', $p, $e, $r,$tp);
	$user = $db ->getUser('mike');
	print_r($user)

	
	
	/**
	 * get current time of day, the egagement is activty/hours of day (how active you are today on average)
	 * (reset every day) // calcuated
	 */
	
	
	
	/**
	 * how responsive you are to challanges (at this point this will only work if activity is logged right
	 * at the tiem the challenge is initiated because we need (new_acitivity - actvity_when_first_prompted )/(timenow - timeprompt)  )
	 * the value of responsiveness should add up throughout the day and maybe somehow factor into participation at the end of the day
	 * (resets every day)
	 */
	
	

	//echo( $db ->checkLogin('mike', 'jello') );


?>