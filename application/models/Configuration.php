<?php

    class Nisanth_Model_Configuration extends Zend_Db_Table 
    {
        //put your code here
        protected $_name = 'configuration';      

        public function getAll()
        {
            return $this->fetchAll();
        }
        
        public function getValue($attribute = null)
        {
            $query = $this->select();
            $query->setIntegrityCheck(false);
            $query->from('configuration', array('id','value'));
            if($attribute)
                $query->where('configuration.attribute= ?', $attribute);
         
            return $this->fetchRow($query);         
        }
        
        /**
	 * Change value
	 * @param $id
	 * @param $status
	 */
	public function updateValue($id, $value)
	{
		$configuration = $this->fetchRow("id = {$id}");
		$configuration->value = $value;
		$configuration->save();                
	}
    }
?>
