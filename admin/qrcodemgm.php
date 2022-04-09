<?php
session_start();
require_once ("../include/gpsvars.php");
require_once ("../include/configure.php"); 
require_once ("../include/db_func.php");
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
$LodgeID = $_SESSION['curLodgeID'];
$LodgeName = $_SESSION['LodgeName'];
if (isset($Save) && !empty($Save)) {
    $Seed = date('YmdHis') . rand(100,999);
    $StartLoc = rand(5,15);
    $SHA = strtoupper(sha1($Seed));
    $Code = substr($SHA, $StartLoc, 25);
    $sqlcmd = "UPDATE lodgedata SET lodgecode='$Code',"
        . "modifyby='$LoginID',modifytime=now() WHERE lodgeid='$LodgeID'";
    $result = updatedb($sqlcmd, $db_conn);
}
$sqlcmd = "SELECT * FROM lodgedata WHERE lodgeid='$LodgeID' AND valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
$LodgeCode = $rs[0]['lodgecode'];
$ServerName = $_SERVER['SERVER_NAME'];
$userLink = 'https://' . $ServerName . '/main/?Code=' . $LodgeID 
    . '__' . $LodgeCode;
$mgrLink = 'https://' . $ServerName . '/manager/recordmgm.php?Code=' . $LodgeID
    . '__' . $LodgeCode;
require_once ("../include/header.php");
$ThisPageTitle = 'QR條碼管理';
$MenuItem = 4;
?>
<body>
<div class="Container">
<?php
require_once("../include/topmenu.php");
?>
<div id="logo" style="font-size:20px;"><?php echo $LodgeName; ?> 條碼資料管理</div>
<div style="margin:10px 6px;font-size:20px;">
入住人員用行動裝置拍攝下方二維條碼即可進入登錄實名資料，但因為行動裝置會記錄此條碼網址資料，
為避免已有網址資料的用戶隨意登錄假造資料，如果發現出現非預期的登錄資料，
請點選下方的『變更條碼』按鈕變更條碼，變更後請重新列印條碼供入住旅客掃碼。
</div>
<div style="text-align:center;margin:3px;">
<a href="qrcodemgm.php?Save=Go">
<button style="background-color:blue;border:none;color:white;padding:4px 10px;text-align:center;font-size:20px;border-radius:8px;">
變更條碼
</button>
</a>
</div>
<div style="text-align:center;display:inline-block;width:48%;">
  <img src="qrgen.php?data=<?php echo $userLink; ?>" style="width:320px;height:320px;"><br />
  <span style="font-size:22px;">入住人員實名登錄條碼</span>
</div>
<div style="text-align:center;display:inline-block;width:48%;">
  <img src="qrgen.php?data=<?php echo $mgrLink; ?>" style="width:320px;height:320px;"><br />
  <span style="font-size:22px;">管理人員條碼</span>
</div>
<?php
echo $userLink;
require_once ("../include/footer.php");
?>
</div>
</body>
</html>