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
$uid = $_SESSION['Loginunitcode'];

$bid = $_POST['SelBuilding'];
$rid = $_POST['rid'];
$rname = $_POST['rname'];
$s = $rid[0].$rid[1];
$unit=$_POST['Selunit'];

$sqlcmd = "SELECT * FROM meetingroom WHERE roomid = '$rid'";
$rs = querydb($sqlcmd,$db_conn);
if(count($rs) > 0){
	echo '<meta http-equiv="refresh" content="1; url=https://booking.isrcttu.net/main/Meetingroom.php">';
	echo '<h1 style="text-align:center;margin:3px 0;">已經有此會議室了!</h1>';
}
else if($s != $bid){
	echo '<meta http-equiv="refresh" content="1; url=https://booking.isrcttu.net/main/Meetingroom.php">';
	echo '<h1 style="text-align:center;margin:3px 0;">新增失敗!</h1>';
}
else{

	$sqlcmd = "INSERT INTO `meetingroom`(buildingid,roomid,roomname,unitcode,valid) VALUES ('$bid','$rid','$rname','$unit','Y')";
	$rs = querydb($sqlcmd,$db_conn);		
	
	
	echo '<meta http-equiv="refresh" content="1; url=https://booking.isrcttu.net/main/Meetingroom.php">';
	echo '<h1 style="text-align:center;margin:3px 0;">會議室新增成功!</h1>';	
}

?>
<body></body>
</html>