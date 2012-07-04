<?php
/**
 * Description of Pincode
 *
 * @author Tobbin
 */
class Admin_Form_Commission_ChitCommission  extends Zend_Form
{
	protected $_request;
        protected $_chitId;
        
	public function getRequest()
	{
            return $this->_request;
	}
	
	public function setRequest(Zend_Controller_Request_Http $request)
	{
            $this->_request = $request;
	}
        
	 public function getChitId()
	{
            return $this->_chitId;
	}
	
	public function setChitId($chitId)
	{
            $this->_chitId = $chitId;
	}
        
	function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formChitCommission');
            
            $objChits = new Nisanth_Model_Chits();            
            $chitsElement = new Zend_Form_Element_Select('chit_id');
            $chitsElement->setLabel('Chit');            
            foreach ($objChits->fetchAll() as $chits) {
                $chitsElement->addMultiOption($chits['id'], $chits['chit_code']);
            }
            $chitsElement->setValue($this->getChitId());
            $chitsElement->setAttrib('class', 'styledselect');
            $chitsElement->setDecorators($decorators->elementDecorators);
                        
            $amount = new Zend_Form_Element_Text('amount');
            $amount->setLabel('amount')->setAttrib('class', 'inp-form');
            $amount->setRequired();
            $amount->setDecorators($decorators->elementDecorators);
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($chitsElement, $amount, $submit));
        }
    }
    

?>
