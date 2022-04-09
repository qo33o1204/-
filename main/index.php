<?php
session_start();
date_default_timezone_set('Asia/Taipei');
$today = date('Y/m/d H:i:s');
require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');
require_once ('../include/header.php');
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
$useremail = $_POST['uemail'];
$password = $_POST['pwd'];
if(isset($Submit) && !empty($Submit) ){ //判斷是否有submit表單，以下是確認帳號密碼是否正確
	if(isset($useremail) && !empty($useremail)){	
		$sqlcmd = "SELECT * FROM bookuser WHERE email = '$useremail'";
		$rs = querydb($sqlcmd , $db_conn);
		if(count($rs) > 0){ 
			$upwd = $rs[0]['userpwd']; 
			if($upwd==$password){  //密碼正確
				if($rs[0]['valid'] == 'Y' || strtotime($today) - strtotime($rs[0]['locktime']) >= 300){ 
					$loginemail = $rs[0]['email'];
					$loginname = $rs[0]['name'];
					$logincode = $rs[0]['unitcode'];
					$loginlimit = $rs[0]['useradmin'];
					$loginsysadmin = $rs[0]['sysadmin'];
					$loginchecktimes=$rs[0]['checktimes'];
					$loginbooktime=$rs[0]['booktime'];
					if($rs[0]['pwderrcount'] <> 0){ //如果有先前有登入失敗但未超過3次，則將值改為0
						$sqlcmd="UPDATE `bookuser` SET `pwderrcount`=0 , `valid`='Y' WHERE `email`='$loginemail' ";
						$rs = querydb($sqlcmd , $db_conn);
					}				
					$_SESSION['LoginID'] = $loginemail;  //使用SESSION來記住該使用者的資訊
					$_SESSION['LoginName'] = $loginname;
					$_SESSION['Loginunitcode'] = $logincode;
					$_SESSION['loginAdmin'] = $loginlimit;
					$_SESSION['Loginsysadmin']=$loginsysadmin;
					$_SESSION['showroom'] = 1;
					$_SESSION['Loginchecktimes']=$loginchecktimes;
					$_SESSION['Loginbooktime']=$loginbooktime;
					
					header ("Location:interface.php");
					exit();	
				}
				
			}
			else{ //密碼不正確
				if ($rs[0]['pwderrcount']>2){ //登入次數超過3次，則將該帳號鎖5分鐘		
					$sqlcmd = "UPDATE bookuser SET valid='S',pwderrcount=pwderrcount+1 , locktime='$today' WHERE email='$useremail' AND valid='Y' ";              
					$result = querydb($sqlcmd, $db_conn);
				}
				else{ //登入沒超過3次則累加pwderrcount的值
					$sqlcmd = "UPDATE bookuser SET pwderrcount=pwderrcount+1 WHERE email='$useremail' AND valid='Y' ";
					$result = querydb($sqlcmd, $db_conn);
				}
			}
		
			
		}	
	}
}
if(!isset($Submit)) $Submit = '';
?>

<body>
<br/><br/>
<div class="Container" style="width:500px;">
	<div class="text-center">
	<div class="border border-white rounded ">
	<form method = "POST" name = 'login' action = ''>
		<font size= 10>LOGIN</font>
		<br/><br/>
		<h2 class="font-italic"> E-mail / 帳號</h2>
		<input type = 'text' class = "lattice" name = 'uemail' placeholder ="input your account" />
		<h2 class="font-italic">Password / 密碼</h2>
		<input type = 'password' class = "lattice" name = 'pwd' placeholder ="input your password" />
		<br><br>
		<button type="submit" name = 'Submit' value ="登入" class="btn btn-outline-dark">登入</button>
		<br/><br/>
	</form>
	</div>
	</div>
</div>
</body>
</html>