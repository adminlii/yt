<?php
class Common_SearchfilterController extends Ec_Controller_Action
{
    public function preDispatch()
    {
        $this->tplDirectory = "common/views/searchfilter/";
        $this->serviceClass = new Service_SearchFilter();
    }

    public function listAction()
    {
        $parent=$this->serviceClass->getLeftJoinByCondition(array('parent_id'=>'0'),array('parent_id','sf_id','search_label','user_right.ur_name as urName'));
        $parentArr=array();
        if(!empty($parent)){
            foreach($parent as $key =>$val){
                $parentArr[$val['sf_id']]=$val;
            }
        }

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
                $showFields=array(
                    
                'warehouse_id',
                'parent_id',
                'search_label',
                'search_value',
                'search_sort',
                'filter_action_id',
                'search_tips',
                'search_input_id',
                'sf_desc',
                'sf_id',
                );
                $showFields = $this->serviceClass->getFieldsAlias($showFields);
                $rows = $this->serviceClass->getLeftJoinByCondition($condition,$showFields, $pageSize, $page, array('parent_id','search_sort','warehouse_id'));
                $return['data'] = $rows;
                $return['state'] = 1;
                $return['parentArr'] = $parentArr;
                $return['message'] = "";
            }
            die(Zend_Json::encode($return));
        }
        $this->view->parentArr=$parentArr;
        echo Ec::renderTpl($this->tplDirectory . "searchfilter_index.tpl", 'layout');
    }

    public function editAction()
    {
        $return = array(
            'state' => 0,
            'message' => '',
            'errorMessage'=>array('Fail.')
        );

        if ($this->_request->isPost()) {
            $params = $this->_request->getParams();
            $row = array(
                
              'sf_id'=>'',
              'warehouse_id'=>'',
              'parent_id'=>'',
              'search_label'=>'',
              'search_value'=>'',
              'search_sort'=>'',
              'filter_action_id'=>'',
              'search_tips'=>'',
              'search_input_id'=>'',
              'sf_desc'=>'',
            );
            $row=$this->serviceClass->getMatchEditFields($params,$row);
            $paramId = $row['sf_id'];
            if (!empty($row['sf_id'])) {
                unset($row['sf_id']);
            }
            $errorArr = $this->serviceClass->validator($row);

            if (!empty($errorArr)) {
                $return = array(
                    'state' => 0,
                    'message'=>'',
                    'errorMessage' => $errorArr
                );
                die(Zend_Json::encode($return));
            }

            if (!empty($paramId)) {
                $result = $this->serviceClass->update($row, $paramId);
            } else {
                if($row['parent_id']!='0'){
                   $searchRow= $this->serviceClass->getByField($row['parent_id']);
                    $row['filter_action_id']=$searchRow['filter_action_id'];
                    $row['search_input_id']=$searchRow['search_input_id'];
                    $row['warehouse_id']=$searchRow['warehouse_id'];
                }

                $result = $this->serviceClass->add($row);
            }
            if ($result) {
                $return['state'] = 2;
                $return['message'] = array('Success.');
            }

        }
        die(Zend_Json::encode($return));
    }

    public function getByJsonAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $paramId = $this->_request->getParam('paramId', '');
        if (!empty($paramId) && $rows = $this->serviceClass->getByField($paramId, 'sf_id')) {
            $rows=$this->serviceClass->getVirtualFields($rows);
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
                    $result['state'] = 2;
                    $result['message'] = 'Success.';
                }
            }
        }
        die(Zend_Json::encode($result));
    }

    public function bindSearchFilterAction()
    {
        $result = array('state' => 0, 'message' => 'Fail', 'data' => array());
        $parentId = $this->_request->getParam('parentId','');
        $actionId = $this->_request->getParam('$actionId','');
        if ($parentId!='') {
            $condition = array(
                "parent_id" => $parentId,
                "filter_action_id"=>$actionId,
            );
            $rows = Service_SearchFilter::getByCondition($condition, '*', 50, 1, array('parent_id asc', 'search_sort asc'));
            if (!empty($rows)) {
                $result = array('state' => 1, 'message' => '', 'data' => $rows);
            }
        }
        die(Zend_Json::encode($result));
    }
}