<?php
session_start();
if (!isset($_SESSION['LoginID']) || empty($_SESSION['LoginID'])) {
    header ("Location: index.php");
    exit();
}
$LapsTime = 1;
$DeletableTime = 24;
$ItemPerPage = 20;
require_once ("../include/gpsvars.php") ;
require_once ("../include/configure.php") ; 
require_once ("../include/db_func.php") ;
if (!isset($UserAdmin) || !$UserAdmin) {
    header ("Location:index.php");
    exit();
}
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname); 
$LodgeID = $_SESSION['curLodgeID'];
$LodgeName = $_SESSION['LodgeName'];
$sqlcmd = "SELECT * FROM lodgedata WHERE lodgeid='$LodgeID' AND valid='Y'";
$rs = querydb($sqlcmd, $db_conn);
if (count($rs)>0) {
    $LapsTime = $rs[0]['modifiabletime'];
    $DeletableTime = $rs[0]['deletabletime'];
}
if (isset($Action) && $Action='Delete' && is_numeric($Seq)) {
    $RTime = time() - $DeletableTime*3600;
    $CanDeleteTime = date('Y-m-d H:i:s', $RTime);  // Can delete only tagged after this time.
    $sqlcmd = "UPDATE tagrecord SET valid='N' WHERE seqno='$Seq' AND lodgeid='$LodgeID' "
        . "AND tagtime>'$CanDeleteTime'";
    $result = updatedb($sqlcmd, $db_conn);
}
if (!isset($_POST['StartDate'])) {
    if (isset($_SESSION['curStartDate'])) $StartDate = $_SESSION['curStartDate'];
    else $StartDate = date('Y-m-d');
}
$_SESSION['curStartDate'] = $StartDate;
$SearchTime = $StartDate . ' 23:59:59';
if (!isset($_POST['Keyword'])) {
    if (isset($_SESSION['curKeyword'])) $Keyword = $_SESSION['curKeyword'];
    else $Keyword = '';
}
$_SESSION['curKeyword'] = $Keyword;
$SearchString = '%' . $Keyword . '%';
$Filter = "WHERE lodgeid='$LodgeID' AND valid<>'N' AND tagtime<='$SearchTime' ";
if (!empty($Keyword)) $Filter .= "AND regname LIKE '$SearchString' ";
    
$sqlcmd = "SELECT count(*) AS reccount FROM tagrecord $Filter";
$rs = querydb($sqlcmd, $db_conn);
$sql = $sqlcmd;
$TotalRecord = 1;
if (count($rs) > 0) $TotalRecord = $rs[0]['reccount'];
$TotalPage = ceil($TotalRecord/$ItemPerPage);
if (!isset($Page)) {
    if (isset($_SESSION['curPage'])) $Page = $_SESSION['curPage'];
    else $Page = 1;
}
if ($Page<1) $Page = 1;
if ($Page>$TotalPage) $Page = $TotalPage;
$PageStart = ($Page-1)*$ItemPerPage;
$PrevPage = $Page-1;
$NextPage = $Page+1;
$sql = $sqlcmd = "SELECT * FROM tagrecord $Filter ORDER BY tagtime DESC "
    . "LIMIT $PageStart,$ItemPerPage";
$Tags = querydb($sqlcmd, $db_conn);
require_once ("../include/header.php");
$ThisPageTitle = '紀錄資料管理';
$MenuItem = 2;
?>
<body>
<div class="Container">
<?php
require_once("../include/topmenu.php");
?>
<div id="logo" style="font-size:20px;"><?php echo $LodgeName; ?> 實名登錄紀錄</div>
<form method="POST" action="">
<div style="text-align:center;margin:2px 12px;">
<?php if ($PrevPage>0) { ?>
<a href="recordmgm.php?Page=<?php echo $PrevPage; ?>">上一頁</a>
<?php } else { echo '上一頁'; } ?>
&nbsp;
<select name="Page" onchange="submit()">
<?php
for ($p=1; $p<=$TotalPage; $p++) {
    echo '<option value="' . $p . '"';
    if ($p==$Page) echo ' selected';
    echo ">$p</option>\n";
}
?>
</select>&nbsp;

<?php if ($NextPage<=$TotalPage) { ?>
<a href="recordmgm.php?Page=<?php echo $NextPage; ?>">下一頁</a>&nbsp;
<?php } else { echo '下一頁'; } ?>
&nbsp;&nbsp;&nbsp;&nbsp;
查詢日期(回溯)：<input type="date" name="StartDate" value="<?php echo $StartDate; ?>"
    max="<?php echo date('Y-m-d'); ?>" />&nbsp;&nbsp;
姓名查詢：<input type="text" name="Keyword" value="<?php echo $Keyword; ?>" size="10" />
&nbsp;&nbsp;
<input type="submit" name="Search" value="送出查詢" />
</div>
</form>
<div style="text-align:center;">
管理者<?php echo $LapsTime; ?>小時內可確認或修改資料，
<?php echo $DeletableTime; ?>小時內可刪除資料，
如果發現有異常登錄資料，請利用『條碼管理』功能變更條碼
<table class="mistab" width="100%">
<tr align="center">
    <th width="90">管理</th>
    <th width="110">姓名</th>
    <th width="110">電話</th>
    <th width="60">體溫</th>
    <th width="100">房號</th>
    <th width="110">登錄時間</th>
    <th style="text-align:left">疫調狀況</th>
    <th width="70">待確認</th>
</tr>    
<?php 
$curTime = time();
foreach ($Tags as $item) {
    $SeqNo = $item['seqno'];
    $RegPhone = $item['regphone'];
    $RegName = $item['regname'];
    $BodyTemp = $item['bodytemp'];
    if ($BodyTemp=='0.0') $BodyTemp = '';
    $RoomNo = $item['roomnumber'];
    $items = $item['items'];
    $TagTime = strtotime($item['tagtime']);
    $RefCode = $item['refcode'];
    $ShowTagTime = date('m-d H:i',$TagTime);
    $CanModify = false;
    if ($curTime-$TagTime<$LapsTime*3600) $CanModify = true;
    $ShowOP = '編輯';
    $ShowStatus = '';
    if ($item['valid']=='P') {
        $ShowOP = '確認';
        if ($CanModify) $ShowStatus = '<img src="../images/check.gif">';
    }
    $CanDelete = false;
    if ($curTime-$TagTime<$DeletableTime*3600) $CanDelete = true;
    $eventItems = explode ('{item}',$items);
?>
<tr align="center">
    <td>
    <?php if ($CanModify) { ?>
    <a href="recordmod.php?RefCode=<?php echo $RefCode; ?>"><?php echo $ShowOP; ?></a>&nbsp;
    <?php } ?>
    <?php if ($CanDelete) { ?>
    <a href="recordmgm.php?Action=Delete&Seq=<?php echo $SeqNo; ?>">刪除</a>
    <?php } ?>
    </td>
    <td><?php echo $RegName; ?></td>
    <td><?php echo $RegPhone; ?></td>
    <td><?php echo $BodyTemp; ?></td>
    <td><?php echo $RoomNo; ?></td>
    <td><?php echo $ShowTagTime; ?></td>
    <td align="left">
<?php 
    $ShowPan = '';
    foreach ($eventItems as $tmp) {
        $temp = explode(':',$tmp);
        if (count($temp)<>2) continue;
        if (!empty($ShowPan)) $ShowPan .= '； ';
        if ($temp[0]=='90') $ShowPan .= '正常';
        else $ShowPan .= $temp[1];
    }
    echo $ShowPan;
?>
    </td>
    <td><?php echo $ShowStatus; ?></td>
</tr>
<?php } ?>
</table>
<?php
require_once ("../include/footer.php");
?>
</div>
</body>
</html>