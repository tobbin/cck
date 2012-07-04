<?php

class Admin_AclController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->auth = Zend_Auth::getInstance();
        if(!$this->auth->hasIdentity()){
            $this->_redirect('admin/');
        }
    }

    public function indexAction()
    {
       echo'dfddddddddd';
	   exit;
    }    
	public function generateresourcesAction() {
	/*$objResources = new Nisanth_Model_Roleresources();
	$resoures  = $objResources->getAll();
	print_r($resoures);exit;8*/
	
	   $objResources = new Nisanth_ACL_Resources();
	   $objResources->buildAllArrays();
	   $objResources->writeToDB();
	   exit;
	}
}

