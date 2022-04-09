<?php
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])) { //SESSION值為空，代表是複製網址到此頁面
    header ("Location:index.php");								   //如果是，則跳轉到登入的介面
    exit();
}
date_default_timezone_set('Asia/Taipei');
$userid = $_SESSION['LoginID'];
$admin = $_SESSION['loginAdmin'];
$sysadmin = $_SESSION['Loginsysadmin'];

require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');
require_once('../include/menu.php');
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
//大樓的ID跟名字放入陣列裡
$sqlcmd = "SELECT * FROM building WHERE valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
$arrBuilding = array();
foreach ($rs as $item) {
	$bid = $item['buildingid'];
	$arrBuilding["$bid"] = $item['buildingname'];
}

if (!isset($SelBuilding)) $SelBuilding='A1';   
if (!isset($SelRoom)) $SelRoom = 'A1-101';
if (!isset($SelDate)) $SelDate = date('Y-m-d');
require_once ('../include/header.php');
$MinDate = date('Y-m-d', strtotime('-60day'));
$MaxDate = date('Y-m-d', strtotime('+90day'));
//判斷要顯示最愛會議室還是全部會議室
if(isset($_POST['favorroom']) && !empty($_POST['favorroom'])){
	$_SESSION['showroom'] = 1;
}
else if(isset($_POST['allroom']) && !empty($_POST['allroom'])){
	$_SESSION['showroom'] = 2;
}
$op = $_SESSION['showroom'];
//如果有點選新增常用會議室，則將該會議室加入使用者的最愛會議室
if(isset($_POST['addfavorroom']) && !empty($_POST['addfavorroom'])){
	$room=$_POST['addroom'];
	$sqlcmd ="INSERT INTO `favouriterooms`(`roomid`, `userid`, `valid`) VALUES ('$room','$userid','Y') ";
	$rs = querydb($sqlcmd,$db_conn);
}
////如果有點選刪除最愛會議室，則從使用者的常用會議室裡刪除該會議室
if(isset($_POST['deletefavorroom']) && !empty($_POST['deletefavorroom'])){
	$room=$_POST['deleteroom'];
	$sqlcmd ="DELETE FROM `favouriterooms` WHERE `userid`='$userid' AND `roomid`='$room' ";
	$rs = querydb($sqlcmd,$db_conn);
}
$arrweek=array('','日','一','二','三','四','五','六');
?>

<body>
<div style="width:90%;text-align:center;margin:3px auto;">
<form method="POST" name="Booking" action="" align = "center">
	<div align='right'>
	<input class="btn btn-light" type="date" name="SelDate" value="<?php echo $SelDate; ?>" min="<?php echo $MinDate; ?>" max="<?php echo $MaxDate; ?>" onchange="submit()"  >
	<input class="btn btn-secondary" type='submit' name='favorroom' value ='常用會議室'/>
	<input class="btn btn-secondary" type='submit' name='allroom' value ='全部會議室'/>
	<?php 
		if($op ==1){  //顯示常用會議室的下拉室選單
			$sqlcmd = "SELECT * FROM favouriterooms WHERE userid='$userid' AND valid='Y'";
			$rs = querydb($sqlcmd , $db_conn);
			if(count($rs) > 0){
				$arrfroom = array();
				foreach ($rs as $item) {
					$bid = $item['seqno'];
					$arrfroom["$bid"] = $item['roomid'];
				}
				echo '<select name="SelRoom" onchange="submit()">';
					foreach ($arrfroom as $rid ) {
						echo '<option value="' . $rid . '"';
						if ($SelRoom == $rid) echo ' selected';
						echo ">$rid</option>\n";
					}
				echo '</select>&nbsp;';
				echo '<input class="btn btn-link" type="submit" name=deletefavorroom value="移除常用"/>';
				echo '<input type="hidden" name=deleteroom value='.$SelRoom.'>';
			}
			else{
				$op=2; 
			}	
			
		}
	?>
	<?php 
		
		if($op == 2){//顯示全部會議室的下拉室選單
			$sqlcmd = "SELECT * FROM meetingroom WHERE buildingid='$SelBuilding' AND valid='Y' ORDER BY roomid";
			$rs = querydb($sqlcmd, $db_conn);
			$arrRooms = array();
			$FirstRoom = '';
			if (count($rs) > 0) {
				foreach ($rs as $item) {
					$rid = $item['roomid'];
					if (empty($FirstRoom)) $FirstRoom = $rid;
					$arrRooms["$rid"] = $item['roomname'];
				}
			}
			if (substr($SelRoom,0,2)<>$SelBuilding) $SelRoom = $FirstRoom;
			echo '<select name="SelBuilding" onchange="submit()">';
			foreach ($arrBuilding as $buildingid=>$buildingName) {
				echo '<option value="' . $buildingid . '"';
				if ($SelBuilding==$buildingid) echo ' selected';
				echo ">$buildingName</option>\n";
			}
			echo '</select>&nbsp;';
			echo '<select name="SelRoom" onchange="submit()">';
				foreach ($arrRooms as $rid => $RoomName) {
					echo '<option value="' . $rid . '"';
					if ($SelRoom == $rid) echo ' selected';
					echo ">$rid</option>\n";
				}
			echo '</select>&nbsp;';
			$sqlcmd = "SELECT * FROM favouriterooms WHERE userid='$userid' AND roomid='$SelRoom' AND valid='Y'";
			$rs = querydb($sqlcmd , $db_conn);
			if(count($rs) == 0){
				echo '<input class="btn btn-link" type="submit" name=addfavorroom value="加入常用"/>';
				echo '<input type="hidden" name=addroom value = '.$SelRoom.'>';
			}
			
		}
	?>
	<br/>
	</div>
	<div style="width:95%;margin:3px auto">
		<table width ="800" border ="3" bgcolor = "white" align="center" class="table table-striped table-sm">			
			<?php
			$t = strtotime($SelDate); 
			$d = date('w',$t);
			$t1 = $t - $d*86400;
			$t2 = $t + (6-$d)*86400;
			$startdate = date('Y-m-d',$t1);
			$enddate = date("Y-m-d", $t2);
			$curdate = date('Y-m-d',$t1);
			echo '</br>';
			// 從資料庫找這一星期有預約的資料
			$sqlcmd = "SELECT * FROM bookrecord WHERE roomid = '$SelRoom' AND date BETWEEN '$startdate' AND '$enddate'  AND permission=1 AND valid='Y' ORDER BY date";  
			$rs = querydb($sqlcmd,$db_conn);
			//新增一個空的陣列
			for($i=0;$i<24;$i++){
				for($j=0;$j<7;$j++){
					$arrtable[$i][$j]= "";
				}
			}
			//將資料放進陣列裡
			foreach ($rs as $item){ 
				$d = (strtotime($item['date']) - strtotime($startdate))/24/60/60;
				$bt = $item['begin']-8;
				$ft = $item['finish']-8;
				for($i=$bt+1;$i<=$ft;$i++){
					$arrtable[$i][$d] = $item['people'];	
				}	
			}
			
			$a = 8;
			$arrdate=array();
			//使用TABLE來顯示以放入資料的陣列
			for($i=0; $i<=16; $i++){  // 表格code 
				
				if($i == 0){ //first column
					echo '<thead class="thead-dark">';
					echo '<tr height = "30">';	
					for($j=0;$j<8;$j++){
						if($j == 0)
							echo '<th width="150" class="text-center">時間/日期</th>'; //first row
						else{
							echo '<th width="150" class="text-center" > '.$curdate.' ('. $arrweek[$j].')'.'</th>';
							$arrdate[$j-1] = $curdate;
							$curdate = date("Y-m-d", strtotime("$curdate +1 days"));
						}
					} 
					echo '</tr>';
					echo '</thead>';
				}
				else{
					echo '<tr height = "50">';	
					for($j=0; $j<8; $j++){
						if($j == 0){
							if($a  == 8){
								echo '<td bgcolor="#cfc7f8">0'.$a.' : 00 - 0'.($a+1) . ': 00 </td>';
							}
							else if($a==9){
								echo '<td bgcolor="#cfc7f8"> 0'.$a.' : 00 - '.($a+1) . ': 00 </td>';
							}
							else
								echo '<td bgcolor="#cfc7f8"> '.$a.' : 00 - '.($a+1) . ': 00 </td>';
						}else{
							if($j==1 || $j==7) echo '<td class="text-center" bgcolor="#e3eeff">';
							else echo '<td class="text-center">'; 
							
							if(!empty($arrtable[$i][$j-1])) echo $arrtable[$i][$j-1];
							else if(strtotime($arrdate[$j-1]) < strtotime('today'));
							else if(strtotime($arrdate[$j-1]) == strtotime('today')){
								if($a > date(H)){
									echo '<a href="reservation.php?startdate=' . $startdate . '&offset=' . ($j-1) 
									. '&session=' . $a . '&build='. $SelRoom.'"><img src="../images/addevent.gif" border="0" width="14" height="12" alt=新增></a>';
								} 
							}
							else{
								echo '<a href="reservation.php?startdate=' . $startdate . '&offset=' . ($j-1) 
								. '&session=' . $a . '&build='. $SelRoom.'"><img src="../images/addevent.gif" border="0" width="14" height="12" alt=新增></a>';
							}
							echo "</td>";
						}
					}
					echo "</tr>";
					$a++;$b++;
				}
				
			}
			?>
		</table>
	</div>
	<div  class="text-right">
	<p><img src="../images/me.gif" border="0" width="150" height="100" alt=designer></p>
	</div>
	

	<?php require_once ('../include/footer.php'); ?>
	<div >
	<input class='btn btn-link' type='button' name='contact' value='聯絡客服' onclick="location.href='contact.php'"></input> 
	<p>或致電於(02)0800-3221 </p>
	</div>

</form>
</div>
</body>
</html>