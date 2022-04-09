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


$ename = $_POST['ename'];
$editemail =$_POST['editemail'];
$eunitcode = $_POST['Selunit'];
$eadmin=$_POST['eadmin'];
$echecktimes=$_POST['checkts'];
$ebooktime=$_POST['booktime'];

if($eadmin == 0){
	$sqlcmd = "UPDATE `bookuser` SET `name`='$ename',`unitcode`='$eunitcode',`useradmin`='1',`sysadmin`='1',`checktimes`='$echecktimes',`booktime`='$ebooktime' WHERE email = '$editemail' ";
}
else{
	$sqlcmd = "UPDATE `bookuser` SET `name`='$ename',`unitcode`='$eunitcode',`useradmin`='$eadmin',`sysadmin`='0',`checktimes`='$echecktimes',`booktime`='$ebooktime' WHERE email = '$editemail' ";
}
$rs = querydb($sqlcmd,$db_conn);

echo '<h1 style="text-align:center;margin:3px 0;">使用者編輯成功!</h1>';	

?>
<body>
<meta http-equiv="refresh" content="1; url=https://booking.isrcttu.net/main/users.php">
</body>
</html>