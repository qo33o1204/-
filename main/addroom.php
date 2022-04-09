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

$sqlcmd = "SELECT * FROM building WHERE valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
$arrBuilding = array();
foreach ($rs as $item) {
	$bid = $item['buildingid'];
	$arrBuilding["$bid"] = $item['buildingid'];
}
$sysadmin = $_SESSION['Loginsysadmin']; 
$ID = $_SESSION['LoginID'];
$UID = $_SESSION['Loginunitcode'];

?>
<body>


<form method="POST" name="AddRoom" action="addroomend.php">
	
	<table width ="500" border ="1" bgcolor = "white" align = "center">
	<tr height = "50">
		<th>大樓編號 </th>
		<td> 
			<select name="SelBuilding">
			<?php
			foreach ($arrBuilding as $buildingid) {
				echo '<option value="' . $buildingid . '"';
				if ($SelBuilding==$buildingid) echo ' selected';
				echo ">$buildingid</option>\n";
			}
			?>

			</select>&nbsp;
		</td>
	</tr>
	<tr height = "50">
		<th>會議室編號</th>
		<td><input type = 'text' name = 'rid' > ex : A1-101 </td>
	</tr>
	<tr height = "50">
		<th>會議室名字</th>
		<td><input type = 'text' name = 'rname'> </td>
	</tr>
	<tr height="50">
		<th>隸屬單位</th>
			<?php if($sysadmin == 1){?>
			<td>
			<select name="Selunit">
				<?php
				$sqlcmd = "SELECT * FROM sysmgr WHERE email='$ID' ";
				$rs = querydb($sqlcmd , $db_conn);
				$arrunit = array();
				foreach($rs as $item){
					$uid = $item['unitname'];
					$arrunit["$uid"] = $item['unitcode'];
				}
				
				foreach($arrunit as $uid => $Unitcode ){
					echo '<option value="'.$Unitcode.'"';
					//if ($Selunit == $uid) echo 'selected';
					echo '>'.$uid.'</option>\n';
				}
				
				?>
			</select>
			</td>
			<?php } else{ ?>
			<td> <input type = 'hidden' name = 'Selunit' value='<?php echo $UID; ?>'><?php echo $UID; ?> </td>
			<?php } ?>
	</tr>

	</table>
	<div style="text-align:center;margin:3px 0;">
		<input class="btn btn-light" type="submit" name="Confirm" value="確認送出" />&nbsp;
		<input class="btn btn-light" type="button" name="Abort" value="放棄新增" onclick = "location.href = 'Meetingroom.php'" />
	</div>
	

</form>
</body>

</html>