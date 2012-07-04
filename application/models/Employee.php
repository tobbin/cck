<?php

class Nisanth_Model_Employee extends Zend_Db_Table 
{
    //put your code here
    protected $_name = 'employee';
    //put your code here
	
	/**
         *Function for slect employee  
         * @param @designation_id
         * @return type array
         */
        public function getAll($desg_id)
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('employee', array('id as employee_id'))                  
                  ->join('user_details', 'employee.user_id = user_details.user_id', array('user_id', 'first_name', 'last_name', 'mobile'));
            if($desg_id)
                $query->where('employee.designation_id =?', $desg_id);            
            //echo $query;exit;            
            return $this->fetchAll($query);
	}
        
        /**
         *Function for slect employee to list in grid   
         * @param @designation_id, @status, @offset, @count
         * @return type array
         */
        public function getEmployees($branch, $designation, $status, $page, $rowCount)
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('employee', array('id as employee_id'))                  
                  ->join('user_details', 'employee.user_id = user_details.user_id', array('user_id', 'first_name', 'last_name', 'IFNULL(`user_details`.`mobile`,`user_details`.`landphone`) AS phone'))
                  ->join('designation', 'employee.designation_id = designation.id', array('designation'))
                  ->order('user_details.first_name ASC')
                  ->limitPage($page, $rowCount);
            if($branch)
                $query->where('employee.branch_id =?', $branch);  
            if($designation)
                $query->where('employee.designation_id =?', $designation);            
            if($status)
                $query->where('employee.status =?', $status);            
            //echo $query;exit;            
            return $this->fetchAll($query);
	}
        
        /**
         *Function for slect employee to list in grid   
         * @param @designation_id, @status
         * @return type array
         */
        public function getEmployeesCount($designation, $status)
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('employee', array('count(employee.id) as total_rows'));
            
            if($designation)
                $query->where('employee.designation_id =?', $designation);            
            if($status)
                $query->where('employee.status =?', $status);            
            //echo $query;exit;            
            return $this->fetchRow($query);
	}
        
        
        /**
         *Function for slect chits customers details by giving chit_id also get chits installment from chits period
         * @param @chits_id
         * @return type array
         */
        public function getDetails($chit_customer_id)
	{           
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('chits_customer', array('customer_id', 'agent_id', 'paid_commission'))
                  ->join('chits', 'chits_customer.chit_id = chits.id', array('current_installment','chits_date'))
                  ->join('chits_type', 'chits_customer.chit_id = chits_type.id', array('period'))
                  ->join('commission', 'chits.id = commission.chit_id', array('amount'));
            $query->where('chits_customer.id =?', $chit_customer_id);
            return $this->fetchRow($query);
	}
        
     /**
     * function for save new employee 
     * @param array $values
     * return 
     */
    public function saveAll($values)
    {
        $employee = $this->fetchNew();
        $employee->setFromArray($values);
        return $employee->save();
    }
}
?>
