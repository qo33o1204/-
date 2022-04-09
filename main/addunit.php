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

?>
<form method="POST" name="Addunit" action="addunitend.php">
	
	<table width ="500" border ="1" bgcolor = "white" align = "center">
	<tr height = "50">
		<th>單位名稱 </th>
		<td> <input type = 'text' name = 'auname' ></td>
	</tr>
	<tr height = "50">
		<th>單位ID</th>
		<td><input type = 'text' name = 'aucode' > </td>
	</tr>

	</table>
	<br/>
	<div style="text-align:center;margin:3px 0;">
		<input class="btn btn-light" type="submit" name="Confirm" value="確認送出" />&nbsp;
		<input class="btn btn-light" type="button" name="Abort" value="放棄新增" onclick = "location.href = 'unit.php'" />
	</div>
	

</form>
</body>

</html>