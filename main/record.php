<?php
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])) {
    header ("Location:index.php");
    exit();
}
date_default_timezone_set('Asia/Taipei');
$today = date('Y/m/d');
require_once('../include/menu.php');
require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');
require_once ('../include/header.php');
$db_conn = connect2db($dbhost , $dbuser , $dbpwd,$dbname);

$useradmin = $_SESSION['loginAdmin']; //使用SESSION來取得使用者資訊
$sysadmin = $_SESSION['Loginsysadmin']; 
$ID = $_SESSION['LoginID'];
$UID = $_SESSION['Loginunitcode'];

?>

<Script Language="JavaScript">
function confirmation(DspMsg, PassArg) {
var name = confirm(DspMsg)
    if (name == true) {
      location=PassArg;
    }
}
</SCRIPT>

<body>
<div style="width:80%;text-align:center;margin:3px auto;">
<table  border ="1" bgcolor = "white" align="center"  class="table table-striped table-sm" >
	<thead class="thead-dark">
		<tr><th width ='50'>操作</th><th width = "100">日期</th><th width = "100">開始時間</th><th width = "100">結束時間</th><th width="100">預約會議室</th><th width="100">預約者</th><th width="100">事由</th><th width="100">說明</th><th width="100">取消說明</th></tr>
	</thead>
	<?php
		$arrunit=array();
		if($sysadmin == 1){
			$sqlcmd="SELECT * FROM sysmgr WHERE email='$ID'";
			$rs=querydb($sqlcmd , $db_conn);
			
			foreach($rs as $item){
				$uid=$item['unitcode'];
				$sql="SELECT * FROM meetingroom WHERE unitcode='$uid' ";
				$result=querydb($sql,$db_conn);
				foreach($result as $it){
					$r = $it['seqno'];
					$arrunit["$r"]=$it['roomid'];
				}
				
			}	
				
			$sqlcmd = "SELECT * FROM `bookrecord` ORDER BY date DESC ";
			$rs = querydb($sqlcmd , $db_conn);
			if(count($rs) > 0){
				foreach($rs as $item){
					$flag = 0;
					foreach($arrunit as $it){
						if($item['roomid'] == $it){
							$flag = 1;
							break;
						}
					}
					if($flag == 1){
						echo '<tr>';
						$fname = $item['date'];					
						$bt=$item['begin'];
						$ft=$item['finish'];
						$p="record";
						$DspMsg = "'確認要刪除資料?'";
						$PassArg = "'delete.php?delfn=$fname & recordp=$p & btime=$bt & ftime=$ft'";
						if(strtotime($fname) > strtotime($today)){
							echo '<td>';
							echo "<a href=\"javascript:confirmation($DspMsg, $PassArg);\" > <img src='../images/cut.gif' border='0' width='14' height='12' alt=刪除> </a>";
							echo '</td>';
						}
						else echo'<td></td>'; 
						echo '<td>'.$item['date'].'</td><td>'.$item['begin'].'</td><td>'.$item['finish'].'</td> <td>' .$item['roomid'].'</td><td>'.$item['people'].'</td><td>'.$item['reason'].'</td><td>'.$item['remark'].'</td><td>'.$item['denyreason'].'</td>';
						echo '</tr>';
					}
					
				}
			}
		}		
		else{
			$sql="SELECT * FROM meetingroom WHERE unitcode='$UID'";
			$result=querydb($sql,$db_conn);
			if(count($result) > 0){
				foreach($result as $it){
					$r = $it['seqno'];
					$arrunit["$r"]=$it['roomid'];
					
				}		
				$sqlcmd = "SELECT * FROM `bookrecord` ORDER BY date DESC ";
				$rs = querydb($sqlcmd , $db_conn);
				if(count($rs) > 0){
					foreach($rs as $item){
						$flag = 0;
						foreach($arrunit as $it){
							if($item['roomid'] == $it){
								$flag = 1;
								break;
							}
						}
						if($flag == 1){
							echo '<tr>';
							$fname = $item['date'];					
							$bt=$item['begin'];
							$ft=$item['finish'];
							$p="record";
							$DspMsg = "'確認要刪除資料?'";
							$PassArg = "'delete.php?delfn=$fname & recordp=$p & btime=$bt & ftime=$ft'";
							if(strtotime($fname) > strtotime($today)){
								echo '<td>';
								echo "<a href=\"javascript:confirmation($DspMsg, $PassArg);\" > <img src='../images/cut.gif' border='0' width='14' height='12' alt=刪除> </a>";
								echo '</td>';
							}
							else echo'<td></td>'; 
							echo '<td>'.$item['date'].'</td><td>'.$item['begin'].'</td><td>'.$item['finish'].'</td> <td>' .$item['roomid'].'</td><td>'.$item['people'].'</td><td>'.$item['reason'].'</td><td>'.$item['remark'].'</td><td>'.$item['denyreason'].'</td>';
							echo '</tr>';
						}
					}
				}
			}
		}
	?>
	
</table>	
</div>			
</body>
</html>