<?php

class Nisanth_Model_Designation extends Zend_Db_Table 
{
	
    //put your code here
    protected $_name = 'designation';
    //put your code here
	
	/**
         *Function for slect designatiion  
         * @param @designation_id
         * @return type array
         */
        public function getAll()
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('designation', array('id', 'designation'))                  
                  ->where('designation.status =?', 'A');            
            //echo $query;exit;            
            return $this->fetchAll($query);
	}
	
}