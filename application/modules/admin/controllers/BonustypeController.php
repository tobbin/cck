<?php

class Admin_BonustypeController extends Zend_Controller_Action
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
        $objBonustype = new Nisanth_Model_Bonustype();
      
        $bonusTypes = $objBonustype->fetchAll();       
        $i = 0;
        foreach($bonusTypes as $bonusType){
             ++$i;           
            $options = '<a href="'.$this->view->baseUrl().'/admin/bonustype/edit/id/' . $bonusType['id'] . '">Edit</a>';
            $options .= '  <a href="'.$this->view->baseUrl().'/admin/bonustype/delete/id/' . $bonusType['id'] . '">Delete</a>';                 
             
            $data1['rows'][] = array(
                       'id' => $bonusType['id'],
                       'cell' => array($i,
                        $bonusType['name'],
                        $options)
                       );
           }
       $users = json_encode($data1);
        echo $users;
        //exit;
    }
    
    public function addAction()
    {
        $objBonustype = new Nisanth_Model_Bonustype();
        $form = new Admin_Form_Bonustype(array('request' => $this->_request));

        $this->view->form = $form;

        if (!$this->_request->isPost()) {
        	return $this->render();
        }

        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {
            $pincode = $objBonustype->fetchNew();
            $pincode->setFromArray($this->_getAllParams());
            $pincode->save();
            $this->_redirect('/admin/bonustype');
        }
    }
    
    public function editAction()
    {
        $form = new Admin_Form_Bonustype(array('request' => $this->_request));
        $objBonustype = new Nisanth_Model_Bonustype();

        $form->populate($objBonustype->fetchRow("id = {$this->_getParam('id')}")->toArray());
        
        $this->view->form = $form;
        if (!$this->_request->isPost()) {
            return $this->render();
        }
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render(); 
        } else {
            $users = $objBonustype->fetchRow("id = {$this->_getParam('id')}");
            $users->setFromArray($this->_getAllParams());
            $users->save();
            
            $this->_redirect('/admin/bonustype');
        }
    }
    
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->delete('bonus_type', array(
            'id = ?' => $id
        ));
        $this->_redirect('/admin/bonustype');
    }
    

}

