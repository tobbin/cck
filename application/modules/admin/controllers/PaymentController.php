<?php
class Admin_PaymentController extends Zend_Controller_Action
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

//    public function indexAction()
//    {       
//      
//    }
    /**
     * action for show bills payments of that day 
     * @param agent_id, from_date, to_date
    */
    public function indexAction()
    {                
        $objPaymentCategory = new Nisanth_Model_PaymentCategory();  
        $tarnsCategory      = $objPaymentCategory->getAll('A');
       
        $form = new Admin_Form_Transaction_Search(array('request' => $this->_request,
                                                  'category'=> $objPaymentCategory->getAll('A')));
        $this->view->form = $form;
    }
   
    /**
     * action for pay all type of bills 
     * @param chit_id, 
     */
    public function paybillAction()
    {                
        $objPaymentCategory = new Nisanth_Model_PaymentCategory();
        $objPaymentOptions  = new Nisanth_Model_PaymentOptions();
        
        $form = new Admin_Form_Transaction_PayBill(array('category'=> $objPaymentCategory->getAll('A'),
                                           'paymentOptions' => $objPaymentOptions->getAll('A')
                                           ));
        $this->view->payBillForm = $form;
        if (!$this->_request->isPost()) {
        	return $this->render();
        }
        
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {
            
            $objTransaction     = new Nisanth_Model_Transaction();
            $values = $this->_getAllParams();
            
            //insert new record to installment details table
            $transaction = $objTransaction->fetchNew();
            $transaction->setFromArray($values);
            $transaction->save();
            $this->_redirect("/admin/billpayment/billdetails");           
        }                       
    }
    
   
     
    
    
   
    /**
     * action for show grid transctions of that day include installment payment    
     * @param chit_id, agent_id
     */
   public function billdetailsgridAction()
   {
                         
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        
        $request    =   $this->getRequest()->getParams();
       
        $fromDate = null;
        if($request['fromDate'] != ''){
            $fromDate = $request['fromDate'];           
        }        
        $toDate = null;
        if($request['toDate'] != ''){
            $toDate = $request['toDate'];           
        }
        $status = null;
        if($request['status'] != ''){
            $status = $request['status'];           
        }
        $category = null;
        if($request['category'] != ''){
            $category = $request['category'];           
        }
         $transactionType = null;
        if($request['transactionType'] != ''){
            $transactionType = $request['transactionType'];           
        }
      
       $objTransaction = new Nisanth_Model_Transaction();
       $transactions    = $objTransaction->getTransactions($fromDate, $toDate, $status, $category, $transactionType, $request['page'], $request['rp']);
       $trans_details   = $objTransaction->getTransCount($fromDate, $toDate, $status, $category, $transactionType);
       
       $i = 0;
       $data1 = array();
       $data1['page'] = $request['page'];
       $data1['total'] = $trans_details->total_rows;
        foreach($transactions as $data){
             ++$i;
                          
           $options = '<a href="'.$this->baseUrl.'/admin/billpayment/paybill/trans/' . $data['id'] .'">edit</a>';                    
           
           $data1['rows'][] = array(
                       'id' => $data['id'],
                       'cell' => array($i,
                        $data['category'],
                        ($data['transaction_type'] == 'C')?'Credit':'Debit',
                        $data['trans_date'],
                        $data['amount'],                           
                        $options)
                       );
           }
           $data1['rows'][] = array(
                       'id' => 'total',
                       'cell' => array('',
                        'Total',
                        ' ',
                        '',
                        $trans_details->total_amount,                           
                        '')
                       );
        $users = json_encode($data1);
        echo $users;
        //exit;
    }  
}

