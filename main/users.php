<?php
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])) {
    header ("Location:index.php");
    exit();
}
require_once('../include/menu.php');
require_once ('../include/header.php');
require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);

$useradmin = $_SESSION['loginAdmin']; //使用SESSION來取得使用者資訊
$sysadmin = $_SESSION['Loginsysadmin'];
$ID = $_SESSION['LoginID'];
$UID = $_SESSION['Loginunitcode'];

?>

<Script Language="JavaScript">
<!--使用javascript彈跳出訊息確認框
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
<div align="right">
<?php if($sysadmin == 1){ ?>
<input class="btn btn-link" type = 'button' value ='+新增管理單位' onclick = "location.href='addmgrunit.php' " />
<?php } ?>
<?php if($useradmin == 1 ){ ?>
<input class="btn btn-link" type = 'button' value ='+新增使用者' onclick = "location.href = 'adduser.php'" />
<?php }?>
</div>
<table width ="700" height ="20" border ="1" bgcolor = "white" align="center" class="table table-striped table-sm">
		<thead class="thead-dark">
		<tr><th width = "100">操作</th><th width="200">使用者</th><th width="200">信箱</th><th width="100">單位代號</th><th >預約次數上限</th><th>可預約總時數</th><th>用戶權限</th><th>系統權限</th></tr>
		</thead>
		<?php
		if($useradmin == 1){  //判斷是否為用戶管理員
			//從資料庫找用戶的資料並顯示
			$sqlcmd = "SELECT * FROM bookuser WHERE unitcode='$UID' AND valid='Y' ORDER BY name";
			$rs = querydb($sqlcmd , $db_conn);

			foreach( $rs as $item){
				echo '<tr>';
				echo '<td>';
				$fname = $item['email'];
				$p="user";
				$DspMsg = "'確認要刪除資料?'";
				$PassArg = "'delete.php?delfn=$fname & userp=$p'";
				echo "<a href=\"javascript:confirmation($DspMsg, $PassArg);\" > <img src='../images/cut.gif' border='0' width='14' height='12' alt=刪除> </a>";
				echo '<a href="edituser.php?ename='. $item['name'] .'&editemail='. $item['email'] .'&eunitcode='. $item['unitcode'] .'&echecktimes='.$item['checktimes'].'"><img src="../images/edit.gif" border="0" width="14" height="12" alt=編輯></a></td>';
				//echo '<a href="edituser.php?ename='.$item['name'].'&editemail='.$item['email'].'">edit</a></td>';
				echo '<td>'. $item['name'] .'</td><td>'.$item['email']. '</td><td>'.$item['unitcode'].'</td><td>'.$item['checktimes'].'</td><td>'.$item['booktime'].'</td>';
				if($item['useradmin'] == 2) echo '<td></td>';
				else echo "<td><img src='../images/check.gif' border='0' width='14' height='12' alt=允許></td>";
				if($item['sysadmin'] == 0) echo '<td></td>';
				else echo "<td><img src='../images/check.gif' border='0' width='14' height='12' alt=允許></td>";
				echo '</tr>';
			}

			if($sysadmin == 1){                                                   //判斷是否為系統管理員
				$sqlcmd="SELECT * FROM `sysmgr` WHERE email='$ID' AND valid='Y'"; //從資料庫找到自己管理的單位，再將該單位的用戶依序顯示
				$rs=querydb($sqlcmd,$db_conn);
				foreach($rs as $it){
					$uid = $it['unitcode'];
					if($uid == $UID) continue;
					$sql = "SELECT * FROM bookuser WHERE unitcode='$uid' AND valid='Y' ORDER BY name";
					$result = querydb($sql , $db_conn);

					foreach($result as $item){
						echo '<tr>';
						echo '<td>';
						$fname = $item['email'];
						$p="user";
						$DspMsg = "'確認要刪除資料?'";
						$PassArg = "'delete.php?delfn=$fname & userp=$p'";
						echo "<a href=\"javascript:confirmation($DspMsg, $PassArg);\" > <img src='../images/cut.gif' border='0' width='14' height='12' alt=刪除> </a>";
						echo '<a href="edituser.php?ename='. $item['name'] .'&editemail='. $item['email'] .'&eunitcode='. $item['unitcode'] .'&echecktimes='.$item['checktimes'].'"><img src="../images/edit.gif" border="0" width="14" height="12" alt=編輯></a></td>';
						echo '<td>'. $item['name'] .'</td><td>'.$item['email']. '</td><td>'.$item['unitcode'].'</td><td>'.$item['checktimes'].'</td><td>'.$item['booktime'].'</td>';
						if($item['useradmin'] == 2) echo '<td></td>';
						else echo "<td><img src='../images/check.gif' border='0' width='14' height='12' alt=允許></td>";
						if($item['sysadmin'] == 0) echo '<td></td>';
						else echo "<td><img src='../images/check.gif' border='0' width='14' height='12' alt=允許></td>";
						echo '</tr>';
					}

				}
			}
		}
		else{  //如果不是用戶管理員或系統管理員，則顯示資己的資訊
			$sqlcmd = "SELECT * FROM bookuser WHERE valid='Y' AND email='$ID' ORDER BY unitcode";
			$rs = querydb($sqlcmd , $db_conn);
			echo "<tr>";
			echo '<td>';
			echo '<a href="edituser.php?ename='.$rs[0]['name'].'&editemail='.$rs[0]['email'].'&eunitcode='.$rs[0]['unitcode'].'" ><img src="../images/edit.gif" border="0" width="14" height="12" alt=編輯> </a></td>';
			echo '<td>'. $rs[0]['name'] .'</td><td>'.$rs[0]['email']. '</td><td>'.$rs[0]['unitcode'].'</td><td>'.$rs[0]['checktimes'].'</td><td>'.$rs[0]['booktime'].'</td><td></td><td></td>';
			echo '</tr>';
		}

		?>
</table><br/><br/>
</div>
</body>
</html>