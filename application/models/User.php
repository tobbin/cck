<?php

class Nisanth_Model_User extends Zend_Db_Table_Abstract 
{
    //put your code here
    protected $_name = 'user';
  
	/**
	 * Create new user
	 *
	 * @param array $values
	 * @return id
	 */
	public function saveAll($values)
	{
            $userDetails = new Nisanth_Model_UserDetails();
            $address = new Nisanth_Model_Address();
            $users = $this->fetchNew();
            $users->setFromArray($values);
            $res = $users->save();

            $userDetails = $userDetails->fetchNew();
            $userDetails->user_id = $res;
            $userDetails->setFromArray($values);
            $userDetails->save();
            
            $address = $address->fetchNew();
            $address->user_id = $res;
            $address->setFromArray($values);
            $address->save();
	}

	/**
	 * Update user when loging credentials
	 *
	 * @param $values
	 */
	public function updateAll($values)
	{
            $users = $this->fetchRow("id = {$values['id']}");
            $users->setFromArray($values);
            $users->save();       
	}

	/**
	 * Change user status
	 *
	 * @param $id
	 * @param $status
	 */
	public function changeStatus($id, $status)
	{
		$users = $this->fetchRow("id = {$id}");
		$users->status = $status;
		$users->save();
                
	}
        
        public function getAll()
        {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('user', array())
              ->join('user_details', 'user.id = user_details.user_id', array('*'))
              ->join('address', 'user.id = address.user_id', array('*'));
            //echo $query;
            return $this->fetchAll($query);
        }
        
        public function deletUser($id)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $db->delete('user', array(
                'id = ?' => $id
            ));
            $db->delete('user_details', array(
                'user_id = ?' => $id
            ));
            $db->delete('address', array(
                'user_id = ?' => $id
            ));

        }
        
        public function getUserByRole($role)
        {
            return $this->fetchAll("role = {$role}");
        }
        
        
        function getAllUsers()
        {
         $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('user', array())
              ->join('user_details', 'user.id = user_details.user_id', array('*'));
            //echo $query;
            return $this->fetchAll($query);
        }
        
        function getUserById($userId)
        {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('user', array())
              ->join('user_details', 'user.id = user_details.user_id', array('*'))
              ->where('user.id =?', $userId);
            //echo $query;
            return $this->fetchRow($query);
        }
        
        function getUserByDetails($firstName, $houseName)
        {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('user_details', array('first_name', 'last_name', 'mobile', 'landphone'))
               ->joinleft('address', 'user_details.user_id = address.user_id', array('street','house_name'));               
            $query->where('user_details.first_name =?', $firstName);
            $query->where('address.house_name =?', $houseName);

            $row = $this->fetchRow($query);
            if(isset ($row->first_name)) {
                $data = $row->toArray();
                $data = $data + array('data' => 'true');
            } else {
                $data = array('data' => 'false');
            }
            echo json_encode($data);
        }
}

?>
