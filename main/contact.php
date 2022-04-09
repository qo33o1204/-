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
$username = $_SESSION['LoginName'];

require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');
require_once('../include/menu.php');
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);

require_once ('../include/header.php');
?>
<style type="text/css">
	.Data-Content{
		width:200px;
		line-height:30px;
	}
	.Data-Title{
		float:left;
		width:25%;
		margin-right:10px;
	}
	.Data-Item{
		line-height:32px;
		float:left;
		width:25%;
		
	}
	.AlignRight {
        text-align: right;
    }
	input{
		width:300px;
		background:#E5E5E6;
		border:0 none;
		border-radius: 5px; 
	}
	textarea{
		background:#E5E5E6;
		border:0 none;
		border-radius: 5px; 
	}
</style>

<html>
<body>
<div style="width:70%;text-align:left;margin:3px auto;">
	<div class = 'row'>
		<div class='col-md-6'>
		<div class='Data-Content'>
			<div class='Data-Title'>
				<div class="AlignRight">
					<label for="txt_name">姓名 :</label><br/><br/>
					<label for="txt_mail">信箱 :</label><br/><br/>
					<label for="txt_subject">主旨 :</label><br/><br/>
					<label for="txt_comment"></label><br/>
					
				</div>
			</div>
			<div class='Data-Item'>
				<input  type='text' id='txt_name' name='name' value='<?php echo $username ?>'/><br/><br/>
				<input  type='text' id='txt_mail' name='mail' value='<?php echo $userid ?>'/><br/><br/>
				<input  type='text' id='txt_subject' name='subject'/><br/><br/>
				<textarea id='txt_comment' name='comment'rows='7' cols='40' ></textarea><br/><br/>
				
				<input class='btn btn-primary' type='submit' name='submit' value='submit' /> 
				
				
			</div>
		</div>
		</div>
		<div class='col-md-6'>
			<img src='../images/material1.jpg' border='0' width='450' height='450' alt=material> 
		</div>
	</div>
</div>
	
</body>
</html>