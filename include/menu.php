<?php
	session_start();
	if(isset($_SESSION['$LoginID']) || empty($_SESSION['LoginID'])) {
		header("Location:index.php");
		exit();
	}
	$userid = $_SESSION['LoginID'];
	$admin = $_SESSION['loginAdmin'];
	$sysadmin = $_SESSION['Loginsysadmin'];
?>
<html>
<body>
	<div align = "center" style=' background:#909BD1;' >
		<ul class="nav nav-pills nav-fill">
		<li class="nav-item">
			<a class="nav-link active" href="interface.php">預約介面</a>
		</li>
		<li class="nav-item">
			<a class="nav-link text-light" href="users.php">用戶管理</a>
		</li>
		<li class="nav-item">
			<a class="nav-link text-light" href="mybook.php">預約狀況</a>
		</li>
		<?php if($sysadmin == 1){ ?>
		<li class="nav-item">
			<a class="nav-link text-light" href="unit.php">單位管理</a>
		</li>
		<?php } ?>
		<?php if($admin == 1){?>
		<li class="nav-item">
			<a class="nav-link text-light" href="Meetingroom.php">會議室管理</a>
		</li>
		<li class="nav-item">
			<a class="nav-link text-light" href="record.php">全部預約</a>
		</li>
		<li class="nav-item">
			<a class="nav-link text-light" href="check.php">預約審核</a>
		</li>
		<?php } ?>
		<li class="nav-item">
			<a class="nav-link text-muted" href="logout.php">登出</a>
		</li>
		</ul>

	</div>
	<!--
	<ul class="nav justify-content-center">
	  <li class="nav-item">
		<a class="nav-link active" href="#">Active</a>
	  </li>
	  <li class="nav-item">
		<a class="nav-link" href="#">Link</a>
	  </li>
	  <li class="nav-item">
		<a class="nav-link" href="#">Link</a>
	  </li>
	  <li class="nav-item">
		<a class="nav-link disabled" href="#">Disabled</a>
	  </li>
	</ul>
	-->
</body>
</html>