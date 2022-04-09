<?php
require_once("../include/gpsvars.php");
require_once("../include/configure.php");
require_once("../include/db_func.php");
require_once("../include/xss.php");
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
if (!isset($ReqID) || !isset($vCode) || !is_numeric($vCode)) {
    header ("Location:index.php");
    exit();
}
$ReqID = addslashes($ReqID);
$ReqID = xsspurify($ReqID);

if (empty($ReqID)) {
    header ("Location:index.php");
    exit();
}
$sqlcmd = "SELECT * FROM adminuser WHERE reqid='$ReqID' "
    . "AND vcode='$vCode' AND valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs) <= 0) 
    die('重設密碼連結已失效，可能是密碼已重設或是已再次申請重設');
$ID = $rs[0]['useremail'];
$uDate = date('Y-m-d');
if (!isset($PWD01)) $PWD01 = '';
if (!isset($PWD02)) $PWD02 = '';
$ErrMsg = '';
if (isset($Confirm)) {
    if (strlen($PWD01)<8 || strlen($PWD01)>20 || $PWD01<>$PWD02)
        $ErrMsg .= '密碼長度少於8或超過20，或是兩個密碼不相同\n';
    if (empty($ErrMsg)) {   // 資料驗證無誤
        $NewvCode = rand(10000,99999);
        $PWD = password_hash($PWD01, PASSWORD_BCRYPT);
        $sqlcmd = "UPDATE adminuser SET userpwd='$PWD',vcode='$NewvCode' "
            . "WHERE reqid='$ReqID' AND vcode='$vCode' AND valid='Y'";
        $result = updatedb($sqlcmd, $db_conn);
        header ("Location:index.php");
        exit();
    }
}
require_once('../include/header.php');
?>
<script type="text/javascript">
function setFocus() {
    document.LoginForm.PWD01.focus();
}
</script>
<body onload="setFocus()">
<div class="Container" style="width:800px">
<div style="text-align:center;width:100%;background:#ffe6e6;">
<img src="../images/logo08.png" width="460">
</div>
<div style="text-align:center;font-weight:bold;margin:10px 0;">
  管理員密碼重置
</div>
  <form method="POST" name="LoginForm" action="">
  <input type="hidden" name="ReqID" value="<?php echo $ReqID;?>">
  <input type="hidden" name="vCode" value="<?php echo $vCode;?>">
  <div style="width:500px;margin:6px auto;">
  登入email：<?php echo $ID; ?>
  </div>
  <div style="width:500px;margin:6px auto;">
    登入密碼：<input type="password" id='PWD01' name="PWD01" size="20" maxlength="20">
    &nbsp;&nbsp;(8~20個英數字或符號)
  </div>
  <div style="width:500px;margin:0 auto;">
    密碼驗證：<input type="password" name="PWD02" size="20" maxlength="20">&nbsp;&nbsp;(需與登入密碼相同)
  </div>
  <div style="text-align:center;margin:6px auto;">
  <input type="submit" name="Confirm" value="更新密碼">
  </div>
  <div style="text-align:center;margin:8px auto;">
  請於上方欄位輸入登入密碼及密碼驗證碼後，點選『更新密碼』按鈕即可重新設定密碼。
  </div>
  </form>
<?php
require_once('../include/footer.php');
?>
</div>
</body>
</html>
