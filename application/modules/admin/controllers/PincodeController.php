<?php

class Admin_PincodeController extends Zend_Controller_Action
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
              
    }
    
    public function gridAction()
    {                         
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $objPincode = new Nisanth_Model_Pincode();
      
        $pincodes = $objPincode->fetchAll();       
        $i = 0;
        foreach($pincodes as $pincode){
             ++$i;           
            $options = '<a href="'.$this->view->baseUrl().'/admin/pincode/edit/id/' . $pincode['id'] . '">Edit</a>';
            $options .= '  <a href="'.$this->view->baseUrl().'/admin/pincode/delete/id/' . $pincode['id'] . '">Delete</a>';                 
             
            $data1['rows'][] = array(
                       'id' => $pincode['id'],
                       'cell' => array($i,
                        $pincode['street'],
                        $pincode['pin'],
                        $options)
                       );
           }
       $users = json_encode($data1);
        echo $users;
        //exit;
    }
    
    public function addAction()
    {
        $objPincode = new Nisanth_Model_Pincode();
        $form = new Admin_Form_Pincode(array('request' => $this->_request));

        $this->view->form = $form;

        if (!$this->_request->isPost()) {
        	return $this->render();
        }

        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {
            $pincode = $objPincode->fetchNew();
            $pincode->setFromArray($this->_getAllParams());
            $pincode->save();
            $this->_redirect('/admin/pincode');
        }
    }
    
    public function editAction()
    {
        $form = new Admin_Form_Pincode(array('request' => $this->_request));
        $objPincode = new Nisanth_Model_Pincode();

        $form->populate($objPincode->fetchRow("id = {$this->_getParam('id')}")->toArray());
        
        $this->view->form = $form;
        if (!$this->_request->isPost()) {
            return $this->render();
        }
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render(); 
        } else {
            $users = $objPincode->fetchRow("id = {$this->_getParam('id')}");
            $users->setFromArray($this->_getAllParams());
            $users->save();
            
            $this->_redirect('/admin/pincode');
        }
    }
    
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->delete('pincode', array(
            'id = ?' => $id
        ));
        $this->_redirect('/admin/pincode');
    }
    

}

