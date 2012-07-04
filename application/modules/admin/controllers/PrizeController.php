<?php
class Admin_PrizeController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->auth = Zend_Auth::getInstance();
        if(!$this->auth->hasIdentity()){
            $this->_redirect('admin/');
        }
         $this->baseUrl = $this->getRequest()->getBasePath();
    }

    public function indexAction()
    {
      // $objPrizeDetails   = new Nisanth_Model_PrizeDetails();  
       $objChits   = new Nisanth_Model_Chits();       
       $this->view->form = new Admin_Form_Prize_PrizeSearch(array('chits'=> $objChits->getAll())); 
          
    }
        
      /**
     * action for show details of a chits prize  
     * @param chit_id
     */
    public function detailsAction()
    {   
        $this->view->chit_id = $this->_request->getParam('chit', '');
    }
    
    /**
     * action for show date wise installment payment report 
     * @param chit_id, customer_id
     */
    public function addAction()
    {            
        $objChits         = new Nisanth_Model_Chits();
        $objChitsCustomer = new Nisanth_Model_ChitsCustomer();
        $chitsBonusModel  = new Nisanth_Model_ChitsBonus();
        
        $chit_id       = $this->_request->getParam('chit_id', '1');
        //$chital_id     = $this->_request->getParam('chital', '1');
         
        $chits_data  = $objChits->getDetails($chit_id);       
        $bonus       = $chitsBonusModel->getTotalBonus($chit_id, $chits_data['current_installment']+1, $user_type='N');
        
        $form = new Admin_Form_Prize_Add(array('request' => $this->_request, 
                                           'chitId' => $chit_id, 
                                           //'chitalId' => $chit_id,      
                                           'chitcode'=>$chits_data['chit_code'],
                                           'installment'=> $chits_data['current_installment'],                                           
                                           'devident'=> $bonus['bonus'],
                                           'nextinstallment'=>$chits_data['installment_amount']- $bonus['bonus'],
                                           'paymentdate'=> $chits_data['payment_duration'],
                                           'chitsCustomer' => $objChitsCustomer->getChitalsByPrize($chit_id, 0)
                                           ));
        $this->view->prizeForm = $form;
          if (!$this->_request->isPost()) {
        	return $this->render();
        }
        
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {
            
            $objPrizeDetails       = new Nisanth_Model_PrizeDetails();  
            $objInstallmentDetails = new Nisanth_Model_InstallmentDetails();
            $values = $this->_getAllParams();
            
            //check if the prize is entered for old installments 
            if($chits_data['current_installment'] == $values['installment']){
                //update current installment status to Old
                $installmentDetails = $objInstallmentDetails->fetchRow("chit_id = {$chit_id} AND installment = {$chits_data['current_installment'] }");
                $installmentDetails->status = 'O';
                $installmentDetails->save();

                //insert new record to installment details table
                $installmentDetails = $objInstallmentDetails->fetchNew();
                $installmentDetails->setFromArray($values);
                $installmentDetails->installment = $chits_data['current_installment']+1;
                $installmentDetails->save();

                //update current installment and next chits date in chits table
                $chitsUpdate =$objChits->fetchRow("id = {$chit_id}");
                $chitsUpdate->current_installment = $chits_data['current_installment']+1;
                $chitsUpdate->next_chit_date = $values['installment_date'];            
                $chitsUpdate->save();
            }
            //inserting to prize details table
            $prizeDetails =  $objPrizeDetails->fetchNew();
            $prizeDetails->setFromArray($values);
            $id = $prizeDetails->save();      
            
            //update chits_customer table prized installment            
            $chitsCustomer = $objChitsCustomer->fetchRow("id = {$values['customer_id']}");
            $chitsCustomer->prized_installment = $values['installment'];
            $chitsCustomer->save();
                    
            $this->_redirect("/admin/prize/details/chit/".$values['chit_id']);            
        }
    }
    
    /**
     * action for edit prize details 
     * @param prize_id
     */
    public function editAction()
    {
        $objPrizeDetails    = new Nisanth_Model_PrizeDetails();        
        $objChitsCustomer   = new Nisanth_Model_ChitsCustomer();
        $chit_id = $this->_request->getParam('chit_id', '');
        $prize_id = $this->_request->getParam('prize_id', '');       
        $form     = new Admin_Form_Prize_Edit(array('request' => $this->_request, 
                                         'chitsCustomer' => $objChitsCustomer->getChitalsByPrize($chit_id,null)));
        $form->populate($objPrizeDetails->getPrizeDetailsByPrizeId($prize_id));
        $this->view->prizeForm = $form;
        
         if (!$this->_request->isPost()) {
            return $this->render();
        }
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render(); 
        } else {
            $values = $this->_getAllParams();
            //update  installment status to Old
            $objPrizeDetails = $objPrizeDetails->fetchRow("id = {$prize_id}");
            $objPrizeDetails->setFromArray($values);
            $objPrizeDetails->save();         
            $this->_redirect('/admin/prize/details/chit/'.$values['chit_id']);
        }
    }
    
    /**
     * action for prize payment 
     * @param prize_id
     */
    public function paymentAction()
    {
        $objPrizeDetails    = new Nisanth_Model_PrizeDetails();        
        $objChitsCustomer   = new Nisanth_Model_ChitsCustomer();
        
        $prize_id = $this->_request->getParam('prize', '');       
        $form     = new Admin_Form_Prize_Payment(array('request' => $this->_request));
        $form->populate($objPrizeDetails->getPrizeDetailsByPrizeId($prize_id));
        $this->view->prizeForm = $form;
        
         if (!$this->_request->isPost()) {
            return $this->render();
        }
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render(); 
        } else {
            $values = $this->_getAllParams();
            //update  installment status to Old
            $objPrizeDetails = $objPrizeDetails->fetchRow("id = {$prize_id}");
            $objPrizeDetails->setFromArray($values);
            $objPrizeDetails->save();         
            $this->_redirect('/admin/prize/details/chit/'.$values['chit_id']);
        }
    }
     /**
     * ajax action for show chital name when onchange of token dropdown  
     * @param chit_id      
     */
    function getuserAction()
    {
        $objChitsCustomer = new Nisanth_Model_ChitsCustomer();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $chital_id = $this->_request->getParam('customer_id', '1');
        $chital_details = $objChitsCustomer->getChitalDetails($chital_id);
        //echo'<pre>';print_r($user);echo'</pre>';
        echo $chital_details->first_name .' '. $chital_details->last_name;
        
    }
    
     /**
     * action for show detailsgrid of prize  
     * @param chit_id      
     */
    public function prizegridAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
          $request    =   $this->getRequest()->getParams();
        
        $objPrizeDetails = new Nisanth_Model_PrizeDetails();                   
        $prize_details = $objPrizeDetails->getNotPayed();
       
        $i = 0;
        $data1 = array();
        $data1['page'] = $request['page'];
        //$data1['total'] = $employees_details->total_rows;
        foreach($prize_details as $prize_deta){
             ++$i;            
            $options = '<a href="'. $this->baseUrl.'/admin/prize/payment/prize/' .$prize_deta['id']. '">Pay </a>';             
            $data1['rows'][] = array(
                       'id' => $prize_deta['id'],                       
                       'cell' => array($i,
                        $prize_deta['chit_code'],
                        $prize_deta['installment'],
                        $prize_deta['prize_type'],
                        $prize_deta['token'],
                        $prize_deta['first_name'].' '.$prize_deta['last_name'],
                        $prize_deta['prize_amount'], 
                        $prize_deta['payment_date'], 
                        $prize_deta['status_name'], 
                        $options)
                       );
        }
        $users = json_encode($data1);
        echo $users;      
    }
    
     /**
     * action for show grid inside details of a chits prize  
     * @param chit_id      
     */
     public function detailsgridAction()
    {        
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $objPrizeDetails = new Nisanth_Model_PrizeDetails();
       
        $chit_id   = $this->_request->getParam('chit', '1');
        $this->view->chit_id = $chit_id;
        
        $bonus = $objPrizeDetails->getPrizeDetails($chit_id);
        $i =0;
       
        foreach($bonus as $data){
            ++$i;
             $options = '<a href="/test2/public/admin/prize/edit/prize_id/' . $data['id'] . '/chit_id/'. $chit_id .'">Edit</a>';
           $data1['rows'][] = array(
                       'id' => $data['id'],
                       'cell' => array($data['installment'],  
                                       $data['token'],
                                       $data['first_name'] . ' ' . $data['last_name'],
                                       ($data['prize_type'] == 'L')?'Lot':'Auction',
                                       $data['prize_amount'],
                                       $options
                               )
                       );
           }
       $bonus = json_encode($data1);
        echo $bonus;
    }
}

