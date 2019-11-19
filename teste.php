<?php
$tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
echo `whoami`
die($tmp_dir);
?>
