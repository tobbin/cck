<?php

class Admin_ChitsController extends Zend_Controller_Action
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
  
    
    public function addAction()
    {
        $form = new Admin_Form_Chits(array('request' => $this->_request));

        $this->view->chitForm = $form;

        if (!$this->_request->isPost()) {
        	return $this->render();
        }

        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {
           $objChits              = new Nisanth_Model_Chits();
           $objInstallmentDetails = new Nisanth_Model_InstallmentDetails();
           $objChitsType          = new Nisanth_Model_ChitsType();
           
           $values  = $this->_getAllParams();
                      
           //taking installment amount from chits type table
           $row = $objChitsType->fetchRow($objChitsType->select()->where('id = ?', $values['type_id']));
           $installment_amount = $row->installment;
          
           $chit_id = $objChits->saveAll($values);
           //insert installments to installment details table
           for($i=1; $i <= $values['current_installment']; $i++ ){
               $month  = $i-1;
               $date   = date("Y-m-d", strtotime(date("Y-m-d", strtotime($values['start_date'])) . " +$month month"));              
               $status = ($i == $values['current_installment'])? 'R' : 'P';              
               $installment_values = array('chit_id' => $chit_id, 'installment'=> $i, 'installment_date' =>$date, 'amount'=>$installment_amount, 'status'=>$status);           
               $objInstallmentDetails->saveAll($installment_values);
           }                    
           $this->_redirect('/admin/commission/addcommission/chit/'.$chit_id );           
        }
    }
    
    public function editAction()
    {
        $form = new Admin_Form_Chits(array('request' => $this->_request));
        $chits = new Nisanth_Model_Chits();
        $populateArray = array();

        $form->populate($chits->fetchRow("id = {$this->_getParam('id')}")->toArray());
        $form->chit_code->setAttrib('readonly', 'true');
        $form->current_installment->setAttrib('readonly', 'true');
        $form->type_id->setAttrib('readonly', 'true');
        
        $this->view->chitForm = $form;
        if (!$this->_request->isPost()) {
            return $this->render();
        }
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render(); 
        } else {
            $users = $chits->fetchRow("id = {$this->_getParam('id')}");
            $users->setFromArray($this->_getAllParams());
            $users->save();
            
            $this->_redirect('/admin/chits');
        }
    }
    
    public function deleteAction()
    {
        $users = new Nisanth_Model_Chits();
        $id = $this->_getParam('id');
        $users->delete("id = $id");
        $this->_redirect('/admin/chits');
    }
    
    public function addchitalAction()
    {
        
        $chit = new Nisanth_Model_Chits();
        $users = new Nisanth_Model_User();
        $chitId = $this->_getParam('chit_id');
        $form = new Admin_Form_UserChits(array('request' => $this->_request, 'chit' => $chit->fetchRow("id = {$chitId}")));
        $this->view->userForm = $form;
        if (!$this->_request->isPost()) {
            return $this->render();
        }
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render(); 
        } else {
            $chitsCustomer = new Nisanth_Model_ChitsCustomer();
            $chitsCustomer = $chitsCustomer->fetchNew();
            $chitsCustomer->setFromArray($this->_getAllParams());
            $chitsCustomer->save();            
            $this->_redirect('/admin/chits/chital/chit_id/' . $chitId);
        }
    }
    
     public function editchitalAction()
    {
        
        $chit = new Nisanth_Model_Chits();
        $users = new Nisanth_Model_User();
        $chitsCustomer = new Nisanth_Model_ChitsCustomer();
        $chitId = $this->_getParam('chit_id');
        $chital = $this->_getParam('id');
        $form = new Admin_Form_UserChits(array('request' => $this->_request, 'chit' => $chit->fetchRow("id = {$chitId}")));
        $form->populate($chitsCustomer->fetchRow("id = {$chital}")->toArray());
        $this->view->userForm = $form;
        if (!$this->_request->isPost()) {
            return $this->render();
        }
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render(); 
        } else {
            
            $chitsCustomer = $chitsCustomer->fetchRow("id = {$chital}");
            $chitsCustomer->setFromArray($this->_getAllParams());
            $chitsCustomer->save();
            
            $this->_redirect('/admin/chits/chital/chit_id/' . $chitId);
        }
    }
    
    /*
     * for show chitals of a chit 
     * @param chit_id 
    */
    public function chitalAction()
    {
        $this->view->chit_id = $this->_getParam('chit_id', '0');        
    }
    
    /*
     * for delete chital from a chit
     * @param chit_id 
    */    
    public function deletechitalAction()
    {
        $chitId = $this->_getParam('chit_id');
        $chitsCustomer = new Nisanth_Model_ChitsCustomer();
        $where = $chitsCustomer->getAdapter()->quoteInto('id = ?', $this->_getParam('id'));
        $chitsCustomer->delete($where);
        //$this->_redirect("admin/bonus");
        $this->_redirect('/admin/chits/chital/chit_id/' . $chitId);
    }
    
    /*
     * for show installments of a chit
     * @param chit_id 
     */
    function installmentsAction()
    {
        $this->view->chitId = $this->_getParam('id');
    }       
    
    /*
     * for show installments of a chit
     * @param chit_id 
     */
    
    function installmentgridAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $objInstallmentDetails = new Nisanth_Model_InstallmentDetails();
        $chitId = $this->_getParam('chit_id');
        $datas = $objInstallmentDetails->getInstallments($chitId);      
       
        foreach($datas as $data){            
            $options = 'Edit';                          
           $data1['rows'][] = array(
                       'id' => $data['installment_id'],
                       'cell' => array(                        
                        $data['installment'],
                        date('d-M-Y', $data['installment_date']),
                        $data['amount'],
                        ($data['status'] == 'P')? 'Completed': 'Running',    
                        $options)
                       );
           }
        $datas = json_encode($data1);
        echo $datas;
    }
    
    /*
     * function for show chitals of a chits 
     * @param chits,
     */
    public function chitalgridAction()
    {                         
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $objChitsCustomer = new Nisanth_Model_ChitsCustomer();
        
        $request    =   $this->getRequest()->getParams();
        $agent = null;
        if(isset ($request['agent'])){
            $designation = $request['agent'];           
        }
        
        $chit = null;
        if(isset ($request['chits'])){
            $chit = $request['chits'];           
        }
        
        $users       = $objChitsCustomer->getChitals($chit, $agent, $request['page'], $request['rp'], $request['qtype'], $request['query']);
        $chtal_count = $objChitsCustomer->getChitalsCount($chit, $agent);
       
        $i = 0;
        $data1 = array();
        $data1['page'] = $request['page'];
        $data1['total'] = $chtal_count->total_rows;
        foreach($users as $user){
             ++$i;
                          
           $options = '<a href="'.$this->view->baseUrl().'/admin/chits/editchital/chit_id/'. $chit .'/id/' . $user['chital_id'] . '">Edit</a>';
           $options .= '  <a href="'.$this->view->baseUrl().'/admin/chits/deletechital/chit_id/'. $chit .'/id/' . $user['chital_id'] . '">Delete</a>';
           
           $data1['rows'][] = array(
                       'id' => $user['user_id'],
                       'cell' => array($i,
                        $user['token'],
                        $user['first_name'].' '.$user['last_name'],
                        $user['house_name'],                           
                        $user['phone'],
                        $user['agent_first_name'] .' '. $user['agent_last_name'],
                        $options)
                       );
           }
        $users = json_encode($data1);
        echo $users;
        //exit;
    }
    
    /*
     * grid to show all chits 
     */
    public function chitsgridAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $chitsModel = new Nisanth_Model_Chits();
        $chits = $chitsModel->fetchAll();
        $i =0;
        foreach($chits as $data){
            ++$i;
           $options  = '<a href="'.$this->view->baseUrl().'/admin/chits/chital/chit_id/' . $data['id'] . '">Chitals </a>|'; 
           $options .= '<a href="'.$this->view->baseUrl().'/admin/chits/edit/id/' . $data['id'] . '"> Edit </a>|';  
           $options .= '<a href="'.$this->view->baseUrl().'/admin/chits/installments/id/' . $data['id'] . '"> Installments </a>|';
           $options .= '<a href="'.$this->view->baseUrl().'/admin/commission/chitcommission/chit/' . $data['id'] . '"> Agent Commission</a>';
           $data1['rows'][] = array(
                       'id' => $data['id'],
                       'cell' => array($i,'<a href="chits/edit/id/' . $data['id']. '">'.$data['chit_code'].'</a>',
                                        $data['start_date'],
                                        $data['current_installment'],
                                        date('d-m-Y', strtotime($data['next_chit_date'])),                                        
                                        $options)
                       );
       }
       $chits = json_encode($data1);
       echo $chits;
    }
}

