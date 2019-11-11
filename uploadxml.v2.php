<?php

$dataPOST = trim(file_get_contents('php://input'));
$xmlData  = simplexml_load_string($dataPOST);
if ($xmlData->count() < 1)
	return;

$records  = $xmlData->children();

$dbname     = "dbNetwork";
$servername = "localhost";
$username   = "localuser";
$password   = "localuser";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // begin the transaction
    $conn->beginTransaction();
    for ($i = 0; $i < $xmlData->count() ; $i++){
    	$timestamp = $records[$i]->timestamp;
    	$device_id = strtoupper($records[$i]->device_id);
    	$pack_type = $records[$i]->pack_type;
    	$mac = strtoupper($records[$i]->mac);
    	$signal = $records[$i]->signal;

      $conn->exec("INSERT INTO tbNetworkRecord_v2 (date_time, device_id, device_signal, mac, pack_type) VALUES  ('" . $timestamp . "', '" . $device_id . "', '" . $signal . "', '" . $mac . "', '". $pack_type . "')");

    }//end-for ($i = 0; $i < $xmlData->count(); $i++){

    // commit the transaction
    $conn->commit();
    echo "New records created successfully";
}
catch(PDOException $e){
    // roll back the transaction if something failed
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn = null;


?>
