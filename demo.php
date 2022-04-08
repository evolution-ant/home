<?php

/**
 * Created by PhpStorm.
 * User: Siam
 * Date: 2019/2/4 0004
 * Time: 下午 10:58
 */
//第 1 步: 创建画布
$image = imagecreatetruecolor(700, 394); //指定画布尺寸

//第 2 步: 创建颜色
$white = imagecolorallocate($image, 238, 245, 255); //创建颜色
$black = imagecolorallocate($image, 238, 245, 255); //创建颜色
imagefill($image, 0, 0, $white); //自定义画布的背景颜色

//第 3 步: 绘制图形
imagefilledrectangle($image, 50, 50, 150, 150, $black);

//第 4 步: 输出图片
$path = "./red.png";
header("Content-Type: image/png"); //需要将图片发送到浏览器
imagepng($image, $path); //输出图片到 $path 的位置. $path 包括图片的名称
imagedestroy($image,); //释放内存
