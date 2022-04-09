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
$sysadmin = $_SESSION['Loginsysadmin']; 
$UID = $_SESSION['Loginunitcode'];
$ID=$_SESSION['LoginID'];
?>

<body>
<div class="Container">
<form method="POST" name="AddUser" action="adduserend.php" class="table table-striped table-sm" >
	<div style="text-align:center;margin:3px 0;">
		<table width ="500" border ="1" bgcolor = "white" align = "center" >
			<tr height = "50">
				<th>電子郵件 </th>
				<td> <input type = 'text' name = 'aemail' ></td>
			</tr>
			<tr height = "50">
				<th>用戶名字</th>
				<td><input type = 'text' name = 'aname' > </td>
			</tr>
			<tr height = "50">
				<th>用戶密碼</th>
				<td><input type = 'text' name = 'apwd' ></td>
			</tr>
			<tr height = "50">
				<th>隸屬單位</th>
				<?php if($sysadmin == 1){?>
				<td>
				<select name="Selunit">
					<?php
					//單位的選擇使用下拉式選單
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
			<tr height = "50">
				<th>權限</th>
				<td>
					<select name = "admin">
						<option value = 2 >一般使用者</option>
						<option value = 1 >用戶管理者</option>
						<?php if($sysadmin == 1){ //只有系統管理員才可以新增系統管理者 ?>
						<option value = 0 >系統管理者</option>
						<?php }?>
					</select>
				</td>
			</tr>
			<tr height = "50">
				<th>預約次數上限</th>
				<td><input type = 'text' name = 'checkts' ></td>
			</tr>
		</table>
		<p>#只要為用戶管理員跟系統管理員，預約次數上限皆為0，即為無限制</p>
		<br/>
		<input class="btn btn-light" type="submit" name="Confirm" value="確認送出" />&nbsp;
		<input class="btn btn-light" type="button" name="Abort" value="放棄新增" onclick = "location.href = 'users.php'" />
	</div>
</form>
</div>
</body>
</html>