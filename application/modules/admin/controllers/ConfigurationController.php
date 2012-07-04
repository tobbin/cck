<?php

class Admin_ConfigurationController extends Zend_Controller_Action
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
        $objConfiguration = new Nisanth_Model_Configuration();
        
        $form = new Admin_Form_Configuration_Edit(array('configuration'=> $objConfiguration->getAll()));
        $this->view->form = $form;
       
        if (!$this->_request->isPost()) {
            return $this->render();
        }

        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else { 
            $form_data = $this->_request->getParams();            
            foreach ($form_data as $key => $value){
                 if (is_numeric($key)) {                  
                     $objConfiguration->updateValue($key, $value);
                 }
            }
            
        }
    }    
}

