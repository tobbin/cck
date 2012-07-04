<?php
class Admin_CommissionController extends Zend_Controller_Action
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
       $objChits   = new Nisanth_Model_Chits();
       $from_date = date("Y-m-d", strtotime(date('m').'/01/'.date('Y')));
       $to_date   = date("Y-m-d");
       $this->view->form = new Admin_Form_Commission_CommissionSearch(array('chits'=> $objChits->getAll(), 'fromDate'=>$from_date, 'toDate'=>$to_date)); 
    }
    
    /**
     * action for show all the members of a chit 
     * @param chit_id, 
     */
    public function detailsAction()
    {  $objChits   = new Nisanth_Model_Chits();
       $from_date = date("Y-m-d", strtotime(date('m').'/01/'.date('Y')));
       $to_date   = date("Y-m-d");
       $this->view->agent_id = $this->_request->getParam('agent', '');                 
       $this->view->form = new Admin_Form_Commission_AgentCommissionSearch(array('chits'=> $objChits->getAll(), 'agent'=>$this->view->agent_id, 'fromDate'=>$from_date, 'toDate'=>$to_date)); 
    }
    
      /**
     * action for change  agent commission status  
     * @param agent_id, form date, to date, current status 
     */
     public function changestatusAction()
    {
        $objAgentCommission = new Nisanth_Model_AgentCommission();
        
        $agent_id = $this->_getParam('agent');
        $fromDate = $this->_getParam('from');
        $toDate   = $this->_getParam('to');
        $frmstatus   = $this->_getParam('frmstatus');
        $tostatus   = $this->_getParam('tostatus');
       //change status to payed in agentcommission table
       $objAgentCommission->changeCommissionStatus($agent_id, $fromDate, $toDate, $frmstatus, $tostatus);            
       $this->_redirect('/admin/commission');
     }
  
    
    /**
     * action for pay commission to agent  
     * @param agent_id, form date, to date 
     */
     public function paymentAction()
    {
        $objAgentCommission = new Nisanth_Model_AgentCommission();
        $ObjTransaction     = new Nisanth_Model_Transaction();
        
        $agent_id = $this->_getParam('agent');
        $fromDate = $this->_getParam('from');
        $toDate   = $this->_getParam('to');
       
        $commission_data = $objAgentCommission->getAgentCommission($agent_id, $fromDate, $toDate, 'A');
        $form = new Admin_Form_Commission_Pay(array('agent' => $agent_id, 'fromDate'=>$fromDate, 'toDate'=>$toDate, 'commission'=>$commission_data->commission));
        $this->view->form = $form;
        
        if (!$this->_request->isPost()) {
            return $this->render();
        }
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render(); 
        } else {
            $request = $this->_getAllParams();
            
            //insert the amount to transaction table
            $transaction = array('payment_option'=>1, 'category'=>2,'amount'=>$request['commission'],'receipt_no'=>$request['Boucher'], 'status'=>1);
            $transaction_id = $ObjTransaction->saveAll($transaction);
            
            //change status to payed in agentcommission table
            $objAgentCommission->changeCommissionStatus($request['agent'], $request['from_date'], $request['to_date'], 'A', 'P');            
            $this->_redirect('/admin/commission');
        }
    }
    
    
     /*
     * for show agent commission for chit
     * @param chit_id 
     */
    function chitcommissionAction()
    {   
        $objChits           = new Nisanth_Model_Chits();   
        $chitId = $this->_getParam('chit','');
            
        $this->view->form   = new Admin_Form_Commission_ChitCommissionSearch(array('chits'=> $objChits->getAll(), 'chitId'=>$chitId)); 
    }
    
    /*
     * for show agent commission for chit
     * @param chit_id 
     */
    function addcommissionAction()
    {
        $chit_id = $this->_getParam('chit','');
        $form = new Admin_Form_Commission_ChitCommission(array('chitId'=>$chit_id));
        $this->view->form = $form;
                
        if (!$this->_request->isPost()) {
        	return $this->render();
        }
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render();
        } else {
           $objChitCommission = new Nisanth_Model_ChitCommission();           
           
           $values  = $this->_getAllParams();           
           $chit_id = $objChitCommission->saveAll($values);
           $this->_redirect('/admin/commission/chitcommission/chit/'.$chit_id );           
        }
    }
    
     public function editchitcommissionAction()
    {        
        $objChitCommission = new Nisanth_Model_ChitCommission();
        $populateArray     = array();

        $form = new Admin_Form_Commission_ChitCommission(array('request' => $this->_request));
        $form->populate($objChitCommission->fetchRow("id = {$this->_getParam('id')}")->toArray());
        
        $this->view->form = $form;
        if (!$this->_request->isPost()) {
            return $this->render();
        }
        if (!$form->isValid($this->_getAllParams())) {
           return $this->render(); 
        } else {
            $commission = $objChitCommission->fetchRow("id = {$this->_getParam('id')}");
            $commission->setFromArray($this->_getAllParams());
            $commission->save();
            
            $this->_redirect('/admin/commission/chitcommission');
        }
    }

   
    public function deletechitcommissionAction()
    {
        $objChitCommission = new Nisanth_Model_ChitCommission();
        $id = $this->_getParam('id');
        $objChitCommission->delete("id = $id");
        $this->_redirect('/admin/commission/chitcommission');
    }
    
     /**
     * action for show all agents and there commission 
     * @param chit_id, 
     */  
    function detailgridAction()
    {                
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $objAgentCommission  = new Nisanth_Model_AgentCommission();            
        
        $request    =   $this->getRequest()->getParams();
        
        $from_date = date("Y-m-d", strtotime(date('m').'/01/'.date('Y').' 00:00:00'));
        $to_date   = date("Y-m-d"). ' 23:59:59';
               
        $from_date = null;
        if(isset ($request['from_date'])){
            $from_date = $request['from_date'] .' 00:00:00';           
        }
        
        $to_date = null;
        if(isset ($request['to_date'])){
            $to_date = $request['to_date'] . ' 23:59:59';           
        }
        
        $agent = null;
        if(isset ($request['agent'])){
            $agent = $request['agent'];           
        }
        
        $status = null;
        if(isset ($request['status'])){
            $status = $request['status'];           
        }
        
        $chits = null;
        if(isset ($request['chits'])){
            $chits = $request['chits'];           
        }
   
        $datas    = $objAgentCommission->getCommissionDetails($agent, $chits, $from_date, $to_date, $status, $request['page'], $request['rp']);    
        $i = 0;
        $data1 = array();
        $data1['page'] = $request['page'];
        //$data1['total'] = $employees_details->total_rows;
        foreach($datas as $data){            
          ++$i;
          if ($data['status']=='A')
              $status = 'Active';
          elseif ($data['status']=='F')
              $status = 'Fine';
          elseif ($data['status']=='H')
              $status = 'Hold';
          
           $options = 'Edit';                          
           $data1['rows'][] = array(
                       'id' => $data['id'],
                       'cell' => array($i,                        
                        $data['chital_name'],
                        $data['chit_code'] .'/'.$data['token'],
                        $data['installment'],
                        $data['inserted_date'], 
                        $data['commission'],
                        $status,
                        $options)
                       );
        }
        $datas = json_encode($data1);
        echo $datas;
    }
    
     /**
     * action for show all agents and there commission 
     * @param chit_id, 
     */  
    function commissiongridAction()
    {                
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $objAgentCommission  = new Nisanth_Model_AgentCommission();            
     
        $request   = $this->getRequest()->getParams();
        $from_date = null;
        
        if(isset ($request['from_date'])){
            $from_date = $request['from_date'] .' 00:00:00';           
        }
        
        $to_date = null;
        if(isset ($request['to_date'])){
            $to_date = $request['to_date'] . ' 23:59:59';           
        }
        
        $datas = $objAgentCommission->getAll($from_date, $to_date);    
        $i = 0;
        $data1 = array();
        $data1['page'] = $request['page'];
        //$data1['total'] = $employees_details->total_rows;
        foreach($datas as $data){ 
           ++$i;
           $options  = '<a href="commission/changestatus/agent/' . $data['agent_id'] . '/frmstatus/A/tostatus/H/from/'.$request['from_date'].'/to/'.$request['to_date'].'"> Hold </a>';  
           $options .= '  |  <a href="commission/changestatus/agent/' . $data['agent_id'] . '/frmstatus/A/tostatus/F/from/'.$request['from_date'].'/to/'.$request['to_date'].'"> Fine </a>';  
           $options .= ' | <a href="commission/payment/agent/' . $data['agent_id'] . '/from/'.$request['from_date'].'/to/'.$request['to_date'].'"> Pay </a>';  
           $options .= ' | <a href="commission/details/agent/' . $data['agent_id'] . '"> Detail Report </a>';
           
           $status  = '<a href="commission/changestatus/agent/' . $data['agent_id'] . '/frmstatus/H/tostatus/A/from/'.$request['from_date'].'/to/'.$request['to_date'].'"> Hold </a>';  
           $status .= '  |  <a href="commission/changestatus/agent/' . $data['agent_id'] . '/frmstatus/F/tostatus/A/from/'.$request['from_date'].'/to/'.$request['to_date'].'"> Fine </a>';  
           $data1['rows'][] = array(
                       'id' => $data['agent_id'],
                       'cell' => array($i,                        
                        $data['agent_name'],
                        $data['fine'],
                        $data['commission_hold'],
                        $data['payed'], 
                        $data['commission_to_pay'],
                        $status,
                        $options)
                       );
           }
        $datas = json_encode($data1);
        echo $datas;
    }
    
     /**
     * action for show all chits commission 
     * @param chit_id, 
     */  
    function chitscommissiongridAction()
    {                
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        $objChitCommission  = new Nisanth_Model_ChitCommission();            
     
        $request    =   $this->getRequest()->getParams();
        $chit_id = null;
        if(isset ($request['chits'])){
            $chit_id = $request['chits'];           
        }
        
        $datas = $objChitCommission->getAll($chit_id);
        $i = 0;
        $data1 = array();
        $data1['page'] = $request['page'];
        //$data1['total'] = $employees_details->total_rows;
        foreach($datas as $data){ 
           ++$i;
          
           $options = '<a href="'.$this->view->baseUrl().'/admin/commission/editchitcommission/id/' . $data['id'] . '"> Edit </a>';
           $options .= '<a href="'.$this->view->baseUrl().'/admin/commission/deletechitcommission/id/' . $data['id'] . '"> | Delete</a>';
           $data1['rows'][] = array(
                       'id' => $data['id'],
                       'cell' => array($i,                        
                        $data['chit_code'],
                        $data['amount'],
                        $options)
                       );
           }
        $datas = json_encode($data1);
        echo $datas;
    }
       
}
