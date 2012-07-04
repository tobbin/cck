<?php

    class Nisanth_Model_Fine extends Zend_Db_Table 
    {
        //put your code here
        protected $_name = 'fine';      

        /*
         * function for get the fine details 
         * @param customer_type
         */
        public function getFineExceptionDays($customer_type)
        {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('fine', array('exception_days'));
            $query->where('fine.customer_type =?', $customer_type);
            $query->where('fine.status =?', 'A');
            $row  = $this->fetchRow($query);
            return $row->exception_days;
        }
        
        /*
         * function for get the fine  
         * @param customer_type
         */
        public function getFine($customer_type)
        {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('fine', array('value'));
            $query->where('fine.customer_type =?', $customer_type);
            $query->where('fine.status =?', 'A');
            $row = $this->fetchRow($query);
            return $row->value;
        }
    }
?>
