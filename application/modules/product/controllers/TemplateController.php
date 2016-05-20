<?php
class Product_TemplateController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "product/views/product/";
        $this->serviceClass = new Service_ProductTemplate();
    }

    public function listAction()
    {
        if ($this->_request->isPost()) {
            $page = $this->_request->getParam('page', 1);
            $pageSize = $this->_request->getParam('pageSize', 20);

            $page = $page ? $page : 1;
            $pageSize = $pageSize ? $pageSize : 20;

            $return = array(
                "state" => 0,
                "message" => "No Data"
            );

            $params = $this->_request->getParams();

            $condition = $this->serviceClass->getMatchFields($params);

            $count = $this->serviceClass->getByCondition($condition, 'count(*)');
            $return['total'] = $count;

            if ($count) {
                $showFields = array(

                    'pt_title',
                    'pt_create_date',
                    'pt_id',
                );
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getByCondition($condition, $showFields, $pageSize, $page, array('pt_id desc'));
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        echo Ec::renderTpl($this->tplDirectory . "product_template_index.tpl", 'layout');
    }

    public function editAction()
    {
        $return = array(
            'state' => 0,
            'message' => '',
            'errorMessage' => array('Fail.')
        );

        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            $fileName =isset( $_FILES['E02']['name'])? $_FILES['E02']['name']:'';
            $filePath =isset( $_FILES['E02']['tmp_name'])? $_FILES['E02']['tmp_name']:'';
            if(empty($fileName) ||  empty($filePath)){
                $this->view->message = '上传失败!';
                echo Ec::renderTpl($this->tplDirectory . "product_template_index.tpl", 'layout');
                exit();
            }
            $newFileName = $this->_userAuth->userId . date('ymdHis') . preg_replace("/[^\.]*(\.[a-zA-Z]+$)/", "\\1", $fileName);
            $targetFile = APPLICATION_PATH . "/../data/images/html/" . $newFileName;
            $content = '';
            if (move_uploaded_file($filePath, $targetFile)) {
                $content = file_get_contents($targetFile, 'UTF-8');
            }
            $row = array(
                'pt_id' => '',
                'pt_title' => '',
                'pt_content' => '',
            );
            $row = $this->serviceClass->getMatchEditFields($params, $row);
            $paramId = $row['pt_id'];
            if (!empty($row['pt_id'])) {
                unset($row['pt_id']);
            }
            $errorArr = $this->serviceClass->validator($row);
            //记录文件名
            $row['pt_file'] = $newFileName;
            if (!empty($errorArr)) {
                $return = array(
                    'state' => 0,
                    'message' => '',
                    'errorMessage' => $errorArr
                );
                die(Zend_Json::encode($return));
            }
            $row['pt_create_date'] = date('Y-m-d H:i:s');
            if (!empty($paramId)) {
                $result = $this->serviceClass->update($row, $paramId);
            } else {
                $result = $this->serviceClass->add($row);
            }
            if ($result) {
                preg_match_all('/<%([0-9A-Za-z]+)%>/', $content, $keys);
                if (isset($keys[1])) {
                    $keyArr = array();
                    $obj = new Service_ProductAttribute();
                    $date = date('Y-m-d H:i:s');
                    foreach ($keys[1] as $val) {
                        if (!in_array($val, $keyArr)) {
                            $keyArr[] = $val;
                            $add = array(
                                'pt_id' => !empty($paramId) ? $paramId : $result,
                                'pa_key' => $val,
                                'pa_create_date' => $date,
                            );
                            $obj->add($add);
                        }
                    }
                }

                $this->view->message = '上传成功!';
                echo Ec::renderTpl($this->tplDirectory . "product_template_index.tpl", 'layout');
                exit();
            } else {
                $this->view->message = '上传失败!';
                echo Ec::renderTpl($this->tplDirectory . "product_template_index.tpl", 'layout');
                exit();
            }

        }
        die(Zend_Json::encode($return));
    }

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'pt_id')) {
            $rows = $this->serviceClass->getVirtualFields($rows);
            $result = array('state' => 1, 'message' => '', 'data' => $rows);
        }
        die(Zend_Json::encode($result));
    }

    public function deleteAction()
    {
        $result = array(
            "state" => 0,
            "message" => "Fail."
        );
        if ($this->_request->isPost()) {
            $paramId = $this->_request->getPost('paramId');
            if (!empty($paramId)) {
                if ($this->serviceClass->delete($paramId)) {
                    Service_ProductAttribute::delete($paramId,'pt_id');
                    $result['state'] = 1;
                    $result['message'] = 'Success.';
                }
            }
        }
        die(Zend_Json::encode($result));
    }

    public function downTestAction()
    {
        $paramId = $this->_request->getParam('paramId', '');

        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'pt_id')) {
            $filename = $rows['pt_title'];

            header("Content-Disposition: attachment; filename=" . $filename . ".csv");
            header('Content-Type:APPLICATION/OCTET-STREAM');
            echo Service_ProductAttribute::csv($paramId);
        } else {
            echo '模板不存在!';
        }
    }

    public function downAction()
    {
        $paramId = $this->_request->getParam('paramId', '');

        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'pt_id')) {
            $filename = $rows['pt_title'];
            require_once("PHPExcel.php");
            require_once("PHPExcel/Reader/Excel2007.php");
            require_once("PHPExcel/Reader/Excel5.php");
            require_once ('PHPExcel/IOFactory.php');
            $objExcel = new PHPExcel();
            $objProps = $objExcel->getProperties();
            $objProps->setCreator('EC');
            $objProps->setTitle('Template');
            $objExcel->setActiveSheetIndex(0);

            $objActSheet = $objExcel->getActiveSheet();
            $objActSheet->setCellValue('A1', 'downFileName');
            $paRows = Service_ProductAttribute::getByCondition(array('pt_id' => $paramId), '*');

            $prefix =$stat= '';
            $num =$ord= 0;
            $limit=25;
            $stat = 97;//A1 已分配
            foreach ($paRows as $k => $val) {
                $num++;
                $ord++;
                if ($ord > $limit) {
                    $ord = 1;
                }
                $mod = ceil(($num) / $limit);
                if ($mod - 1) {
                    $prefix = chr(ord('a') + $mod - 2);
                }
                //   echo strtoupper($prefix.chr( $stat+ $ord)) . '1---'.$stat.'----'.$ord.'=='.$val['pa_key'].'<br>';

                $objActSheet->setCellValue(strtoupper($prefix . chr($stat + $ord)) . '1', $val['pa_key']);
                if (strtoupper(chr($stat + $ord)) == 'Z') {
                    $stat = 96;
                }
            }
/*            foreach ($paRows as $k => $val) {
                $objActSheet->setCellValue(strtoupper(chr(ord('a') + $k + 1)) . '1', $val['pa_key']);
            }
*/
            header('Pragma:public');
            header('Content-Type:application/x-msexecl;name="' . $filename . '.xls');
            header("Content-Disposition:inline;filename=" . $filename . '.xls');
            $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
            $objWriter->save('php://output');
        } else {
            echo '模板不存在!';
        }
    }

    public function downHtmlAction()
    {
        $fileName = $this->_request->getParam('fileName', '');
        $url = APPLICATION_PATH . "/../data/images/" . $fileName;
        if (file_exists($url) && $fileName) {
            Common_Common::downloadFile($url);
        } else {
            echo 'NO Files !';
        }
    }

    public function uploadAction()
    {
        $paramId = $this->_request->getParam('E0', '');
        $fileName = $_FILES['E2']['name'];
        $filePath = $_FILES['E2']['tmp_name'];
        $result = $this->serviceClass->readUploadFile($fileName, $filePath);
        //print_r($result);die;
        $fileNameArr = array();
        if (!isset($result[1]) || !is_array($result[1])) {
            $this->view->message = '请输入模板标签!';
            echo Ec::renderTpl($this->tplDirectory . "product_template_index.tpl", 'layout');
            exit();
        } else {
            $rows = $this->serviceClass->getByField($paramId, 'pt_id');
            $name = $this->_userAuth->userId . date('ymdHis');
            $uploadDir = APPLICATION_PATH . "/../data/images/" . $name . '/';
            $suffix = '.html';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777);
            }
            $htmlUploadDir = APPLICATION_PATH . "/../data/images/html/" . $rows['pt_file'];
            $htmlContent = '';
            if (file_exists($htmlUploadDir)) {
                $htmlContent = file_get_contents($htmlUploadDir, 'UTF-8');
            } else {
                $this->view->message = '服务器中的模板文件,不存在,请重新添加模板文件.';
                echo Ec::renderTpl($this->tplDirectory . "product_template_index.tpl", 'layout');
                exit();
            }

            require_once("PHPExcel.php");
            require_once("PHPExcel/Reader/Excel2007.php");
            require_once("PHPExcel/Reader/Excel5.php");
            require_once ('PHPExcel/IOFactory.php');
            $objExcel = new PHPExcel();
            $objProps = $objExcel->getProperties();
            $objProps->setCreator('EC');
            $objProps->setTitle('Template');
            $objExcel->setActiveSheetIndex(0);
            $objActSheet = $objExcel->getActiveSheet();
            $objActSheet->setCellValue('A1', 'FileName');
            $objActSheet->setCellValue('B1', 'HTML');
            $dFileName = 'x';
            $i=2;
            foreach ($result as $keys) {
                $cel=$i++;
                $content = $htmlContent;
                foreach ($keys as $k => $v) {
                    if ($k == 'downFileName') {
                        if (isset($fileNameArr[$v])) {
                            continue;
                        } else {
                            $fileNameArr[$v] = '';
                        }
                        $dFileName = $v;
                    }
                    $content = preg_replace("/<%" . $k . "%>/", "{$v}", $content);
                }

                $objActSheet->setCellValue('A'.$cel, $dFileName);


                $content= $this->serviceClass->strReplace($content);
                $content = preg_replace('/\s+=/i', '=', $content);
                $content = preg_replace('/=\s+/i', '=', $content);
                $content = preg_replace('/<img[^>]+src="">/i', '', $content);
                $content = preg_replace('/<img[^>]+src=""([^>]+)?>/i', '', $content);
                $content = preg_replace('/<img[^>]+src="\s+"([^>]+)?>/i', '', $content);
                $content = preg_replace('/<img[^>]+src=\'\'([^>]+)?>/i', '', $content);   
                $content = preg_replace('/<img[^>]+src=\'\s+\'([^>]+)?>/i', '', $content);
                $objActSheet->setCellValue('B'.$cel, $content);
                $fileUrl = $uploadDir . $dFileName . $suffix;
                file_put_contents($fileUrl, $content);
            }

            $objWriter = PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
            $objWriter->save($uploadDir . 'html.xls');
            if (!empty($fileNameArr)) {
                // $this->view->fileList = $fileNameArr;
                $zipObj = Common_Common::zip("../" . $name . ".zip");
                $zipObj->add_files($uploadDir . "*");
                $zipObj->create_archive();
                $zipObj->download_file();
                Common_Common::clearFile($name);
            } else {
                $this->view->message = '操作错误,无法生成文件!';
            }
            echo Ec::renderTpl($this->tplDirectory . "product_template_index.tpl", 'layout');
            exit();
        }
    }

    public function exportAction(){

        $sql='select * from user_department';
        $result= Common_Export::getDateBySql($sql);
        if($result['state']=='1'){
            $filename='order';
            header("Content-Disposition: attachment; filename=" . $filename . ".csv");
            header('Content-Type:APPLICATION/OCTET-STREAM');
            echo Common_Export::exportCsv($result['data']);
            exit;
        }else{
            print_r($result);
        }
    }

    public function testAction(){
         $eeer=new Ebay_OrderEbay();
        $user_account="ezwmsjason";//test账号，修改时，需要修改company.ini的开发者账号
        //$user_account="www.temtop_com";// 生产账号
        $nowtime = date("Y-m-d H:i:s");
        $start	 = date('Y-m-d',strtotime("$nowtime -300 hours")) .'T'. date('H:i:s',strtotime("$nowtime -10 hours"));
        $end	 = date('Y-m-d',strtotime("$nowtime +1 days"))."T23:59:59";

        $data=array(
            'load_start_time'=>$start,
            'load_end_time'=>$end,
            'user_account'=>$user_account,
        );
        $data1[]=$data;
        $id=$eeer->configLoad($data1);
       // print_r($id["success"]);die;
        foreach($id["success"] as $val){
            $eeer->runOrder($val);
        }
        //$eeer->runOrder(9);
    }
}