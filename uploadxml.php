<?php
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

function execute_sql($mysql){
	global $conn;
	$result = $conn->query($mysql);
	return $result;
}

//echo "Hello\n";
$dataPOST = trim(file_get_contents('php://input'));
$xmlData = simplexml_load_string($dataPOST);

//$fileName = "log/" . getRealIpAddr() . "." . date("Y_m_d-H_i_s", time()) . ".xml";
//$handle = fopen($fileName, "w");
//fwrite($handle,  $xmlData->asXML());
//fclose($handle);

if ($xmlData->count() < 1)
	return;

$records = $xmlData->children();
for ($i = 0; $i < $xmlData->count(); $i++){
	$timestamp = $records[$i]->timestamp;
	$device_id = $records[$i]->device_id;
	$mac = strtoupper($records[$i]->mac);
	$signal = $records[$i]->signal;
	$pack_type = $records[$i]->pack_type;

	$result = execute_sql("SELECT ID FROM tbDeviceAddr WHERE addr='". $mac . "';");

	if($field = $result->fetch_assoc()){
		$id = $field['ID'];
	}else {
		execute_sql("INSERT INTO tbDeviceAddr (addr, type) VALUES ('". $mac . "', 'W');");
		$result = execute_sql("SELECT ID FROM tbDeviceAddr WHERE addr='". $mac . "';");
		$field = $result->fetch_assoc();
		$id = $field['ID'];

	}//end-if($id = $result->fetch_assoc()){
	execute_sql("INSERT INTO tbNetworkRecord (date_time, device_id, device_signal, id_addr, pack_type) VALUES ('" . $timestamp . "', '" . $device_id . "', '" . $signal . "', '" . $id . "', '" . $pack_type . "');");

	//echo 'ID' . $id . PHP_EOL;
}//end-for ($i = 0; $i < $xmlData->count(); $i++){

$conn->close();
echo 'END' . PHP_EOL;


?>
