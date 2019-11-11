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

  $conn->close();
  return $result;
}


//  echo "Hello\n";

    $dataPOST = trim(file_get_contents('php://input'));
    $xmlData = simplexml_load_string($dataPOST);

    $fileName = "log/" . getRealIpAddr() . "." . date("Y_m_d-H_i_s", time()) . ".xml";

    $handle = fopen($fileName, "w");
    fwrite($handle,  $xmlData->asXML());
    fclose($handle);

    $records = $xmlData->children();
    for ($i = 0; $i < $xmlData->count(); $i++){
      $timestamp = $records[$i]->timestamp;
      $device_id = $records[$i]->device_id;
      $mac = strtoupper($records[$i]->mac);
      $signal = $records[$i]->signal;
      $result = execute_sql("SELECT ID FROM tbDeviceAddr WHERE addr='". $mac . "';");

      if($field = $result->fetch_assoc()){
        $id = $field['ID'];
      }else {
        execute_sql("INSERT INTO tbDeviceAddr (addr, type) VALUES ('". $mac . "', 'W');");
        $result = execute_sql("SELECT ID FROM tbDeviceAddr WHERE addr='". $mac . "';");
        $field = $result->fetch_assoc();
        $id = $field['ID'];

      }//end-if($id = $result->fetch_assoc()){
      execute_sql("INSERT INTO tbNetworkRecord (date_time, device_id, device_signal, id_addr) VALUES ('" . $timestamp . "', '" . $device_id . "', '" . $signal . "', '" . $id . "');");

      echo 'ID' . $id . PHP_EOL;
    }//end-for ($i = 0; $i < $xmlData->count(); $i++){

  echo 'END' . PHP_EOL;


?>
