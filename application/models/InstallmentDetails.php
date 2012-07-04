<?php

class Nisanth_Model_InstallmentDetails extends Zend_Db_Table 
{
    //put your code here
    protected $_name = 'installment_details';
    //put your code here
	
   
     /**
     * function for select installment amount by giving installment id
     * @param installment_id
     * return array
     */
     public function getAmount($installment_id)
    {   
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from('installment_details',array('amount'))              
              ->where('installment_details.id =?', $installment_id);
        //echo $query;exit;                      
        return $this->fetchRow($query);        
    }
    
    /**
     * function for select installment details with payment details to pay installments
     * @param chits_id
     * return array
     */
     public function getInstallments($chit_id)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from('installment_details',array('id as installment_id', 'chit_id', 'installment','Unix_Timestamp(installment_date) as installment_date','amount', 'status'))              
              ->where('installment_details.chit_id =?', $chit_id);
              //->where('installment_payment.status != ?', 'C');
        //echo $query; exit;
        return $this->fetchAll($query);
    }
    
    /**
     * function for select installment details with payment details to pay installments
     * @param chits_id, customer
     * return array
     */
//    public function getChitalInstallments($chit_id,$customer)
//    {
//        $query = $this->select();
//        $query->setIntegrityCheck(false);
//        $query->from('installment_details',array('id as installment_id', 'installment','Unix_Timestamp(installment_date) as installment_date','amount','amount as amount_to_pay', 'status'))
//              ->joinleft('chits', 'installment_details.chit_id = chits.id', array('bonus'))
//              ->joinleft('installment_payment', 'installment_details.id = installment_payment.installment_id AND installment_payment.customer_id='.$customer, array('Unix_Timestamp(payed_on) as payed_date', 'amount_received', 'status as payment_status'))
//              ->where('installment_details.chit_id =?', $chit_id);
//              //->where('installment_payment.status != ?', 'C');
//        //echo $query; exit;
//        return $this->fetchAll($query);
//    }
//    
    
    
//    /**
//     * function for select installment details with payment details for report 
//     * @param chits_id, customer_id, start_date, end_date
//     * return array
//     */
//    public function getPaymentByDates($chit_id, $customer, $start_date, $end_date)
//    {
//        $query = $this->select();
//        $query->setIntegrityCheck(false);
//        $query->from('installment_details',array('id as installment_id', 'installment','Unix_Timestamp(installment_date) as installment_date','amount','amount as amount_to_pay', 'status'))
//              ->joinleft('chits', 'installment_details.chit_id = chits.id', array('bonus'))
//              ->joinleft('installment_payment', 'installment_details.id = installment_payment.installment_id AND installment_payment.customer_id='.$customer, array('Unix_Timestamp(payed_on) as payed_date', 'amount_received', 'status as payment_status'))
//              ->where('installment_details.chit_id =?', $chit_id)
//              ->where("installment_details.installment_date between '" . $start_date . "' and ?", $end_date);               
//              //->where('installment_payment.status != ?', 'C');
//       // echo $query; exit;
//        return $this->fetchAll($query);
//    }
    
     /**
     * function for select installment details with payment details for chitwise 
     * @param chits_id, customer_id, start_date, end_date
     * return array
     */
    public function getPaymentByInstallments($chit_id, $customer)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from('installment_details',array('id as installment_id', 'installment','Unix_Timestamp(installment_date) as installment_date', 'DATEDIFF(CURDATE(), installment_date) AS late_days', 'amount', 'IFNULL((`installment_details`.`amount`-`installment_payment`.`amount_received`),`installment_details`.`amount`) AS amount_to_pay', 'status'))
              ->joinleft('chits', 'installment_details.chit_id = chits.id', array('bonus'))
              ->joinleft('installment_payment', 'installment_details.id = installment_payment.installment_id AND installment_payment.customer_id='.$customer, array('Unix_Timestamp(payed_on) as payed_date', 'amount_received', 'status as payment_status'))
              ->where('installment_details.chit_id =?', $chit_id);
              //->where("installment_details.installment between " . $from_installment . " and ?", $to_installment);               
              //->where('installment_payment.status != ?', 'C');
       // echo $query; exit;
        return $this->fetchAll($query);
    }
//    /**
//     * function for select installment details with payment details for chitwise 
//     * @param chits_id, customer_id, start_date, end_date
//     * return array
//     */
//    public function getPaymentByInstallments($chit_id, $customer, $from_installment, $to_installment)
//    {
//        $query = $this->select();
//        $query->setIntegrityCheck(false);
//        $query->from('installment_details',array('id as installment_id', 'installment','Unix_Timestamp(installment_date) as installment_date','amount', 'IFNULL((`installment_details`.`amount`-`installment_payment`.`amount_received`),`installment_details`.`amount`) AS amount_to_pay', 'status'))
//              ->joinleft('chits', 'installment_details.chit_id = chits.id', array('bonus'))
//              ->joinleft('installment_payment', 'installment_details.id = installment_payment.installment_id AND installment_payment.customer_id='.$customer, array('Unix_Timestamp(payed_on) as payed_date', 'amount_received', 'status as payment_status'))
//              ->where('installment_details.chit_id =?', $chit_id)
//              ->where("installment_details.installment between " . $from_installment . " and ?", $to_installment);               
//              //->where('installment_payment.status != ?', 'C');
//       // echo $query; exit;
//        return $this->fetchAll($query);
//    }
           
    
    /**
     * function for get details of installment of a chit for chitwise report
     * @param chits_id, from_installment , to_installment
     * return array
     */
    public function getInstallmentsDates($chit_id,$from_installment , $to_installment)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from('installment_details', array(new Zend_Db_Expr('DISTINCT installment_details.installment'),'installment_date'))              
              ->where("installment_details.chit_id = ?", $chit_id)
               ->where("installment_details.installment between " . $from_installment . " and ?", $to_installment); 
         //echo $query; exit;
        return $this->fetchAll($query);
    }
    
    /**
     * function for select an installment details with payment details to pay installment
     * @param customer, installment_id
     * return array
     */
    public function getInstallmentDetails($customer, $installment_id)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from('installment_details',array('id as installment_id', 'installment','Unix_Timestamp(installment_date) as installment_date','amount','IFNULL((`installment_details`.`amount`-`installment_payment`.`amount_received`),`installment_details`.`amount`) as amount_to_pay', 'status'))
              ->joinleft('chits', 'installment_details.chit_id = chits.id', array('bonus'))
              ->joinleft('installment_payment', 'installment_details.id = installment_payment.installment_id AND installment_payment.customer_id='.$customer, array('id as payment_id', 'fine', 'discount', 'Unix_Timestamp(payed_on) as payed_date', 'amount_received', 'fine as fine_received', 'status as payment_status'))
              ->where('installment_details.id =?', $installment_id);           
        return $this->fetchRow($query);
    }
    
      /**
     * function for insert new installments 
     * @param array $values
     * return 
     */
    public function saveAll($values)
    {
        $installment = $this->fetchNew();
        $installment->setFromArray($values);
        $installment->save();
    }
}
?>
