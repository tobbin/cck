<?php
    /**
     * Class Roles for acl 
     *
    */
    class Nisanth_Model_Roles extends Zend_Db_Table 
   {
    //put your code here
    protected $_name = 'roles';
    //put your code here
	
    /**
     *funcion for fetch all resoutces
     * @param null
     * @return array
     */
	public function getAll() {
	    $query = $this->select();
	    $query->from('roles', array('name as role'));
	    return $this->fetchAll($query);
	}
        
      /**
     *funcion for fetch role name
     * @param role id,
     * @return array
     */
	public function getName($id) {
	    $query = $this->select();
	    $query->from('roles', array('name'));
            $query->where('roles.id =?', $id);
	    $row = $this->fetchRow($query);
            return $row->name;
	}
}
