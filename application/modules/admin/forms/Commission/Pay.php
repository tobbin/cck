<?php
/**
 * chitalwise search form for payment
 * @author Tobin
 * @since 13-03-2012
 * @package Form
 *
 */
class Admin_Form_Commission_Pay extends Zend_Form
{
        protected $_agent;
        protected $_fromDate;
        protected $_toDate;
        protected $_commission;
       
        public function getAgent()
	{
            return $this->_agent;
	}
	
	public function setAgent($agent)
	{
            $this->_agent = $agent;
	}
        
          public function getFromDate()
	{
            return $this->_fromDate;
	}
	
	public function setFromDate($fromDate)
	{
            $this->_fromDate = $fromDate;
	}
        
        public function getToDate()
	{
            return $this->_toDate;
	}
	
	public function setToDate($toDate)
	{
            $this->_toDate = $toDate;
	}
        
        public function getCommission()
	{
            return $this->_commission;
	}
	
	public function setCommission($commission)
	{
            $this->_commission = $commission;
	}
        
	function init()
	{           
            $class = 'dateEdit';
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('agentCommission');
            $this->addAttribs(array("id"=>"agentCommission")); 
            
            $agentElement = new Zend_Form_Element_Hidden('agent');            
            $agentElement->setValue($this->getAgent());
            
            $fromDateElement = new Zend_Form_Element_Text('from_date');
            $fromDateElement->setLabel('From Date')->setAttrib("class", "inp-form datepicker $class");
            $fromDateElement->setValue($this->getFromDate());
            $fromDateElement->setRequired();
            $fromDateElement->addValidator(new Zend_Validate_Date());
            $fromDateElement->setDecorators($decorators->elementDecorators);

            $toDateElement = new Zend_Form_Element_Text('to_date');
            $toDateElement->setLabel('To Date')->setAttrib("class", "inp-form datepicker $class");
            $toDateElement->setValue($this->getToDate());
            $toDateElement->setRequired();
            $toDateElement->addValidator(new Zend_Validate_Date());
            $toDateElement->setDecorators($decorators->elementDecorators);
            
            $commissionElement = new Zend_Form_Element_Text('commission');
            $commissionElement->setLabel('Commission Amount');
            $commissionElement->setValue($this->getCommission());
            $commissionElement->setRequired();
            $commissionElement->setDecorators($decorators->elementDecorators);
         
            $boucherElement = new Zend_Form_Element_Text('Boucher');
            $boucherElement->setLabel('Boucher');           
            $boucherElement->setRequired();
            $boucherElement->setDecorators($decorators->elementDecorators);
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Pay');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($agentElement, $fromDateElement, $toDateElement, $commissionElement, $boucherElement, $submit));
	}
}
