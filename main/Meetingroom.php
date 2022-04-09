<?php
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])) {
    header ("Location:index.php");
    exit();
}
require_once('../include/menu.php');
require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');
require_once ('../include/header.php');
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
if(!isset($Meetingroom)) $Meetingroom ="";
$ID = $_SESSION['LoginID'];
$admin = $_SESSION['loginAdmin'];
$UID = $_SESSION['Loginunitcode'];
$useradmin = $_SESSION['loginAdmin'];
$sysadmin = $_SESSION['Loginsysadmin'];

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
<div style="width:80%;text-align:center;margin:3px auto;">
<form method="POST" name="Booking" action="" align="center">
	<div align="right"> 
	<input class="btn btn-link" type = 'button' value = '+新增會議室' onclick = "location.href = 'addroom.php' "/>&nbsp;
	</div>
	<table width ="500" height ="20" border ="1" bgcolor = "white" align="center" class="table table-striped table-sm">
		<thead class="thead-dark">
			<tr><th>操作</th><th>會議室ID</th><th>會議室名稱</th><th>隸屬單位</th></tr>
		</thead>
		<?php	
			$sql = "SELECT * FROM meetingroom WHERE unitcode='$UID' AND valid='Y' ORDER BY unitcode "; 
			$rs = querydb($sql,$db_conn) ;
			//print_r($result);
			
			if(count($rs) > 0){
				foreach($rs as $item){
					echo '<tr>';
					echo '<td>';
					$bid = $item['roomid'][0].$item['roomid'][1];
					$fname = $item['roomid'];
					$p = "room";
					$DspMsg = "'確認要刪除資料?' ";
					$PassArg = "'delete.php?delfn=$fname & roomp= $p'";
					echo "<a href=\"javascript:confirmation($DspMsg, $PassArg);\" > <img src='../images/cut.gif' border='0' width='14' height='12' alt=刪除 ></a>&nbsp;";
					echo '<a href="editroom.php?seqno='.$item['seqno'].'&rid='.$item['roomid'].'&rname='.$item['roomname'].'&bingid='.$bid.'"> <img src="../images/edit.gif" border="0" width="14" height="12" alt=編輯 ></a>&nbsp;</td>';
					echo "<td>". $item['roomid'] ."</td><td>".$item['roomname']."</td><td> ". $item['unitcode'] .'</td>';
					echo "</tr>";
				}
			}
			if($sysadmin == 1){
				$sqlcmd="SELECT * FROM `sysmgr` WHERE email='$ID' AND valid='Y'";
				$rs=querydb($sqlcmd,$db_conn);
				if(count($rs) > 0){
					foreach($rs as $it){
						$uid = $it['unitcode'];
						if($uid == $UID) continue;
						$sql = "SELECT * FROM meetingroom WHERE unitcode='$uid' AND valid='Y' ORDER BY unitcode "; 
						$result = querydb($sql,$db_conn) ;
						
						if(count($result) > 0){
							foreach($result as $item){
								echo '<tr>';
								echo '<td>';
								$bid = $item['roomid'][0].$item['roomid'][1];
								$fname = $item['roomid'];
								$p = "room";
								$DspMsg = "'確認要刪除資料?' ";
								$PassArg = "'delete.php?delfn=$fname & roomp= $p'";
								echo "<a href=\"javascript:confirmation($DspMsg, $PassArg);\" > <img src='../images/cut.gif' border='0' width='14' height='12' alt=刪除 ></a>&nbsp;";
								echo '<a href="editroom.php?seqno='.$item['seqno'].'&rid='.$item['roomid'].'&rname='.$item['roomname'].'&bingid='.$bid.'"> <img src="../images/edit.gif" border="0" width="14" height="12" alt=編輯 ></a>&nbsp;</td>';
								echo "<td>". $item['roomid'] ."</td><td>".$item['roomname']."</td><td> ". $item['unitcode'] .'</td>';
								echo "</tr>";
							}
						}
					}
				}
				
			}
			
		?>
		
	</table>

</form>
</div>
</body>

</html>
