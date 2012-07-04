<?php

class Nisanth_Model_ChitsType extends Zend_Db_Table 
{
    //put your code here
    protected $_name = 'chits_type';
    //put your code here

     /**
     *funcion for get all activated chits type
     * @return array 
     */
    public function getAllType($status)
    {       
       $query = $this->select();
       $query->setIntegrityCheck(false);
       $query->from('chits_type',array('*'))->where('status =?', $status);             
       return $this->fetchAll($query);
    }               
        
}
?>
