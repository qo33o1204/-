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
if (!isset($_SESSION['UserAdmin']) || !$_SESSION['UserAdmin']) {
    header ("Location:index.php");
    exit();
}
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
$PWD = 'Password_NotSet';
if (isset($Confirm) && isset($eMail)) {
    $UserID = xsspurify($eMail);
    if (empty($eMail) || $eMail<>addslashes($eMail) || strlen($eMail)>50) {
        $ErrMsg = '電子郵件帳號資料錯誤或是長度超過50個字\n';
    } else {
        $sqlcmd = "SELECT * FROM adminuser WHERE useremail='$eMail'";
        $rs = querydb($sqlcmd, $db_conn);
        if (count($rs) > 0) $ErrMsg .= '用戶' . $usereMail . '已存在\n';
    }
    if (!isset($UserName) || empty($UserName) || $UserName <> addslashes($UserName)) 
        $ErrMsg .= '用戶姓名資料錯誤!\n';
    if (!isset($selLodgeID) || !isset($arrLodges["$selLodgeID"])) 
        $ErrMsg .= '隸屬學校資料錯誤\n';
    if (!isset($Sys_Priv) || $Sys_Priv<>'Y') $Sys_Priv = 'N';
    if (!isset($User_Priv) || $User_Priv<>'Y') $User_Priv = 'N';
    if (strlen($UserPWD)<6 || strlen($UserPWD)>20) $ErrMsg .= '密碼長度6~20\n';
    if (isset($UserPWD) && !empty($UserPWD)) {
        $PWD = password_hash($UserPWD, PASSWORD_BCRYPT);
    }
    if (empty($ErrMsg)) {
        $UserName = xsspurify($UserName);
        $eMail = xsspurify($eMail);
        $sqlcmd = "INSERT INTO adminuser (username,userpwd,useremail,lodgeid,"
            . "sysadmin,useradmin,createby,createtime) VALUES ("
            . "'$UserName','$PWD','$eMail','$selLodgeID','$Sys_Priv',"
            . "'$User_Priv','$LoginID',now())";
        $rs = updatedb($sqlcmd, $db_conn); 
        header("Location: adminmgm.php");
        exit();
    }
}

if (!isset($eMail)) {
    $UserName = $eMail = '';
    $selLodgeID = $LodgeID;
    $Sys_Priv = $User_Priv = 'N';
}
require_once("../include/header.php");
$ThisPageTitle = '新增用戶資料';
$MenuItem = 1;
?>
<body>
<div class="Container">
<?php
require_once("../include/topmenu.php");
?>
<div style="width:100%;margin:3px 0 2px 0;text-align:center;font-weight:bold;font-size:1.1em;">
新增用戶資料
</div>
<div style="font-size:1em;font-weight:bold;color:Brown;text-align:center;">
請於填寫資料完畢後用滑鼠點選『確認送出』按鈕，不欲新增請按『放棄新增』按鈕
</div>
<div style="width:100%;margin:6px 0 2px 0;">
<form method="POST" name="ModForm" action="">
<table width="760" class="mistab" align="center">
<tr height="30">
  <th width="160">電子郵件</th>
  <td><input type="text" name="eMail" size="40" value="<?php echo $eMail; ?>" />
  <br />此為登入帳號，不可與他人同，設定後不允許修改</td>
</tr>
<tr height="30">
  <th>用戶姓名</th>
  <td><input type="text" name="UserName" size="20" value="<?php echo $UserName; ?>" /></td>
</tr>
<tr height="30">
  <th>用戶密碼</th>
  <td>
  <input type="password" name="UserPWD" size="20" value="" />&nbsp;密碼：6~20碼; 
  &nbsp; 如不設定密碼請留白
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
<?php if ($SysAdmin) { ?>
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
  <input type="submit" name="Abort" value="放棄新增" class="button" />
</div>
</form>
</div>
<?php
require_once("../include/footer.php");
?>
</div>
</body>
</html>