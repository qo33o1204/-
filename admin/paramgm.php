<?php
session_start();
require_once ("../include/gpsvars.php");
require_once ("../include/configure.php"); 
require_once ("../include/db_func.php");
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
$LodgeID = $_SESSION['curLodgeID'];
$LodgeName = $_SESSION['LodgeName'];
if (isset($Save) && !empty($Save)) {
    if (!is_numeric($ModifiableTime) || $ModifiableTime<1 || $ModifiableTime>24) 
        $ErrMsg .= '可異動資料時間需介於1~24小時！\n'; 
    if (!is_numeric($DeletableTime) || $DeletableTime<12 || $DeletableTime>72) 
        $ErrMsg .= '可刪除資料時間需介於12~72小時！\n'; 
    if (empty($ErrMsg)) {
        $sqlcmd = "UPDATE lodgedata SET "
            . "modifiabletime='$ModifiableTime',deletabletime='$DeletableTime',"
            . "modifyby='$LoginID',modifytime=now() WHERE lodgeid='$LodgeID'";
        $result = updatedb($sqlcmd, $db_conn);
        $ErrMsg = '資料已更新！';
    }
}
$sqlcmd = "SELECT * FROM lodgedata WHERE lodgeid='$LodgeID' AND valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
if (!isset($ModifiableTime)) {
    $ModifiableTime = $rs[0]['modifiabletime'];
    $DeletableTime = $rs[0]['deletabletime'];
}
require_once ("../include/header.php");
$ThisPageTitle = '紀錄資料複核/修改';
$MenuItem = 3;
?>
<body>
<div class="Container">
<?php
require_once("../include/topmenu.php");
?>
<div id="logo" style="font-size:20px;"><?php echo $LodgeName; ?> 旅宿資料修改</div>
<form method="POST" action="">
<div style="font-size:18px;margin:5px 2px 0px 8px;text-align:center;">
可異動資料期間：<input type="text" name="ModifiableTime" size="8" 
value="<?php echo $ModifiableTime; ?>"> 小時 (介於 1~24小時)
</div>
<div style="font-size:18px;margin:5px 2px 0px 8px;text-align:center;">
可刪除資料期間：<input type="text" name="DeletableTime" size="8" 
value="<?php echo $DeletableTime; ?>"> 小時 (介於12~72小時)
</div>
<div style="text-align:center;margin:8px auto;">
<input type="submit" name="Save" value="儲存">
</div>
</form>
<?php
require_once ("../include/footer.php");
?>
</div>
</body>
</html>