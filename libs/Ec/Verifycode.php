<?php
// -------------------------------------------------------------
	// 名称: my_authimg class
    //
    // 用途: 根据图片数字字母验证
    // 实例:
    // $ai = new my_authimg();
    // $ai->render();
    //
    // 判断结果是否正确
    // $ai = new my_authimg();
    // $ai->is_true($str);
    //
    // @作者: hightman
    // @版本: 0.0.0
    // @时间: 2005/05/26
    // $Id: $
// -------------------------------------------------------------
require_once 'Zend/Session.php';

class Ec_Verifycode {
	// public var
    public $width          = 72;
    public $height         = 18;
    public $text_font      = 5;
    public $text_space     = 10;
    public $text_length    = 5;
    public $sess_name      = "＿＿auth_wms_123__";
    public $num_only       = false;
               
    // private
    private $_top           = 1;
    private $_move          = 3;
    private $_lines         = 5;
               
    function VerifyCode($params = array()) {
		settype($params, "array");
        foreach ($params as $key => $value) {
        	if (isset($this->$key)) {
            	$this->$key = $value;
            }
       }
       $this->_lines = intval($this->height / 10);
	}
               
	function set_sess_name($name = "") {
		if (!empty($name)) {
        	$this->sess_name = $name;
        }
	}
               
	function set_text_length($length = 5) {
		$this->text_length = $length;
	}
               
	function set_img_size($width = 100, $height = 15) {
		$this->width = $width;
		$this->height = $height;
	}
               
	function get_sess_value() {
		$sess_key = $this->sess_name;
		$session=new Zend_Session_Namespace('adminAuth');
        return $session->$sess_key;
	}
               
	function is_true($str) {
		$sess_value = $this->get_sess_value();
        return (!strcasecmp($sess_value, $str));
	}
               
	function render() {
		$radix = "123456789";
        if (!$this->num_only) {
        	$radix .= "ABCDEFGHIJKLMNPQRSTUVWXYZ";
        }
        $radix_len = strlen($radix);
      
        // 种下随机种子
        mt_srand();

       	// 初始化图片
		$image          = ImageCreate($this->width, $this->height);
                       
		// 设定颜色
        $r = mt_rand() % 100; //2 ? 255 : 0;
       	$g = mt_rand() % 100; //2 ? 255 : 0;
        $b = mt_rand() % 100; //2 ? 255 : 0;
        $fgcolor = ImageColorAllocate($image, $r, $g, $b);
        $bgcolor = ImageColorAllocate($image, 250 - $r, 254 - $g, 253 - $b);
        $silver = ImageColorAllocate($image, (255 - $r) * 2, (255 - $g) * 2, (255 - $b) * 2);
                       
        // 生成背景
        ImageFill($image, 0, 0, $bgcolor);                       
                       
        // 画出横向干扰线
        $line_space = ceil($this->height / ($this->_lines + 1));
        $line_move  = ceil($line_space * 2);
        for ($i = 1; $i <= $this->_lines; $i++) {
        	$y  = $line_space * $i;
            $y2 = $y + (($i - rand(0, 2 * $i)) % 2) * $line_move;
            ImageLine($image, 0, $y, $this->width, $y2, $silver);
        }
                       
       	// 画出干扰点
        $pixel_num = intval($this->height * $this->width / 20);
        for($i = 0; $i < $pixel_num; $i++) {
        	$x = mt_rand() % $this->width;
            $y = mt_rand() % $this->height;
            ImageSetPixel($image, $x, $y, $silver);
        }
                       
        // 画出字符
        $rand_str = "";
       	$step_len = intval($this->width / ($this->text_length + 1));
        $left_len = $step_len - 6;

        for ($i = 0; $i < $this->text_length; $i++) {
        	$x = $left_len + ($step_len * $i);
            $y = ($i % 2) * $this->_move + $this->_top;

            $rand = mt_rand(0, $radix_len - 1);
            $rand_str .= substr($radix, $rand, 1);
                               
            ImageString($image, $this->text_font, $x, $y, substr($radix, $rand, 1), $fgcolor);
        }
                       
        // 设定 SESSION 值
		$session=new Zend_Session_Namespace('adminAuth');
		$sess_key = $this->sess_name;
        $session->$sess_key=$rand_str;                      
                       
        // 输出图象
        header("Content-type: image/png");
        ImagePNG($image);
        ImageDestroy($image);
	}
}