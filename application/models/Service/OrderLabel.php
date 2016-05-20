<?php
class Service_OrderLabel extends Common_Service
{
    /**
     * @var null
     */
    public static $_modelClass = null;

    /**
     * @return Table_OrderLabel|null
     */
    public static function getModelInstance()
    {
        if (is_null(self::$_modelClass)) {
            self::$_modelClass = new Table_OrderLabel();
        }
        return self::$_modelClass;
    }

    /**
     * @param $row
     * @return mixed
     */
    public static function add($row)
    {
        $model = self::getModelInstance();
        return $model->add($row);
    }


    /**
     * @param $row
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function update($row, $value, $field = "ol_id")
    {
        $model = self::getModelInstance();
        return $model->update($row, $value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @return mixed
     */
    public static function delete($value, $field = "ol_id")
    {
        $model = self::getModelInstance();
        return $model->delete($value, $field);
    }

    /**
     * @param $value
     * @param string $field
     * @param string $colums
     * @return mixed
     */
    public static function getByField($value, $field = 'ol_id', $colums = "*")
    {
        $model = self::getModelInstance();
        return $model->getByField($value, $field, $colums);
    }

    /**
     * @return mixed
     */
    public static function getAll()
    {
        $model = self::getModelInstance();
        return $model->getAll();
    }

    /**
     * @param array $condition
     * @param string $type
     * @param int $pageSize
     * @param int $page
     * @param string $order
     * @return mixed
     */
    public static function getByCondition($condition = array(), $type = '*', $pageSize = 0, $page = 1, $order = "")
    {
        $model = self::getModelInstance();
        return $model->getByCondition($condition, $type, $pageSize, $page, $order);
    }

    /**
     * @param $val
     * @return array
     */
    public static function validator($val)
    {
        $validateArr = $error = array();
        
        return  Common_Validator::formValidator($validateArr);
    }


    /**
     * @param array $params
     * @return array
     */
    public  function getFields()
    {
        $row = array(
        
              'E0'=>'ol_id',
              'E1'=>'order_code',
              'E2'=>'org_path',
              'E3'=>'path',
              'E4'=>'sm_code',
              'E5'=>'ol_file_type',
              'E6'=>'ol_status',
              'E7'=>'ol_run_qty',
              'E8'=>'ol_label_url',
              'E9'=>'ol_note',
              'E10'=>'ol_create_date',
              'E11'=>'ol_update_time',
        );
        return $row;
    }

    /**
     * @desc 自动将pdf转png
     */
    public static function autoPdf2pngEc()
    {
    	$runQty = 10;
    	$date = date('Y-m-d H:i:s');
    	$rows = Service_OrderLabel::getByCondition(array('ol_status_arr' => array(0, 2), 'ol_run_qty_lt' => $runQty), '*', 0, 0, array("ol_run_qty desc"));
    	if (!empty($rows)) {
    		foreach ($rows as $row) {
    			$arr = array(
    					'ol_file_type' => 'png',
    					'ol_update_time' => $date,
    					'ol_run_qty' => $row['ol_run_qty'] + 1,
    			);
    			try {
    				//绝对路径
    				$_abs_pdf_path = APPLICATION_PATH . '/..' . $row['org_path'];
    				//文件路径中剔除pdf的后缀
    				$_abs_png_fold_path = dirname($_abs_pdf_path) . '/img/' . preg_replace('/(.pdf)$/i', '', basename($_abs_pdf_path));
    				$path = dirname($row['org_path']) . '/img/' . preg_replace('/(.pdf)$/i', '', basename($row['org_path']));
    				$pdf2png = new Ec_Pdf2Png ();
    				$pdf2png->setPdfPath($_abs_pdf_path);
    				$pdf2png->setPngFoldPath($_abs_png_fold_path);
    				$pdf2png->pdf2png();
    				$arr['ol_status'] = 1;
    				$arr['path'] = $path;
    				$message = 'success';
    			} catch (Exception $e) {
    				$arr['ol_status'] = 2;
    				$message = $e->getMessage();
    				$arr['ol_note'] = $message;
    				//发送邮件通知
    				if ($row['ol_run_qty'] + 1 >= $runQty) {
    					Common_Email::sendErrorMessage("pdf2pnf_err", print_r($row, true) . ' ' . $message);
    				}
    			}
    			//更新状态
    			self::update($arr, $row['ol_id'], 'ol_id');
    			echo $message . "\r\n";
    		}
    	} else {
    		echo 'not data';
    	}
    
    }
    
    
    /**
     * @desc 自动将pdf转png (使用外部服务器)
     */
    public static function autoPdf2png()
    {
    	$configRow = Service_Config::getByField('SVC-FOR-PDF2PNG-URL', 'config_attribute');
    	if (empty($configRow)) {
    		Common_Email::sendErrorMessage("pdf2png未配置", "config表增加SVC-FOR-PDF2PNG-URL");
    		echo 'pdf2png-URL未配置';
    		return;
    	}
    	$url = $configRow['config_value'];
    	//可配置多个URL
    	$urlArr = array_filter(explode(';', $url));
    
    	$runQty = 10;
    	$date = date('Y-m-d H:i:s');
    	$rows = Service_OrderLabel::getByCondition(array('ol_status_arr' => array(0, 2), 'ol_run_qty_lt' => $runQty), '*', 0, 0, array("ol_run_qty desc"));
    	if (!empty($rows)) {
    		$tmpUrlArr = array();
    		foreach ($rows as $row) {
    			//平均分配URL
    			if (!isset($tmpUrlArr) || count($tmpUrlArr) == 0) {
    				$tmpUrlArr = $urlArr;
    			}
    			$url = array_pop($tmpUrlArr);
    
    			$arr = array(
    					'ol_file_type' => 'png',
    					'ol_update_time' => $date,
    					'ol_run_qty' => $row['ol_run_qty'] + 1,
    			);
    			try {
    				//绝对路径问题
    				if (file_exists($row['org_path'])) {
    					$_abs_pdf_path = $row['org_path'];
    					$row['org_path'] = str_replace(APPLICATION_PATH . '/..', '', $row['org_path']);
    				} else {
    					$_abs_pdf_path = APPLICATION_PATH . '/..' . $row['org_path'];
    				}
    
    				//文件路径中剔除pdf的后缀
    				$_abs_png_fold_path = dirname($_abs_pdf_path) . '/img/' . preg_replace('/(.pdf)$/i', '', basename($_abs_pdf_path));
    				$path = dirname($row['org_path']) . '/img/' . preg_replace('/(.pdf)$/i', '', basename($row['org_path']));
    				//创建文件夹
    				Common_Common::mkdirs($_abs_png_fold_path);
    
    				//判断文件是否存在
    				$downLabel = false;
    				if (file_exists($_abs_pdf_path)) {
    					if (abs(filesize($_abs_pdf_path)) < 100) {
    						$downLabel = true;
    					}
    				} else {
    					$downLabel = true;
    				}
    
    				//保存标签
    				if ($downLabel && !empty($row['ol_label_url'])) {
    					$_abs_pdf_path = APPLICATION_PATH . '/../data/pdf/' . $row["order_code"] . ".pdf";
    					if (!file_exists($_abs_pdf_path)) {
    						fopen($_abs_pdf_path, "w+");
    					}
    					$fileContents = file_get_contents($row['ol_label_url']);
    					file_put_contents($_abs_pdf_path, $fileContents);
    				}
    				$base64_content = base64_encode(file_get_contents($_abs_pdf_path));
    				if (empty($base64_content)) {
    					throw new Exception("文件不存在");
    				}
    				//echo $_abs_png_fold_path."\r\n";
    				$rs = Common_Common::curlRequest($url, $base64_content);
    				if (strtoupper($rs['Ack']) == 'SUCCESS') {
    					$png_arr = $rs ['png_base64_arr'];
    					foreach ($png_arr as $key => $png) {
    						file_put_contents($_abs_png_fold_path . "/" . $key . ".png", base64_decode($png['base64']));
    						//echo "<p>{$png['name']}</p><p><img src='data:image/gif;base64,{$png['base64']}' width='500'/></p>";
    					}
    				} else {
    					throw new Exception($rs['message']);
    				}
    				$arr['ol_status'] = 1;
    				$arr['path'] = $path;
    				$message = 'success';
    			} catch (Exception $e) {
    				$arr['ol_status'] = 2;
    				$message = $e->getMessage();
    				$arr['ol_note'] = $message;
    				//发送邮件通知
    				if ($row['ol_run_qty'] + 1 >= $runQty) {
    					Common_Email::sendErrorMessage("pdf2pnf_err", print_r($row, true) . ' ' . $message);
    				}
    			}
    			//更新状态
    			self::update($arr, $row['ol_id'], 'ol_id');
    			echo $message . "\r\n";
    		}
    	} else {
    		echo 'not data';
    	}
    
    }
}