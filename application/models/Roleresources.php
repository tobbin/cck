<?php
    /**
     * Class roleresoutces 
     *
    */
    class Nisanth_Model_Roleresources extends Zend_Db_Table 
   {
    //put your code here
    protected $_name = 'roleresources';
    //put your code here
	
    /**
     *funcion for fetch all role resources relations 
     * @param null
     * @return array
     */
	public function getAll() {
		
		$query = $this->select();
		$query->setIntegrityCheck(false);
		$query->from('roleresources', '')
			  ->join('roles', 'roleresources.roleId = roles.id', array('name as role'))
			  ->join('resources', 'roleresources.resourceId = resources.id', array('module', 'controller', 'action'));
			 // echo $query;exit;
		return $this->fetchAll($query);
	}
}
