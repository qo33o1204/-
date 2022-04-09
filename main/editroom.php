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
if (!isset($eSelBuilding)) $eSelBuilding = $bingid;
$arrBuilding = array();
foreach ($rs as $item) {
	$bid = $item['buildingid'];
	$arrBuilding["$bid"] = $item['buildingid'];
}
$sysadmin = $_SESSION['Loginsysadmin']; 
$UID = $_SESSION['Loginunitcode'];
$ID=$_SESSION['LoginID'];

?>

<body>
	<form method='POST' name='eroom' action ='editroomend.php' align = "center">
	<table width= "500" border ="1" bgcolor = "white" align="center" >
		<tr height = "50">
		  <th>編號</th>
		  <input type = 'hidden' name ='eseqno' value='<?php echo $seqno; ?>'/>
		  <td><?php echo $seqno; ?></td>
		</tr>
		<tr height = "50">
		  <th>大樓編號 </th>
		  <td> 
			<select name="eSelBuilding">
			<?php
			foreach ($arrBuilding as $buildingid) {
				echo '<option value="' . $buildingid . '"';
				if ($eSelBuilding==$buildingid) echo ' selected';
				echo ">$buildingid</option>\n";
			}
			?>

			</select>&nbsp;
   		  </td>
		</tr>
		<tr height = "50">
		  <th>會議室編號</th>
		  <td><input type = 'text' name = 'erid' value ='<?php echo $rid; ?>'></td>
		</tr>
		<tr height = "50">
		  <th>會議室名字</th>
		  <td><input type = 'text' name = 'ername' value ='<?php echo $rname; ?>'> </td>
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
		
	</table><br/><br/>
	<input class="btn btn-light" type = 'submit' value = '確認修改' />
	<input class="btn btn-light" type = 'button' value = '放棄修改'  onclick = "location.href = 'Meetingroom.php'"/>
	</form>
</body>
</html>