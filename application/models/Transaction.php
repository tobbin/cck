<?php

class Nisanth_Model_Transaction extends Zend_Db_Table 
{
    //put your code here
    protected $_name = 'transaction';

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
	 * Enter a new transaction
	 *
	 * @param array $values
	 * @return id
	 */
	public function saveAll($values)
	{
           
            $transaction = $this->fetchNew();
            $transaction->setFromArray($values);
            return $trans_id = $transaction->save();

	}
        
        /**
         * for select transations 
         * @param start_date, end_date, status, trnsaction_type, category
         * @return array
         */
	public function getTransactions($fromDate, $toDate, $status, $category, $transactionType, $page, $count)
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('transaction', array('*'))
                  ->join('payment_category', 'transaction.category = payment_category.id', array('name as category','transaction_type'))
                  ->joinleft('payment_options', 'transaction.payment_option = payment_options.id', array('name as payment_option'))
                  ->order('transaction.id DESC');
            if($page)
                $query->limitPage ($page,$count);
            if($status)
                $query->where('transaction.status = ?', $status); 
            if($fromDate)
                $query->where("transaction.trans_date between '" . $fromDate . " 00:00:00' and '". $toDate ." 23:59:59'");
            if($category)
                $query->where('payment_category.id = ?', $category);
            if($transactionType)
                $query->where('payment_category.transaction_type = ?', $transactionType);
            // echo $query;exit; 
            return $this->fetchAll($query);
        }
        
         /**
         * for select transations 
         * @param start_date, end_date, status, trnsaction_type, category
         * @return array
         */
	public function getTransCount($fromDate, $toDate, $status, $category, $transactionType)
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('transaction', array('count(transaction.id) as total_rows', 'sum(amount) total_amount'))
                  ->join('payment_category', 'transaction.category = payment_category.id', array(''))
                  ->joinleft('payment_options', 'transaction.payment_option = payment_options.id', array(''));
            if($status)
                $query->where('transaction.status = ?', $status); 
            if($fromDate)
                $query->where("transaction.trans_date between '" . $fromDate . " 00:00:00' and '". $toDate ." 23:59:59'");
            if($category)
                $query->where('payment_category.id = ?', $category);
            if($transactionType)
                $query->where('payment_category.transaction_type = ?', $transactionType);             
            return $this->fetchRow($query);
        }
}


