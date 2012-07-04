<?php
    class Nisanth_Model_ChitsCustomer extends Zend_Db_Table 
    {
        //put your code here
        protected $_name = 'chits_customer';
        //put your code here

         /**
         * function for select unpayed installment by give chital id used for take payment from daily collection
         * @param chits_id, customer
         * return array
          * want to eleminate the payed installments from here hope that subquery will do this no time now for that 
         */
         public function getUnPayedInstallments($chital)
        {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('chits_customer',array('id as chital_id', 'chit_id', 'prized_installment'))
                  ->join('installment_details', 'chits_customer.chit_id = installment_details.chit_id', array('installment', 'id as installment_id','amount', 'DATEDIFF(CURDATE(), installment_date) AS late_days','status'))                                     
                  ->joinleft('installment_payment', 'chits_customer.id = installment_payment.customer_id AND installment_details.id = installment_payment.installment_id', array('IFNULL((`installment_details`.`amount`-`installment_payment`.`amount_received`),`installment_details`.`amount`) AS amount_to_pay', 'status as payment_status'))
                  ->where('chits_customer.id =?', $chital)
                  ->order('installment_details.id ASC');
                  //->where('installment_details.id NOTIN ?', array(1,2));     
             //echo $query; exit;
            return $this->fetchAll($query);
        }        

        /**
         * Get chitals details acoording to chits and agent	to dispaly in grid 
         * @param chit_id, agent_id, page, number of records
         * @return array
         */     
         function getChitals($chit_id, $agent_id, $page, $rowCount, $qtype = null, $searchText = null)
         {
             $query = $this->select();
             $query->setIntegrityCheck(false);  
             $query->from('chits_customer',array('id as chital_id', 'customer_id', 'collection_type', 'token', 'agent_id'))
                  ->join('user_details', 'chits_customer.customer_id = user_details.user_id', array('user_id', 'first_name', 'last_name', 'IFNULL(`user_details`.`mobile`,`user_details`.`landphone`) AS phone'))         
                   ->join('chits','chits_customer.chit_id = chits.id', array('id as chit_id', 'chit_code'))
                   ->joinleft('employee', 'chits_customer.agent_id = employee.id', array(''))               
                   ->joinleft('user_details', 'employee.user_id = user_details_2.user_id', array('first_name as agent_first_name', 'last_name as agent_last_name'))               
                   ->joinleft('address', 'user_details.user_id = address.user_id', array('house_name'))
                   //->joinleft('installment_details', 'chits_customer.chit_id = installment_details.chit_id', array('sum(amount) as total_amount'))
                   //->joinleft('installment_payment', 'installment_details.id = installment_payment.installment_id AND installment_payment.customer_id = chits_customer.id', array('sum(amount_received)'))                                
                   //->group('chits_customer.id')
                   ->order(array('chits.id', 'chits_customer.token'));   
             if($chit_id)
                 $query->where('chits_customer.chit_id =?', $chit_id);
             if ($agent_id) 
                 $query->where('chits_customer.agent_id =?', $agent_id);
             if ($searchText)
                  $query->where("user_details.first_name LIKE ?", "%$searchText%");
             if($page)
                 $query->limitPage ($page, $rowCount);
               // echo $query; exit;
             return $this->fetchAll($query);
          }
          
           /**
         * Get chitals count acoording to chits and agent to dispaly in grid 
         * @param chit_id, agent_id, page, number of records
         * @return array
         */     
         function getChitalsCount($chit_id, $agent_id, $qtype = null, $searchText = null)
         {
             $query = $this->select();
             $query->setIntegrityCheck(false);  
             $query->from('chits_customer',array('count(chits_customer.id) as total_rows'))
                  ->join('user_details', 'chits_customer.customer_id = user_details.user_id', array(''))         
                   ->join('chits','chits_customer.chit_id = chits.id', array(''))
                   ->joinleft('employee', 'chits_customer.agent_id = employee.id', array(''))               
                   ->joinleft('user_details', 'employee.user_id = user_details_2.user_id', array(''))               
                   ->joinleft('address', 'user_details.user_id = address.user_id', array(''))
                   //->joinleft('installment_details', 'chits_customer.chit_id = installment_details.chit_id', array('sum(amount) as total_amount'))
                   //->joinleft('installment_payment', 'installment_details.id = installment_payment.installment_id AND installment_payment.customer_id = chits_customer.id', array('sum(amount_received)'))                                
                   //->group('chits_customer.id')
                   ->order(array('chits.id', 'chits_customer.token'));   
             if($chit_id)
                 $query->where('chits_customer.chit_id =?', $chit_id);
             if ($agent_id) 
                 $query->where('chits_customer.agent_id =?', $agent_id);
             if ($searchText)
                  $query->where("user_details.first_name LIKE ?", "%$searchText%");
             // echo $query; exit;
             return $this->fetchRow($query);
          }
          
        /**
         * function for select sum of installment amount to payed according to agent
         * @param agent_id, collection_type
         * return array
         */
        public function getInstallmentsByAgents($designation, $agent_id, $collection_type, $chit_id, $chital)
        {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('chits_customer',array('id as chital_id', 'token'))
                  ->join('user_details', 'chits_customer.customer_id = user_details.user_id', array('first_name','last_name'))
                  ->joinleft('chits', 'chits_customer.chit_id = chits.id', array('chit_code', 'current_installment'))
                  ->joinLeft('installment_details', 'installment_details.chit_id = chits.id', array(''))             
                  ->joinLeft('installment_payment', "chits_customer.id = installment_payment.customer_id AND installment_payment.installment_id = installment_details.id", array('SUM(IFNULL((`installment_details`.`amount`-`installment_payment`.`amount_received`),`installment_details`.`amount`)) AS amount_to_pay'))
                  //->where('installment_payment.status != ?','C')              
                  ->group(array('chits_customer.token','chits_customer.id'))
                  ->having('amount_to_pay >0');        
            if($designation == 6) {
                $query->where('chits_customer.collection_agent =?', $agent_id);  
                $query->order('chits_customer.chit_id');
            } elseif($designation == 7) {
                $query->where('chits_customer.agent_id =?', $agent_id);                    
                $query->order('chits_customer.chit_id');
            }        
            if($collection_type)
                $query->where('chits_customer.collection_type =?', $collection_type);
            if($chit_id)
                $query->where ('chits_customer.chit_id =?', $chit_id);
            if($chital)
                $query->where ('chits_customer.customer_id =?', $chital);        
           // echo $query; exit;
            return $this->fetchAll($query);
        }

        /**
         *Function for slect chits customers according to agent chtis and all 
         * @param @chits_id, agent_id
         * @return type array
         */
        public function getAll($chit_id = null, $agent_id = null)
        {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('chits_customer', array('id as chital_id','collection_type', 'token', 'customer_id'))
                  ->join('chits', 'chits_customer.chit_id = chits.id', array('id as chit_id','chit_code','current_installment','chits_date'))
                  ->join('user_details', 'chits_customer.customer_id = user_details.user_id', array('first_name', 'last_name','mobile'))
                  ->join('employee', 'chits_customer.agent_id = employee.id', array(''))
                  ->join('user_details', 'employee.user_id = user_details_2.user_id', array('first_name as agent_name'));
            if($chit_id != null)
                 $query->where('chits_customer.chit_id =?', $chit_id);
            if($agent_id != null)
                 $query->where('chits_customer.agent_id =?', $agent_id);
            //echo $query;exit;            
            return $this->fetchAll($query);
        }

        /**
         *Function for select non prized chitals 
         * @param @chits_id
         * @return type array
         */
        public function getChitalsByPrize($chit_id, $prize_status)
        {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('chits_customer', array('id as chital_id','collection_type', 'token', 'customer_id'))
                  ->where('chits_customer.chit_id =?', $chit_id);
            if($prize_status != null)
                $query->where('chits_customer.prized_installment =?', $prize_status);

            //echo $query;exit;            
            return $this->fetchAll($query);
        }
        /**
         *Function for get chits customer details 
         * @param @chital_id
         * @return type array
         */
        public function getChitalDetails($chital_id)
        {           
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('chits_customer', array('token'))
                  ->join('user_details', 'chits_customer.customer_id = user_details.user_id', array('first_name','last_name'));
            $query->where('chits_customer.id =?', $chital_id);
            return $this->fetchRow($query);
        }

        /**
         *Function for slect chits customers details by giving chit_id also get chits installment from chits period for calculating commission
         * @param @chital_id
         * @return type array
         */
        public function getDetails($chital_id)
        {           
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('chits_customer', array('chit_id', 'customer_id', 'agent_id', 'paid_commission'))
                  ->join('chits', 'chits_customer.chit_id = chits.id', array('current_installment','chits_date'))
                  ->join('chits_type', 'chits_customer.chit_id = chits_type.id', array('period'))
                  ->join('chits_commission', 'chits.id = chits_commission.chit_id', array('amount as commission'));
            $query->where('chits_customer.id =?', $chital_id);

            //echo $query;exit;
            return $this->fetchRow($query);
        }

    }
?>
