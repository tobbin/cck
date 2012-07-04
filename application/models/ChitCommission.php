<?php
class Nisanth_Model_ChitCommission extends Zend_Db_Table 
{
    //put your code here
    protected $_name = 'chits_commission';
    //put your code here
	
    /**
     *Function for slect commission  
     * @param @chit_id
     * @return type array
     */
    public function getAll($chit_id)
    {
        $query = $this->select();
        $query->setIntegrityCheck(false);
        $query->from('chits_commission', array('id', 'amount'))                  
              ->join('chits','chits_commission.chit_id =  chits.id', array('chit_code'));
        if($chit_id)
           $query->where('chits_commission.chit_id =?', $chit_id);            
        //echo $query;exit;            
        return $this->fetchAll($query);
    }
    
     /**
     * function for save 
     * @param array $values
     * return 
     */
    public function saveAll($values)
    {
        $chit = $this->fetchNew();
        $chit->setFromArray($values);
        return $chit->save();
    }
}
?>
