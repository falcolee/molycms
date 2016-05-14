<?php
// +----------------------------------------------------------------------
// | MOLYCMS	前台公告控制器
// +----------------------------------------------------------------------
//

class public_control extends control{
	public $_cfg = array();	// 全站参数
	public $_var = array();	// 
	
	/**
     * 生成图像验证码
     * @static
     * @access public
     * @param string $length  位数
     * @param string $mode  类型
     * @param string $type 图像格式
     * @param string $width  宽度
     * @param string $height  高度
     * @return string
     */
    public function verify($length=4,$type='png', $width=50, $height=25, $verifyName='verify') {
        $randval = random(4,1);
        
        $name = R('name','G');
        if($name == ''){
        	$name = $verifyName;
        }
        
        R('width','G') && $width = R('width','G');
        R('height','G') && $height = R('height','G');
        
        session::set($name,md5($randval));
        $width = ($length * 10 + 10) > $width ? $length * 10 + 10 : $width;
        if ($type != 'gif' && function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($width, $height);
        } else {
            $im = imagecreate($width, $height);
        }
        $r = Array(225, 255, 255, 223);
        $g = Array(225, 236, 237, 255);
        $b = Array(225, 236, 166, 125);
        $key = mt_rand(0, 3);

        $backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]);    //背景色（随机）
        $borderColor = imagecolorallocate($im, 100, 100, 100);                    //边框色
        imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
        $stringColor = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120));
        // 干扰
        for ($i = 0; $i < 10; $i++) {
            imagearc($im, mt_rand(-10, $width), mt_rand(-10, $height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $stringColor);
        }
        for ($i = 0; $i < 25; $i++) {
            imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $stringColor);
        }
        for ($i = 0; $i < $length; $i++) {
            imagestring($im, 5, $i * 10 + 5, mt_rand(1, 8), $randval{$i}, $stringColor);
        }
        header("Content-type: image/" . $type);
        $ImageFun = 'image' . $type;
        $ImageFun($im);
        imagedestroy($im);
    }

	// hook public_control_after.php
}
