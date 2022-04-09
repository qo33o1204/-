<?php
session_start();
if (!isset($_SESSION['VerifyCode'])) die();
$vCode = $_SESSION['VerifyCode'];
$img = imagecreatetruecolor(70,30);

$white = imagecolorallocate($img, 255, 255, 255);
$red = imagecolorallocate($img, 255, 0, 0);
$purple = imagecolorallocate($img, 70, 20, 100);
$pink = imagecolorallocate($img, 200, 0, 180);
$blue = imagecolorallocate($img, 80, 80, 255);

for($i=1; $i<=4; $i++){
}

imagefill($img, 0, 0, $white);

$line_color = imagecolorallocate($img, 64,64,64); 
$color = (mt_rand(1,2) == 1) ? $pink : $red;
imageline($img, mt_rand(3,10), 0, mt_rand(50,69), 29, $color);
$color = (mt_rand(1,2) == 1) ? $blue : $purple;
imageline($img, mt_rand(3,10), 29, mt_rand(50,69), 0, $color);
for($i=0; $i<2; $i++) {
    imageline($img, 0, mt_rand(0,29), 69, mt_rand(0,29), $purple);
}
$noise_color = imagecolorallocate($img, 0,0,255);
for($i=0;$i<150;$i++) {
    imagesetpixel($img, mt_rand(0,69), mt_rand(0,29), $noise_color);
}  
$c0 = imagecolorallocate($img, mt_rand(0,20)+51, mt_rand(0,20)+30, mt_rand(0,20)+43); 
$c1 = imagecolorallocate($img, mt_rand(0,20)+64, mt_rand(0,20)+43, mt_rand(0,20)+21); 
$c2 = imagecolorallocate($img, mt_rand(0,20)+80, mt_rand(0,20)+47, mt_rand(0,20)+21); 
$c3 = imagecolorallocate($img, mt_rand(0,20)+76, mt_rand(0,20)+21, mt_rand(0,20)+33); 
putenv('GDFONTPATH=' . realpath('.'));
for ($i=0; $i<4; $i++) {
    $coloridx = mt_rand(0,3);
    switch ($coloridx) {
        case 0: $txtcolor = $c0; break;
        case 1: $txtcolor = $c1; break;
        case 2: $txtcolor = $c2; break;
        default: $txtcolor = $c3; break;
    }
    $c = substr($vCode, $i, 1);
    $x = $i * 15 + 6;
    $y = mt_rand(0,3) + 21;
    $a = mt_rand(0,18) - 9;
    imagettftext($img, 16, $a, $x, $y, $txtcolor, "LobsterTwo-Bold.otf", $c);
}

header("Content-type: image/png");
imagepng($img);
imagedestroy($img);
?>
