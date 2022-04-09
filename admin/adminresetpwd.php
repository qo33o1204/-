<?php
if (isset($_POST['Login']) && !empty($_POST['Login'])) {
    header ("Location:index.php");
    exit;
}
session_start();
require_once ("../include/gpsvars.php");
require_once ("../include/configure.php");
require_once ("../include/db_func.php");
require_once ("../include/xss.php");
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
$ErrMsg = '';
if (isset($_POST['Submit']) && !empty($_POST['Submit'])  
    && isset($eMail) && isset($vCode)) {
    $VerifyCode = $_SESSION['VerifyCode'];
    if ($vCode<>$VerifyCode) {
        $ErrMsg = '驗證碼錯誤！\n';
    }
    if (!isset($eMail)) $eMail = '';
    $eMail = xsspurify($eMail);
    $eMail = addslashes($eMail);
    if (!isset($Name)) $Name = '';
    $Name = xsspurify($Name);
    $Name = addslashes($Name);
    if (!filter_var($eMail, FILTER_VALIDATE_EMAIL)) {
        $ErrMsg .= '電子郵件格式錯誤\n';
    }
    if (!isset($Name) || empty($Name)) $ErrMsg .= '姓名資料錯誤！\n';
    if (empty($ErrMsg)) {
        $sqlcmd = "SELECT * FROM adminuser WHERE username='$Name' AND "
            . "useremail='$eMail' AND valid='Y'";
        $rs = querydb($sqlcmd, $db_conn);
        if (count($rs)<=0) {
            $ErrMsg = '查無您所輸入之電子郵件地址與姓名組合，請確認輸入資料是否正確\n';
        } else {
            $SeqNo = $rs[0]['seqno'];
            header ("Location:sendpwdinfo.php?eMail=$eMail&SeqNo=$SeqNo");
            exit();
        }
    }
}
$vCode = mt_rand(1000,9999);
$_SESSION['VerifyCode'] = $vCode;
if (!isset($Name)) {
    $Name = $eMail = $vCode = '';
}
require_once('../include/header.php');
?>
<script type="text/javascript">
<!--
function setFocus()
{
    document.LoginForm.ID.focus();
}
//-->
</script>
<body onload="setFocus()">
<div class="Container">
<div style="text-align:center;width:100%;background:#ffe6e6;">
<img src="../images/logo08.png" width="460">
</div>
<div style="text-align:center;font-weight:bold;margin:3px 0;">
管理員密碼重置
</div>
<div style="width:386px;border:solid 2px blue;margin:0 auto;">
  <div style="width:380px;margin:3px auto;">
  <form method="POST" name="LoginForm" action="">
  <table width="360" align="center">
  <tr height="30">
    <td>
  電子郵件：<input type="text" name="eMail" value="<?php echo $eMail; ?>" 
    size="20" maxlength="50">
    </td>
  </tr>
  <tr height="30">
    <td>
  用戶姓名：<input type="text" name="Name" value="<?php echo $Name; ?>" 
    size="20" maxlength="50">
    </td>
  </tr>
  <tr>
    <td>
    驗證數碼：<input type="text" name="vCode" size="4" maxlength="4" 
        placeholder="4個數字">&nbsp;&nbsp;
    <img src="../images/chapcha.php" style="vertical-align:text-bottom;">
    <input type="submit" name="ReGen" value="重新產生" />
    </td>
  </tr>
  </table>
  <div style="text-align:center;margin:8px 0;">
  <input type="submit" name="Submit" value="發出重置密碼函">
  </div>
  <div style="text-align:center;margin:8px 0;">
  請於上方欄位輸入所提示資料<br />
  點選按鈕後，系統會寄出密碼重置郵件。
  </div>
  <div style="text-align:center;margin:8px 0 0 0;">
  <input type="submit" name="Login" value="返回登入頁面">
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
