<?php
    /**
     * Class installment related functions
     *
    */
    class Nisanth_Model_PaymentTransactionId extends Zend_Db_Table 
{
    //put your code here
    protected $_name = 'payment_transaction_id';
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
     *
     * @param array $values
     * @return id
     */
    public function updatePayment($values)
    {              
        $transaction = $this->fetchRow("id= {$values['id']}");
        $transaction->setFromArray($values);
        $transaction->save();
    }
}
