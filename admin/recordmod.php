<?php
if (isset($_POST['Abort']) && !empty($_POST['Abort'])) {
    header ("Location:recordmgm.php");
    exit();
}
session_start();
require_once ("../include/gpsvars.php");
require_once ("../include/configure.php"); 
require_once ("../include/db_func.php");
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
if (!isset($RefCode) || strlen($RefCode)>30) {
    header ("Location:recordmgm.php");
    exit();
}
$maxLapsTime = 1;
$LodgeID = $_SESSION['curLodgeID'];
$LodgeName = $_SESSION['LodgeName'];
$sqlcmd = "SELECT * FROM lodgedata WHERE lodgeid='$LodgeID' AND valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs)>0) {
    $maxLapsTime = $rs[0]['modifiabletime'];
}
$RefCode = addslashes($RefCode);
$sqlcmd = "SELECT * FROM tagrecord WHERE refcode='$RefCode' AND valid<>'N'";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs)<=0) {
    header ("Location: recordmgm.php");
    exit();
}
$TagTime = strtotime ($rs[0]['tagtime']);
$CanModify = FALSE;
if (time()-$TagTime < $maxLapsTime*3600) $CanModify = TRUE;   // 一小時之內管理員可以修改資料
if (!$CanModify) {
    header ("Location: recordmgm.php");
    exit();
}
if (isset($Save) && !empty($Save)) {
    if (!isset($Phone)) $Phone = '';
    if (!isset($VisitorName) || empty($VisitorName) || strlen($VisitorName)>30
        || $VisitorName<>addslashes($VisitorName))
        $ErrMsg .= '姓名不得為空白或超過30個字或含引號等特殊符號！\n';
    if (!is_numeric($Phone) || strlen($Phone)<9 || strlen($Phone)>16) 
        $ErrMsg .= '手機需為數字且介於9~15個字！\n'; 
    if (!is_numeric($Temperature)) $Temperature = 0; 
    if (!isset($RoomNo) || strlen($RoomNo)>20 || $RoomNo<>addslashes($RoomNo)) 
        $ErrMsg .= '房號需少於20個字且不得包含引號等特殊符號！\n'; 
    if (empty($ErrMsg)) {
        $sqlcmd = "UPDATE tagrecord SET regname='$VisitorName',regphone='$Phone',"
            . "bodytemp='$Temperature',roomnumber='$RoomNo',valid='Y',"
            . "confirmby='$LoginID',confirmtime=now() WHERE refcode='$RefCode'";
        $result = updatedb($sqlcmd, $db_conn);
        header ("Location: recordmgm.php");
        exit();
    }
}

if (!isset($VisitorName)) {
    $VisitorName = $rs[0]['regname'];
    $Phone = $rs[0]['regphone'];
    $Temperature = $rs[0]['bodytemp'];
    if ($Temperature==0.0) $Temperature = '';
    $RoomNo = $rs[0]['roomnumber'];
    $Items = $rs[0]['items'];
}
require_once ("../include/header.php");
$ThisPageTitle = '紀錄資料複核/修改';
$MenuItem = 2;
?>
<body>
<div class="Container">
<?php
require_once("../include/topmenu.php");
?>
<div id="logo" style="font-size:20px;">實名制登錄資料複核/修改</div>
<form method="POST" action="">
<div style="font-size:18px;margin:3px auto 5px;text-align:center;">
入住人員資料複核/修改
</div>
<input type="hidden" name="RefCode" value="<?php echo $RefCode; ?>">
<div style="font-size:18px;margin:5px 2px 0px 8px;text-align:center;">
姓名：<input type="text" name="VisitorName" size="16" value="<?php echo $VisitorName; ?>">
</div>
<div style="font-size:18px;margin:5px 2px 0px 8px;text-align:center;">
手機：<input type="text" name="Phone" size="16" value="<?php echo $Phone; ?>">
</div>
<div style="font-size:18px;margin:5px 2px 0px 8px;text-align:center;">
體溫：<input type="text" name="Temperature" size="16" value="<?php echo $Temperature; ?>">
</div>
<div style="font-size:18px;margin:5px 2px 0px 8px;text-align:center;">
房號：<input type="text" name="RoomNo" size="16" value="<?php echo $RoomNo; ?>">
</div>
<div style="text-align:center;margin:8px auto;">
<input type="submit" name="Save" value="確認送出">&nbsp;&nbsp;
<input type="submit" name="Abort" value="放棄修改" />
</div>
</form>
<?php
require_once ("../include/footer.php");
?>
</div>
</body>
</html>