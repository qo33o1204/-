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
$ID=$_SESSION['LoginID'];

if(isset($delfn) && !empty($delfn)){
	$sqlcmd="DELETE FROM `sysmgr` WHERE `unitcode`='$delfn' AND `email`='$ID' ";
	$rs = querydb($sqlcmd,$db_conn);
}

if(isset($_POST['Selunit']) && !empty($_POST['Selunit'])){
	$UID = $_POST['Selunit'];
	
	$sqlcmd="SELECT * FROM sysmgr WHERE email='$ID' AND unitcode='$UID' AND valid='Y' ";
	$rs = querydb($sqlcmd , $db_conn);
	if(count($rs)==0){ 
		
		$sqlcmd="SELECT * FROM units WHERE unitcode='$UID' ";
		$rs=querydb($sqlcmd,$db_conn);
		if(count($rs) > 0){
			$uname = $rs[0]['unitname'];
			$sql="INSERT INTO `sysmgr`(`email`,`unitname`,`unitcode`,`valid`) VALUES ('$ID','$uname','$UID','Y')";
			$result=querydb($sql,$db_conn);
		}
	}
	$_POST['Selunit'] = '';
}
?>

<Script Language="JavaScript">
<!--
function confirmation(DspMsg, PassArg) {
var name = confirm(DspMsg)
    if (name == true) {
      location=PassArg;
    }
}
-->
</SCRIPT>

<body>
	<div style="width:70%;text-align:center;margin:3px auto;">
	<div class="text-right">
	<form method='POST' name='addunit' action=''>
	<select name="Selunit" class="btn btn-light">
		<?php
		$sqlcmd = "SELECT * FROM units WHERE showinlist='Y' ";
		$rs = querydb($sqlcmd , $db_conn);
		$arrunit = array();
		foreach($rs as $item){
			$uid = $item['unitname'];
			$arrunit["$uid"] = $item['unitcode'];
		}
		
		foreach($arrunit as $uid => $Unitcode ){
			echo '<option value="'.$Unitcode.'"';
			echo '>'.$uid.'</option>\n';
		}
		
		?>
	</select>
	<input class="btn btn-link" type="submit" name="addunit" value="+加入"/>
	<input class="btn btn-link" type="button" value="返回" onclick ="location.href ='users.php'"/>
	</form>
	</div>
	<table height ="20" border ="1" bgcolor = "white" align="center" class="table table-striped table-sm">
			<thead class="thead-dark">
			<tr><th width = "100">操作</th><th width="100">單位名稱</th><th width="100">單位代號</th></tr>
			</thead>
			<?php
			$sqlcmd="SELECT * FROM `sysmgr` WHERE email='$ID' AND valid='Y'";
			$rs=querydb($sqlcmd,$db_conn);
			if(count($rs)>0){
				foreach($rs as $item){
					echo '<tr>';
					echo '<td>';
					$fname = $item['unitcode'];
					$DspMsg = "'確認要刪除資料?'";
					$PassArg = "'addmgrunit.php?delfn=$fname'";
					echo "<a href=\"javascript:confirmation($DspMsg, $PassArg);\" > <img src='../images/cut.gif' border='0' width='14' height='12' alt=刪除> </a></td>";
						
					echo '<td>'.$item['unitname'].'</td><td>'.$item['unitcode'].'</td>';
					echo '</tr>'; 
				}
			}
			?>
	</table>	
	</div>
</body>
</html>