<?php
// Quit button pressed. Return to calling program.
if (isset($_POST['Quit']) && !empty($_POST['Quit'])) {
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
if (!isset($_SESSION['SysAdmin']) || $_SESSION['SysAdmin']<>'Y') {
    exit('1');
    header ("Location:index.php");
    exit();
}
if (!isset($uID) || $uID<>addslashes($uID) || !isset($SeqNo) || !is_numeric($SeqNo)) {
print_r($_GET);
    exit('2');
    header ("Location:index.php");
    exit();
}
$sqlcmd = "SELECT * FROM adminuser WHERE seqno='$SeqNo' AND valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs)<=0 || $uID<>$rs[0]['email']) {    
    exit('3');
    header ("Location:index.php");
    exit();
}
$useruCode = $rs[0]['ucode'];
$UserName = $rs[0]['username'];
$sqlcmd = "SELECT * FROM university WHERE queryable='Y' ORDER BY ucode";
$rs = querydb($sqlcmd, $db_conn);
$University = array();
$uRegion = array();
if (count($rs)>0) {
    foreach ($rs as $item) {
        $Code = $item['ucode'];
        $University["$Code"] = $item['uname'];
        $uRegion["$Code"] = $item['region'];
    }
}
$sqlcmd = "SELECT regionname FROM twregion WHERE showseq>0 ORDER BY showseq";
$rs = querydb($sqlcmd, $db_conn);
$RegionNames = array();
foreach ($rs as $item) {
    $RegionNames[] = $item['regionname'];
}
$ErrMsg = '';
if (isset($Action) && $Action=="Remove" && isset($ruCode) 
    && isset($University["$ruCode"])) {
    $sqlcmd = "UPDATE userpriv SET valid='N' WHERE uid='$uID' "
        . "AND ucode='$ruCode' AND valid='Y'";
    $rs = updatedb($sqlcmd, $db_conn);
}
if (!isset($selRegion) || !in_array($selRegion, $uRegion)) {
    if (isset($_SESSION['curRegion'])) $selRegion = $_SESSION['curRegion'];
    else $selRegion = '北北基';
}
if (isset($Add) && !empty($Add) && isset($seluCode) 
        && isset($University["$seluCode"])) {
    $sqlcmd = "SELECT * from userpriv WHERE uid='$uID' AND ucode='$seluCode'";
    $rs = querydb($sqlcmd, $db_conn);
    if (count($rs) > 0) {
        if ($rs[0]['valid']=='N') {
            $sqlcmd = "UPDATE userpriv SET valid='Y' WHERE uid='$uID' "
                . "AND ucode='$seluCode'";
            $result = updatedb($sqlcmd, $db_conn);
        } 
    } else {
        $sqlcmd = "INSERT INTO userpriv (uid,ucode) VALUES ("
            . "'$uID','$seluCode')";
        $rs = updatedb($sqlcmd, $db_conn); 
    }
}
$sqlcmd = "SELECT * FROM userpriv WHERE uid='$uID' AND valid='Y' ORDER BY ucode";
$rs = querydb($sqlcmd, $db_conn);
$ExistuCodes = array();
if (count($rs) > 0) {
    foreach ($rs as $item) {
        $thisuCode = $item['ucode'];
        $ExistuCodes["$thisuCode"] = 1;
    }
}
if (!isset($seluCode)) $seluCode = '';
require_once("../include/header.php");
$ThisPageTitle = '夥伴學校權限設定';
$MenuItem = 3;
?>
<body>
<div class="Container">
<?php
require_once("../include/topmenu.php");
?>
<div style="width:100%;margin:3px 0 2px 0;text-align:center;font-weight:bold;font-size:1.1em;">
<?php echo $UserName; ?> 夥伴學校權限設定
</div>
<div style="font-size:1em;font-weight:bold;color:Brown;text-align:center;">
如欲新增夥伴學校，請於右方選擇學校後點選『加入』按鈕
</div>
<div style="width:100%;margin:2px 0 2px 0;">
<div style="width:760px;margin:0px auto;">
<form method="POST" name="ModForm" action="">
<input type="hidden" name="SeqNo" value="<?php echo $SeqNo; ?>">
<input type="hidden" name="uID" value="<?php echo $uID; ?>">
<span style="font-weight:bold;">
已經設定之夥伴學校
</span>
<span style="float:right">
區域：<select name="selRegion" onchange="submit()">
<?php
foreach ($RegionNames as $rName) {
    echo '<option value="' . $rName . '"';
    if ($rName == $selRegion) echo ' selected';
    echo ">$rName</option>\n";
}
?>
  </select>
  <select name="seluCode">
<?php
foreach ($University as $curuCode=>$uName) {
    if ($uRegion["$curuCode"] <> $selRegion || $curuCode==$useruCode
        || isset($ExistuCodes["$curuCode"])) continue;
    echo '<option value="' . $curuCode . '"';
    if ($curuCode==$seluCode) echo ' selected';
    echo ">$uName</option>\n";
}    
?>
  </select>
  <input type="submit" name="Add" value="加入">&nbsp;
  <input type="submit" name="Quit" value="離開">
</span>
<table width="760" class="mistab" align="center">
<tr height="30">
  <th width="80">編號</th>
  <th width="80">動作</th>
  <th width="100">校代碼</th>
  <th align="left">校名</th>
</tr>
<?php
$Seq = 0;
foreach ($ExistuCodes as $curuCode=>$value) {
    if (!isset($University["$curuCode"])) continue;
    $uName = $University["$curuCode"];
    $Seq++;
?>
<tr align="center">
  <td><?php echo $Seq; ?></td>
  <td>
    <a href="adminright.php?SeqNo=<?php echo $SeqNo; ?>&uID=<?php 
    echo $uID; ?>&Action=Remove&ruCode=<?php echo $curuCode; ?>"><img 
    src="../images/cut.gif" border="0"></a></td>
  <td><?php echo $curuCode; ?></td>
  <td align="left"><?php echo $uName; ?></td>
</tr>
<?php } ?>  
</table>
</form>
</div>
</div>
<?php
require_once("../include/footer.php");
?>
</div>
</body>
</html>