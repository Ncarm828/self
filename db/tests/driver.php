<?php


// driver to insert and query into mongo dob

//TODO catch requessts for either inserts or queries

$method = $_SERVER['REQUEST_METHOD'];
$paths = $_SERVER['REQUEST_URI'];
echo $paths;
echo "<br>";
echo $method;

echo "<br>";

// this is localhost by default but it oculd be  "mongodb://mikekorostelev.com"
$connection = new MongoClient(); // connect to a remote host (default port: 27017)
$db = $connection->self_db;
$collection = $db->self_host;

//TODO process queries and respond in json format
//equations that translate should be server side i think


//handle query
$document = $collection->findOne();
var_dump( $document );


//handle insert


?>