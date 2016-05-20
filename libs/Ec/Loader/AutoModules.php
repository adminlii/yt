<?php
class Ec_Loader_AutoModules extends Zend_Loader_Autoloader_Resource
{
    public function __construct()
    {
        $options = array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH . '/models',
        );
        parent::__construct($options);

        $this->initMyResourceTypes(array('Table', 'DbTable', 'Service', 'Common', 'Ebay','Paypal','Amazon','Aliexpress','Process','Platform','API','Mabang'));
    }

}
