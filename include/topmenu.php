<?php
if (!isset($MenuItem)) $MenuItem = 1;
$Item1FG = $Item2FG = $Item3FG = $Item4FG = $GSpan = '<span style="padding:2px 4px 4px 4px;">';
$Item1Tail = $Item2Tail = $Item3Tail = $Item4Tail = '';
$ColorSet = '<span style="color:white;background:blue;padding:2px 4px 4px 4px;">';
switch ($MenuItem) {
    case 1:
        $Item1FG = $ColorSet;
        $Item1Tail = '</span>';
        break;
    case 2:
        $Item2FG = $ColorSet;
        $Item2Tail = '</span>';
        break;
    case 3:
        $Item3FG = $ColorSet;
        $Item3Tail = '</span>';
        break;
    case 3:
        $Item4FG = $ColorSet;
        $Item4Tail = '</span>';
        break;
    default:
}
if (!isset($LogoName)) $LogoName = 'homecarelogo_2.png';
?>
<div style="width:100%;background:#ffe6e6;padding:3px 10px;font-weight:bold;font-size:16px;">
<div style="display:inline-block;width:420px;">
<img src="../images/homecarelogo_2.png" height="52">
</div>
<div style="display:inline-block;vertical-align:text-bottom;">
<a href="../admin/adminmgm.php"><?php echo $Item1FG; ?>用戶管理<?php echo $Item1Tail; ?></a> |
<a href="../admin/adminmgm.php"><?php echo $Item2FG; ?>用戶管理<?php echo $Item2Tail; ?></a> |
<a href="../index.php"><?php echo $GSpan; ?>登出系統</span></a>
</div>
</div>
