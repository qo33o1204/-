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

if(isset($roomp) && !empty($roomp) ){
	$sqlcmd = "SELECT * FROM meetingroom WHERE roomid='$roomp' AND valid='N' ";
	$rs = querydb($sqlcmd ,$db_conn);
	if(count($rs) > 0){
		$sqlcmd = "UPDATE `meetingroom` SET `valid`='Y' WHERE `roomid`='$roomp'";
		$rs = querydb($sqlcmd ,$db_conn);
		
		$sqlcmd = "UPDATE `roomadmin` SET `valid`='Y' WHERE `roomid`='$roomp'";
		$rs = querydb($sqlcmd ,$db_conn);
		header("Location:Meetingroom.php");
	}
}
else{
	$sqlcmd = "SELECT * FROM bookuser WHERE email='$uemail' AND seqno=$useqno AND valid='S'";
	$rs = querydb($sqlcmd ,$db_conn);
	if(count($rs) > 0){
		$sqlcmd = "UPDATE `bookuser` SET `valid`='Y',`pwderrcount`=0 WHERE `seqno`=$useqno";
		$rs = querydb($sqlcmd ,$db_conn);
		header("Location:users.php");
	}
}

?>