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
date_default_timezone_set('Asia/Taipei');
$today = date('Y/m/d');
$LoginID= $_SESSION['LoginID'];
$UserName = $_SESSION['LoginName'];
$useradmin = $_SESSION['loginAdmin']; 
$sysadmin = $_SESSION['Loginsysadmin']; 
$UID = $_SESSION['Loginunitcode'];
$checktimes=$_SESSION['Loginchecktimes'];
$booktime=$_SESSION['Loginbooktime'];
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);

$reason = $_POST['reason'];
$d = $_POST['usedate'];
$bt = $_POST['btime'];
$ft = $_POST['ftime'];
$build = $_POST['usebuild'];
$remark = $_POST['remark'];
$s = $build[0].$build[1]; 

$sqlcmd = "SELECT * FROM meetingroom WHERE roomid = '$build' ";
$rs = querydb($sqlcmd,$db_conn);
if(count($rs) > 0){
	$rname = $rs[0]['roomname'];
	$uid=$rs[0]['unitcode'];
}

if($checktimes == 0){
	$sqlcmd = "INSERT INTO `bookrecord`(`email`, `date`, `begin`, `finish`, `people`, `buildingid`, `roomid`, `roomname`, `reason`, `remark`,`permission`,`valid`)
	VALUES ('$LoginID','$d','$bt:00','$ft:00','$UserName','$s','$build','$rname','$reason','$remark','1','Y')";
	$rs = querydb($sqlcmd,$db_conn);
	echo '<script> alert("預約成功");</script> ';
	//echo '<h1 style="text-align:center;margin:3px 0;">成功預約!</h1>';
}
else{
	$sqlcmd = "SELECT * FROM bookrecord WHERE date > '$today' AND valid='Y' AND email='$LoginID'";
	$rs = querydb($sqlcmd,$db_conn);
	if(count($rs)> 3){
		echo '<script> alert("預約失敗");</script> ';
		//echo '<h1 style="text-align:center;margin:3px 0;">預約失敗!</h1>';
	}
	else{
		$t=0;
		foreach($rs as $item){
			$b = $item['begin'];
			$f = $item['finish'];
			$t+= $f - $b;
		}
		$t += $ft-$bt;
		if($t >= $booktime){
			echo '<script> alert("預約失敗");</script> ';
			//echo '<h1 style="text-align:center;margin:3px 0;">預約失敗!</h1>';
		}
		else{
			$sqlcmd = "INSERT INTO `bookrecord`(`email`, `date`, `begin`, `finish`, `people`, `buildingid`, `roomid`, `roomname`, `reason`, `remark`)
			 VALUES ('$LoginID','$d','$bt:00','$ft:00','$UserName','$s','$build','$rname','$reason','$remark')";
			$rs = querydb($sqlcmd,$db_conn);
			
			echo '<script> alert("預約成功");</script> ';
			//echo '<h1 style="text-align:center;margin:3px 0;">成功預約!</h1>';
		}
	}
	
}


?>
<body>

</body>

</html>