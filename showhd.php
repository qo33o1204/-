<?php
// Locate DocumentRoot and ProgName
$DocumentRoot = $_SERVER['DOCUMENT_ROOT'];
$ScriptName = explode('/',$_SERVER['PHP_SELF']);
$nArg = count($ScriptName);
$ProgName = $ScriptName[$nArg-1];
if (!file_exists($DocumentRoot . '/include')) {
    foreach ($ScriptName as $SubDir) {
        $DocumentRoot .= '/' . $SubDir;
        if (file_exists($DocumentRoot . '/include')) break;
    }
}
if (!file_exists($DocumentRoot . '/include')) $DocumentRoot = '..';
require_once($DocumentRoot . "/include/initialize.php");
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
require_once($DocumentRoot . "/include/header.php");
@chdir($basedir);
// Something wrong if the user home directory does not exist.
if (!file_exists($LoginID)) die("Something Wrong");
$Perms = fileperms($CurDir);
if (!($Perms&00400 && $Perms&00100 && $Perms&004 && $Perms&001))
    chmod($CurDir,0755);
@chdir ($CurDir);
$rootdir = $basedir . '/' . $LoginID;
if (isset($chgdir) && !empty($chgdir) && substr($chgdir,0,1) <> '/') {
    if (($rootdir<>$CurDir || substr($chgdir,0,2)<>'..')
        && file_exists($chgdir)) {
        $Perms = fileperms($chgdir);
        if ($Perms&00400 && $Perms&00100 && $Perms&004 && $Perms&001)
            @chdir($chgdir);
    }
}
if (isset($goDir) && !empty($goDir) && substr($goDir,0,1)<>'/') {
    $Perms = fileperms($rootdir);
    if ($Perms&00400 && $Perms&00100 && $Perms&004 && $Perms&001)
        @chdir($rootdir);
    if (file_exists($goDir)) {
        $Perms = fileperms($goDir);
        if ($Perms&00400 && $Perms&00100 && $Perms&004 && $Perms&001)
            @chdir($goDir);
    }
}
$CurDir = getcwd();
if (!strstr($CurDir, $rootdir)) {
    $Perms = fileperms($rootdir);
    if (!($Perms&00400 && $Perms&00100 && $Perms&004 && $Perms&001))
        chmod($rootdir,0755);
    @chdir($rootdir);
    $CurDir = getcwd();
}
$_SESSION['CurDir']=$CurDir;
$CurSize = $_SESSION['CurSize'];
$SizeLeft = $Quota - $CurSize;
if ($SizeLeft < 0) $SizeLeft = $Language['QuotaFull'];
else $SizeLeft = number_format($SizeLeft,0) . ' kBytes';
$ShowDir = substr($CurDir,strlen($basedir));
$arrDir = explode('/',$ShowDir,10);
$DirDepth = count($arrDir);
$DirStr = '<a href="showhd.php?goDir=.">' . $Language['RootDir'] . '</a>';
$CurDirs = '';
for ($i=2; $i<count($arrDir); $i++) {
    if (!empty($CurDirs)) $CurDirs .= '/';
    $CurDirs .= $arrDir[$i];
    $DirStr .= '/' . "<a href=\"showhd.php?goDir=$CurDirs\">"
        . fndecode($arrDir[$i]) . '</a>';
}
?>
<Script Language="JavaScript">
<!--
function confirmation(DspMsg, PassArg) {
var name = confirm(DspMsg)
    if (name == true) {
      location=PassArg;
    }
}
-->
</SCRIPT>
<div id="logo"><b><?php echo $Language['SystemName'] ?></b></div>
<?php require_once("topmenu.php"); ?>
<table width="98%" align="center" border="0">
<tr>
  <td align="center"> 
  <?php echo $Language['Quota'] ?> : <?php 
  echo number_format($Quota,0); ?> kBytes &nbsp;&nbsp;
  <?php echo $Language['UsedSize'] ?> : <?php 
  echo number_format($CurSize,0); ?> kBytes &nbsp;&nbsp;
  <?php echo $Language['RemainSize'] ?>: <?php echo $SizeLeft ?> 
  </td>
</tr>
</table>
<table width="98%" align="center" border="0">
<tr>
  <td><?php echo $Language['Directory'] ?> : <?php echo $DirStr ?></td>
</tr>
</table>
<?php
if (isset($Action)) {
    switch ($Action) {
    case 'Upload':
        require_once('uploadform.php');
        break;
    case 'AddDir':
        require_once("newdirform.php");
        break;
    case 'ModUser':
        require_once("userdataform.php");
        break;
    case 'RenameFile':
        require_once("renameform.php");
        break;
    case 'ShareFile':
        require_once("sharefileform.php");
        break;
    case 'ShareDir':
        require_once("sharedirform.php");
        break;
    default:
        break;
    }
}
?>
<form method="POST" name="WebHDMgm" action="hdproc.php">
<div align="center">
<table width="98%" class="cistab" align="center">
<tr>
<th width="60"><?php echo $Language['Delete'] ?></th>
<th width="60"><?php echo $Language['Share'] ?></th>
<th width="60"><?php echo $Language['Rename'] ?></th>
<th align="left">
<a href="showhd.php?Order=filename"><?php echo $Language['FileName'] ?></a></th>
<th align="right" width="110">
<a href="showhd.php?Order=filesize"><?php echo 
$Language['FileSize'] ?></a>&nbsp;</th>
<th width="170">
<a href="showhd.php?Order=time"><?php 
echo $Language['FileModTime'] ?></a></th>
</tr>
<?php
$handle = opendir('.');
$numFile = 0;
$numDir = 0;
while ($file = readdir($handle)) {
  if ($file <> '.' && $file <> '..') {
    $RelFN = $CurDir . '/' . $file;
    if (is_dir($RelFN)) {
      $DN[$numDir] = $file;
      $OrgDN[$numDir] = fndecode($file);
      $DT[$numDir] = date('Y-m-d H:i:s',filemtime($RelFN));
      ++$numDir;
    } else {
      $FN[$numFile] = $file;
      $OrgFN[$numFile] = fndecode($file);
      $FS[$numFile] = filesize($RelFN);
      $FT[$numFile] = date('Y-m-d H:i:s',filemtime($RelFN));
      ++$numFile;
    }
  }
}
closedir($handle);
if (isset($DN)) array_multisort($OrgDN,SORT_ASC,$DN,$DT);
if (isset($FN)) {
    array_multisort($OrgFN,SORT_ASC,$FN,$FS,$FT);
$OrderChg = 1;
if (!isset($Order)) {
    $Order = $LastKey;
    $OrderChg = 0;
}
switch($Order) {
case "time":
    if ($LastKey == 'time') {
        if ($OrderChg) $CurOrder = ($CurOrder+1) & 1;
    } else {
        $CurOrder = 0;
    }
    $LastKey = 'time';
    if ($CurOrder) {
        array_multisort($FT,SORT_DESC,$FS,$FN);
        if (isset($DN)) array_multisort($DT,SORT_DESC,$DN);
    } else {
        array_multisort($FT,SORT_ASC,$FS,$FN);
        if (isset($DN)) array_multisort($DT,SORT_ASC,$DN);
    }
    break;
case "filesize":
    if ($LastKey == 'filesize') {
        if ($OrderChg) $CurOrder = ($CurOrder+1) & 1;
    } else {
        $CurOrder = 0;
    }
    $LastKey = 'filesize';
    if ($CurOrder) array_multisort($FS,SORT_DESC,$FN,$FT);
    else array_multisort($FS,SORT_ASC,$FN,$FT);
    break;
case "filename":
    if ($LastKey == 'filename') {
        if ($OrderChg) $CurOrder = ($CurOrder+1) & 1;
    } else {
        $CurOrder = 0;
    }
    $LastKey = 'filename';
    if ($CurOrder) {
        array_multisort($OrgFN,SORT_DESC,$FN,$FS,$FT);
        if (isset($DN)) array_multisort($OrgDN,SORT_DESC,$DN,$DT);
    } else {
        array_multisort($OrgFN,SORT_ASC,$FN,$FS,$FT);
        if (isset($DN)) array_multisort($OrgDN,SORT_ASC,$DN,$DT);
    }
    break;
default:
    break;
}
}
$_SESSION['CurOrder']=$CurOrder;
$_SESSION['LastKey']=$LastKey;

$LineNo = 0;
if ($CurDir <> $rootdir) {
  $UpDir = "..";
  echo "\n<tr bgcolor=\"$DirLC1\">\n";
  echo "<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>\n";
  echo "<td colspan=3>";
  echo "<a href=\"showhd.php?chgdir=$UpDir\">";
  echo "<img src=\"../images/folder.gif\" border=0 "
    . '"align=absbottom"> ';
  echo $Language['Ret2UpperDir'];
  echo "</a></td>\n</tr>\n";
  $temp = $DirLC1;
  $DirLC1 = $DirLC2;
  $DirLC2 = $temp;
}
for ($i=0; $i<$numDir; $i++) {
  $dname=$DN[$i];
  $dname = htmlspecialchars($dname, ENT_QUOTES);
  $decoded_dname = fndecode($dname);
  $htmlFName = htmlspecialchars($decoded_dname);
  $htmlGetFName = str_replace('?','_',$htmlFName);
  $htmlGetFName = str_replace('#','_',$htmlFName);
  $dureturn = exec("du -s '$dname'");
  $DirSize = strtok($dureturn,"\t");
  echo "\n<tr bgcolor=\"$DirLC1\">\n";
  $temp = $DirLC1;
  $DirLC1 = $DirLC2;
  $DirLC2 = $temp;
  $DspMsg = "'{$Language['ConfirmDeleteDir']}'";
  $PassArg = "'rmdir.php?RmDir=$dname'";
  echo '<td align="center">';
  echo "<a href=\"javascript:confirmation($DspMsg, $P assArg);\">";
  echo "<img src=\"../images/cut.gif\" border=\"0\" "
    . "align=\"absbottom\" alt=\"{$Language['PressToDelDir']}\"></a></td>\n";
  echo '<td align="center">';
  echo "<a href=\"showhd.php?Action=ShareDir&fname=$dname\">";
  echo "<img src=\"../images/shareicon.gif\" border=\"0\" "
    . "align=\"absbottom\" alt=\"{$Language['PressToSetShare']}\"></a>\n";
  echo "</td>\n<td>&nbsp;</td>";
  echo "<td>";
  echo "<img src=\"../images/folder.gif\" border=\"0\" "
    . "align=\"absmiddle\"> ";
  echo "<input type=\"checkbox\" name=\"D$i\" value=\"$dname\">\n";
  echo "<a href=\"showhd.php?chgdir=$dname\">";
  echo "$htmlGetFName</a></td>\n";
  echo '<td align="right">' . number_format($DirSize) . "k </td>\n";
  echo '<td align="center"><nobr> ' . $DT[$i] . " </nobr></td>\n";
  echo '</tr>';
}

$ScrChr='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890*';
mt_srand ((double) microtime()*100000);
$scmkey = date('d')+date('m');
$LeftLen = $scmkey % $LeftMod + 2;
$RightLen = $scmkey % $RightMod + 5;
for ($i=0; $i<$numFile; $i++) {
  $fname=$FN[$i];
  $decoded_fname = fndecode($fname);
  $htmlFName = htmlspecialchars($decoded_fname);
  $htmlGetFName = str_replace('?','_',$htmlFName);
  $htmlGetFName = str_replace('#','_',$htmlFName);
  $Scramble = "";
  for ($j=0; $j<30; $j++)
    $Scramble .= substr($ScrChr,mt_rand(0,61),1);
  $para = substr($Scramble,9,$LeftLen) . $ShowDir . '/' . $fname
      . substr($Scramble,5,$RightLen);
  $para = substr($Scramble,7,$LeftLen) . enbase64($para);
  echo "\n<tr bgcolor=\"$FileLC1\">\n";
  $temp = $FileLC1;
  $FileLC1 = $FileLC2;
  $FileLC2 = $temp;
  echo '<td align="center">';
  $DspMsg = "'{$Language['ConfirmDeleteFile']}'";
  $PassArg = "'hdproc.php?delfn=$fname'";
  echo "<a href=\"javascript:confirmation($DspMsg, $PassArg);\">";
  echo "<img src=\"../images/cut.gif\" border=\"0\" "
    . "align=\"absbottom\" alt=\"{$Language['PressToDelFile']}\"></a></td>\n";
  echo '<td align="center">';
  echo "<a href=\"showhd.php?Action=ShareFile&fname=$fname\">";
  echo "<img src=\"../images/shareicon.gif\" border=\"0\" "
    . "align=\"absbottom\" alt=\"{$Language['PressToSetSharefFile']}\"></a>";
  echo "</td>\n";
  echo '<td align="center">';
  echo "<a href=\"showhd.php?Action=RenameFile&fname=$fname\">";
  echo "<img src=\"../images/rename.gif\" border=\"0\" "
    . "align=\"absbottom\" alt=\"{$Language['PressToRename']}\"></a>";
  echo "</td>\n";
  echo "<td>";
  echo "<img src=\"../images/doc.gif\" border=\"0\" "
    . "align=\"absmiddle\"> ";
  echo '<input type="checkbox" '. "name=\"F$i\" value=\"$fname\">\n";
  echo "<a href=\"/download/$htmlGetFName?para=$para\" TARGET=\"_BLANK\">";
  echo "$htmlGetFName</a></td>\n";
  echo '<td align="right">'
    . number_format($FS[$i],0) . " </td>\n";
  echo '<td align="center"><nobr> ' . $FT[$i] . " </nobr></td>\n";
  echo '</tr>';
}
?>
</table>
<?php if ($numFile > 0 || $numDir > 0) { ?>
<table width="98%" align="center" border="0">
<tr>
<td width="186">&nbsp;</td>
<td>
<input type="submit" name="DelChk" value="<?php echo $Language['DeleteChecked'] ?>">
<?php if ($numDir>=1 || $CurDir<>$rootdir) { ?>
&nbsp;<?php echo $Language['OR'] ?>
&nbsp;<input type="submit" name="Move2Dir" 
value="<?php echo $Language['MoveChecked'] ?>"> 
&nbsp;<?php echo $Language['TO'] ?> <select name="DstDir">
<option value="/">/</option>
<?php if ($CurDir <> $rootdir) { ?>
<option value=".."><?php echo $Language['UpperDir'] ?></option>
<?php } ?>
<?php
for ($i=0; $i<$numDir; $i++) {
    $dname=$DN[$i];
    $decoded_dname = fndecode($dname);
    $decoded_dname = htmlspecialchars($decoded_dname, ENT_QUOTES);
    echo "<option value=\"$dname\">$decoded_dname</option>\n";
}
?>
</select>
<?php } ?>
</td>
</tr>
</table>
<?php } ?>
<input type="hidden" name="FCount" value="<?php echo $numFile ?>">
<input type="hidden" name="DCount" value="<?php echo $numDir ?>">
</form>
<div id="message">
<?php echo $Language['SizeCalculation'] ?>
</div>
<?php if (date('Y-m-d') <= '2007-12-30') { ?>
<div id="message">
<font color="Red">原硬碟資料損毀無法救回，請重新上傳檔案，造成不便，尚請見諒。
</font>
</div>
<?php } ?>
<?php
require_once($DocumentRoot . "/include/footer.php");
?>
