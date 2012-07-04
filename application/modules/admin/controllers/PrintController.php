<?php
class Admin_PrintController extends Zend_Controller_Action
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
    
     /**
     * action for print bill after successful payment 
     * @param chit_id, customer_id
     */
    public function receiptAction()
    {
        $objInstallmentPayment = new Nisanth_Model_InstallmentPayment();              

        $prints = unserialize(urldecode($this->_request->getParam('prints','')));
         
        $bills = array();
        foreach ($prints as $print_data){
            $customer_id  = $print_data['chital'];
            $chit_id	  = $print_data['chit'];
            $installments = $print_data['installments'];
            try {
                array_push($bills, $objInstallmentPayment->getBills($customer_id, $chit_id, $installments));
            }catch (Exception $e){
                echo $e->getMessage();
            }
        }
        //echo '<pre>';print_r($bills); exit;
        $this->view->data = $bills;
        $this->_helper->layout->disableLayout();        
    }
    
    /**
     * action for show chit wise installment payment report 
     * @param chit_id, from_installment, to_installment
     */
    public function chitwiseAction()
    {  
      $objReports            = new Nisanth_Model_Reports();
      $objInstallmentDetails = new Nisanth_Model_InstallmentDetails();             
      
      $chit_id          = $this->_request->getParam('chit', '');
      $to_installment   = $this->_request->getParam('to_installment', '');
      $from_installment = $this->_request->getParam('from_installment', '');
            
      $this->view->from_installment   = $from_installment;
      $this->view->to_installment     = $to_installment;
      $this->view->installmentDetails = $objInstallmentDetails->getInstallmentsDates($chit_id, $from_installment, $to_installment);    
      $this->view->data               = $objReports->chitWiseDetailReport($chit_id);                 
      $this->_helper->layout->disableLayout();  
    }
    
    /**
     * action for show agent wise installment payment report 
     * @param agent_id, from_date, to_date
    */
    public function agentwiseAction()
    {           
        $objReports  = new Nisanth_Model_Reports();
      
        $agent_id  = $this->_request->getParam('agent','');      
        $from_date = $this->_request->getParam('from_date', '');
        $to_date   = $this->_request->getParam('to_date', '');
              
        $this->view->agent_id  = $agent_id;      
        $this->view->from_date = $from_date;
        $this->view->to_date   = $to_date;        
        $this->view->data      = $objReports->agentWiseDetailReport($agent_id, $from_date, $to_date);    
        $this->_helper->layout->disableLayout();        
    }
}

