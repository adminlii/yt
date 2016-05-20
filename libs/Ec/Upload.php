<?php
class Ec_Upload
{
	
	// 上传图片用到的连路径参数
	private $_basePath = '';
	private $_savePath = "";
	private $_sqlPath = "";
	
	// 上传时属性
	private $_picName = "";
	private $_picType = "";
	private $_picSize = "";
	private $_picPath = "";
	private $_picSuffix = "";
	
	//
	private $_file;
	private $_typeMapping = array(
			'image/gif',
			'image/jpeg',
			'image/png',
			'image/pjpeg',
			'image/x-png'
	);

	public static function getBasePath(){
	    return APPLICATION_PATH . "/../data/images/";
	}
	
	public function init($basePath='')
	{
        // 设置上传的路径
        if(empty($basePath)){
            $this->_basePath = APPLICATION_PATH . "/../data/images/";
        }else{
            //$this->_basePath = $basePath;
            $this->_basePath = APPLICATION_PATH . "/../data/images/";
        }
        // echo $this->_basePath;exit;
    }
	/**
	 * 获取图片的属性
	 */
	public function __construct(&$file,$basePath='')
	{
		$this->init($basePath);
		if ($file)
		{
			$this->setName($file['name']);
			$this->setType($file['type']);
			$this->setSize($file['size']);
			$this->setPath($file['tmp_name']);
			$this->getStuffix();
		}
	}
	
	/**
	 * 检查图片格式
	 * 
	 * @return boolean 图片格式符合要求
	 */
	public function checkType()
	{
		return in_array($this->_picType, $this->_typeMapping);
	}
	
	/**
	 * 按指定名称上传图片到指定位置
	 * 
	 * @param $file_name String
	 *       	 指定文件存储的名字
	 * @param $dir String
	 *       	 指定文件存储的相对路径（ID拆分的部分）
	 * @param
	 *       	 String item 存储项（_basePath之后id之前的部分）
	 * @return String sqlPath 返回用于存储在数据库的路径
	 */
	public function upload($file_name, $dir)
	{
		// 图片保存的路径
		$this->_savePath = $this->_sqlPath = $dir."/";
		// 判断文件夹是否存在，创建文件夹
		if (! file_exists($this->_basePath . $this->_savePath))
		{
			$this->mkdirs($this->_basePath . $this->_savePath);
		}
		// 复制图片到制定的闻知系
		if (move_uploaded_file($this->_picPath, $this->_basePath . $this->_savePath . $file_name . "." . $this->_picSuffix))
		{
			$this->_sqlPath .= $file_name. "." . $this->_picSuffix;
			
			return $this->_sqlPath;
		} else
		{			
			return false;
		}
	}
	
	/**
	 * 判断图片的 类型
	 * 
	 * @return picSuffix 图片类型
	 */
	public function getStuffix()
	{
        if (strpos(strtolower($this->_picName), 'jpg') !== false) {
            $this->_picSuffix = "jpg";
        } elseif (strpos(strtolower($this->_picName), 'jpeg') !== false) {
            $this->_picSuffix = "jpg";
        } elseif (strpos(strtolower($this->_picName), 'gif') !== false) {
            $this->_picSuffix = "gif";
        } elseif (strpos(strtolower($this->_picName), 'png') !== false) {
            $this->_picSuffix = "png";
        } else {
            return false;
        }
        return $this->_picSuffix;
    }
	/**
	 * 缩略图
	 * 
	 * @param $path 图片路径       	
	 * @param $name 图片名称       	
	 * @param $width 图片宽度       	
	 * @param $height 图片高度       	
	 * @param $bgcolor 图片背景颜色
	 *       	 @判断长和宽的长度，计算比例
	 */
	public function getThumb($path, $name, $width, $height, $bgcolor = "FFFFFF")
	{
		$ori_path = $this->_basePath . $path;
		$org_info = @getimagesize($ori_path);
		$img_org = $this->img_resource($ori_path, $org_info[2]);
		/*
		 * 原始图片以及缩略图的尺寸比例
		 */
		$scale_org = $org_info[0] / $org_info[1];
		$img_thumb = imagecreatetruecolor($width, $height);
		$red = $green = $blue = "";
		sscanf($bgcolor, "%2x%2x%2x", $red, $green, $blue);
		$clr = imagecolorallocate($img_thumb, $red, $green, $blue);
		imagefilledrectangle($img_thumb, 0, 0, $width, $height, $clr);
		if ($org_info[0] / $width > $org_info[1] / $height)
		{
			$lessen_width = $width;
			$lessen_height = $width / $scale_org;
		} else
		{
			/*
			 * 原始图片比较高，则以高度为准
			 */
			$lessen_width = $height * $scale_org;
			$lessen_height = $height;
		}
		$dst_x = ($width - $lessen_width) / 2;
		$dst_y = ($height - $lessen_height) / 2;
		@imagecopyresampled($img_thumb, $img_org, $dst_x, $dst_y, 0, 0, $lessen_width, $lessen_height, $org_info[0], $org_info[1]);
		$thumb_path = $this->_savePath . $name;
		$filename = "";
		if (function_exists('imagejpeg'))
		{
			$filename .= '.jpg';
			imagejpeg($img_thumb, $this->_basePath . $thumb_path . $filename, 100);
		} elseif (function_exists('imagegif'))
		{
			$filename .= '.gif';
			imagegif($img_thumb, $this->_basePath . $thumb_path . $filename);
		} elseif (function_exists('imagepng'))
		{
			$filename .= '.png';
			imagepng($img_thumb, $this->_basePath . $thumb_path . $filename);
		}
		imagedestroy($img_thumb);
		imagedestroy($img_org);
		return $thumb_path . $filename;
	}
	
	public function img_resource($img_file, $mime_type, &$thumb_width = 0, &$thumb_height = 0)
	{
		switch ($mime_type)
		{
			case 1 :
			case 'image/gif' :
				$res = imagecreatefromgif($img_file);
				break;
			
			case 2 :
			case 'image/pjpeg' :
			case 'image/jpeg' :
				$res = imagecreatefromjpeg($img_file);
				break;
			
			case 3 :
			case 'image/x-png' :
			case 'image/png' :
				$res = imagecreatefrompng($img_file);
				break;
			
			default :
				return false;
		}
		return $res;
	}
	
	/**
	 * 分隔数字路径
	 * 
	 * @param $id long
	 *       	 用于计算路径的ID
	 * @param $level int
	 *       	 分几级
	 * @param
	 *       	 boolean is_all 最后3位是否作为一级文件夹（是的话，则每个ID对应单独图片文件夹）
	 */
	public function split($id, $level, $is_all = false)
	{
		if (! $is_all)
			$id = ($id - $id % 1000) / 1000;
		$i = $level * 3;
		while ( $i > 0 )
		{
			$id = "0" . $id;
			$i --;
		}
		$id = substr($id, - $level * 3);
		$i = 0;
		$path = "";
		while ( $i < $level )
		{
			$path .= substr($id, $i * 3, 3) . "/";
			$i ++;
		}
		return $path;
	}
	
	/**
	 * 递归创建路径
	 * 
	 * @param
	 *       	 $path
	 */
	public  function mkdirs($path)
	{
		if (! file_exists($path))
		{
			$this->mkdirs(dirname($path));
			mkdir($path, 0777);
		}
	}
	
	/**
	 * 设置上传图片名称
	 */
	public function setName($name)
	{
		$this->_picName = $name;
	}
	
	/**
	 * 设置上传图片类型
	 */
	public function setType($type)
	{
		$this->_picType = $type;
	}
	
	/**
	 * 设置上传图片大小
	 */
	public function setSize($size)
	{
		$this->_picSize = $size;
	}
	
	/**
	 * 设置上传图片临时路径
	 */
	public function setPath($path)
	{
		$this->_picPath = $path;
	}
	
	public function getSqlPath()
	{
		return $this->_sqlPath;
	}

}