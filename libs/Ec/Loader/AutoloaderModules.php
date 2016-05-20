<?php
/**
 * Zend Framework
 * LICENSE
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Module
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Autoloader.php 24593 2012-01-05 20:35:02Z matthew $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** @see Zend_Loader_Autoloader_Resource */
require_once 'Zend/Loader/Autoloader/Resource.php';

/**
 * Resource loader for application module classes
 * @uses       Zend_Loader_Autoloader_Resource
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Module
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ec_Loader_AutoloaderModules extends Zend_Loader_Autoloader_Resource
{
    /**
     * Constructor
     * @param  array|Zend_Config $options
     * @return void
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->initDefaultResourceTypes();
    }

    /**
     * Initialize default resource types for module resource classes
     * @return void
     */
    public function initDefaultResourceTypes()
    {
        $basePath = $this->getBasePath();
        $this->addResourceTypes(
            array(
                'Validate' => array(
                    'namespace' => 'Validate',
                    'path' => 'validate',
                ),
                'Service' => array(
                    'namespace' => 'Service',
                    'path' => 'services',
                ),
                /*
                'View' => array(
                    'namespace' => 'View',
                    'path' => 'views',
                ),
                */
            ));
        $this->setDefaultResourceType('model');
    }
}
