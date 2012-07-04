<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	/**
	 * Initialize the application autoload
	 *
	 * @return Zend_Application_Module_Autoloader
	 */
    protected function _initAppAutoload()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Nisanth',
            'basePath'  => dirname(__FILE__),
        ));
        return $autoloader;
    }

    /**
     * Initialize the layout loader
     */
    protected function _initLayoutHelper()
    {
    	$this->bootstrap('frontController');
    	$layout = Zend_Controller_Action_HelperBroker::addHelper(
    		new Nisanth_Controller_Action_Helper_LayoutLoader());
    }
//    protected function _initPlugins() {
//    $front = Zend_Controller_Front::getInstance();
//    $front->registerPlugin(new Nisanth_Controller_Plugin_ACL(), 1);
//   }
}