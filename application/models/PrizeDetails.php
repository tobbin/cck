<?php
    /**
     * Class contaings prize details
     *
    */
    class Nisanth_Model_PrizeDetails extends Zend_Db_Table 
    {
        //put your code here
        protected $_name = 'prize_details';

        /**
         * get not payed chits 
         * @param 
         * @return array
         */
	public function getNotPayed()
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('prize_details', array('id', 'installment', 'prize_type', 'prize_amount', 'payment_date', 'status'))
                  ->join('prize_payment_status', 'prize_details.status = prize_payment_status.id', array('name as status_name'))
                  ->join('chits', 'prize_details.chit_id = chits.id', array('chit_code'))
                  ->joinleft('chits_customer', 'prize_details.customer_id = chits_customer.id', array('token'))
                  ->joinleft('user_details', 'chits_customer.customer_id = user_details.user_id', array('first_name', 'last_name'))
                  ->where('prize_details.status != ?', 1);        
            //echo $query; exit;
            return $this->fetchAll($query);
        }
        
         /**
         * get prize details of a chit 
         * @param 
         * @return array
         */
	public function getPrizeDetails($chit_id)
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('prize_details', array('id', 'installment', 'prize_type', 'prize_amount', 'payment_date', 'status'))
                  ->join('chits', 'prize_details.chit_id = chits.id', array('chit_code'))
                  ->joinleft('chits_customer', 'prize_details.customer_id = chits_customer.id', array('token'))
                  ->joinleft('user_details', 'chits_customer.customer_id = user_details.user_id', array('first_name', 'last_name'))
                  ->where('prize_details.chit_id = ?', $chit_id);            
            return $this->fetchAll($query);
        }
       
         /**
         * get prize details of a chit 
         * @param 
         * @return array
         */
	public function getPrizeDetailsByPrizeId($prize_id)
	{
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('prize_details', array('id', 'installment', 'prize_type', 'customer_id','prize_amount', 'prized_date', 'payment_date', 'status'))
                  ->join('prize_payment_status', 'prize_details.status = prize_payment_status.id', array('name as status_name'))
                  ->join('chits', 'prize_details.chit_id = chits.id', array('id as chit_id','chit_code'))
                  ->joinleft('chits_customer', 'prize_details.customer_id = chits_customer.id', array('token'))
                  ->joinleft('user_details', 'chits_customer.customer_id = user_details.user_id', array('first_name as customer_name', 'last_name'))
                  ->where('prize_details.id = ?', $prize_id);            
            return $this->fetchRow($query)->toArray();
        }
}
