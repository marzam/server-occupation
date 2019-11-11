<?php

function get_values($mysql){
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

	$result = $conn->query($mysql);
	if ($conn->connect_error) {
		myLog("Error querinng:" . $conn->error);
		return;
	}

	$myArray = array();
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		//printf ("%s,%s\n", $row["date_time"], $row["device_signal"]);	    
		//array_push($myArray, array("y" => $row["device_signal"], "x" => $row["date_time"]));
		array_push($myArray, array("y" => $row["device_signal"], "x" => strtotime($row["date_time"])*1000));
		//array_push($myArray, array("y" => $row["device_signal"], "x" => date_create_from_format('Y/m/d H:i:s', $row["date_time"])));
	}
	//echo json_encode($myArray);

	#$result->num_rows;
	$conn->close();
	return $myArray;
}

$stmt = "SELECT * FROM tbNetworkRecord WHERE id_addr='9';";
$dataPoints = get_values($stmt);

//exit;


?>
    <!DOCTYPE HTML>
    <html>
    <head>
<script>
window.onload = function () {

	var chart = new CanvasJS.Chart("chartContainer", {
	title: {
		text: "Push-ups Over a Week"
	},
	axisY: {
		title: "Number of Push-ups"
	},
	data: [{
	type: "line",
		//color: "blue",
		xValueType: "dateTime",
		dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
	}]
});
chart.render();

}
</script>
</head>
<body>
<div id="chartContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>                              
