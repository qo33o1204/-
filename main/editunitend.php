<?php
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])) {
    header ("Location:index.php");
    exit();
}
require_once ('../include/header.php');
require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);

$eunitname = $_POST['eunitname'];
$eunitcode = $_POST['eunitcode'];
$s = $_POST['seqno'];

$sqlcmd = "SELECT * FROM units WHERE seqno =! '$s' AND unitcode = '$eunitcode' AND unitname = '$eunitname' ";
$rs = querydb($sqlcmd ,$db_conn);
if(count($rs) > 0){
	echo '<h1 style="text-align:center;margin:3px 0;">編輯失敗!!</h1>';
	echo '<meta http-equiv="refresh" content="1; url=https://booking.isrcttu.net/main/unit.php">';

}
else{
	
	$sqlcmd = " UPDATE `units` SET `unitcode`= '$eunitcode',`unitname`='$eunitname' WHERE `seqno` = '$s'";
	$rs=querydb($sqlcmd ,$db_conn);
	
	echo '<h1 style="text-align:center;margin:3px 0;">編輯成功!!</h1>';
	echo '<meta http-equiv="refresh" content="1; url=https://booking.isrcttu.net/main/unit.php">';
   
	
}



?>
<body>

</body>

</html>