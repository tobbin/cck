<?php
    /**
     * Class contaings Report generation functions 
     *
    */
    class Nisanth_Model_Reports
    {
       
       /**
        * function for generate commission report
        * @param  
        * return array
       */
        public function getCommission()
        {
            $objAgentCommission  = new Nisanth_Model_AgentCommission();            
            $data = $objAgentCommission->getAll();               
            return  $data;                      
        }
//        /**
//        * function for generate date wise report
//        * @param  from_date, todate
//        * return array
//       */
//        public function chitWiseReport()
//        {
//            $objChits  = new Nisanth_Model_Chits();            
//            $data = $objChits->getAllChitsReport();               
//            return  $data;                      
//        }
        
        /**
        * function for generate date wise report
        * @param  
        * return array
       */
        public function agentWiseReport()
        {
            $objEmployee  = new Nisanth_Model_Employee();            
            $data         = $objEmployee->getAll($desg_id = 7);               
            return  $data;                      
        }
        
       /**
        * function for generate agent wise report
        * @param  from_date, todate
        * return array
       */
        public function dateWiseReport($fromDate, $toDate, $transaction_type, $category)
        {
           $objTransaction = new Nisanth_Model_Transaction();
           $transactions    = $objTransaction->getTransactions($fromDate, $toDate, $staus= null, $category, $transaction_type, $page=null, $count = null);
           return  $transactions;                      
        }
        
        
        /**
        * function for generate chitwise report
        * @param chit_id
        * return array
       */
        public function chitWiseDetailReport($chit_id)
        {
            $objChitsCustomer      = new Nisanth_Model_ChitsCustomer();
            $objInstallmentDetails = new Nisanth_Model_InstallmentDetails();
             
            //taking chitals of the given agent
            $chits_customer = $objChitsCustomer->getAll($chit_id, $agent_id=0);                         
            $i= 0;
            foreach ($chits_customer as $value) {
                ++$i;                                                   
                //select installment details 
                $installment_details = $objInstallmentDetails->getPaymentByInstallments($value['chit_id'], $value['chital_id']);                         
                $chitaldata[$i] = array('user_details'=>$value, 'installments_details' => $installment_details);                                  
             }
             return  $chitaldata;          
        } 
        
        /**
        * function for generate agentwise report
        * @param agent_id,
        * return array
       */
        public function agentWiseDetailReport($agent_id)
        {
             $objChitsCustomer      = new Nisanth_Model_ChitsCustomer();
             $objInstallmentDetails = new Nisanth_Model_InstallmentDetails();
             
             //taking chitals of the given agent
             $chits_customer = $objChitsCustomer->getAll($chit_id=0, $agent_id);            
             $i= 0;
             foreach ($chits_customer as $value) {
                 ++$i;                                                   
                 //select installment details getInstallmentsPayment($chit_id, $customer, $start_date, $end_date)
                 $installment_details = $objInstallmentDetails->getPaymentByInstallments($value['chit_id'], $value['chital_id']);                         
                 $chitaldata[$i] = array('user_details'=>$value, 'installments_details' => $installment_details);                                  
              }
              return  $chitaldata;                      
        }
        
        /**
        * function for generate detailed commission report
        * @param  $agent_id
        * return array
       */
        public function getAgentCommission($agent_id)
        {
            $objAgentCommission  = new Nisanth_Model_AgentCommission();            
            $data = $objAgentCommission->getCommissionDetails($agent_id);               
            return  $data;                      
        }
}
