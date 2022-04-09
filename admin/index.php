<?php
if (isset($_POST['ResetPWD']) && !empty($_POST['ResetPWD'])) {
    header ("Location:adminresetpwd.php");
    exit();
}
function userauth($ID, $PWD, $db_conn) {
    $sqlcmd = "SELECT * FROM adminuser WHERE useremail='$ID' AND valid='Y'";
    $rs = querydb($sqlcmd, $db_conn);
    $retcode = 0;
    if (count($rs) > 0) {
        $hashedPWD = $rs[0]['userpwd'];
        $LodgeID = $rs[0]['lodgeid'];
        if (password_verify($PWD, $hashedPWD)) {
            $retcode = 1;
            $SessionID = session_id();
            $sqlcmd = "UPDATE adminuser SET lastlogintime=now(),loginsession='$SessionID',"
                . "pwderrorcount='0' WHERE useremail='$ID' AND valid='Y'";
            $result = updatedb($sqlcmd, $db_conn);
            if (isset($_SERVER['HTTP_VIA']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                $UserIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
            else $UserIP = $_SERVER['REMOTE_ADDR'];
            $sqlcmd = "INSERT INTO loginlog (id,ucode,ipaddr) VALUES ('$ID','$LodgeID','$UserIP') ";
            $result = updatedb($sqlcmd, $db_conn);
        } else {
            if ($rs[0]['pwderrcount']>2) {
                $sqlcmd = "UPDATE adminuser SET valid='S',pwderrcount=pwderrcount+1 "
                    . "WHERE useremail='$ID' AND valid='Y'";
                $result = updatedb($sqlcmd, $db_conn);
            } else {
                $sqlcmd = "UPDATE adminuser SET pwderrcount=pwderrcount+1 "
                    . "WHERE useremail='$ID' AND valid='Y'";
                $result = updatedb($sqlcmd, $db_conn);
            }
        }
    }
    return $retcode;
}
session_start();
$_SESSION['LoginID'] = '';
require_once("../include/gpsvars.php");
require_once("../include/configure.php");
require_once("../include/db_func.php");
require_once("../include/xss.php");
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
$ErrMsg = "";
$uDate = date('Y-m-d');
if (!isset($ID)) $ID = '';
if (isset($Submit) && isset($vCode)) {
    $VerifyCode = $_SESSION['VerifyCode'];
    if ($vCode<>$VerifyCode) $ErrMsg = '驗證碼錯誤！\n';
}
if (empty($ErrMsg) && isset($Submit) && !empty($Submit) && !empty($ID)) {
    $ID = addslashes($ID);
    if (!empty($ID) && strlen($ID)<=50) {
        $Authorized = userauth($ID,$PWD,$db_conn);
        if ($Authorized) {
            $sqlcmd = "SELECT * FROM adminuser WHERE useremail='$ID' AND valid='Y'";
            $rs = querydb($sqlcmd, $db_conn);
            $LoginID = $rs[0]['useremail'];
            $UserName = $rs[0]['username'];
            $LodgeID = $rs[0]['lodgeid'];
            $UserAdmin = FALSE;
            if ($rs[0]['useradmin']=='Y') $UserAdmin = TRUE;
            $SysAdmin = FALSE;
            if ($rs[0]['sysadmin']=='Y') $SysAdmin = TRUE;
            $_SESSION['LoginID'] = $LoginID;
            $_SESSION['LoginName'] = $UserName;
            $_SESSION['LodgeID'] = $LodgeID;
            $_SESSION['SysAdmin'] = $SysAdmin;
            $_SESSION['UserAdmin'] = $UserAdmin;
            header ("Location:adminmgm.php");
            exit();
        }
        $sqlcmd = "SELECT * FROM adminuser WHERE useremail='$ID' AND valid='S'";
        $rs = querydb($sqlcmd, $db_conn);
        if (count($rs)>0) {
            header ("Location: suspendrecall.php");
            exit();
        }
    }
    $ErrMsg = '帳號/密碼錯誤、已被停權或是帳號未登錄';
}
require_once('../include/header.php');
$vCode = '';
$_SESSION['VerifyCode'] = mt_rand(1000,9999);
?>
<script type="text/javascript">
<!--
function setFocus()
{
<?php if (empty($ID)) { ?>
    document.LoginForm.ID.focus();
<?php } else { ?>
    document.LoginForm.PWD.focus();
<?php } ?>
}
//-->
</script>
<body onload="setFocus()">
<div class="Container">
<div style="width:100%;background:#E2FEF5;text-align:center;">
<img src="../images/logo.png" style="width:100%">
</div>
<div style="text-align:center;font-size:20px;font-weight:bold;margin:3px auto;">
管理員登入
</div> 
<div style="width:386px;border:solid 2px blue;margin:0 auto 10px auto;">
  <div style="width:380px;margin:3px auto;">
  <form method="POST" name="LoginForm" action="">
    <table border="0" align="center" width="100%">
    <tr height="30">
      <td align="right">電子郵件：&nbsp;</td>
      <td>
      <input type="text" name="ID" size="20" maxlength="50" value="<?php echo $ID; ?>">
      </td>
    </tr>
    <tr height="30">
      <td align="right">登入密碼：&nbsp;</td>
      <td>
      <input type="password" name="PWD" size="20" maxlength="20" class="pwdtext">
      </td>
    </tr>
    <tr>
      <td align="right">驗證數碼：&nbsp;</td>
      <td>
      <input type="text" name="vCode" size="4" maxlength="4"
        placeholder="4個數字">&nbsp;&nbsp;
      <img src="../images/chapcha.php" style="vertical-align:text-bottom;">
      <input type="submit" name="ReGen" value="重新產生" />
      </td>
    </tr>
    </table>
  <div style="text-align:center;margin:6px 0 0 0;">
  <input type="submit" name="Submit" value="登入">&nbsp;&nbsp;
  </div>
  <div style="text-align:center;margin:8px 0 0 0;">
  請以系統管理員幫您建立的帳號登入<br />
  不知或忘記密碼，請點選『忘記密碼』按鈕重設密碼
  </div>
  <div style="text-align:center;margin:8px 0 0 0;">
  <input type="submit" name="ResetPWD" value="忘記密碼">
  </div>
  </form>
  </div>
</div>
<?php
require_once('../include/footer.php');
?>
</div>
</body>
</html>
