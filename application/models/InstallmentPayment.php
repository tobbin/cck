<?php
    /**
     * Class installment related functions
     *
    */
    class Nisanth_Model_InstallmentPayment extends Zend_Db_Table 
   {
    //put your code here
    protected $_name = 'installment_payment';
    //put your code here
	
    /**
     * Enter a new installment payment 
     *
     * @param array $values
     * @return id
     */
    public function saveAll($values)
    {
        $installment = $this->fetchNew();
        $installment->setFromArray($values);
        return $trans_id = $installment->save();
    }
    
   /**
     * update installment payment      
     * @param array $values
     * @return id
     */
    public function updateInstallment($values)
    {
        $installment = $this->fetchRow("id= {$values['id']}");
        $installment->setFromArray($values);
        $installment->save();
    }
    
    /**
     *funcion for check details of an installment is paid or not
     * @param 
     * @return type 
     */
    public function getInstallment($customer_id, $chit_id, $installment)
    {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('installment_payment', array('id','amount_received', 'Unix_Timestamp(payed_on) as payed_on', 'status'))
                  ->where('installment_payment.customer_id =?', $customer_id)
                  ->where('installment_payment.chit_id =?', $chit_id)
                  ->where('installment_payment.installment_id =?', $installment);
            return $this->fetchRow($query);
    }
    
    /**
     *funcion for take details for print bill 
     * @param $customer_id, $chit_id, $installment(array)
     * @return type array
     */
    public function getBills($customer_id, $chit_id, $installments)
    {
        $installments = implode(", ",$installments) ;
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from('installment_payment', array('id','amount_received', 'Unix_Timestamp(payed_on) as payed_on', 'status'))
              ->join('chits', 'installment_payment.chit_id = chits.id', array('chit_code'))
              ->join('chits_type', 'chits.type_id = chits_type.id',array('installment as installment_amount'))
              ->join('chits_customer', 'installment_payment.customer_id = chits_customer.id', array('token'))
              ->join('user_details', 'chits_customer.customer_id = user_details.user_id', array('first_name','last_name'))
              ->join('installment_details','installment_details.id = installment_payment.installment_id',array('installment', 'amount as amount_to_pay','status as installment_status'))
              ->where('installment_payment.customer_id =?', $customer_id)
              ->where('installment_payment.chit_id =?', $chit_id)
              ->where('installment_payment.installment_id in ('.$installments . ')');
       //echo $query;exit;
       return $this->fetchAll($query);
    }  
}
