<?php
class Admin_ChitspaymentController extends Zend_Controller_Action
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
      
    }
    /*
     * For display chitals in grid according to different search criteria 
     */
    public function customerwiseAction()
    {       
       $objChits   = new Nisanth_Model_Chits();
       $this->view->form = new Admin_Form_Transaction_ChitalSearch(array('chits'=> $objChits->getAll()));       
    }

    /**
     * action for show all the members of a chit 
     * @param chit_id, 
     */
    public function chitwiseAction()
    {
       $this->view->chit_id = $this->_request->getParam('chit', '');                       
    }
    
     /**
     * action for show agent wise installment payment report 
     * @param agent_id, from_date, to_date
    */
    public function agentwiseAction()
    {                      
        $this->view->form = new Admin_Form_Transaction_AgentSearch();       
    }
    
    public function multichitalpaymentAction()
    {
        $ObjChitsCustomer = new Nisanth_Model_ChitsCustomer();
        
        $agent_id        = $this->_request->getParam('agent', '');
        $collection_type = $this->_request->getParam('type', '');
        $designation     = $this->_request->getParam('designation', '');
        $chit_id           = $this->_request->getParam('chits', '');
        $chital           = $this->_request->getParam('chital', '');
        
        $installment_details = $ObjChitsCustomer->getInstallmentsByAgents($designation, $agent_id, $collection_type, $chit_id, $chital);
        
        $form = new Admin_Form_Transaction_MultiChitalPayment(array('chitsInstallment' => $installment_details, 'collectionAgent' => $agent_id));
        $this->view->form = $form;
        
        if (!$this->_request->isPost()) {
           return $this->render();
        }

        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {         
            //$ObjChits                         = new Nisanth_Model_Chits();       
            $ObjPayment                       = new Nisanth_Model_Payment();
            $ObjTransaction                   = new Nisanth_Model_Transaction();        
            $objInstallmentPayment            = new Nisanth_Model_InstallmentPayment();
            $objInstallmentPaymentTransaction = new Nisanth_Model_InstallmentPaymentTransaction();   
          
            //geting values from form
            $collection_agent = $this->_request->getParam('collection_agent', '');
            $total_amount     = $this->_request->getParam('total_amount', '');
            $installments     = $this->_request->getParam('installments', '');
            
            $count_installments = count($installments)/2;
          
            //insert the amount to transaction table
            $transaction = array('payment_option'=>3, 'category'=>1,'amount'=>$total_amount, 'status'=>1);
            $transaction_id = $ObjTransaction->saveAll($transaction);

             
            for($i =1; $i <= $count_installments; $i++) {
                $chital     = $installments['chital_'.$i]; 
                $amount_res = $installments['amount_'.$i]; 
                $installment_details =  $ObjChitsCustomer->getUnPayedInstallments($chital);                
                
                //insert the details to installment payment transaction table
                $installment_transaction = array('collection_agent'=>$collection_agent, 'customer_id'=>$chital,'transaction_id'=>$transaction_id);
                $installment_trans_id    = $objInstallmentPaymentTransaction->saveAll($installment_transaction);            
                
                foreach($installment_details as $installment_data){
                    if($installment_data->payment_status != 'C'){
                        $installment_id = $installment_data->installment_id;
                        $amount_res = $ObjPayment->payInstallment($installment_data->chit_id, $chital, $installment_id, $amount_res, $installment_trans_id);
                        //array_push($print_installments, $installment_id);
                    }
                }
            }
            
             $this->_redirect("/admin/chitspayment");    
        }
    }
    /**
     * action for pay installment of a single chits and single customer for multiple installment 
     * @param chit_id, customer_id
     */
    public function multiinstallmentspaymentAction()
    {   
        $chit_id     = $this->_request->getParam('chit', '');
        $customer_id = $this->_request->getParam('chital', '');
                     
        $ObjChitsCustomer      = new Nisanth_Model_ChitsCustomer();
        
        $installment_details   = $ObjChitsCustomer->getUnPayedInstallments($customer_id);       
        
        $form = new Admin_Form_Transaction_MultiInstallmentPayment(array('chitsInstallment' => $installment_details, 'chitId' => $chit_id, 'customerId' => $customer_id));
        $this->view->form = $form;
       
        if (!$this->_request->isPost()) {
        	return $this->render();
        }

        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {
            
           // $ObjChits                         = new Nisanth_Model_Chits();       
            $ObjPayment                       = new Nisanth_Model_Payment();
            $ObjTransaction                   = new Nisanth_Model_Transaction();        
            $objInstallmentPayment            = new Nisanth_Model_InstallmentPayment();
            $objInstallmentDetails            = new Nisanth_Model_InstallmentDetails();    
            $objInstallmentPaymentTransaction = new Nisanth_Model_InstallmentPaymentTransaction();   
             
            //get values
            $installments	= $this->_request->getParam('installments', '');
            $total_amount      	= $this->_request->getParam('total_amount','');
            $customer_id	= $this->_request->getParam('customer_id','');
            $chit_id		= $this->_request->getParam('chit_id','');
            $agent_id		= $this->_request->getParam('agent_id','0');
            $print  		= $this->_request->getParam('print','');
            $count_installments = count($installments)/2;
            
            //insert the amount to transaction table
            $transaction    = array('payment_option'=>1, 'category'=>1,'amount'=>$total_amount, 'status'=>1);
            $transaction_id = $ObjTransaction->saveAll($transaction);

            //insert the details to installment payment transaction table
            $installment_transaction = array('collection_agent'=>$agent_id, 'customer_id'=>$customer_id,'transaction_id'=>$transaction_id);
            $installment_trans_id    = $objInstallmentPaymentTransaction->saveAll($installment_transaction);

            //sort($installments);
            $print_installments = array('0');    
            for($i =1; $i <= $count_installments; $i++) {
                //echo"<br>". $installments['installment_'.$i] .' = '. $installments['amount_'.$i];
                $installment_id = $installments['installment_'.$i];
                $amount_res     = $installments['amount_'.$i]; 
                if($installment_id != 0){
                    $amount_res = $ObjPayment->payInstallment($chit_id, $customer_id, $installment_id, $amount_res, $installment_trans_id);
                    array_push($print_installments, $installment_id);
                }
            }
            
            //check if recept is need else go back to payment
            if($print){                                
                array_shift($print_installments);
                $print_array = urlencode(serialize($print_installments));
                $this->_redirect("/admin/print/bill/chit/$chit_id/customer/$customer_id/installments/$print_array");
            }else{
                $this->_redirect("/admin/chitspayment");
            }  
                exit;    //show the sucess message.  
          }
          
    }
    
   
    /**
     * action for showing chitals according to search criteria 
     * @param chit_id, agent_id
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
            $chits = $request['chits'];           
        }
        
        $users    = $objChitsCustomer->getChitals($chit, $agent, $request['page'], $request['rp']);
      
        $i = 0;
        foreach($users as $user){
             ++$i;
                          
           $options = '<a href="'.$this->baseUrl.'/admin/chitspayment/multiinstallmentspayment/chit/' . $user['chit_id'] . '/chital/' . $user['chital_id'] . '">Pay</a>';
           $chital = '<a href="'. $this->baseUrl.'/admin/chitspayment/multichitalpayment/chital/'.$user['customer_id'].'">'. $user['first_name'].' '.$user['last_name']. ' -- ' .$user['house_name'] .'</a>';
           $data1['rows'][] = array(
                       'id' => $user['user_id'],
                       'cell' => array($i,
                        $user['chit_code'],
                        $user['token'],
                        $chital,                           
                        $user['phone'],
                        $user['agent_first_name'] .' '. $user['agent_last_name'],
                        $options)
                       );
           }
        $users = json_encode($data1);
        echo $users;
        //exit;
    } 
    
    /**
     * list agent in grid according to search criteria    
     * @param branch, designation, 
     */
    public function agentgridAction()
    {                         
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $request    =   $this->getRequest()->getParams();
         
        $designation = null;
        if(isset ($request['designation'])){
            $designation = $request['designation'];           
        }
        
        $branch = null;
        if(isset ($request['branch'])){
            $branch = $request['branch'];           
        }
        
        $status = null;
        
        $objEmployee = new Nisanth_Model_Employee();
                            
        $employees = $objEmployee->getEmployees($branch,$designation, $status, $request['page'], $request['rp']);
        $employees_details = $objEmployee->getEmployeesCount($designation, $status);
       
        $i = 0;
        $data1 = array();
        $data1['page'] = $request['page'];
        $data1['total'] = $employees_details->total_rows;
        foreach($employees as $employee){
             ++$i;  
            $options = '<a href="'. $this->baseUrl.'/admin/chitspayment/multichitalpayment/designation/' . $designation . '/agent/' . $employee['employee_id'] .'/type/">All  | </a>';   
            $options .= '<a href="'. $this->baseUrl.'/admin/chitspayment/multichitalpayment/designation/' . $designation . '/agent/' . $employee['employee_id'] .'/type/D"> Daily  | </a>';   
            $options .= '<a href="'. $this->baseUrl.'/admin/chitspayment/multichitalpayment/designation/' . $designation . '/agent/' . $employee['employee_id'] .'/type/M"> Monthly</a>';           
            $data1['rows'][] = array(
                       'id' => $employee['employee_id'],
                       'designation'=>$designation,
                       'cell' => array($i,
                        $employee['first_name'].' '.$employee['last_name'],
                        $employee['phone'],
                        $employee['designation'],   
                        $options)
                       );
           }
       $users = json_encode($data1);
        echo $users;
        //exit;
    }
      
    /**
     * list chits in grid according to search criteria    
     * @param  
     */
    public function flexychitgridAction()
    {                
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $request    =   $this->getRequest()->getParams();
        
        $objChits = new Nisanth_Model_Chits();                   
        $chits = $objChits->getAllChitsReport();
       
         $i = 0;
        $data1 = array();
        $data1['page'] = $request['page'];
        //$data1['total'] = $employees_details->total_rows;
        foreach($chits as $chits_details){
             ++$i;  
            $options = '<a href="'. $this->baseUrl.'/admin/chitspayment/multichitalpayment/chits/' . $chits_details['chit_id'] .'">Pay </a>';             
            $data1['rows'][] = array(
                       'id' => $chits_details['chit_id'],                       
                       'cell' => array($i,
                        $chits_details['chit_code'],
                        $chits_details['period'],
                        $chits_details['sala'], 
                        $chits_details['current_installment'], 
                        $chits_details['next_chit_date'], 
                        $chits_details['installment_amount'], 
                        $options)
                       );
        }
        $users = json_encode($data1);
        echo $users;
        //exit;
    }
}

