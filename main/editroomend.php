<?php
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])) {
    header ("Location:index.php");
    exit();
}
require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');
require_once ('../include/header.php');
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);

$eseqno =$_POST['eseqno'];
$ebid = $_POST['eSelBuilding'];
$erid = $_POST['erid'];
$ername = $_POST['ername'];
$s = $erid[0].$erid[1];
$unit=$_POST['Selunit'];


if($s != $ebid){
	echo '<h1 style="text-align:center;margin:3px 0;">編輯失敗!!</h1>';
	echo '<meta http-equiv="refresh" content="1; url=https://booking.isrcttu.net/main/Meetingroom.php">';
}
else{

	$sqlcmd = "UPDATE `meetingroom` SET `buildingid`='$ebid',`roomid`='$erid',`roomname`='$ername',`unitcode`='$unit' WHERE `seqno`= '$eseqno' ";
	//echo $sqlcmd;
	$rs = querydb($sqlcmd , $db_conn);
	
	echo '<h1 style="text-align:center;margin:3px 0;">編輯會議室成功!!</h1>';
	echo '<meta http-equiv="refresh" content="1; url=https://booking.isrcttu.net/main/Meetingroom.php">';
}
	
?>
<body>

</body>
</html>