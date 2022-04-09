<?php
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])) {
    header ("Location:index.php");
    exit();
}
require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');
require_once ('../include/header.php');

$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
$useremail = $_POST['uemail'];
$password = $_POST['pwd'];

if(isset($Submit) && !empty($Submit) ){
	
	if(isset($useremail) && !empty($useremail)){	
		$sqlcmd = "SELECT * FROM bookuser WHERE email = '$useremail' AND valid='Y'";
		$rs = querydb($sqlcmd , $db_conn);
		
		if(count($rs) > 0){
			
			$upwd = $rs[0]['userpwd']; 
			if($upwd==$password){	
				$loginemail = $rs[0]['email'];
				$loginname = $rs[0]['name'];
				$logincode = $rs[0]['unitcode'];
				$loginlimit = $rs[0]['userlimit'];	
				$_SESSION['LoginID'] = $loginemail;
				$_SESSION['LoginName'] = $loginname;
				$_SESSION['Loginunitcode'] = $logincode;
				$_SESSION['loginAdmin'] = $loginlimit;
				header ("Location:interface.php");
				exit();
			}
			else{
				if ($rs[0]['pwderrcount']>2) {				
					$sqlcmd = "UPDATE bookuser SET valid='S',pwderrcount=pwderrcount+1 WHERE email='$useremail' AND valid='Y' ";              
					$result = querydb($sqlcmd, $db_conn);
				}
				else{
					$sqlcmd = "UPDATE bookuser SET pwderrcount=pwderrcount+1 WHERE email='$useremail' AND valid='Y' ";
					$result = querydb($sqlcmd, $db_conn);
				}
				//還有登入失敗的東西沒寫
				echo "<script>alert('登入失敗')</script>";
			}
		}
		else{
			echo "<script>alert('登入失敗')</script>";
		}
		
	}
	else{
		echo "<script>alert('登入失敗')</script>";
	}
}

?>

<body>
<form method = "POST" name = 'login' action = '' align = 'center'>

	<font size= 10 >LOGIN !</font>
	<h2> Mail \ 電子郵件</h2> 
	<input type = 'email' class = "lattice" name = 'uemail' placeholder ="input your account" />
	<h2> Password \ 密碼</h2>
	<input type = 'text' class = "lattice" name = 'pwd' placeholder ="input your password" />

	<br><br>
	<input type="submit" name = 'Submit'value ="登入" style="width:12vh;height:6vh;border-radius:5px; position : absolute; left:45%;border:2px white solid">
	<br><br><br>
	
	<input type="button" value ="返回" onclick = "location.href='index.php'" />
	<input type="submit" value ="忘記密碼"/>
		
</form>
</body>
</html>