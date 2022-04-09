<?php
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])) {
    header ("Location:index.php");
    exit();
}
require_once("../include/gpsvars.php");
require_once("../include/configure.php");
require_once("../include/db_func.php");
require_once("../include/xss.php");
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
if (!isset($_SESSION['UserAdmin']) || !$_SESSION['UserAdmin']) {
    header ("Location:index.php");
    exit();
}
$PageSize = 20;
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
if (isset($VoidUser) && is_numeric($VoidUser) && isset($eMail) 
        && $eMail==addslashes($eMail) && $eMail<>$LoginID) {
    $eMail = xsspurify($eMail);
    $sqlcmd = "UPDATE adminuser SET valid='N',modifyby='$LoginID' "
        . "WHERE seqno='$VoidUser' AND useremail='$eMail' AND valid<>'N'";
    $result = updatedb($sqlcmd, $db_conn);
}
if (isset($VoidUser) &&  $eMail==$LoginID) {
    $ErrMsg = '不能刪除本人帳號';
}
if (isset($Recall) && is_numeric($Recall) && isset($eMail) && $eMail==addslashes($eMail)) {
    $eMail = xsspurify($eMail);
    $sqlcmd = "UPDATE adminuser SET valid='Y',modifyby='$LoginID' "
        . "WHERE seqno='$Recall' AND useremail='$eMail' AND valid<>'Y'";
    $result = updatedb($sqlcmd,$db_conn);
}
if (!isset($HideDelUser)) {
    if (isset($_SESSION['HideDel'])) $HideDelUser = $_SESSION['HideDel'];
    else $HideDelUser = 'Y';
}
$Filter = '';
if ($HideDelUser <> 'N') $HideDelUser = 'Y';
if(isset($HideDelUser) && $HideDelUser == 'Y') {
  $Filter = "WHERE valid='Y'";
}
$_SESSION['HideDel'] = $HideDelUser;
$KeyWord = '';
if (!$SysAdmin) {
    if (!empty($Filter)) $Filter .= ' AND ';
    else $Filter = 'WHERE ';
    $Filter .= " lodgeid='$LodgeID'";
} else {
    if (!empty($KeyWord)) {
        if (!empty($Filter)) $Filter .= ' AND ';
        $Filter .= "username LIKE '%$KeyWord%' ";
    }
}
$sqlcmd = "SELECT count(*) AS reccount FROM adminuser $Filter";
$rs = querydb($sqlcmd, $db_conn);
$TotalUser = 0;
if (count($rs) > 0) $TotalUser = $rs[0]['reccount'];
$TotalPage = ceil($TotalUser/$PageSize);
if ($TotalPage == 0) $TotalPage = 1;
if (!isset($Page)) {
    if (isset($_SESSION['CurPage'])) $Page = $_SESSION['CurPage'];
    else $Page = 1;
}
if ($Page > $TotalPage || $Page < 1) $Page = 1;
$_SESSION['CurPage'] = $Page;
$PrevPage = $Page-1;
if ($PrevPage<1) $PrevPage = 1;
$NextPage = $Page+1;
if ($NextPage>$TotalPage) $NextPage = $TotalPage;
$Offset = ($Page-1)*$PageSize;
$sqlcmd = "SELECT * FROM adminuser $Filter ORDER BY valid,lodgeid,useremail LIMIT $Offset,$PageSize";
$Users = querydb($sqlcmd, $db_conn);
$Link = '<a href="adminmgm.php?HideDelUser=Y">隱藏刪除用戶</a>';
if ($HideDelUser=='Y') $Link = '<a href="adminmgm.php?HideDelUser=N">顯示刪除用戶</a>';
require_once ("../include/header.php");
$ThisPageTitle = '用戶管理';
$MenuItem = 1;
?>
<body>
<div class="Container">
<?php
require_once("../include/topmenu.php");
?>
<form method="POST" action="" style="padding:0;margin:0;">
<div style="width:100%;margin:3px 0 2px 0;">
<table width="99%" border="0" align="center">
<tr>
  <td width="120">
<a href="adminadd.php">
    <img src="../images/plus.png" border="0" align="absmiddle" height="14" 
    title="新增用戶" alt="按此紐新增用戶">新增用戶
</a>
  </td>
  <td align="center">
<?php if ($SysAdmin) { ?>
  姓名關鍵字：<input type="text" size="6" maxlength="6" name="KeyWord" 
  value="<?php echo $KeyWord; ?>" />&nbsp;
<?php } ?>
  共 <?php echo $TotalUser; ?> 筆&nbsp;
<?php if ($TotalPage > 1) { ?>
<a href="adminmgm.php?Page=<?php echo $PrevPage; ?>"><img src="../images/prevmonth.png"
    border="0" height="14" align="absmiddle"></a>
&nbsp; 
  <select name="Page" onchange="submit();">
<?php
    for ($i=1; $i<=$TotalPage; $i++) {
        echo "  <option value=\"$i\"";
        if ($i == $Page) echo ' selected';
        echo ">$i</option>\n";
    }
?>
  </select> / <?php echo $TotalPage ?> 頁
<a href="adminmgm.php?Page=<?php echo $NextPage; ?>"><img src="../images/nextmonth.png"
    border="0" height="14" align="absmiddle"></a>&nbsp;&nbsp;&nbsp;
<?php } ?>
  </td>
  <td align="right" width="120"><?php echo $Link; ?></td>
</tr>
</table>
</div>
</form>
<table class="mistab" align="center" width="99%">
<tr align="center" height="30">
  <th width="80">處理</th>
  <th style="text-align:left;">電子郵件(登入帳號)</th>
  <th width="180">用戶姓名</th>
  <th width="220">旅宿名稱</th>
<?php if ($SysAdmin) { ?>
  <th width="90">系統管理</th>
<?php } ?>
  <th width="90">用戶管理</th>
</tr>
<?php
foreach ($Users as $item) {
    $SeqNo = $item['seqno'];
    $UserName = $item['username'];
    $curid = $item['lodgeid'];
    $Org = '-';
    if (isset($arrLodges["$curid"])) $Org = $arrLodges["$curid"];
    $eMail = $item['useremail'];
    $SystemAdmin = '&nbsp;';
    if ($item['sysadmin']=='Y') 
        $SystemAdmin = '<img src="../images/check.gif" border="0">';
    $UserAdmin = '&nbsp;';
    if ($item['useradmin']=='Y') 
        $UserAdmin = '<img src="../images/check.gif" border="0">';
?>
<tr align="center" height="30">
  <td align="center">
<?php   if ($item['valid']=='Y') { ?>
<?php if ($LoginID<>$eMail) { ?>
  <a href="adminmgm.php?VoidUser=<?php echo $SeqNo; ?>&eMail=<?php echo $eMail; ?>">
  <img src="../images/cut.gif" border="0" align="absmiddle" title="停權" 
  alt="按此鈕停止本用戶使用權"></a>&nbsp;
<?php } ?>
  <a href="adminmod.php?SeqNo=<?php echo $SeqNo; ?>&eMail=<?php echo $eMail; ?>">
  <img src="../images/edit.gif" border="0" align="absmiddle" title="修改" alt="修改本筆資料"></a>&nbsp;
<?php   } ?>
<?php   if ($item['valid']<>'Y') { ?>
  <a href="adminmgm.php?Recall=<?php echo $SeqNo; ?>&eMail=<?php echo $eMail; ?>">
  <img src="../images/recover.gif" border="0" align="absmiddle" title="恢復" 
  alt="按此鈕恢復本用戶使用權"></a>
<?php   } ?>
  </td>
  <td align="left"><?php echo $eMail; ?> </td>
  <td><?php echo $UserName; ?></td>
  <td><?php echo $Org; ?></td>
<?php if ($SysAdmin) { ?>
  <td align="center"><?php echo $SystemAdmin; ?></td>
<?php } ?>
  <td align="center"><?php echo $UserAdmin; ?></td>
</tr>
<?php
}
?>
</table>
<?php
require_once("../include/footer.php");
?>
</div>
</body>
</html>
