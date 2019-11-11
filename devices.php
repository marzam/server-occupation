<?php

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

	$myArray = array();
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
	//	echo json_encode($row);
		//printf ("%s,%s\n", $row["date_time"], $row["device_signal"]);	    
		//array_push($myArray, array("y" => $row["size"], "x" => $row["hour"]));
		array_push($myArray, array("y" => $row["size"], "label" => $row["id_addr"]));
		//array_push($myArray, array("y" => $row["device_signal"], "x" => date_create_from_format('Y/m/d H:i:s', $row["date_time"])));
	}
	return $myArray;
}

$conn = connect_mysql();

$date = htmlspecialchars($_GET["date"]);
#$dataPoints = get_value($conn, "select id_addr, count(id_addr) as 'size' from tbNetworkRecord group by id_addr order by size;");

$dataPoints = get_value($conn, "select * from (select a.id_addr, count(a.id_addr) as 'size' from tbNetworkRecord as a group by a.id_addr) as b where size > '0' order by size; ");

$conn->close();

//exit;
?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="UTF-8" />
		<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
		<script type="text/javascript" >
			window.onload = function() {
				var urlParams = new URLSearchParams(window.location.search);
				var dataPoints = [];
				var chart = new CanvasJS.Chart("chartContainer", {
					zoomEnabled: true,
					title: {
						text: "Dispositivos detectados"
					},
					axisY: {
						logarithmic: true,
						title: "Ocorrências"
					},
					data: [{
						type: "column",
						//xValueType: "dateTime",
						dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
					}]
				});
				chart.render();
				//console.log(dataPoints);
			}
		</script>
	</head>
	<body>
		<div id="chartContainer" style="height: 370px; width: 100%;"></div>
	</body>
</html>                              
