<?php

class Nisanth_Model_Chits extends Zend_Db_Table 
{
    //put your code here
    protected $_name = 'chits';
    //put your code here

     
    /**
     *Function for slect chits
     * @param @null
     * @return type array
     */
    public function getAll()
    {
        $query = $this->select();
        $query->from('chits', array('id', 'chit_code'));                                
        return $this->fetchAll($query);
    }

    /**
     *funcion for get all chits chits details    
     * @return array 
     */
    public function getAllChitsReport()
    {       
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from('chits', array('id as chit_id', 'chit_code', 'current_installment', 'Unix_Timestamp(next_chit_date) as next_chit_date', ))
              ->join('chits_type', 'chits.type_id = chits_type.id', array('sala', 'period', 'installment', 'type'))
              ->joinleft('installment_details', 'chits.id = installment_details.chit_id AND installment_details.installment = chits.current_installment ', array('amount as installment_amount'));                         
        //echo $query;exit;
        return $this->fetchAll($query);
    }
        
    /**
     *funcion for take chitwise report 
     * @param chit_id
     * @return type 
     */
    public function chitWiseReport($chit_id)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from('chits', array())
              ->join('chits_customer', 'chits.id = chits_customer.chit_id', array('collection_type', 'token', 'customer_id'))
              ->join('user_details', 'chits_customer.customer_id = user_details.user_id', array('first_name', 'last_name','mobile'))
              ->joinleft('employee', 'chits_customer.agent_id = employee.id', array(''))
              ->joinleft('user_details', 'employee.user_id = user_details_2.user_id', array('first_name as agent_name'));                   
      // echo $query;exit;
        return $this->fetchAll($query);
    }
    
    /**
     * function for select chit details 
     * @param chits_id
     * return array
     */
    public function getDetails($chit_id)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from('chits',array('chit_code','current_installment','Unix_Timestamp(next_chit_date) as next_chit_date', 'payment_duration', 'chits_date'))
              ->join('chits_type', 'chits.type_id = chits_type.id', array('installment as installment_amount'))
              ->where('chits.id =?', $chit_id);
        return $this->fetchRow($query);
    }
        
      /**
     * function for select bonus  associated to a chit  
     * @param chits_id
     * return array
     */
    public function getBonus()
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from('chits',array('id','chit_code','current_installment','Unix_Timestamp(next_chit_date) as next_chit_date', 'payment_duration', 'chits_date'))
              ->joinleft('chits_bonus_relation', 'chits.id = chits_bonus_relation.chit_id', array(''))
              ->joinleft('bonus_type', 'chits_bonus_relation.bonus_id = bonus_type.id', array('id as bonus_id', 'name as bonus'));             
        return $this->fetchAll($query);
    }
    
     /**
     * function for save new chits  
     * @param array $values
     * return 
     */
    public function saveAll($values)
    {
        $chit = $this->fetchNew();
        $chit->setFromArray($values);
        return $chit->save();
    }
        
}
?>
