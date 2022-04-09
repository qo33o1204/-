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
$admin = $_SESSION['loginAdmin'];

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
<div align="right">
<?php if($admin == 1){?>
<input class="btn btn-link" type = 'button' value ='+新增單位' onclick = "location.href = 'addunit.php'"/>
<?php }?>
</div>
<table width ="500" height ="20" border ="1" bgcolor = "white" align="center" class="table table-striped table-sm">
<thead class="thead-dark">
	<tr><th width='100' >操作</th><th>單位名稱</th><th>單位ID</th></tr>
</thead>
<?php 
	$sqlcmd = "SELECT * FROM units WHERE showinlist = 'Y' ";
	$rs = querydb($sqlcmd , $db_conn);
	
	foreach($rs as $item){
		echo '<tr>';
		if($admin == 1){
			echo '<td>';
			$fname = $item['unitcode'];
			$p = 'unit';
			$DspMsg = "'確認要刪除資料?' ";
			$PassArg = "'delete.php?delfn=$fname & unitp= $p'";			
			echo "<a href=\"javascript:confirmation($DspMsg, $PassArg);\" > <img src='../images/cut.gif' border='0' width='14' height='12' alt=刪除> </a>";
			echo'<a href="editunit.php?seqno='.$item['seqno'].'&eunitcode='.$item['unitcode'].'&eunitname='.$item['unitname'].' " ><img src="../images/edit.gif" border="0" width="14" height="12" alt=編輯></a></td>';	
		}else{
			echo '<td></td>';
		}
	
		echo '<td>'.$item['unitname'].'</td><td>'.$item['unitcode'].'</td>';
		echo '</tr>';
	}
?>
</table>
<br/><br/>
</div>	
</body>
</html>