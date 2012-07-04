<?php
    /**
     * Class contaings payment related calculations
     *
    */
    class Nisanth_Model_Payment 
    {                                                     
        /**
        * function for pay installment 
        * @param chit_id, customer_id,installment,amount
        * return int installment_amount
       */     
        /*
         * for akshaya check the bounus status
         * 
         */
        public function payInstallment($chit_id, $customer_id, $installment_id, $amount_res, $installment_trans_id, $actual_fine=null, $discount=null)
        {
            $objInstallmentDetails   = new Nisanth_Model_InstallmentDetails();
            $objInstallmentPayment   = new Nisanth_Model_InstallmentPayment();
            $objPaymentTransactionId = new Nisanth_Model_PaymentTransactionId();
         
            $installment_details = $objInstallmentDetails->getInstallmentDetails($customer_id, $installment_id);
            //fine management
            $fine_received  = 0;
            $total_discount = 0;
            $fine_to_take   = 0;
            if($actual_fine > 0){
                $fine_remains  = $actual_fine - $installment_details['fine_received'];
                $fine_to_take  = $fine_remains - $discount; 
                
                $amount_res     = ($amount_res > $fine_to_take)? $amount_res - $fine_to_take : 0;                
                $fine_to_take   = ($amount_res > $fine_to_take)? $fine_to_take : $amount_res;
                $fine_received  = $installment_details['fine_received'] + $fine_to_take;                                
                $total_discount = $discount + $installment_details['discount'];
            }                        
           
            if($installment_details['payment_status'] != 'C'){     
                $amount_to_pay = $installment_details['amount_to_pay'];
                //payment status 
                $payment_status = ($amount_res >= $amount_to_pay)?'C':'R';
                $amount_for_pay = ($amount_res >= $amount_to_pay)? $amount_to_pay : $amount_res;
                            
                //check if pay by installments                
                if($installment_details['payment_status'] == 'R'){
                    $installment_payment_id = $installment_details['payment_id'];
                    $total_amt_res = $amount_for_pay + $installment_details['amount_received'];  
                    
                    $data_array = array('id'=>$installment_payment_id, 'amount_received'=> $total_amt_res, 'total_bonus'=>0, 'fine'=> $fine_received, 'discount'=> $total_discount, 'status'=> $payment_status);     
                    $objInstallmentPayment->updateInstallment($data_array);                    
                }  else {
                    $data_array = array('customer_id'=>$customer_id, 'chit_id'=>$chit_id,'installment_id'=>$installment_id, 'amount_received'=>$amount_for_pay, 'total_bonus'=>0, 'fine'=> $fine_received, 'discount'=> $total_discount, 'status'=>$payment_status);     
                    $installment_payment_id = $objInstallmentPayment->saveAll($data_array);
                }
                //insert values to payment_transaction_id table 
                $transaction_amount = $amount_for_pay + $fine_to_take;
                $values = array('installment_payment_id'=>$installment_payment_id, 'installment_transaction_id'=>$installment_trans_id,'amount'=>$transaction_amount);     
                $objPaymentTransactionId->saveAll($values);
                
                //
                $amount_res = $amount_res -$amount_for_pay;                
                //if the installment is complete then agent commission
                if($payment_status == 'C'){
                   $this->agentCommission($customer_id,$installment_details['installment']);
                }
            }
            return $amount_res; 
        }
        
        
        /**
        * function for calculate the instalment amount by deducting bonus from sala
        * and add fine if applicable
        * @param chit_id, installment, prized_installment,installment_amount
        * return int installment_amount
       */
     /**
      * not manage timely payment bonus. need to do before akshaya
      */
        public function calculateInstallment($chit_id, $installment, $prized_installment)
        {
            $chitsBonusModel = new Nisanth_Model_ChitsBonus();
            $chits = new Nisanth_Model_Chits();
            //user type manupulation
            $user_type = ($prized_installment != 0 and $installment > $prized_installment)? 'P':'N';
           
            $bonus         = $chitsBonusModel->getTotalBonus($chit_id, $installment, $user_type);
            $chitsDetails  = $chits->getDetails($chit_id);           
            return $chitsDetails['installment_amount']-$bonus['bonus'];           
        }
        
        /**
         * function for calculate fine for the given installment for a chital 
         * @param chial_type, late_days, amount_to_pay
         * @return intrest          
         */
        public function calculateFine($chital_type, $late_days, $amount_to_pay){
            
            $objFine = new Nisanth_Model_Fine();            
            $fine = $objFine->getfine($chital_type);
            $intrest = ($amount_to_pay * ($fine/100)) *($late_days/365);           
            return round($intrest);
        }
        
        /**
         * function for calculate total amount of fine for the given chital 
         * @param chial_id, installment_no
         * @return intrest          
         */
        public function calculateTotalFine($chital_id){
            
            $objChitsCustomer = new Nisanth_Model_ChitsCustomer();            
            $objFine          = new Nisanth_Model_Fine();
            $installments     = $objChitsCustomer->getUnPayedInstallments($chital_id);
            
            $fine = 0;
            foreach ($installments as $installment){    
                if($installment['status'] == 'P' && $installment['payment_status'] != 'C'){
                    $chital_type = 'N';
                    if($installment['prized_installment'] != 0 && $installment['installment'] > $installment['prized_installment']) 
                        $chital_type = 'P'; 
                    $exceptionDays = $objFine->getFineExceptionDays($chital_type);  
                    if($installment['late_days'] > $exceptionDays){                         
                        $fine += $this->calculateFine($chital_type, $installment['late_days'], $installment['amount_to_pay']);                                        
                    }    
                }
            }                            
            return round($fine);
        }


        /**
        * function for pay commission to the agent 
        * @param chit_id, customer_id,installment
        * return int installment_amount
       */     
        public function agentCommission($chital_id, $installment)
        {
            $ObjChitsCustomer   = new Nisanth_Model_ChitsCustomer();
            $ObjAgentCommission = new Nisanth_Model_AgentCommission();
            
            $commission_details = $ObjChitsCustomer->getDetails($chital_id);            
            $permonth_amount    = $commission_details['commission'] / $commission_details['period']; 
            $total_commission   = $permonth_amount * $installment;
            
            if($total_commission > $commission_details['paid_commission']){
                $commission_status = (($commission_details['current_installment']-$installment)<2)? 'A':'F';
                $agent_commission_details = array('agent_id'=>$commission_details['agent_id'], 'customer_id'=> $commission_details['customer_id'], 'installment'=> $installment, 'chits_id'=> $commission_details['chit_id'], 'amount'=> $permonth_amount, 'status'=>$commission_status);
                $ObjAgentCommission->saveAll($agent_commission_details);
                //update chits customer paid commission 
                $values =array('paid_commission'=> $commission_details['paid_commission']+ $permonth_amount);
                $users = $ObjChitsCustomer->fetchRow("id = $chital_id");
                $users->setFromArray($values);
                $users->save();
            }
        }
}
