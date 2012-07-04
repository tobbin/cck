<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 17-09-2011
 * @package Form
 *
 */
class Admin_Form_Report_AgentWiseSearch extends Zend_Form
{	
        protected $_fromDate;
        protected $_toDate;
        protected $_agentId;
        
	
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
        
         public function getAgentId()
	{
            return $this->_agentId;
	}
	
	public function setAgentId($agentId)
	{
            $this->_agentId = $agentId;
	}
	
	function init()
	{
            $class = 'dateEdit';
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('frmAgentWise');
            
            $agentElement = new Zend_Form_Element_Hidden('agent_id');
            $agentElement->setValue($this->getAgentId());
            
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
            
            $printButtonElement = new Zend_Form_Element_Submit('button');
            $printButtonElement->setLabel('Print');
            $printButtonElement->setAttrib('onclick', 'print()');
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($agentElement, $fromDateElement, $toDateElement, $submit, $printButtonElement));
	}
}