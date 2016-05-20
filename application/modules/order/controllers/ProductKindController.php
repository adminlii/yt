<?php
class Order_ProductKindController extends Ec_Controller_Action
{

    public function preDispatch()
    {
        $this->tplDirectory = "order/views/product_kind/";
    }

    public function listAction()
    {
        $type = $this->getParam('type', '');
        $this->view->productKind = Process_ProductRule::getProductKind();
        if($type == 'dialog'){
            echo $this->view->render($this->tplDirectory . "product_kind_list.tpl");
            exit();
        }else{
            echo Ec::renderTpl($this->tplDirectory . "product_kind_list.tpl", 'layout');
        }
    }
}