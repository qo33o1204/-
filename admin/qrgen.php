<?php
require_once("../include/gpsvars.php");
include ("../phpqrcode/qrlib.php");
if(!isset($data)) die("Should set QRCode content.");
$PixelSize = 6; // 1 ~ 10
$FrameSize = 6;
QRcode::png($data, false, QR_ECLEVEL_M, $PixelSize, $FrameSize, false);
?>

