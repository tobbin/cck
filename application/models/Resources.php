<?php
    /**
     * Class resources for using ACL
     *
    */
    class Nisanth_Model_Resources extends Zend_Db_Table 
   {
    //put your code here
    protected $_name = 'resources';
    //put your code here
	
    /**
     *funcion for check if the resources is existing if not insert 
     * @param moduleName, controllerName, ActionName
     * @return type null
     */
    public function writeToDB_IfNotExists($strModuleName, $strControllerName, $strActionName) {
            $query = $this->select();
            $query->from('resources', array('id'))
                  ->where('resources.module =?', $strModuleName)
                  ->where('resources.controller =?', $strControllerName)
                  ->where('resources.action =?', $strActionName);
            $row =  $this->fetchRow($query);
			if(!$row){
			    $resource = $this->fetchNew();
				$resource->module = $strModuleName;
				$resource->controller = $strControllerName;
				$resource->action = $strActionName;
				$resource->save();
			}
    }
	
	/**
     *funcion for fetch all resoutces
     * @param null
     * @return array
     */
	public function getAll() {
	    return $this->fetchAll();
	}
}
