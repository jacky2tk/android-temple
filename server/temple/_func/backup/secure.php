<?php
session_start();
if(rand(0,1)){
    $mathematics = "+";
    $Number1 = RandPass(1);
    $Number2 = RandPass(3);
    $_SESSION["Secure"] = $Number1 + $Number2;
}else{
    $mathematics = "-";
    $Number1 = RandPass(3);
    $Number2 = RandPass(1);
    $_SESSION["Secure"] = $Number1 - $Number2;
}
$MathNum = strlen($Number1);
$MathMsg = $Number1.$mathematics.$Number2."=";

# 判斷作業系統

if($_ENV["OS"] != "Windows_NT") $TextPath = "/usr/X11R6/lib/X11/fonts/TTF/luximri.ttf"; # for Linux System
elseif($_ENV["windir"] == "C:\WINNT") $TextPath = "c:\\winnt\\fonts\\arial.ttf"; # for Win2K System
else $TextPath = "c:\\windows\\fonts\\arial.ttf"; # for WinXP System
$TextPath =  dirname(__FILE__)."/arial.ttf";
# 產生一圖塊
$im = imagecreate(135, 50) or die("您的 PHP 版本不支援 GD LIBRARY");
$background_color = imagecolorallocate($im, 255, 204, 255);

# 將亂數填入圖塊裡
for ($i = 0; $i <= 5; $i++){
	$text_color = imagecolorallocate($im, rand(100, 170 ), rand(50, 120), rand(50, 120));
	$x = 10 + $i * 20;
	$y = rand(25, 45);
    $y = 35;
	if($MathNum == $i OR $i == 5) $z = 0;
	else $z = rand(-30, 30);
	ImageTTFText($im, 25, $z, $x, $y, $text_color, $TextPath, $MathMsg[$i]);
}

# 在圖塊填入色塊
for($i = 0; $i <= 255; $i++)
{
	$point_color = imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
	imagesetpixel($im, rand(2, 132), rand(2, 48), $point_color);
}

# 將圖塊輸出成 png
header("Content-type: image/png");
imagepng($im);
imagedestroy($im);

function RandPass($Num){
    $Str = "";
    for($i=0;$i<$Num;$i++){
        $k = chr(rand(48,57));
        while($k==0 AND !$i) $k = chr(rand(48,57));
        $Str .= $k;
    }
    return $Str;
}
?>
