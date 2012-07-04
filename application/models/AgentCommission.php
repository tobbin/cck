<?php
    /**
     * Class installment related functions
     *
    */
    class Nisanth_Model_AgentCommission extends Zend_Db_Table 
{
    //put your code here
    protected $_name = 'agent_commission';
    //put your code here
	
    /**
     * Enter a new installment payment 
     *
     * @param array $values
     * @return id
     */
    public function saveAll($values)
    {

        $agentCommission = $this->fetchNew();
        $agentCommission->setFromArray($values);
        return $trans_id = $agentCommission->save();

    }
       
    
	/**
         *Function for slect commissions 
         * @param 
         * @return type array
         */
        public function getAll($from_date, $to_date)
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('agent_commission', array('agent_id'))                  
                  ->joinleft('agent_commission as agent_commission_1', "agent_commission.id = agent_commission_1.id AND agent_commission_1.status = 'P'", array('sum(agent_commission_1.amount) as payed'))
                  ->joinleft('agent_commission', "agent_commission.id = agent_commission_2.id AND agent_commission_2.status = 'A'", array('sum(agent_commission_2.amount) as commission_to_pay'))
                  ->joinleft('agent_commission', "agent_commission.id = agent_commission_3.id AND agent_commission_3.status = 'H'", array('sum(agent_commission_3.amount) as commission_hold'))
                  ->joinleft('agent_commission', "agent_commission.id = agent_commission_4.id AND agent_commission_4.status = 'F'", array('sum(agent_commission_4.amount) as fine'))
                  ->join('employee', 'agent_commission.agent_id = employee.id', array(''))
                  ->join('user_details', 'employee.user_id = user_details.user_id', array('agent_name' => new Zend_Db_Expr("CONCAT(user_details.first_name, ' ', user_details.last_name)")))                             
                  ->group('agent_commission.agent_id');
            if($from_date)
             $query->where("agent_commission.inserted_date between '" . $from_date . "' and ?", $to_date);
            //$query->where('agent_commission.status =?', 2);            
           //echo $query;exit; 
            return $this->fetchAll($query);
	}
        
        /**
         *Function for slect commissions 
         * @param 
         * @return type array
         */
        public function getCommissionDetails($agent, $chits=null, $from_date, $to_date, $status=null, $page, $rowCount)
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('agent_commission', array('id', 'amount as commission', 'inserted_date','installment','status'))
                  ->joinleft('chits', 'agent_commission.chits_id = chits.id', array('id as chit_id','chit_code'))
                  ->joinleft('chits_customer', 'agent_commission.customer_id = chits_customer.customer_id', array('token'))                  
                  ->joinleft('user_details', 'chits_customer.customer_id = user_details.user_id', array('chital_name' => new Zend_Db_Expr("CONCAT(user_details.first_name, ' ', user_details.last_name)")));
            if($agent)
                $query->where('agent_commission.agent_id =?', $agent);  
            if($chits)
                $query->where('agent_commission.chits_id =?', $chits);  
            if($from_date)
                $query->where("agent_commission.inserted_date between '" . $from_date . "' and ?", $to_date);
            if($status)
                $query->where('agent_commission.status =?', $status);
             if($page)
               $query->limitPage ($page, $rowCount);
            //echo $query;exit; 
            return $this->fetchAll($query);
	}
        
         /**
         * Function for slect sum of commision for an agent for payment  
         * @param agent_id, start_date, end_date, status 
         * @return type array
         */
        public function getAgentCommission($agent_id, $start_date, $end_date, $status)
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('agent_commission', array('sum(amount) as commission'));
            if($start_date)
              $query->where("agent_commission.inserted_date between '" . $start_date . "' and ?", $end_date);
            if($status)
              $query->where('agent_commission.status =?', $status);
            if($agent_id)
              $query->where('agent_commission.agent_id =?', $agent_id);
            //echo $query;exit; 
            return $this->fetchRow($query);
        }
        
        /**
         * Function for change status  
         * @param agent_id, start_date, end_date, current_status , new_status 
         * @return type array
         */
        public function changeCommissionStatus($agent_id, $start_date, $end_date, $current_status, $new_status)
	{                  
            $values = array('status'=>$new_status);
            $this->update($values," agent_id = {$agent_id} and status = '{$current_status}' and inserted_date between '{$start_date}' and '{$end_date}'");            
        }
}
