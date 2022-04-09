<?php
require_once ('../include/gpsvars.php');
require_once ('../include/configure.php');
require_once ('../include/db_func.php');
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
$sqlcmd = "SELECT * FROM building WHERE valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
$arrBuilding = array();
foreach ($rs as $item) {
	$bid = $item['buildingid'];
	$arrBuilding["$bid"] = $item['buildingname'];
}
require_once ('../include/header.php');
if (!isset($SelBuilding)) $SelBuilding='A1';
?>
<body>
<div class = "mark1"> 會議室預約系統  </div> 
<div class = "mark2">Conference room reservation system</div>
<br /><br />
<form method="POST" name="Booking" action="">
	<input type="button" value="用戶管理"  onclick="location.href='personal.php'" />&nbsp;
	<input  type="button" value="預約情形" onclick="location.href='record.php'" />&nbsp;
	<input type="button" value="群組管理"  onclick="location.href='group.php' " />&nbsp;
	<input type="button" value="會議室管理"  />&nbsp;
	<input type="button" value="相關限制"  />&nbsp;
	<input type="button" value="登出" onclick ="location.href ='index.php'" />&nbsp;
<br /><br />
	<input type="date" name="bday">
<select name="SelBuilding" onchange="submit()">
<?php
foreach ($arrBuilding as $buildingid=>$buildingName) {
	echo '<option value="' . $buildingid . '"';
	if ($SelBuilding==$buildingid) echo ' selected';
	echo ">$buildingName</option>\n";
}
?>
</select>

				
			<select id="sector-list"></select>
			
						
			<script type="text/javascript"> 
				
				var building =['經營大樓','實驗大樓','挺生大樓','綜合大樓','尚志大樓','德惠大樓','新德惠大樓'
						,'尚志教育研究管','北設工大樓'];
				var buildingSelect=document.getElementById("building-list");
				var inner="";
				for(var i=0;i<building.length;i++){
					inner=inner+'<option value=i>'+building[i]+'</option>';
				}
				buildingSelect.innerHTML=inner;
				
				var sectors=new Array();
				sectors[0]=['A1-101','A1-102','A1-108A','A1-206','A1-207','A1-208A','A1-208B','A1-301','A1-306','A1-307','A1-308A','A1-308B',,'A1-401',,'A1-408A',,'A1-502','A1-508B'];
				sectors[1]=['A2-204','A2-205','A2-302','A2-304','A2-306','A2-503','A2-504','A2-505','A2-603','A2-604','A2-605','A2-802','A2-803','A2-805','A2-807'];
				sectors[2]=['A3-100','A3-101','A3-102','A3-103','A3-105','A3-106','A3-108','A3-109','A3-125','A3-200','A3-2000','A3-201','A3-203','A3-204','A3-206','A3-207','A3-209','A3-210','A3-214','A3-216','A3-307','A3-316','A3-400A','A3-404','A3-506','A3-507','A3-509','A3-511','A3-512','A3-524','A3-607','A3-609','A3-611','A3-616','A3-620','A3-711','A3-713','A3-801','A3-803','A3-804','A3-804A','A3-807','A3-812'];
				sectors[3]=['A4-105','A4-107','A4-111','A4-113','A4-114','A4-201'];
				sectors[4]=['A5-501','A5-502','A5-503','A5-504','A5-505','A5-508','A5-509','A5-510','A5-511','A5-512','A5-514','A5-611','A5-612','A5-708','A5-710','A5-711','A5-801','A5-802','A5-803','A5-804','A5-807','A5-808','A5-809','A5-810','A5-811','A5-814','A5-900','A5-B210'];
				sectors[5]=['A6-101']
				sectors[6]=['A7-307','A7-311','A7-313','A7-403','A7-404','A7-405','A7-406','A7-407','A7-411','A7-413','A7-504','A7-506','A7-510','A7-603','A7-606','A7-609','A7-701','A7-702','A7-703','A7-704','A7-705','A7-706','A7-707','A7-708','A7-709','A7-710','A7-716','A7-812'];
				sectors[7]=['A8-103','A8-106','A8-B105','A8-B106','A8-B107','A8-B113','A8-B201','A8-B202','A8-B203','A8-B204','A8-B205','A8-B206','A8-B207','A8-B208','A8-B209','A8-B210'];
				sectors[8]=['A9-1003','A9-1004','A9-1005','A9-1009','A9-1010','A9-1114','A9-1115'];
				
				function changebuilding(index){
					var sinner="";
					for(var i =0;i<sectors[index].length;i++){
						sinner = sinner+'<option value=i>' + sectors[index][i] + '</option>';
						
					}
					var sectorSelect=document.getElementById("sector-list");
					
					sectorSelect.innerHTML=sinner;
						
				}
				changebuilding(document.getElementById("building-list").selectedIndex);
				
									
			</script><br><br>    
			<input type = 'button' value = '預約介面' onclick = "location.href = 'reservation.php'" / >
			<br>


			<table width ="1000" height ="500" border ="1" bgcolor = "white">
				<td width=”100”>節\星期</td><td width=”100”>星期一</td><td width=”100”>星期二</td><td width=”100”>星期三</td><td width=”100”>星期四</td><td width=”100”>星期五</td><td width=”100”>星期六</td>
				<td width=”100”>星期七</td>
			<?php
			$a = 1;
			for($i=1; $i<=12; $i++){
				echo "<tr height = '50'>";
				for($j=1; $j<=8; $j++){
					if($j == 1){
						echo "<td height='70' >第 $a 節</td>";
					}else{
						echo "<td height='70' ></td>";
					}
				}
				echo "</tr>";
				$a++;
			}
			?>
	
	  
	</body>
</html>
