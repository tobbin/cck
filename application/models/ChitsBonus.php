<?php
/**
     * Class contaings bonus related functions
     *
    */
class Nisanth_Model_ChitsBonus extends Zend_Db_Table 
{
    //put your code here
    protected $_name = 'chits_bonus';
    //put your code here
	
    
    /**
     *function for take sum of all bonus for that installment   
     * @param type $chits_id
     * @param type $instalment
     * @param type $user_type
     * @return type 
     */
	public function getTotalBonus($chit_id, $installment, $user_type)
	{
            $payto_array = array('A',$user_type);
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('chits_bonus', array(new Zend_Db_Expr('SUM(amount) as bonus')) )
                  ->join('bonus_type', 'chits_bonus.bonus_type = bonus_type.id', array(''))
                  ->join('chits_bonus_relation', 'chits_bonus.chit_id = chits_bonus_relation.chit_id and chits_bonus.bonus_type = chits_bonus_relation.bonus_id', array(''))
                  ->where('chits_bonus.chit_id =?', $chit_id)
                  ->where('? between start_installment and end_installment', $installment)
                  ->where('chits_bonus_relation.pay_to IN(?)', $payto_array);
            return $this->fetchRow($query);
	}
        
        
    /**
     *function for take all bonus with its name for a chits installment need (not using now need when list bonuses)  
     * @param type $chits_id
     * @param type $instalment
     * @param type $user_type
     * @return type 
     */
	public function getBonus($chits_id, $installment, $user_type)
	{
            $payto_array = array('A',$userType);
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('chits_bonus', array(new Zend_Db_Expr('SUM(amount) as bonus')) )
                  ->join('bonus_type', 'chits_bonus.bonus_type = bonus_type.id', array('name'))
                  ->join('chits_bonus_relation', 'chits_bonus.chit_id = chits_bonus_relation.chit_id and chits_bonus.bonus_type = chits_bonus_relation.bonus_id', array(''))
                  ->where('chits_bonus.chit_id =?', $chits_id)
                  ->where('? between start_installment and end_installment', $instalment)
                  ->where('chits_bonus_relation.pay_to IN(?)', $payto_array);
            return $this->fetchAll($query);
	}
        
    /**
     *function for take bonus of a chit   
     * @param type $chits_id , $bonus_type
     * @return type array 
     */
	public function getBonusDetails($chits_id, $bonus_type)
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('chits_bonus', array('id', 'start_installment', 'end_installment','amount') )
                  ->where('chits_bonus.chit_id =?', $chits_id)
                  ->where('chits_bonus.bonus_type =?', $bonus_type);
            return $this->fetchAll($query);
	}
        
        /**
	 * Create new user
	 *
	 * @param array $values
	 * @return id
	 */
	public function saveAll($values)
	{
            $chitBonus = $this->fetchNew();
            $chitBonus->setFromArray($values);
            $chitBonus->save();
            $this->addDevident($values);
            return;
        }
        
         /**
	 * Add devident to installment amount of installment_details table
	 *
	 * @param $values 
	 * @return nil
	 */
	public function addDevident($values)
	{
            $start   = $values['start_installment'];
            $end     = $values['end_installment'];
            $chit_id = $values['chit_id'];
            
            $objInstallmentDetails = new Nisanth_Model_InstallmentDetails();
            $objChits = new Nisanth_Model_Chits();
            $chits_data = $objChits->getDetails($chit_id);
            
            $installment_amount = $chits_data['installment_amount'] - $values['amount'];
            
            for($i = $start; $i<= $end; ++$i ){            
                $installmentDetails = $objInstallmentDetails->fetchRow("chit_id = {$chit_id} AND installment = {$i}");
                if($installmentDetails){
                    $installmentDetails->amount = $installment_amount;
                    $installmentDetails->save();
                }else{
                    return;
                }
            }
            return;
        }
}
?>
