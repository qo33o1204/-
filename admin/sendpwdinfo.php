<?php
session_start();
require_once ("../include/gpsvars.php");
require_once ("../include/configure.php");
require_once ("../include/db_func.php");
require_once ("../include/xss.php");
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
$ErrMsg = '';
if (!isset($eMail) || !isset($SeqNo) || !is_numeric($SeqNo)) {
    die('Check point 1');
    header ("Location: index.php");
    exit();
}
$eMail = addslashes(xsspurify($eMail));
if (empty($eMail)) {
    header ("Location: index.php");
    exit();
}
$sqlcmd = "SELECT * FROM adminuser WHERE useremail='$eMail' AND "
    . "seqno='$SeqNo' AND valid<>'N'";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs)<=0) {
    die('Check point 3');
    header ("Location: index.php");
    exit();
}
$eMail = $rs[0]['useremail'];
$vCode = $_SESSION['VerifyCode'];
if (empty($ErrMsg)) {
    $ReqID = sha1($eMail . date('His'));
    $sqlcmd = "UPDATE adminuser SET reqid='$ReqID',vcode='$vCode' "
        . "WHERE useremail='$eMail' AND valid='Y'";
    $result = updatedb($sqlcmd, $db_conn);
    $ServerName = $_SERVER['SERVER_NAME'];
    $Link = 'https://' . $ServerName . '/admin/suspendng.php?ReqID=' 
    . $ReqID . '&vCode=' . $vCode;
    // Notify user about the account and password  
    $From = "Mail Master <mailmaster@cc-isac.org>";
    $To = $eMail;
    $Subject = '安心旅宿實名登錄系統 管理者密碼重置通知';
    $Recipient = $eMail;
    $Message = "\n有用戶透過重設密碼功能申請安心旅宿實名登錄系統管理者密碼重置，"
        . "如果不是您所申請，則可不予理會\n\n"
        . "請點選下列連結進入系統設定新密碼：\n\n"
        . $Link . "\n\n"
        . "This is an automactically generated response email. "
        . "If you do not expect to receive it, then someone might regist your "
        . "email address in our Lodging management system. "
        . "If this is the case, please accept our apology.";
    $_SESSION['VerifyCode'] = mt_rand(1000,9999);
    require_once('../include/sendmail_inc.php');
}
require_once("../include/header.php");
?>
<body>
<div class="Container" style="width:800px">
<div style="text-align:center;width:100%;background:#ffe6e6;">
<img src="../images/logo08.png" width="460">
</div>
<div style="text-align:center;font-weight:bold;margin:10px 0;">
  管理員 密碼重置
</div>
<?php if (empty($ErrMsg)) { ?>
  <div style="text-align:center;margin:8px 0 0 0;">
  密碼重置郵件已寄到您的電子郵件信箱，請依指示重設您的密碼。
  </div>
<?php } else { ?>
  <div style="text-align:center;margin:8px 0 0 0;">
  資料有錯誤，請回上一頁重新輸入。
  </div>
<?php } ?>
<div style="text-align:center;margin:12px 0;">
<a href="index.php">返回登入頁面</a>
</div>
<?php
require_once('../include/footer.php');
?>
</div>
</body>
</html>
