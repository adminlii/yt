<?php

class Auth_IndexController extends Ec_Controller_Action
{
    public function listAction()
    {
        $this->_userAuth->isAcl = 1;
        $this->_redirect('/');
    }
}