<?php
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])) {
    header ("Location:index.php");
    exit();
}
date_default_timezone_set('Asia/Taipei');
$today = date('Y/m/d');

$useradmin = $_SESSION['loginAdmin']; //使用SESSION來取得使用者資訊
$sysadmin = $_SESSION['Loginsysadmin']; 
$ID = $_SESSION['LoginID'];
$UID = $_SESSION['Loginunitcode'];
require_once('../include/menu.php');
require_once ('../include/header.php');
require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);

if (isset($delfn) && !empty($delfn)) {
	
	$sqlcmd="SELECT * FROM `bookrecord` WHERE `date`='$delfn' AND `begin`='$btime' AND `email`='$userid' AND `finish`='$ftime' ";
	$rs = querydb($sqlcmd , $db_conn);
	if(count($rs) > 0){
		$sqlcmd = "UPDATE `bookrecord` SET `permission`=1 WHERE `permission`=0  AND `date`='$delfn' AND `begin`='$btime' AND `email`='$userid' AND `finish`='$ftime'";
		$rs = querydb($sqlcmd , $db_conn);
		
		$sqlcmd = "SELECT * FROM `bookrecord` WHERE `date`='$delfn' AND (`begin` BETWEEN '$btime' AND '$ftime' OR `finish` BETWEEN '$btime' AND '$ftime') AND `valid`='Y' AND `permission`=0 ";
		$rs = querydb($sqlcmd , $db_conn);
		if(count($rs) > 0){
			foreach($rs as $item){
				$b=$item['begin'];
				$f=$item['finish'];
				$sqlcmd = "UPDATE `bookrecord` SET `denyreason`='已經有人預約' ,`valid`= 'N' WHERE `date`='$delfn' AND `begin`='$b' AND `finish`='$f'";
				$rs = querydb($sqlcmd , $db_conn);
			}
		}
		
	}
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
<div style="width:80%;text-align:center;margin:3px auto;">
	<table width = '1000' border ="1" bgcolor = "white" align="center" class="table table-striped table-sm">
		<thead class="thead-dark">
			<tr><th width = "50">情形</th><th width = "70">日期</th><th width = "100">開始時間</th><th width = "100">結束時間</th><th width="100">預約會議室</th><th width="70">預約者</th><th width="100">事由</th><th width="100">說明</th></tr>
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
				
			$sqlcmd = "SELECT * FROM `bookrecord` WHERE date>'$today' AND permission=0 AND valid = 'Y' ORDER BY date DESC ";
			$rs = querydb($sqlcmd , $db_conn);
			if(count($rs) > 0){
				foreach($rs as $item){
					$flag = 0;
					foreach($arrunit as $it){
						if($item['unitcode'] == $it){
							$flag = 1;
							break;
						}
					}
					if($flag = 1){
					
						echo '<tr>';
						echo '<td>';
						$fname = $item['date'];
						$bt = $item['begin'];
						$ft = $item['finish'];
						$user=$item['email'];
						$DspMsg = "'確認要允許此預約?' ";
						$PassArg = "'check.php?delfn=$fname & btime=$bt & ftime=$ft & userid=$user'";
						echo "<a href=\"javascript:confirmation($DspMsg, $PassArg);\" > <img src='../images/check.gif' border='0' width='14' height='12' alt=允許> </a>";
						echo '<a href ="deletecheck.php?d='.$fname.'&btime='.$bt.'&ftime='.$ft.'"><img src="../images/cross.gif" border="0" width="14" height="12" alt=刪除> </a>';
						echo '</td>';
						echo '<td>'.$item['date'].'</td><td>'.$item['begin'].'</td><td>'.$item['finish'].'</td><td>' .$item['roomid'].'</td><td>'.$item['people'].'</td><td>'.$item['reason'].'</td><td>'.$item['remark'].'</td>';
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
				$sqlcmd = "SELECT * FROM `bookrecord` WHERE date>'$today' AND permission=0 AND valid = 'Y' ORDER BY date DESC ";
				$rs = querydb($sqlcmd , $db_conn);
				if(count($rs) > 0){
					foreach($rs as $item){
						$flag = 0;
						foreach($arrunit as $it){
							if($item['unitcode'] == $it){
								$flag = 1;
								break;
							}
						}
						if($flag = 1){
							echo '<tr>';
							echo '<td>';
							$fname = $item['date'];
							$bt = $item['begin'];
							$ft = $item['finish'];
							$user=$item['email'];
							$DspMsg = "'確認要允許此預約?' ";
							$PassArg = "'check.php?delfn=$fname & btime=$bt & ftime=$ft & userid=$user'";
							echo "<a href=\"javascript:confirmation($DspMsg, $PassArg);\" > <img src='../images/check.gif' border='0' width='14' height='12' alt=允許> </a>";
							echo '<a href ="deletecheck.php?d='.$fname.'&btime='.$bt.'&ftime='.$ft.'"><img src="../images/cross.gif" border="0" width="14" height="12" alt=刪除> </a>';
							echo '</td>';
							echo '<td>'.$item['date'].'</td><td>'.$item['begin'].'</td><td>'.$item['finish'].'</td><td>' .$item['roomid'].'</td><td>'.$item['people'].'</td><td>'.$item['reason'].'</td><td>'.$item['remark'].'</td>';
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