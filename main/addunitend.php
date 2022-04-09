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

$auname =$_POST['auname'];
$aucode =$_POST['aucode'];


$sqlcmd = "SELECT * FROM units WHERE unitname = '$auname' OR unitcode = '$aucode' ";
$rs = querydb($sqlcmd , $db_conn);
if(count($rs) > 0){
	echo '<h1 style="text-align:center;margin:3px 0;">資料有重複!新增失敗</h1>';
}
else{
	$sqlcmd = "INSERT INTO `units`(`unitcode`, `unitname`, `showinlist`) VALUES ('$aucode','$auname','Y')";
	$rs = querydb($sqlcmd , $db_conn);
	echo '<h1 style="text-align:center;margin:3px 0;">新增成功!</h1>';
	
}
echo '<meta http-equiv="refresh" content="1; url=https://booking.isrcttu.net/main/unit.php">';

?>
<body>

</body>
</html>