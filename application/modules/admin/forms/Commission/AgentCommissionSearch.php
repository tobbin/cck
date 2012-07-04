<?php
/**
 * chitalwise search form for payment
 * @author Tobin
 * @since 19-01-2012
 * @package Form
 *
 */
class Admin_Form_Commission_AgentCommissionSearch extends Zend_Form
{
	
        protected $_chits;
        protected $_fromDate;
        protected $_toDate;     
        
        public function getChits()
	{
            return $this->_chits;
	}
	
	public function setChits($chits)
	{
            $this->_chits = $chits;
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
            $this->setName('agentCommissionSearch');
            $this->addAttribs(array("id"=>"agentCommissionSearch")); 
            
            $agentElement = new Zend_Form_Element_Hidden('agent');
            $agentElement->setValue($this->getAgentId());
                       
            $chitsElement = new Zend_Form_Element_Select('chits');
            $chitsElement->addMultiOption('', '--Select one--');
            foreach ($this->getChits() as $data) {
                $chitsElement->addMultiOption($data->id, $data->chit_code);
            }
            $chitsElement->setLabel('Chits')->setAttrib('class', 'styledselect');           
            $chitsElement->setRequired();
            $chitsElement->setDecorators($decorators->elementDecorators);
            
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
            
            $status =array(''=>'--Select One--','A'=>'Active', 'P'=>'Payed', 'H'=>'Hold', 'F'=>'Fine');
            $statusElement = new Zend_Form_Element_Select('status');           
            foreach ($status as $key => $value) {
                $statusElement->addMultiOption($key, $value);
            }
            $statusElement->setLabel('Status')->setAttrib('class', 'styledselect');           
            $statusElement->setRequired();
            $statusElement->setDecorators($decorators->elementDecorators);
         
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($chitsElement, $statusElement, $fromDateElement, $toDateElement, $submit));

	}
}
