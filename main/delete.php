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

if (isset($delfn) && !empty($delfn)) {
	
	if(isset($userp) && !empty($userp)){
		$sqlcmd = "DELETE FROM `bookuser` WHERE email='$delfn' ";
		$rs = updatedb($sqlcmd , $db_conn);
		header('Location: users.php');
		
	}
	else if(isset($unitp) && !empty($unitp)){
		$sqlcmd = "UPDATE `units` SET `showinlist`= 'N' WHERE unitcode='$delfn' ";
		$rs = updatedb($sqlcmd , $db_conn);
		header('Location: unit.php');
	}
	else if(isset($roomp) && !empty($roomp)){
		$sqlcmd="UPDATE `meetingroom` SET `valid`='N' WHERE `roomid`='$delfn'";
		$rs = querydb($sqlcmd , $db_conn);
	
		header('Location: Meetingroom.php');
		//echo "刪除room";
	}
	else if(isset($recordp) && !empty($recordp)){
		$sqlcmd = "DELETE FROM `bookrecord` WHERE date='$delfn' AND begin='$btime' AND finish='$ftime' ";
		$rs = querydb($sqlcmd , $db_conn);
		if(isset($pos) && !empty($pos)){
			header('Location: mybook.php');
		}
		else header('Location: record.php');
	}
	
	
}


?>