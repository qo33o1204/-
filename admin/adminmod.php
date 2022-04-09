<?php
// Abort button pressed. Return to calling program.
if (isset($_POST['Abort']) && !empty($_POST['Abort'])) {
    header("Location: adminmgm.php");
}
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])) {
    header ("Location:index.php");
    exit();
}
require_once ("../include/gpsvars.php");
require_once ("../include/configure.php");
require_once ("../include/db_func.php");
require_once ("../include/xss.php");
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
if (!isset($UserAdmin) || !$UserAdmin) {
    header ("Location:index.php");
    exit();
}
if (!isset($SeqNo) || !is_numeric($SeqNo) || !isset($eMail) 
        || $eMail<>addslashes($eMail)) {
    header ("Location:adminmgm.php");
    exit();
}
$LoginID = $_SESSION['LoginID'];
$eMail = xsspurify($eMail);
$sqlcmd = "SELECT * FROM adminuser WHERE seqno='$SeqNo' AND useremail='$eMail' "
    . "AND valid='Y'";
$UserInfo = querydb($sqlcmd, $db_conn);
if (count($UserInfo) <= 0) {
    header ("Location:adminmgm.php");
    exit;
}
$SeqNo = $UserInfo[0]['seqno'];
$sqlcmd = "SELECT * FROM lodgedata ";
if (!$SysAdmin) $sqlcmd .= "WHERE lodgeid='$LodgeID' ";
$sqlcmd .= "ORDER BY lodgeid";
$rs = querydb($sqlcmd, $db_conn);
$arrLodges = array();
if (count($rs)>0) {
    foreach ($rs as $item) {
        $Code = $item['lodgeid'];
        $arrLodges["$Code"] = $item['lodgename'];
    }
}
$ErrMsg = '';
if (isset($Confirm) && isset($UserName)) {
    if (!isset($UserName) || empty($UserName) || $UserName <> addslashes($UserName)) 
        $ErrMsg .= '姓名資料錯誤!\n';
    if (!isset($selLodgeID) || !isset($arrLodges["$selLodgeID"])) 
        $ErrMsg .= '宿旅資料錯誤\n';
    if (!isset($Sys_Priv) || $Sys_Priv<>'Y') $Sys_Priv = 'N';
    if ($SysAdmin=='Y' && $LoginID==$eMail) $Sys_Priv = 'Y';
    if (!isset($User_Priv) || $User_Priv<>'Y') $User_Priv = 'N';
    $PWD = '';
    if (isset($UserPWD) && !empty($UserPWD)) {
        if (strlen($UserPWD)<6 || strlen($UserPWD)>20) $ErrMsg .= '密碼長度6~20\n';
        $PWD = password_hash($UserPWD, PASSWORD_BCRYPT);
    }
    if (empty($ErrMsg)) {
        $UserName = xsspurify($UserName);
        $sqlcmd = "UPDATE adminuser SET username='$UserName',"
            . "lodgeid='$selLodgeID',sysadmin='$Sys_Priv',useradmin='$User_Priv',"
            . "modifyby='$LoginID'";
        if (!empty($PWD)) $sqlcmd .= ",userpwd='$PWD'";
        $sqlcmd .= " WHERE seqno='$SeqNo' AND useremail='$eMail'"; 
        $rs = updatedb($sqlcmd, $db_conn); 
        header("Location: adminmgm.php");
        exit();
    }
}

if (!isset($UserName)) {
    $UserName = $UserInfo[0]['username'];
    $selLodgeID = $UserInfo[0]['lodgeid'];
    $Sys_Priv = $UserInfo[0]['sysadmin'];
    $User_Priv = $UserInfo[0]['useradmin'];
}
require_once("../include/header.php");
$ThisPageTitle = '修改用戶資料';
$MenuItem = 1;
?>
<body>
<div class="Container">
<?php
require_once("../include/topmenu.php");
?>
<div style="width:100%;margin:3px 0 2px 0;text-align:center;font-weight:bold;font-size:1.1em;">
修改用戶資料
</div>
<div style="font-size:1em;font-weight:bold;color:Brown;text-align:center;">
請於修改資料完畢後用滑鼠點選『確認送出』按鈕，不欲修改請按『放棄修改』按鈕
</div>
<div style="width:100%;margin:6px 0 2px 0;">
<form method="POST" name="ModForm" action="">
<input type="hidden" name="SeqNo" value="<?php echo $SeqNo; ?>" />
<input type="hidden" name="eMail" value="<?php echo $eMail; ?>" />

<table width="760" class="mistab" align="center">
<tr height="30">
  <th width="160">電子郵件</th>
  <td><?php echo $eMail; ?></td>
</tr>
<tr height="30">
  <th>用戶名稱</th>
  <td><input type="text" name="UserName" size="20" value="<?php echo $UserName; ?>" /></td>
</tr>
<tr height="30">
  <th>用戶密碼</th>
  <td>
  <input type="password" name="UserPWD" size="20" value="" />&nbsp;密碼：6~20碼; 
  &nbsp; 如不變更密碼請留白
  </td>
</tr>
<tr height="30">
  <th>旅宿名稱</th>
  <td>
    <select name="selLodgeID">
<?php
foreach ($arrLodges as $curLodgeID=>$uName) {
    echo '<option value="' . $curLodgeID . '"';
    if ($selLodgeID==$curLodgeID) echo ' selected';
    echo ">$curLodgeID:$uName</option>\n";
}    
?>
    </select>
  </td>
</tr>
</table>
<br />
<table width="760" class="mistab" align="center">
<tr height="30">
  <th colspan="2">用戶權限</th>
</tr>
<?php if ($SysAdmin && $LoginID<>$eMail) { ?>
<tr height="30">
  <th width="160">系統管理</th>
  <td><input type="radio" name="Sys_Priv" value="Y"<?php 
        if ($Sys_Priv=='Y') echo ' checked';?> />是&nbsp;
    <input type="radio" name="Sys_Priv" value="N"<?php 
        if ($Sys_Priv=='N') echo ' checked';?> />否&nbsp;&nbsp;具備系統管理權限
  </td>
</tr>
<?php } ?>
<tr height="30">
  <th width="160">用戶管理</th>
  <td><input type="radio" name="User_Priv" value="Y"<?php 
        if ($User_Priv=='Y') echo ' checked';?> />是&nbsp;
    <input type="radio" name="User_Priv" value="N"<?php 
        if ($User_Priv=='N') echo ' checked';?> />否&nbsp;&nbsp;具備用戶管理權限
  </td>
</tr>
</table>
<div style="text-align:center;margin:3px 0;">
  <input type="submit" name="Confirm" value="確認送出" class="button" />&nbsp;
  <input type="submit" name="Abort" value="放棄修改" class="button" />
</div>
</form>
</div>
<?php
require_once("../include/footer.php");
?>
</div>
</body>
</html>