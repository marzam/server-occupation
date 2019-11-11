<?php

function execute_sql($mysql){
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

        printf ("date,signal\n");	    
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
            //$myArray[] = $row;
	    printf ("%s,%s\n", $row["date_time"], $row["device_signal"]);	    
	}
	//echo json_encode($myArray);

	#$result->num_rows;
	$conn->close();
	return;
}

echo $_SERVER['REQUEST_URI'];

echo "\n";
//$username = 'localuser';
//$password = 'localuser';
//$database = 'dbNetwork';
//$hostname = 'localhost';

// id of a user
//$id_addr = '1';
//$dbh = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
$stmt = "SELECT * FROM tbNetworkRecord WHERE id_addr='9';";
//$stmt->bindParam( ':id_addr', $id_addr, PDO::PARAM_STR );
//$stmt->execute();
//$result = $stmt->fetchAll( PDO::FETCH_ASSOC );
//echo json_encode( execute_sql($stmt) );

execute_sql($stmt);

?>

