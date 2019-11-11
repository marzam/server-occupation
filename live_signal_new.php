<?php
// Script start
//$rustart = getrusage();

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

function get_value($conn, $mysql){
	$result = $conn->query($mysql);
	if ($conn->connect_error) {
		myLog("Error querinng:" . $conn->error);
		return;
	}

	//$num = mysqli_num_rows($result);
//	exit;

	$myArray = array();
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
	//	echo json_encode($row);
		//printf ("%s,%s\n", $row["date_time"], $row["device_signal"]);	    
		//array_push($myArray, array("y" => $row["size"], "x" => $row["hour"]));
		$date = date_create_from_format("Y-m-d H:i:s.u", $row["time"]); 
		array_push($myArray, array("id" => $row["mac"], "y" => $row["signal"], "x" => ($date->getTimestamp()*1000)+substr($date->format('u'),0,3))); //strtotime($row["time"])*1000)); //$row["time"]));

		//array_push($myArray, array("y" => $row["device_signal"], "x" => date_create_from_format('Y/m/d H:i:s', $row["date_time"])));
	}
	//exit;
	//echo json_encode($myArray);

	//$result->num_rows;
	//return $num;
	return $myArray;
}

//echo 'Data: ' . htmlspecialchars($_GET["date"]) . ' !';
//$stmt = "SELECT * FROM tbNetworkRecord WHERE id_addr='9';";
//$dataPoints = get_values($stmt);
//exit;
//$date = htmlspecialchars($_GET["date"]);
//echo "select distinct id_addr from tbNetworkRecord where date_time BETWEEN  '" . $date . " " . "09" . ":00:00' AND '" . $date . " " . "09" . ":59:59';";
//get_values("select distinct id_addr from tbNetworkRecord where date_time BETWEEN  '" . $date . " " . "09" . ":00:00' AND '" . $date . " " . "09" . ":59:59';");

$conn = connect_mysql();

$minutos = htmlspecialchars($_GET["minutos"]);
$dataPoints = get_value($conn, "select mac, date_time as 'time', device_signal as 'signal' from tbNetworkRecord_v2 where date_time < current_timestamp and date_time > date_add(current_timestamp, interval -" . $minutos . " minute) and substring(pack_type,4,1) = '8' order by mac, time;");
#$dataPoints = get_value($conn, "select id_addr, date_time as 'time', device_signal as 'signal' from tbNetworkRecord where date_time < current_timestamp and date_time > date_add(current_timestamp, interval -" . $minutos . " minute) and substring(pack_type,4,1) = '8' order by id_addr, time;");
#$dataPoints = get_value($conn, "select id_addr, date_time as 'time', device_signal as 'signal' from tbNetworkRecord where date_time < current_timestamp and date_time > date_add(current_timestamp, interval -" . $minutos . " minute) and substring(pack_type,3,2) = '40' order by id_addr, time;");


//for ($i = 0; $i < 24; $i++) {
//	$value = get_value($conn, "select distinct id_addr from tbNetworkRecord where date_time BETWEEN  '" . $date . " " . $i . ":00:00' AND '" . $date . " " . $i . ":59:59';");
	//$value = get_value($conn, "select id_addr from tbNetworkRecord where date_time BETWEEN  '" . $date . " " . $i . ":00:00' AND '" . $date . " " . $i . ":59:59';");
//	array_push($dataPoints, array("y" => $value, "x" => strtotime($date . " " . sprintf("%02d", $i) . ":00:00")*1000));
	//echo sprintf(" %02d", $i);
//}

$conn->close();

echo json_encode($dataPoints);

//exit;

// Script end
function rutime($ru, $rus, $index) {
	return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
		-  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}

//$ru = getrusage();
//echo "This process used " . rutime($ru, $rustart, "utime") .
//	" ms for its computations\n";
//echo "It spent " . rutime($ru, $rustart, "stime") .
//	" ms in system calls\n";
exit;
?>
