<?php
function myLog($msg){
  $fileName = getRealIpAddr() . ".error.txt";
  $handle = fopen($fileName, "w");
  fwrite($handle,  $msg . "\n");
  fclose($handle);
}
function getRealIpAddr(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])){
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
      $ip=$_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

function execute_sql($conn, $mysql){
	$result = $conn->query($mysql);
	if ($conn->connect_error) {
		myLog("Error quering:" . $conn->error);
	}

	return $result;
}

$servername = "localhost";
$username   = "localuser";
$password   = "localuser";
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
	myLog("Error loading character set utf8:" . $conn->error);
	return;
}
if (!$conn->set_charset("utf8")) {
	myLog("Error loading character set utf8:" . $conn->error);
	return;
}

$sql = "USE dbNetwork;";
$conn->query($sql);
if ($conn->connect_error) {
	myLog("Error querinng:" . $conn->error);
	return;
}

$dataPOST = trim(file_get_contents('php://input'));
$xmlData = simplexml_load_string($dataPOST);


$records = $xmlData->children();
$stringsql = "INSERT INTO tbNetworkRecord_v2 (date_time, device_id, device_signal, mac, pack_type) VALUES";

for ($i = 0; $i < $xmlData->count(); $i++){
	$timestamp = $records[$i]->timestamp;
	$device_id = strtoupper($records[$i]->device_id);
	$pack_type = $records[$i]->pack_type;
	$mac = strtoupper($records[$i]->mac);
	$signal = $records[$i]->signal;

	$stringsql .= " ('" . $timestamp . "', '" . $device_id . "', '" . $signal . "', '" . $mac . "', '". $pack_type . "'),";

}//end-for ($i = 0; $i < $xmlData->count(); $i++){
$stringsql = substr( $stringsql , 0, -1) ;
//echo $stringsql . PHP_EOL;
var_dump(execute_sql($stringsql));
//$conn->multi_query($stringsql);
$conn->close();
echo 'OK' . PHP_EOL;
?>
