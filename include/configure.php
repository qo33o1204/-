<?php
$SystemName = '會議室預約系統';
$dbhost = "mariadb.cc-isac.org";
$dbname = "projbooking";
$dbuser = "bookingdbuser";
$dbpwd = "yxul4dj4";
$uDate = date("Y-m-d H:i:s");
$ErrMsg = "";
$UserIP = '';
if (isset($_SERVER['HTTP_VIA']) && isset($_SERVER['HTTP_X_FORWARDED_FOR'])) 
    $UserIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
else if (isset($_SERVER['REMOTE_ADDR'])) $UserIP = $_SERVER['REMOTE_ADDR'];
$ThisYear = date('Y');
?>
