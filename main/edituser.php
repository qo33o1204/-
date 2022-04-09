<?php
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])){
    header ("Location:index.php");
    exit();
}
require_once ('../include/header.php');
require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
$sysadmin = $_SESSION['Loginsysadmin']; 
$UID = $_SESSION['Loginunitcode'];
$ID=$_SESSION['LoginID'];
$useradmin = $_SESSION['loginAdmin'];

?>

<body>
<div style="text-align:center;margin:3px 0;">
<form method='POST' name='euser' action ='edituserend.php' align = "center">
	<table width= "500" border ="1" bgcolor = "white" align="center" >
		<tr height = "50">
		  <th>名字</th>
		  <td><input type = 'text' name ='ename' value='<?php echo $ename; ?>'/></td>
		</tr>
		<tr height = "50">
		  <th>電子郵件 </th>
		  <input type = 'hidden' name ='editemail' value='<?php echo $editemail; ?>'/>
		  <td><?php echo $editemail; ?></td>
		</tr>
		<tr height = "50">
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
						echo '>'.$uid.'</option>\n';
					}
					
					?>
				</select>
				</td>
				<?php } else{ ?>
				<td> <input type = 'hidden' name = 'Selunit' value='<?php echo $UID; ?>'><?php echo $UID; ?> </td>
				<?php } ?>
		</tr>
		<?php if($useradmin==1){ ?>
		<tr height="50">
			<th>權限</th>
			<td>
				<select name = "eadmin">
					<?php if($sysadmin == 1){ ?>
					<option value = 0 >系統管理者</option>
					<?php }?>
					<option value = 1 >用戶管理者</option>
					<option value = 2 >一般使用者</option>				
				</select>
			</td>
		</tr>
		<tr height = "50">
			<th>預約上限</th>
			<td><input type = 'text' name = 'checkts' value='<?php echo $echecktimes; ?>'></td>
		</tr>
		<tr height="50">
			<th>可預約時數</th>
			<td>
			<select name = "booktime">
				<option value =0>0</optione>
				<option value = 8>8</option>
				<option value = 16>16</option>
				<option value = 24>24</option>
			</select>
			</td>
		</tr>
		<?php } ?>
	</table>
	<p>#只要為用戶管理員跟系統管理員，預約次數上限皆為0，即為無限制</p><br/>
	<input class="btn btn-light" type = 'submit' value = '確認修改' />
	<input class="btn btn-light" type = 'button' value = '放棄修改'  onclick = "location.href = 'users.php'"/>
</form>
</div>
</body>
</html>
