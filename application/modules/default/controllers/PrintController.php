<?php

class Default_PrintController extends Ec_Controller_DefaultAction
{

    /**
     * @desc 打印货架条码
     */
    public function printShelfAction()
    {
        $orderlist = isset($_POST['orderlist']) ? $_POST['orderlist'] : '';
        if (!$orderlist) {
            echo '<html><head><meta http-equiv="content-type" content="text/html;charset=utf-8"></head><body>';
            echo '注：将单号复制到输入框中，每个单号以换行区分；<br>格式：A4 * 5<form action="" method="post">
                <textarea rows="36" cols="70" name="orderlist"></textarea>
                <input type="submit" name="submit" value="submit">
            </form>';
            echo '</body></html>';
        } else {
            $orderlist = $_POST['orderlist'];
            $orderlist = preg_split('/\r\n/', $orderlist, -1, PREG_SPLIT_NO_EMPTY);
            // print_r($orderlist);die;
            $result = array_chunk($orderlist, 2);
            $this->view->results = $result;
            echo $this->view->render($this->tplDirectory . 'print_thermal_9X8.5x2.tpl');
        }
    }

    /**
     * @desc 用于打印货架标签
     */
    public function shelfAction()
    {
        $data = $this->_request->getParam('dataList');
        if (!$data) {
            echo '<html><head><meta http-equiv="content-type" content="text/html;charset=utf-8"></head><body>';
            echo '注：将货位号复制到输入框中，每个货位号以换行区分；<br>格式：A4 * 5<br>';
            echo '<form action="" method="post">
                <textarea rows="36" cols="50" name="dataList"></textarea>
                <input type="submit" name="submit" value="submit">
            </form>';
            echo '</body></html>';
        } else {
            $data = $this->_request->getParam('dataList');
            $data = preg_split('/\r\n/', $data, -1, PREG_SPLIT_NO_EMPTY);
            $result = array_chunk($data, 5);
            $this->view->results = $result;
            echo $this->view->render("default/views/default/print_shelf.tpl");
        }
    }

    public function barcodeAction()
    {
        Common_Barcode::barcode($this->_request->code);
        exit;
    }

    /*
     * @desc 打印异常处理通知单
     * @tip 安全起见进行URL加密验证
     */
    public function printNoticeDocumentsAction()
    {
        $codes = $this->_request->getParam('code', array());
        $key = trim($this->_request->getParam('key', ''));
        $userId = trim($this->_request->getParam('userId', '0'));
        $data = array();
        if (!empty($codes) && is_array($codes) && !empty($key)) {
            $code = $codes[0];
            $rKey = Common_Common::authcode($key, 'DECODE', 'AB', 60);
            if ($code != $rKey) {
                exit('Unable to get parameters.');
            }
            $obj = new Process_ReceivedAbnormal();
            foreach ($codes as $key => $code) {
                $result = $obj->getReceivingAbnormal($code);
                if ($result['state']) {
                    $data[] = $result['data'];
                }
            }

            $userRow = Service_User::getByField($userId, 'user_id', array('user_name', 'user_name_en'));
            $this->view->data = $data;
            $this->view->printer = isset($userRow['user_name']) ? $userRow['user_name'] : '--';
            echo Ec::renderTpl("receiving/views/abnormal/print_abnormal.tpl", 'layout');
        } else {
            exit('Unable to get parameters');
        }
    }

    /**
     * @desc 打印头程配货单
     * @return  Json array
     */
    public function printPickingDocumentsAction()
    {
        $errorArr = $result = array();
        $codes = $this->_request->getParam('code', array());
        $key = trim($this->_request->getParam('key', ''));
        $userId = trim($this->_request->getParam('userId', '0'));
        if (!empty($codes) && is_array($codes) && !empty($key)) {
            $code = $codes[0];
            $rKey = Common_Common::authcode($key, 'DECODE', 'TP', 60);
            if ($code != $rKey) {
                exit('Unable to get parameters.');
            }
            foreach ($codes as $pickCode) {
                $return = Service_TransferOrderProcess::downGoodsShelves($pickCode);
                if ($return['state'] == '1') {
                    $result[] = $return['data'];
                } else {
                    $errorArr = $return['message'];
                }
            }
        } else {
            exit('Unable to get parameters');
        }
        $this->view->error = $errorArr;
        $this->view->result = $result;
        echo Ec::renderTpl('transfer/views/print_down_goods_shelves.tpl', 'layout');
    }

    /**
     * @desc 打印订单下架单
     * @return  Json array
     */
    public function printPickingAction()
    {
        $errorArr = $result = array();
        $codes = $this->_request->getParam('code', array());
        $key = trim($this->_request->getParam('key', ''));
        $userId = trim($this->_request->getParam('userId', '0'));
        $pickType = $this->_request->getParam('pickType', '');
        $tpl = '';
        if (empty($pickType)) {
            die('pickType Err');
        }
        if (!empty($codes) && is_array($codes) && !empty($key)) {
            $code = $codes[0];
            $rKey = Common_Common::authcode($key, 'DECODE', 'PL', 60);
            if ($code != $rKey) {
                exit('Unable to get parameters.');
            }
            if ($pickType == 'more') {
                $tpl = "print_picking.tpl";
                foreach ($codes as $pickCode) {
                    $return = Service_PickProcess::getPacking($pickCode);
                    if ($return['state'] == '1') {
                        $result[] = $return['data'];
                    } else {
                        $errorArr = $return['message'];
                    }
                }
            } else {
                $tpl = "print_down_goods_shelves.tpl";
                foreach ($codes as $pickCode) {
                    $return = Service_PickProcess::downGoodsShelves($pickCode);
                    if ($return['state'] == '1') {
                        $result[] = $return['data'];
                    } else {
                        $errorArr = $return['message'];
                    }
                }
            }
        } else {
            exit('Unable to get parameters');
        }
        $this->view->error = $errorArr;
        $this->view->result = $result;
        echo Ec::renderTpl('shipment/views/pickup/' . $tpl, 'layout');

    }

    /**
     * @desc 打印入库单
     */
    public function printReceivingListAction()
    {
        $code = trim($this->_request->getParam('code', ''));
        $key = trim($this->_request->getParam('key', ''));
        $userId = trim($this->_request->getParam('userId', '0'));
        if (!empty($code) && !empty($key)) {
            $rKey = Common_Common::authcode($key, 'DECODE', 'ASN', 60);
            if ($code != $rKey) {
                exit('Unable to get parameters.');
            }
            $result = Service_ReceivingProcess::getAsnDetail($code);
            if (isset($result['state']) && $result['state'] == '1') {
                $userRow = Service_User::getByField($userId, 'user_id', array('user_name'));
                $result['data']['other']['userName'] = isset($userRow['user_name']) ? $userRow['user_name'] : '--';
                $this->view->data = $result['data'];
                $this->view->firstPartySystem = Common_Config::firstPartySystem(); //判断是否为第一方
            }
        } else {
            exit('Unable to get parameters');
        }
        echo Ec::renderTpl("receiving/views/default/print_list.tpl", 'layout');
    }

    /**
     * @desc 物流标签
     */
    public function printOrderShipAction()
    {
        $code = trim($this->_request->getParam('code', ''));
        $result = Service_PackProcess::getOrderInfo($code);
        if ($result['state'] != '1') {
            die($result['message']);
        }

        $this->view->data = $result['data'];
        //$smCode = $result['data']['order']['sm_code'];
        $tpl = isset($result['data']['sm']['template']['st_path']) ? $result['data']['sm']['template']['st_path'] : 'shipment/views/template/Error.tpl';
        //echo Ec::renderTpl($tpl, 'layout');
        //$tpl= 'shipment/views/template/3CR.tpl';
        echo $this->view->render($tpl);
    }


}