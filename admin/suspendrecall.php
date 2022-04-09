<?php
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
    if (!isset($Name)) $Name = '';
    $Name = xsspurify($Name);
    if (!filter_var($eMail, FILTER_VALIDATE_EMAIL)) {
        $ErrMsg .= '電子郵件格式錯誤\n';
    }
    if (!isset($Name) || empty($Name)) $ErrMsg .= '姓名資料錯誤！\n';
    if (empty($ErrMsg)) {
        $sqlcmd = "SELECT * FROM adminuser WHERE username='$Name' AND "
            . "useremail='$eMail' AND valid='S'";
        $rs = querydb($sqlcmd, $db_conn);
        if (count($rs)<=0) {
            $ErrMsg = '資料庫中查無您所輸入之電子郵件地址與姓名組合的停權紀錄，請確認輸入資料是否正確\n';
        } else {
            $ReqID = sha1($eMail . date('His'));
            $sqlcmd = "UPDATE adminuser SET reqid='$ReqID',vcode='$vCode' "
                . "WHERE useremail='$eMail' AND valid='S'";
            $result = updatedb($sqlcmd, $db_conn);
            $ServerName = $_SERVER['SERVER_NAME'];
            $Link = 'https://' . $ServerName . '/admin/suspendng.php?ReqID=' 
            . $ReqID . '&vCode=' . $vCode;
            // Notify user about the account and password  
            $From = "Mail Master <mailmaster@gm.ttu.edu.tw>";
            $To = $eMail;
            $Subject = '開放資料管理系統 管理者帳號解除鎖定通知';
            $Recipient = $eMail;
            $Message = "\n有用戶透過解除鎖定功能申請管理者帳號解除鎖定，"
                . "如果不是您所申請，則可不予理會\n\n"
                . "請點選下列連結解除鎖定，解除後直接進入登入頁面：\n\n"
                . $Link . "\n\n"
                . "This is an automactically generated response email. "
                . "If you do not expect to receive it, then someone might regist your "
                . "email address in our U9 Inter University course registration system. "
                . "If this is the case, please accept our apology.";
            $_SESSION['VerifyCode'] = mt_rand(1000,9999);
            require_once('../include/sendmail_inc.php');
    require_once('../include/header.php');
?>
<body>
<div class="Container">
<div style="text-align:center;width:100%;background:#ffe6e6;">
<img src="../images/logo08.png" width="460">
</div>
<div style="text-align:center;font-weight:bold;margin:10px 0;">
  管理者帳號 解除鎖定
</div>
  <div style="text-align:center;margin:8px 0 0 0;">
  解除鎖定郵件已寄到您的電子郵件信箱，請點選連結解除鎖定狀態。
  </div>
<div style="text-align:center;margin:12px 0;">
<a href="index.php">返回登入頁面</a>
</div>
<?php
    require_once('../include/footer.php');
?>
</div>
</body>
</html>
<?php
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
<div style="text-align:center;font-weight:bold;margin:10px 0;">
管理者帳號 鎖定狀態解除鎖定
</div>
<div style="text-align:center;color:red;margin:0 0 10px 0;">
您的帳號已被鎖定，請輸入下列資料申請解除
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
    <input type="submit" name="Submit" value="申請解除鎖定">&nbsp;&nbsp;
    </div>
    <div style="text-align:center;margin:8px 0 0 0;">
    請於上方欄位輸入所要求之資料後，<br />點選『申請解除鎖定』按鈕，
    系統會寄解除鎖定郵件給您。
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
