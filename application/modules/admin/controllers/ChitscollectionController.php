<?php
class Admin_ChitscollectionController extends Zend_Controller_Action
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
        $objChits         = new Nisanth_Model_Chits();
        $this->view->form = new Admin_Form_Transaction_ChitsCollectionSearch(array('chits'=> $objChits->getAll())); 
    }
    
    public function edittransactionAction()
    {
        $objInstallmentPaymentTransaction = new Nisanth_Model_InstallmentPaymentTransaction();
        
        $transId      = $this->_request->getParam('trans', '');        
        $transactions = $objInstallmentPaymentTransaction->getTransactions($transId);
        
        $form = new Admin_Form_Transaction_EditTransaction(array('transactions' => $transactions));
        $this->view->form = $form;
               
        if (!$this->_request->isPost()) {
            echo 'kk';
           return $this->render();
        }

        if (!$form->isValid($this->_getAllParams())) {
            echo 'ff';
           return $this->render();
        } else { 
            echo 'gg';
            $ObjTransaction                   = new Nisanth_Model_Transaction();        
            $objInstallmentPayment            = new Nisanth_Model_InstallmentPayment();
            $objInstallmentDetails            = new Nisanth_Model_InstallmentDetails();
            $objInstallmentPaymentTransaction = new Nisanth_Model_InstallmentPaymentTransaction();   
             $objPaymentTrasactionId = new Nisanth_Model_PaymentTransactionId();
             
            //geting values from form            
            $transactions         = $this->_request->getParam('transactions', '');
            $total_amount         = $this->_request->getParam('total_amount', '');
            $installment_trans_id = $this->_request->getParam('installmentTransId', '');
            
            $payment_transaction_data  = $objInstallmentPaymentTransaction->find($installment_trans_id);
            //update payment_trans_id table and transaction table if total amount is different
            if($payment_transaction_data[0]['amount'] != $total_amount){
                $values = array('id'=> $payment_transaction_data[0]['id'], 'amount'=> $total_amount);
                $objInstallmentPaymentTransaction->updateTransaction($values);
               
                //update in transaction table
                $amount_diff      = $total_amount-$payment_transaction_data[0]['amount'];
                $transaction_data = $ObjTransaction->find($payment_transaction_data[0]['transaction_id']);
                $trans_amount     = $transaction_data[0]['amount'] + $amount_diff;
                $values = array('id'=> $transaction_data[0]['id'], 'amount'=> $trans_amount);
                $ObjTransaction->updateTransaction($values);
            }
           
            $count_transactions = count($transactions)/2;                        
            for($i =1; $i <= $count_transactions; $i++) {
                 $payment_trans_id = $transactions['paymentTransId_'.$i]; 
                 $amount_res       = $transactions['amount_'.$i]; 
                 $discount         = $transactions['discount_'.$i];
                 
                 $payment_trasaction_data = $objPaymentTrasactionId->find($payment_trans_id);
                 
                 if($payment_trasaction_data[0]['amount'] != $amount_res){  
                    //update values of payment_transaction_id
                     $values = array('id'=> $payment_trans_id, 'amount'=> $amount_res);
                     $objPaymentTrasactionId->updatePayment($values);

                     $installment_payment_data = $objInstallmentPayment->find( $payment_trasaction_data[0]['installment_payment_id']);
                     $amount_after_fine        = ($amount_res - $installment_payment_data[0]['fine']) + $discount;
                     $amount_diff              = $amount_after_fine - $installment_payment_data[0]['amount_received'];   
                     $amount_received          = ($installment_payment_data[0]['amount_received'] + $amount_diff);

                     //for change the payment status
                     $installmentamount = $objInstallmentDetails->getAmount($installment_payment_data[0]['installment_id']);
                     $amount_to_pay     = ($installmentamount->amount + $installment_payment_data[0]['fine']) - ($installment_payment_data[0]['discount']+$installment_payment_data[0]['bonus']);
                     $status            = ($amount_res >= $amount_to_pay)? 'C':'R';
                     $values = array('id'=> $installment_payment_data[0]['id'], 'amount_received'=> $amount_received, 'discount'=>$discount, 'status'=> $status);
                     $objInstallmentPayment->updateInstallment($values);                                 
                 }
             }                                  
            $this->_redirect("/admin/chitscollection");
        }
    }
    
    /*
     * For display chitals in grid according to different search criteria 
     */
    public function customerwiseAction()
    {       
       $objChits         = new Nisanth_Model_Chits();
       $this->view->form = new Admin_Form_Transaction_ChitalSearch(array('chits'=> $objChits->getAll()));       
    }

    /**
     * action for show all the members of a chit 
     * @param chit_id, 
     */
    public function chitwiseAction()
    {
                        
    }
    
    /**
     * action for show agent wise installment payment report 
     * @param agent_id, from_date, to_date
    */
    public function agentwiseAction()
    {                      
        $this->view->form = new Admin_Form_Transaction_AgentSearch();       
    }
    
    public function chitalwiseAction()
    {
        $ObjChitsCustomer = new Nisanth_Model_ChitsCustomer();
        $ObjConfiguration = new Nisanth_Model_Configuration();
        
        $agent_id        = $this->_request->getParam('agent', '');
        $collection_type = $this->_request->getParam('type', '');
        $designation     = $this->_request->getParam('designation', '');
        $chit_id         = $this->_request->getParam('chits', '');
        $chital          = $this->_request->getParam('chital', '');
        
        $installment_details = $ObjChitsCustomer->getInstallmentsByAgents($designation, $agent_id, $collection_type, $chit_id, $chital);
        $configuration       = $ObjConfiguration->getValue('fine');
        $fine_status         = $configuration->value;
        
        $form = new Admin_Form_Transaction_MultiChitalPayment(array('chitsInstallment' => $installment_details, 'collectionAgent' => $agent_id, 'fine' => $fine_status));
        $this->view->form = $form;
        
        if (!$this->_request->isPost()) {
           return $this->render();
        }

        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {         
            //$ObjChits                       = new Nisanth_Model_Chits();       
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
                if($amount_res>0){
                    //insert the details to installment payment transaction table
                    $installment_transaction = array('collection_agent'=>$collection_agent, 'customer_id'=>$chital,'transaction_id'=>$transaction_id, 'amount'=>$amount_res);
                    $installment_trans_id    = $objInstallmentPaymentTransaction->saveAll($installment_transaction);            

                    foreach($installment_details as $installment_data){
                        if($installment_data->payment_status != 'C' && $amount_res > 0){
                            $installment_id     = $installment_data->installment_id;
                            $prized_installment = $installment_data->prized_installment;
                            $installment        = $installment_data->installment;
                            
                            //get fine
                            $actual_fine = null;
                            if($fine_status == 'A'){
                               $chital_type = ($prized_installment !=0 && $prized_installment < $installment_data->installment)? 'P':'N';
                               $actual_fine = $ObjPayment->calculateFine($chital_type, $installment_data->late_days, $installment_data->amount_to_pay);
                            }
                            //end fine
                            
                            $amount_res = $ObjPayment->payInstallment($installment_data->chit_id, $chital, $installment_id, $amount_res, $installment_trans_id, $actual_fine, $discount = 0);
                            //array_push($print_installments, $installment_id);
                        }
                    }
                }
            }            
            $this->_redirect("/admin/chitscollection");    
        }
    }
    
    /**
     * action for pay installment of a single chits and single customer for multiple installment 
     * @param chit_id, customer_id
     */
    public function installmentwiseAction()
    {                        
        $ObjChitsCustomer      = new Nisanth_Model_ChitsCustomer();        
        $ObjConfiguration      = new Nisanth_Model_Configuration();
        
        $chit_id     = $this->_request->getParam('chit', '');
        $customer_id = $this->_request->getParam('chital', '');
        
        $installment_details   = $ObjChitsCustomer->getUnPayedInstallments($customer_id);       
        $configuration         = $ObjConfiguration->getValue('fine');
        $chit_id  = $installment_details[0]['chit_id'];
        
        $form = new Admin_Form_Transaction_MultiInstallmentPayment(array('chitsInstallment' => $installment_details, 'chitId' => $chit_id, 'customerId' => $customer_id, 'fine' => $configuration->value));
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
            $installment_transaction = array('collection_agent'=>$agent_id, 'customer_id'=>$customer_id,'transaction_id'=>$transaction_id,'amount'=>$total_amount);
            $installment_trans_id    = $objInstallmentPaymentTransaction->saveAll($installment_transaction);

            //sort($installments);
            $print_installments = array('0');    
            for($i =1; $i <= $count_installments; $i++) {
                //echo"<br>". $installments['installment_'.$i] .' = '. $installments['amount_'.$i];
                $installment_id = $installments['installment_'.$i];
                $amount_res     = $installments['amount_'.$i]; 
                $discount       = $installments['discount_'.$i];
                $actual_fine    = $installments['fine_'.$i];
                
                if($installment_id != 0 && $amount_res > 0){
                    $amount_res = $ObjPayment->payInstallment($chit_id, $customer_id, $installment_id, $amount_res, $installment_trans_id, $actual_fine, $discount);
                    array_push($print_installments, $installment_id);
                }
            }
           
            //check if recept is need else go back to payment
            if($print){                                
                array_shift($print_installments);
                $print_data = array(array('chital'=>$customer_id,'chit'=>$chit_id, 'installments'=>$print_installments));
                $print_data = urlencode(serialize($print_data));
                $this->_redirect("/admin/print/receipt/prints/$print_data");
            }else{
                $this->_redirect("/admin/chitscollection");
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
        
        $users       = $objChitsCustomer->getChitals($chit,$agent, $request['page'], $request['rp'],$request['qtype'], $request['query']);
        $chtal_count = $objChitsCustomer->getChitalsCount($chit,$agent,$request['qtype'], $request['query']);
        $i = 0;
        $data1 = array();
        $data1['page'] = $request['page'];
        $data1['total'] = $chtal_count->total_rows;
        foreach($users as $user){
           ++$i;                          
           $options = '<a href="'.$this->baseUrl.'/admin/chitscollection/installmentwise/chit/' . $user['chit_id'] . '/chital/' . $user['chital_id'] . '">Chital </a>';
           $options .= ' | <a href="'. $this->baseUrl.'/admin/chitscollection/chitalwise/chital/'.$user['customer_id'].'"> Customer </a>';
           $data1['rows'][] = array(
                       'id' => $user['user_id'],
                       'cell' => array($i,
                        $user['chit_code'],
                        $user['token'],
                        $user['first_name'].' '.$user['last_name']. ' -- ' .$user['house_name'],                           
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
            $options = '<a href="'. $this->baseUrl.'/admin/chitscollection/chitalwise/designation/' . $designation . '/agent/' . $employee['employee_id'] .'/type/">All  | </a>';   
            $options .= '<a href="'. $this->baseUrl.'/admin/chitscollection/chitalwise/designation/' . $designation . '/agent/' . $employee['employee_id'] .'/type/D"> Daily  | </a>';   
            $options .= '<a href="'. $this->baseUrl.'/admin/chitscollection/chitalwise/designation/' . $designation . '/agent/' . $employee['employee_id'] .'/type/M"> Monthly</a>';           
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
            $options = '<a href="'. $this->baseUrl.'/admin/chitscollection/chitalwise/chits/' . $chits_details['chit_id'] .'">Pay </a>';             
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
    
     /**
     * action for show installment payments in a grid    
     * @param fromdate, todate
     */
   public function collectiondetailsgridAction()
   {
                         
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $request    =   $this->getRequest()->getParams();
       
        $fromDate = null;
        if(isset ($request['fromDate'])){
            $fromDate = $request['fromDate'];           
        }        
        $toDate = null;
        if(isset ($request['toDate'])){
            $toDate = $request['toDate'];           
        }
        
        //to do it now it taken from report controller date wise action
       $objInstallmentPaymentTransaction  = new Nisanth_Model_InstallmentPaymentTransaction();            
       $transactions = $objInstallmentPaymentTransaction->dateWiseTransactions($fromDate, $toDate, $request['page'], $request['rp']);               
       
       //$trans_details   = $objTransaction->getTransCount($fromDate, $toDate, $status, $category, $transactionType);
       
       $i = 0;
       $total_amount = 0;
       $data1 = array();
       $data1['page'] = $request['page'];
       //$data1['total'] = $trans_details->total_rows;
        foreach($transactions as $data){
             ++$i;
            $total_amount +=  $data['amount'];             
           $options = '<a href="'.$this->baseUrl.'/admin/chitscollection/edittransaction/trans/' . $data['installment_trns_id'] .'">edit</a>';                    
           
           $data1['rows'][] = array(
                       'id' => $data['installment_trns_id'],
                       'cell' => array($i,
                        date('Y-m-d', $data['transaction_date']),
                        ($data['agent_name'] == null)?'Office':$data['agent_name'],
                        $data['chit_code'] .'-->'. $data['token'],   
                        $data['chital_name'],
                        $data['phone'],
                        $data['amount'],                           
                        $options)
                       );
           }
           $data1['rows'][] = array(
                       'id' => 'total',
                       'cell' => array('',
                        'Total',
                        ' ','','','',
                        $total_amount,                           
                        '')
                       );
        $users = json_encode($data1);
        echo $users;
        //exit;
    }
}

