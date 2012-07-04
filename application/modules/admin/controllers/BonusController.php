<?php

class Admin_BonusController extends Zend_Controller_Action
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
        
        $chitsModel = new Nisanth_Model_Chits();
        $chits = $chitsModel->getBonus();
        $i =0;
        foreach($chits as $data){
            ++$i;
           $data1['rows'][] = array(
                       'id' => $data['id'],
                       'cell' => array($i,'<a href="payment/chitwise/chit/' . $data['id']. '">'.$data['chit_code'].'</a>',                                      
                                        $data['current_installment'],
                                        date('d-m-Y', $data['next_chit_date']),
                                        '<a href="bonus/details/chit/' . $data['id']. '/bonus/'.$data['bonus_id'] .'">'.$data['bonus'].'</a>'
                               )
                       );
           }
       $chits = json_encode($data1);
        echo $chits;
    }
    
   public function detailsAction()
    {      
       $this->view->chit_id = $this->_request->getParam('chit', '');
       $this->view->bonus   = $this->_request->getParam('bonus', '');
    }
    
    function addAction()
    {
        $form = new Admin_Form_Bonus(array('chitId' => $this->_getParam('chit_id'), 'bonusTypeId' => $this->_getParam('bonus_type')));
        $this->view->bonusForm = $form;

        if (!$this->_request->isPost()) {
            return $this->render();
        }

        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {
            $chitsBonus = new Nisanth_Model_ChitsBonus();
            $chitsBonus->saveAll($this->_getAllParams());
            $this->_redirect("/admin/bonus/details/chit/{$this->_getParam('chit_id')}/bonus/{$this->_getParam('bonus_type')}");
        }       
    }
    
    function editAction()
    {
        $chitsBonus = new Nisanth_Model_ChitsBonus();
        $form = new Admin_Form_BonusEdit(array('bonus' => $this->_getParam('bonus')));
        $form->populate($chitsBonus->fetchRow("id = {$this->_getParam('bonus')}")->toArray());
        $this->view->bonusForm = $form;

        if (!$this->_request->isPost()) {
            return $this->render();
        }

        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {
            $where = $chitsBonus->getAdapter()->quoteInto('id = ?', $this->_getParam('id'));
            $chitsBonus->update($form->getValues(), $where);
            $this->_redirect("admin/bonus");

            //$chitsBonus->saveAll($this->_getAllParams());
            //$this->_redirect("/admin/bonus/details/chit/{$this->_getParam('chit_id')}/bonus/{$this->_getParam('bonus_type')}");
        }       
    }
    
    public function deleteAction()
    {
        $chitsBonus = new Nisanth_Model_ChitsBonus();
        $where = $chitsBonus->getAdapter()->quoteInto('id = ?', $this->_getParam('bonus'));
        $chitsBonus->delete($where);
        $this->_redirect("admin/bonus");
    }
    
    public function detailsgridAction()
    {        
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $objChitsbonus = new Nisanth_Model_ChitsBonus();
       
        $chit_id   = $this->_request->getParam('chit', '1');
        $bonus_type = $this->_request->getParam('bonus', '1');
                
        $bonus = $objChitsbonus->getBonusDetails($chit_id,$bonus_type);
        $i =0;
        foreach($bonus as $data){
            ++$i;
           $data1['rows'][] = array(
                       'id' => $data['id'],
                       'cell' => array($i,
                                       $data['start_installment'],
                                       $data['end_installment'],
                                       $data['amount'],
                                       '<a href="'.$this->view->baseUrl().'/admin/bonus/edit/bonus/' . $data['id'].'">Edit</a> <a href="'.$this->view->baseUrl().'/admin/bonus/delete/bonus/' . $data['id'].'">Delete</a>',
                                       'Edit Delete'
                               )
                       );
           }
       $bonus = json_encode($data1);
        echo $bonus;
    }
}

