<?php

class Nisanth_Model_InstallmentPaymentTransaction extends Zend_Db_Table 
{
    //put your code here
    protected $_name = 'installment_payment_transactions';

    /**
     * update installment payment      
     * @param array $values
     * @return 
     */
    public function updateTransaction($values)
    {
        $installment = $this->fetchRow("id= {$values['id']}");
        $installment->setFromArray($values);
        $installment->save();
    }
    
    /**
     * Enter a new installment payment transaction     
     * @param array $values
     * @return id
     */
    public function saveAll($values)
    {           
        $installment_transaction = $this->fetchNew();
        $installment_transaction->setFromArray($values);
        return $trans_id = $installment_transaction->save();
    }
    
    /**
     *funcion for take the transaction by giving payment_transaction_id 
     * @param trans_id
     * @return type 
     */
    public function getTransactions($transId)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from('installment_payment_transactions', array('id as installment_trans_id'))                                   
              ->join('payment_transaction_id', 'installment_payment_transactions.id = payment_transaction_id.installment_transaction_id', array('id as payment_trans_id', 'amount'))
              ->join('installment_payment', 'payment_transaction_id.installment_payment_id = installment_payment.id', array('fine', 'discount'))
              ->join('installment_details', 'installment_payment.installment_id = installment_details.id', array('installment'))
              ->join('transaction', 'transaction.id = installment_payment_transactions.transaction_id', array('Unix_Timestamp(trans_date) as transaction_date'))
              ->join('chits_customer', 'installment_payment_transactions.customer_id = chits_customer.id', array('token'))
              ->join('chits', 'chits_customer.chit_id = chits.id', array('chit_code'))
              ->join('user_details', 'chits_customer.customer_id = user_details.user_id', array('chital_name' => new Zend_Db_Expr("CONCAT(user_details.first_name, ' ', user_details.last_name)")))                  
              ->where("installment_payment_transactions.id = ?" , $transId);       
        //echo $query;exit;
        return $this->fetchAll($query);
    }    
        
     /**
     *funcion for take the payment report between two dates 
     * @param form_date, to_date
     * @return type 
     */
    public function dateWiseTransactions($start_date, $end_date, $page, $count)
    {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('installment_payment_transactions', array('id as installment_trns_id', 'amount'))
                  ->join('transaction', 'transaction.id = installment_payment_transactions.transaction_id', array('Unix_Timestamp(trans_date) as transaction_date'))
                  ->join('chits_customer', 'installment_payment_transactions.customer_id = chits_customer.id', array('token'))
                  ->join('chits', 'chits_customer.chit_id = chits.id', array('chit_code'))
                  ->join('user_details', 'chits_customer.customer_id = user_details.user_id', array('chital_name' => new Zend_Db_Expr("CONCAT(user_details.first_name, ' ', user_details.last_name)"),  'IFNULL(`user_details`.`mobile`,`user_details`.`landphone`) AS phone'))
                  ->joinleft('user_details', 'installment_payment_transactions.collection_agent = user_details_2.user_id', array('agent_name' => new Zend_Db_Expr("CONCAT(user_details_2.first_name, ' ', user_details_2.last_name)")));
            if($start_date)
                $query->where("transaction.trans_date between '" . $start_date . "' and ?", $end_date);       
            if($page)
                $query->limitPage ($page, $count);
            //echo $query;exit;
            return $this->fetchAll($query);
    }
}