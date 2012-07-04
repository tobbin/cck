<?php

class Nisanth_Model_PaymentOptions extends Zend_Db_Table 
{
    //put your code here
    protected $_name = 'chits_customer';
    //put your code here
	
	/**
         *Function for select all according to status 
         * @param @chits_id, agent_id
         * @return type array
         */
        public function getAll($status = null)
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('payment_options', array('id','name'));
            if($status != null)
                 $query->where('payment_options.status =?', $status);            
            //echo $query;exit;            
            return $this->fetchAll($query);
	}
        
        
}
?>
