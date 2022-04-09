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
//echo $ftime;
if(isset($_POST['Submit']) && !empty($_POST['Submit'])){
	$sqlcmd = "UPDATE bookrecord SET denyreason='$dreason',valid='N' WHERE date='$d' AND begin='$btime' AND finish='$ftime'";
	$rs = querydb($sqlcmd , $db_conn); 
	header("Location:check.php");
}


?>
<body>

<div style="text-align:center;margin:3px 0;">
<form method='POST' action='' >
	<div class="text-center">
	<h1 class="text-white" style="font-size:2.5em">刪除原因<h1>
	</div>
	<input type ='hidden' name = 'd' value = '<?php echo $d; ?>'/>
	<input type ='hidden' name = 'btime' value = '<?php echo $btime; ?>'/>
	<input type ='hidden' name = 'ftime' value = '<?php echo $ftime; ?>'/>
	<textarea cols="60" rows="3" name = 'dreason'><?php echo $dreason; ?></textarea>
	<br/>
	<select name="dreason" class="btn btn-light" >
		<?php
			$sqlcmd="SELECT * FROM `denyreason` WHERE 1";
			$rs = querydb($sqlcmd,$db_conn);
			$arreason=array();
			foreach($rs as $item){
				$s = $item['seqno'];
				$arreason["$s"]=$item['reason'];
				
			}
			foreach($arreason as $rea){
				echo '<option value="'.$rea.'"';
				echo '>'.$rea.'</option>\n';
			}
		?>
	</select>
	<br/><br/>
	<input class="btn btn-light" type = 'submit'  name = 'Submit' value = '確定' />
	<input class="btn btn-light" type = 'button'  value = '放棄' onclick = "location.href='check.php '"/>
</form>
</div>

</body>
</html>