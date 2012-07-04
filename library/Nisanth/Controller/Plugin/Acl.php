<?php
class Nisanth_Controller_Plugin_ACL extends Zend_Controller_Plugin_Abstract {
 
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $objAuth = Zend_Auth::getInstance();
	$clearACL = false;
 
	// initially treat the user as a guest so we can determine if the current
	// resource is accessible by guest users
	$role = 'guest';
 
	// if its not accessible then we need to check for a user login
	// if the user is logged in then we check if the role of the logged
	// in user has the credentials to access the current resource
 
        try {
	    if($objAuth->hasIdentity()) {
	        $arrUser = $objAuth->getIdentity();
	         $sess = new Zend_Session_Namespace('Nisanth_ACL');
	         if($sess->clearACL) {
	             $clearACL = true;
	              unset($sess->clearACL);
	         }
 
                 $objAcl = Nisanth_ACL_Factory::get($objAuth,$clearACL);
 
	         if(!$objAcl->isAllowed($arrUser->role, $request->getModuleName() .'::' .$request->getControllerName() .'::' .$request->getActionName())) {
	             $request->setModuleName('default');
        	     $request->setControllerName('error');
        	     $request->setActionName('noauth');
	         }
 
            } else {
	        $objAcl = Nisanth_ACL_Factory::get($objAuth,$clearACL);
	        if(!$objAcl->isAllowed($role, $request->getModuleName() .'::' .$request->getControllerName() .'::' .$request->getActionName())) {
	           //echo 'login';exit;
			   //return Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->setGotoRoute(array(),"login");	
			   $request->setModuleName('admin');
            $request->setControllerName('index');
            $request->setActionName('index');
	        } 
	    }
        } catch(Zend_Exception $e) { 
		echo $e->getMessage();
		exit;
            $request->setModuleName('default');
            $request->setControllerName('error');
            $request->setActionName('noresource');
        }
    }
}