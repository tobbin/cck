<?php

class Nisanth_Model_UserDetails  extends Zend_Db_Table_Abstract
{
    
     protected $_name = 'user_details';
    
     /**
     * Update user details
     *
     * @param $values
     */
    public function updateAll($values)
    {
        $user_id = $values['id'];
        unset($values['id']);

        $userDetails = $this->fetchRow("user_id = {$user_id}");
        $userDetails->setFromArray($values);
        $userDetails->save();

        $address = new Nisanth_Model_Address();
        $address = $address->fetchRow("user_id = {$user_id}");
        $address->setFromArray($values);
        $address->save();
    }
     
     /**
     * Get all user details new user	 
     * @param null
     * @return array
     */
     function getUserDetails()
     {
         $query = $this->select();
         $query->setIntegrityCheck(false);
         $query->from('user', array(''))
               ->joinleft('user_details', 'user.id = user_details.user_id', array('user_id', 'first_name', 'last_name', 'mobile', 'landphone'))               
               ->joinleft('address', 'user.id = address.user_id', array('house_name'))                         
               ->group('user_details.user_id')
               ->order('user_details.first_name');        
            //echo $query;
            return $this->fetchAll($query);
     }
      /**
     *funcion for fetch user name
     * @param role id,
     * @return array
     */
	public function getName($id) {
	    $query = $this->select();
	    $query->from('user_details', array('name' => new Zend_Db_Expr("CONCAT(first_name, ' ', last_name)")));
            $query->where('user_details.user_id =?', $id);
	    $row = $this->fetchRow($query);
            return $row->name;
	}
//      /**
//     * Get users according to chits	 
//     * @param null
//     * @return array
//     */
//     
//     function getChitals($chit_id)
//     {
//         $query = $this->select();
//         $query->setIntegrityCheck(false);         
//         $query->from('user_details', array('user_id', 'first_name', 'last_name', 'mobile', 'landphone'))
//               ->join('chits_customer', 'user_details.user_id = chits_customer.customer_id', array('id as chital_id', 'collection_type', 'token', 'agent_id','prized_installment'))                     
//               ->joinleft('employee', 'chits_customer.agent_id = employee.id', array(''))               
//               ->joinleft('user_details', 'employee.user_id = user_details_2.user_id', array('first_name as agent_first_name', 'last_name as agent_last_name'))               
//               ->joinleft('address', 'user_details.user_id = address.user_id', array('house_name'))
//               ->where('chits_customer.chit_id =?', $chit_id)
//               ->group('chits_customer.id')
//               ->order('chits_customer.token');        
//            //echo $query;
//            return $this->fetchAll($query);
//     }    
     
      /**
     * Get chitals details with pending to show 	 
     * @param chit_id
     * @return array
     */    
//     function getChitalsDetails($chit_id, $agent_id)
//     {
//         $query = $this->select();
//         $query->setIntegrityCheck(false);    
//         $query->from('user_details',  array('user_id', 'first_name', 'last_name', 'mobile', 'landphone'))
//               ->join('chits_customer', 'user_details.user_id = chits_customer.customer_id', array('id as chital_id', 'collection_type', 'token', 'agent_id','prized_installment'))                     
//               ->join('chits','chits_customer.chit_id = chits.id', array('id as chit_id', 'chit_code'))
//               ->joinleft('employee', 'chits_customer.agent_id = employee.id', array(''))               
//               ->joinleft('user_details', 'employee.user_id = user_details_2.user_id', array('first_name as agent_first_name', 'last_name as agent_last_name'))               
//               ->joinleft('address', 'user_details.user_id = address.user_id', array('house_name'))
//               //->joinleft('installment_details', 'chits_customer.chit_id = installment_details.chit_id', array('sum(amount) as total_amount'))
//               //->joinleft('installment_payment', 'installment_details.id = installment_payment.installment_id AND installment_payment.customer_id = chits_customer.id', array('sum(amount_received)'))                                
//               ->group('chits_customer.id')
//               ->order('chits_customer.token');   
//         if($chit_id)
//             $query->where('chits_customer.chit_id =?', $chit_id);
//         elseif ($agent_id) {
//             $query->where('chits_customer.agent_id =?', $agent_id);
//         }
//            //echo $query; exit;
//            return $this->fetchAll($query);
//     }
}

?>
