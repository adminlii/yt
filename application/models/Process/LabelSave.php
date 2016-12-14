<?php

/**
 * @desc 自动通知处理类
 */
class Process_LabelSave
{
   protected $savePathDir = ""; //在public目录下不允许随便修改
   protected $childrenDirModel;
   protected $savePath = "";
   protected $extension = "";
   /**
    * 
    * @param string $dirModel
    * @param string $savePath
    * @help :: 开启子目录后，在savePath下会建立 时间格式的子目录
    */
   public function __construct($savePath="",$dirModel=false){
   		//php中配置变量名不能拼接常量
   		$this->savePathDir = APPLICATION_PATH . '/../public/';
   		if($dirModel){
   			$savePath = rtrim($savePath,'\\/').DIRECTORY_SEPARATOR.$this->getChildrenDir();
   		}
   		Common_Common::mkdirs($this->savePathDir.$savePath);
   		$this->savePath = $savePath;
   }
   public function getSavePath(){
   		return $this->savePathDir;
   }
   //子目录命名格式
   private function getChildrenDir(){
   		//Y-m-d
   		return date('Ymd').DIRECTORY_SEPARATOR;
   }
   
   //文件保存
   public function save($file_name,$data=false,$fileType=false){
   		$return = array (
			'ask' => 0,
			'message' => 'Fail'
		);
   		try {
   			if(!$fileType){
   				$this->extension = $this->getFileExtension($file_name);
   			}else
   				$this->extension = $fileType;
   			$filePath = rtrim($this->savePath,'\\/').DIRECTORY_SEPARATOR.$file_name;
   			$fileSavePath = $this->savePathDir.$filePath;
   			//data  false 则只是返回文件路径，不创建
   			if($data!=false){
   				if(!file_put_contents($fileSavePath, $data)){
   					throw new Exception("文件保存到本地失败");
   				}
   			}
   			$return ['ask'] = 1;
			$return ['message'] = 'Success';
			$return ['data'] = array(
					"filePath"=>$filePath,
					"fileSavePath"=>$fileSavePath,
					"extension"=>$this->extension
			);
   		} catch (Exception $e) {
   			$return ['ask'] = 0;
   			$return ['message'] = $e->getMessage ();
   		}
   		return $return;
   }
   
   //获取文件名后缀
   private  function getFileExtension($file_name){
   		return pathinfo($file_name, PATHINFO_EXTENSION);
   }

   
   //添加额外处理保存文件路径的代码
   public function saveFileLog($key,$filepath,$type=""){
   	$return = array (
   			'ask' => 0,
   			'message' => 'Fail'
   	);
   	try {
   		if(empty($key)||empty($filepath)){
   			throw new Exception("参数不全");
   		}
   		$saveRow = array(
   			'find_key'=>$key,
   			'savePath'=>$filepath, 	
   			'extension'=>$type,
   			'createtime'=>date('Y-m-d H:i:s'),
   			'status'=>1,
   		);
   		$rs = Service_SaveLabellog::add($saveRow);
   		if(!rs){
   			//错误日志
   			Ec::showError(print_r($key,true)."\n====================\n".print_r($filepath,true),'ZT_saveLabellog_'.date('Y-m-d'));
   			throw new Exception("插入失败");
   		}
   		$return ['ask'] = 1;
   		$return ['message'] = 'Success';
   		
   	} catch (Exception $e) {
   		$return ['ask'] = 0;
   		$return ['message'] = $e->getMessage ();
   	}
   	return $return;
   }
}