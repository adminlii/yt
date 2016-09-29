<?php
class Common_FileToZip{
	public $savepath	=	''; //zip保存路径
	public $zipName	 =	'';
	public $removeAfterDown	= '';
	public $err_num	 =	0;
	public $errflag	 =	false;
	public function __construct($savepath,$zipName='',$removeAfterDown=true){
		if(is_dir($savepath))
			$this->savepath	=	$savepath;
		else{
			$this->err_num	=	1;
			$this->errflag	=	true;
		}
		if(!empty($zipName)){
			$this->zipName = $zipName;
		}
		$this->removeAfterDown	=	$removeAfterDown;
	}
	//打开目录取一级文件
	public function  getFileInDir($dirname){
		if(empty($dirname))
			return array();
		if(!is_dir($dirname)){
			$this->err_num	=	2;
			$this->errflag	=	true;
			return array();
		}
		$handler = opendir($dirname); //$cur_file 文件所在目录
		$filelist = array();
		$i = 0;
		while( ($filename = readdir($handler)) !== false ) {
			if($filename != '.' && $filename != '..') {
				$filelist[] = $filename;
			}
		}
		closedir($handler);
		return $filelist;
	}
	/**
	 *
	 * @param unknown $filelist 可以数组和单文件
	 * @param string $skip      如果是true单文件忽略不进行压缩
	 * @param string $return	true返回文件名false直接下载
	 * @notic 这里的所有文件都应该用绝对路径 （realpath）
	 * @return unknown|boolean
	 */
	public function toZip($filelist,$skip=true,$return=false){
		if($this->errflag){
			echo "<script>alert('".$this->getErrorMsg()."')</script>";
			return false;
		}
		if(is_array($filelist)){
			//如果是数组但是长度也是1则就和单文件一样处理
			if(count($filelist)==1){
				if($skip){
					if($return)
						return $filelist;
					else{
						if(!$return){
							$dw=new download($this->zipName,basename($filelist[0]),dirname($filelist[0]).DIRECTORY_SEPARATOR); //下载文件
							$dw->getfiles();
						}else
							return $filelist;
					}
				}else{
	
					$zip_name	=	$this->toZipByArray($filelist);
					if($return){
						return $zip_name;
					}else{
						$dw=new download($this->zipName,basename($zip_name),dirname($zip_name).DIRECTORY_SEPARATOR); //下载文件
						$dw->getfiles(1);
					}
				}
			}else{
				$zip_name	=	$this->toZipByArray($filelist);
				if($return){
					return $zip_name;
				}else{
					$dw=new download($this->zipName,basename($zip_name),dirname($zip_name).DIRECTORY_SEPARATOR); //下载文件
					$dw->getfiles(1);
				}
			}
		}else if(is_file($filelist)){
			if($return)
				return $filelist;
			else{
				$dw=new download($this->zipName,basename($filelist),dirname($filelist).DIRECTORY_SEPARATOR); //下载文件
				$dw->getfiles();
			}
		}else{
			$this->err_num	=	3;
			$this->errflag	=	true;
			return false;
		}
	}
	
	private function toZipByArray($filelist){
		$zip=new \ZipArchive();
		$m=0;
		do{
			if(empty($this->zipName))
				$zipFileName	=	rtrim($this->savepath,'/').'/'.date('YmdHis').mt_rand(1,999).'.zip';
			else 
				$zipFileName	=	rtrim($this->savepath,'/').'/'.date('mdHis').mt_rand(1,999).'-'.$this->zipName;
			if(!file_exists($zipFileName)){
				break;
			}
			$m++;
		}while($m<5);
		if ($zip->open($zipFileName,$zip::OVERWRITE)!== TRUE){
			$this->err_num	=	4;
			$this->errflag	=	true;
			return false;
		}
		foreach ($filelist as $v){
			$zip->addFile($v,basename($v));
		}
		$zip->close();
		return $zipFileName;
	}
	
	
	public function getErrorMsg(){
		$msg	=	'';
		switch ($this->err_num){
			case 1:$msg='1:初始化保存目录不存在';break;
			case 2:$msg='2:调用函数getfileInDir参数不合法';break;
			case 3:$msg='3:调用函数toZip参数不合法非数组非文件名';break;
			case 3:$msg='4:初始化类失败或者打开zip文件失败';break;
		}
	}
}

class download{
	protected $_filename;
	protected $_downfilename;
	protected $_filepath;
	protected $_filesize;//文件大小
	protected $savepath;//文件大小
	public function __construct($downfilename,$filename,$savepath){
		if(!empty($downfilename))
			$this->_downfilename=$downfilename;
		else 
			$this->_downfilename=$filename;
		$this->_filename=$filename;
		$this->_filepath=$savepath.$filename;
	}
	//获取文件名
	public function getfilename(){
		return $this->_filename;
	}
	//获取文件路径（包含文件名）
	public function getfilepath(){
		return $this->_filepath;
	}
	//获取文件大小
	public function getfilesize(){
		return $this->_filesize=number_format(filesize($this->_filepath)/(1024*1024),2);//去小数点后两位
	}
	//下载文件的功能
	public function getfiles($delete=0){
		//检查文件是否存在
		if (file_exists($this->_filepath)){
			//打开文件
			$file = fopen($this->_filepath,"r");
			//返回的文件类型
			Header("Content-type: application/octet-stream");
			//按照字节大小返回
			Header("Accept-Ranges: bytes");
			//返回文件的大小
			Header("Accept-Length: ".filesize($this->_filepath));
			//这里对客户端的弹出对话框，对应的文件名
			Header("Content-Disposition: attachment; filename=".$this->_downfilename);
			//修改之前，一次性将数据传输给客户端
			echo fread($file, filesize($this->_filepath));
			//修改之后，一次只传输1024个字节的数据给客户端
			//向客户端回送数据
			$buffer=1024;//
			$file_count=0;
			//判断文件是否读完
			while (!feof($file)&&$file_count<filesize($this->_filepath)) {
				//将文件读入内存
				$file_count+=$buffer;
				$file_data=fread($file,$buffer);
				//每次向客户端回送1024个字节的数据
				echo $file_data;
			}
			fclose($file);
			if($delete){
				@unlink($this->_filepath);
			}
		}else {
			echo "<script>alert('对不起,您要下载的文件不存在');</script>";
		}
	}
}
