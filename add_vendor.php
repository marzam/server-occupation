<?php
include "MacvendorsApi.php";
use \macvendors_co;
$api = new \macvendors_co\MacVendorsApi();

function connect_mysql(){
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

	return $conn;
}

function query($conn, $mysql){
	$result = $conn->query($mysql);
	if ($conn->connect_error) {
		myLog("Error querinng:" . $conn->error);
		return;
	}

	$myArray = array();
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
	//	echo json_encode($row);
		//printf ("%s,%s\n", $row["date_time"], $row["device_signal"]);	    
		//array_push($myArray, array("y" => $row["size"], "x" => $row["hour"]));
		array_push($myArray, $row);
		//array_push($myArray, array("y" => $row["device_signal"], "x" => date_create_from_format('Y/m/d H:i:s', $row["date_time"])));
	}
	return $myArray;
}

$conn = connect_mysql();

//$date = htmlspecialchars($_GET["date"]);
$devices = query($conn, "select addr from tbDeviceAddr where vendor is null;");

foreach ($devices as $mac){
	echo $mac["addr"] . " ";
	$vendor = $api->get_vendor ($mac["addr"],'csv');
	echo $vendor['company']."\n";
	echo "Updating... ";
	//Exemplo:    update tbDeviceAddr set vendor = 'teste                     ' where addr = 'DC:BF:E9:C7:D7:8B   ';
	$conn->query("update tbDeviceAddr set vendor = '" . $vendor['company'] . "' where addr = '" . $mac["addr"] . "';");
	if ($conn->connect_error) {
		myLog("Error querinng:" . $conn->error);
		return;
	}
	echo "done!\n";
}

//echo json_encode($devices);

$conn->close();

exit;
?>
