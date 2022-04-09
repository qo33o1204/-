<?php
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])) {
    header ("Location:index.php");
    exit();
}
$LoginID= $_SESSION['LoginID'];
$UserName = $_SESSION['LoginName'];
$checktimes=$_SESSION['Loginchecktimes'];

require_once ('../include/header.php');
require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');

if(!isset($reason)) $reason = "";
$t = date("Y-m-d", strtotime("$startdate +$offset days"));

?>
<body>
<form method="POST" name="Booking" action="addreservation.php" align = "center" class="table table-striped table-sm" >
	<br/><br/>
	<table width ="700" border ="3px solid" bgcolor = "white" align = "center"  >
		<tr height="50">
		  <th>預約用途  : </th>
		  <td><input type="text" name="reason" value="<?php echo $reason; ?>" />
		  </td>
		</tr>
		<tr height="50"> 
		  <th >使用日期 : </th>
		  <td > <input type = "hidden" name = "usedate" value ="<?php echo $t; ?>"/> <?php echo $t; ?> </td>
		</tr>
		<tr height="50"> 
		  <th>使用時間 : </th>
		  <td><input type = "hidden" name = "btime" value ="<?php echo $session; ?>"/><?php echo $session; ?> ~ <select name="ftime" id="">
			<?php
				for($i=$session+1; $i <= 24; $i++){
					echo '<option value="' .$i .'">'. $i . '</option>';
				}
			?>	
			</select>
		  </td>
		</tr>	
		<tr height="50">
		 <th>使用場地 : </th>
		 <td><input type = "hidden" name = "usebuild" value ="<?php echo $build; ?>"/><?php echo $build; ?> </td>
		</tr>
		<tr height="50">
		  <th>預約者 : </th>
		  <td><?php echo $UserName; ?></td>
		</tr>
		<tr height="50">
		 <th>其他說明 : </th>
		 <td><textarea cols="60" rows="5" name = 'remark'><?php echo $Remark; ?></textarea>
		 </td>
		</tr>
	</table><br/>
	
	<input class="btn btn-outline-dark" type = 'submit' value = '提交' />
	<input class="btn btn-outline-dark" type = 'button' value = '放棄'  onclick = "location.href='interface.php '"/>
	
</form>
</body>

</html>