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
    . "AND vcode='$vCode' AND valid='S'";
$rs = querydb($sqlcmd, $db_conn);

if (count($rs) <= 0) 
    die('<div style="text-align:center;">帳號解鎖連結已失效，可能是帳號已解鎖</div>');
$LoginID = $rs[0]['useremail'];
$NewvCode = rand(1000,9999);
$sqlcmd = "UPDATE adminuser SET valid='Y',vcode='$NewvCode',"
    . "pwderrcount=0 WHERE reqid='$ReqID' AND vcode='$vCode' "
    . "AND valid='S'"; 
$result = updatedb($sqlcmd, $db_conn);
header ("refresh:5; url=index.php?ID=$LoginID",true,303);
?>
<div style="text-align:center;margin:30px 0 0 0;">帳號鎖定已解除</div>
<div style="text-align:center;margin:30px 0;">
5秒後自動切換至登入畫面
</div>
