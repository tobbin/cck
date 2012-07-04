<?php
class Admin_ReportController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->baseUrl = $this->getRequest()->getBasePath();
    }

    public function indexAction()
    {
      
    }
    
    
    /**
     * action for show date wise search 
     * @param chit_id, customer_id
     */
    public function datewiseAction()
    {   
       $objPaymentCategory = new Nisanth_Model_PaymentCategory();
      
       $form = new Admin_Form_Report_DateWiseSearch(array('request' => $this->_request,                                            
                                           'category'=> $objPaymentCategory->getAll('A')
                                           ));
       $form->setAction($this->baseUrl.'/admin/report/datewisereport');
       $this->view->form = $form;            
    }
    
   /**
     * action for show chit to select for report 
     * @param chit_id, from_installment, to_installment
     */
    public function chitwiseAction()
    {   
        
      
    }
    
    /**
     * action for show agent to show 
     * @param agent_id, from_date, to_date
    */
    public function agentwiseAction()
    {           
       $this->view->form = new Admin_Form_Transaction_AgentSearch();    
    }
    /**
     * action for show date wise installment payment report 
     * @param chit_id, customer_id
     */
    public function datewisereportAction()
    {   
         $objReports  = new Nisanth_Model_Reports();
         
         $start_date       = $this->_request->getParam('start_date', '') . ' 00:00:00';
         $end_date         = $this->_request->getParam('end_date', '') . ' 23:59:00';
         $transaction_type = $this->_request->getParam('transaction_type', '');
         $category         = $this->_request->getParam('category', '');
       $this->view->data   = $objReports->dateWiseReport($start_date, $end_date, $transaction_type, $category);               
    }
    
    /**
     * action for show chit wise installment payment report 
     * @param chit_id, from_installment, to_installment
     */
    public function chitwisereportAction()
    {           
      $objReports         = new Nisanth_Model_Reports();
      
      $chit_id            = $this->_request->getParam('chit', '');
      $this->view->data   = $objReports->chitWiseDetailReport($chit_id);                 
    }
    
   /**
     * action for show chit wise installment payment report 
     * @param chit_id, from_installment, to_installment
     */
    public function chitwisedetailreportAction()
    {           
      $objReports            = new Nisanth_Model_Reports();
      $objInstallmentDetails = new Nisanth_Model_InstallmentDetails();             
      $objChits              = new Nisanth_Model_Chits();
      
      $chit_id          = $this->_request->getParam('chit', '');
      $to_installment   = $this->_request->getParam('to_installment', '');
      $from_installment = $this->_request->getParam('from_installment', '');
      
      //taking current installment from db        
      $chits_details       = $objChits->getDetails($chit_id);
      $current_installment = $chits_details->current_installment;
      //calculating from and to installment 
      $to_installment   = ($to_installment == '')? $current_installment: $to_installment;
      $from_installment = ($from_installment == '')? ($to_installment > 5)?$to_installment - 5:1 : $from_installment;
     
      $this->view->form = new Admin_Form_Report_ChitWiseSearch(array('chitId'=>$chit_id,
                                            'currentInstallment'=> $current_installment,                                        
                                           'fromInstallment'=> $from_installment,
                                           'toInstallment'=> $to_installment
                                           ));
      $this->view->chit               = $chit_id;
      $this->view->from_installment   = $from_installment;
      $this->view->to_installment     = $to_installment;
      $this->view->installmentDetails = $objInstallmentDetails->getInstallmentsDates($chit_id, $from_installment, $to_installment);    
      $this->view->data               = $objReports->chitWiseDetailReport($chit_id);                 
    }
    
     /**
     * action for show agent wise installment payment report 
     * @param agent_id, from_date, to_date
    */
    public function agentwisereportAction()
    {           
        $objReports  = new Nisanth_Model_Reports();      
        $agent_id  = $this->_request->getParam('agent','');                   
        $this->view->data      = $objReports->agentWiseDetailReport($agent_id);         
    }
    
    /**
     * action for show agent wise installment payment report 
     * @param agent_id, from_date, to_date
    */
    public function agentwisedetailreportAction()
    {           
        $objReports  = new Nisanth_Model_Reports();
      
        $agent_id  = $this->_request->getParam('agent','');      
        $from_date = $this->_request->getParam('from_date', '');
        $to_date   = $this->_request->getParam('to_date', '');
      
        $current_date = date('Y-m-d',time());
      
        $to_date   = ($to_date == '')? $current_date: $to_date;
        $from_date = ($from_date == '')? date('Y-m-d',strtotime("-5 month", time())): $from_date;
     
        $this->view->agent_id  = $agent_id;      
        $this->view->from_date = $from_date;
        $this->view->to_date   = $to_date;
        $this->view->form      = new Admin_Form_Report_AgentWiseSearch(array('agent'=>$agent_id, 'fromDate'=>$from_date, 'toDate'=>$to_date));
        $this->view->data      = $objReports->agentWiseDetailReport($agent_id);         
    }
    
    /**
     * action for show agent wise installment payment report 
     * @param agent_id, from_date, to_date
    */
    public function chitalpaymentdetailsAction()
    {           
        $chital_id  = $this->_request->getParam('chital','');
        $chit_id    = $this->_request->getParam('chit','');
        $objInstallmentDetails = new Nisanth_Model_InstallmentDetails();
        $this->view->data      = $installment_details = $objInstallmentDetails->getPaymentByInstallments($chit_id, $chital_id);
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
            $options = '<a href="'. $this->baseUrl.'/admin/report/agentwisereport/agent/' . $employee['employee_id'] .'" > Report  </a>';   
             $options .= '<a href="'. $this->baseUrl.'/admin/report/agentwisedetailreport/agent/' . $employee['employee_id'] .'" > | Detail Report </a>';             
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
    public function chitsgridAction()
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
            $options = '<a href="'. $this->baseUrl.'/admin/report/chitwisereport/chit/' . $chits_details['chit_id'] .'">Report </a>';  
            $options .= '<a href="'. $this->baseUrl.'/admin/report/chitwisedetailreport/chit/' . $chits_details['chit_id'] .'"> | Detail Report </a>';             
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

