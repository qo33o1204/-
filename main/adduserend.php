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

$aemail = $_POST['aemail'];
$aname = $_POST['aname'];
$apwd = $_POST['apwd'];
$aunitcode = $_POST['Selunit'];
$admin = $_POST['admin'];
$ct=$_POST['checkts']; 


$sqlcmd = "SELECT * FROM bookuser WHERE email = '$aemail' ";
$rs = querydb($sqlcmd , $db_conn);

if(count($rs) > 0){
	echo '<h1 style="text-align:center;margin:3px 0;">已經有此人!</h1>';
}
else{
	if($admin == 0){
		
		$sqlcmd="INSERT INTO `bookuser`(`name`, `email`, `userpwd`, `unitcode`, `useradmin`,`sysadmin`, `valid`, `pwderrcount`,`checktimes`,`booktime`) 
	VALUES ('$aname','$aemail','$apwd','$aunitcode','1','1','Y',0,0,0)";
	}
	if($admin == 1){
		
		$sqlcmd="INSERT INTO `bookuser`(`name`, `email`, `userpwd`, `unitcode`, `useradmin`,`sysadmin`, `valid`, `pwderrcount`,`checktimes`,`booktime`) 
		VALUES ('$aname','$aemail','$apwd','$aunitcode','$admin','0','Y','0','0','0')";
		
	}
	if($admin == 2){ 
		$bookt = 8;
		if($ct == 0){
			$bookt = 0;
		}
		$sqlcmd="INSERT INTO `bookuser`(`name`, `email`, `userpwd`, `unitcode`, `useradmin`,`sysadmin`, `valid`, `pwderrcount`,`checktimes`,`booktime`) 
		VALUES ('$aname','$aemail','$apwd','$aunitcode','$admin','0','Y','0','$ct','$bookt')";
	}
	$rs=querydb($sqlcmd ,$db_conn);
	echo '<h1 style="text-align:center;margin:3px 0;">新增成功!</h1>';

}

?>
<meta http-equiv="refresh" content="1; url=https://booking.isrcttu.net/main/users.php">
<body>
</body>
</html>
