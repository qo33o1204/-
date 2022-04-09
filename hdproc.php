<?php
// 找出DocumentRoot及ProgName
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
// 含入初始化的程式碼
require_once($DocumentRoot . "/include/initialize.php");
$db_conn = connect2db($dbhost, $dbuser, $dbpwd, $dbname);
@chdir($basedir);
$SizeRecalculate = 0;
// Delete file button clicked and confirmed.
if (isset($delfn) && !empty($delfn)) {
    $fname = "$CurDir/$delfn";
    if (file_exists($fname)) {
        unlink ($fname);
        $fname = substr($fname,strlen($basedir));
        $sqlcmd = "DELETE FROM sharefile WHERE ownerid='$LoginID' "
          . "AND filename='$fname'";
        $result = updatedb($sqlcmd, $db_conn);
        $SizeRecalculate = 1;
    }
}
// Check to see if check box delete is triggered.
if (isset($DelChk) && isset($FCount) && is_numeric($FCount)) {
    for ($k=0; $k<$FCount; $k++) {
        $ChkVar = 'F' . $k;
        if (isset($_POST["$ChkVar"])) {
            $fname = "$CurDir/" . $_POST["$ChkVar"];
            if (!file_exists($fname)) continue;
            unlink ($fname);
            $fname = substr($fname,strlen($basedir));
            $sqlcmd = "DELETE FROM sharefile WHERE ownerid='$LoginID' "
            . "AND filename='$fname'";
            $result = updatedb($sqlcmd, $db_conn);
        }
    }
    $SizeRecalculate = 1;
}
if (isset($DelChk) && isset($DCount) && is_numeric($DCount)) {
    for ($k=0; $k<$DCount; $k++) {
        $ChkVar = 'D' . $k;
        if (isset($_POST["$ChkVar"])) {
            $dname = "$CurDir/" . $_POST["$ChkVar"];
            if (!file_exists($dname) || !is_dir($dname)) continue;
            $CMD = "rm -R '$dname'";
            exec($CMD);
            $dname = substr($dname,strlen($basedir));
            $sqlcmd = "DELETE FROM sharefile "
            . "WHERE ownerid='$LoginID' AND filename='$dname'";
            $result = updatedb($sqlcmd, $db_conn);
        }
    }
    $SizeRecalculate = 1;
}
if (isset($Move2Dir) && isset($DstDir) && isset($FCount) && is_numeric($FCount)) {
    $Mv2Dir = $DstDir;
    $ToDir = $CurDir . '/' . $Mv2Dir;
    if ($DstDir == "/") $ToDir = $basedir . '/' . $LoginID;
    if ($DstDir == "..") {
        $lastslash = strrpos($CurDir,'/');
        $ToDir = substr($CurDir,0,$lastslash);
    }
    if (is_dir($ToDir)) {
        for ($k=0; $k<$FCount; $k++) {
            $ChkVar = 'F' . $k;
            if (!isset($_POST["$ChkVar"])) continue;
            $fname = "$CurDir/" . $_POST["$ChkVar"];
            if (!file_exists($fname)) continue;
            $ToFileName = $ToDir . '/' . $_POST["$ChkVar"];
            if (file_exists($ToFileName)) continue;
            $CMD = "mv '$fname' '$ToDir'";
            exec($CMD);
            $fname = substr($fname,strlen($basedir));
            $sqlcmd = "DELETE FROM sharefile "
            . "WHERE ownerid='$LoginID' AND filename='$fname'";
            $result = updatedb($sqlcmd, $db_conn);
        }
    }
}
if (isset($Move2Dir) && isset($DstDir) && isset($DCount) && is_numeric($DCount)) {
    $Mv2Dir = $DstDir;
    $ToDir = $CurDir . '/' . $Mv2Dir;
    if ($DstDir == "/") $ToDir = $basedir . '/' . $LoginID;
    if ($DstDir == "..") {
        $lastslash = strrpos($CurDir,'/');
        $ToDir = substr($CurDir,0,$lastslash);
    }
    if (is_dir($ToDir)) {
        for ($k=0; $k<$DCount; $k++) {
            $ChkVar = 'D' . $k;
            if (!isset($_POST["$ChkVar"])) continue;
            $dname = "$CurDir/" . $_POST["$ChkVar"];
            if (!file_exists($dname) || !is_dir($dname)) continue;
            if ($ToDir == $dname) continue;
            $CMD = "mv '$dname' '$ToDir'";
            exec($CMD);
            $dname = substr($dname,strlen($basedir));
            $sqlcmd = "DELETE FROM sharefile "
            . "WHERE ownerid='$LoginID' AND filename='$dname'";
            $result = updatedb($sqlcmd, $db_conn);
        }
    }
}
if ($SizeRecalculate==1) {
    $rootdir = $basedir . '/' . $LoginID;
    $dureturn = exec("du -s $rootdir");
    $CurSize = strtok($dureturn,'/');
    $sqlcmd = "UPDATE user SET cursize=$CurSize WHERE loginid='$LoginID'";
    $result = updatedb($sqlcmd, $db_conn);
    $_SESSION['CurSize'] = $CurSize;
}
header('Location: showhd.php');
?>