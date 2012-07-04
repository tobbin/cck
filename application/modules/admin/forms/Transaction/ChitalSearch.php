<?php
/**
 * chitalwise search form for payment
 * @author Tobin
 * @since 19-01-2012
 * @package Form
 *
 */
class Admin_Form_Transaction_ChitalSearch extends Zend_Form
{
	protected $_request;
        protected $_chits;

        public function getRequest()
	{
            return $this->_request;
	}
	
	public function setRequest(Zend_Controller_Request_Http $request)
	{
            $this->_request = $request;
	}
        
        public function getChits()
	{
            return $this->_chits;
	}
	
	public function setChits($chits)
	{
            $this->_chits = $chits;
	}
        
       
        
	function init()
	{           
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('chitalSearch');
            $this->addAttribs(array("id"=>"chitalSearch")); 

            $branchElement = new Zend_Form_Element_Select('branch');
            $branchElement->addMultiOption('1', 'Head office');
            $branchElement->setLabel('Branch')->setAttrib('class', 'styledselect');
            $branchElement->setDecorators($decorators->elementDecorators);

            $designationElement = new Zend_Form_Element_Select('designation');
            $designationElement->addMultiOption('', '-- designation --');
            $designationElement->addMultiOption('7', 'Agent');
            $designationElement->addMultiOption('6', 'Collection Agent');
            $designationElement->setLabel('Designation')->setAttrib('class', 'styledselect');           
            $designationElement->setRequired();
            $designationElement->setDecorators($decorators->elementDecorators);                                   
            
            $agentElement = new Zend_Form_Element_Select('agent');
            $agentElement->addMultiOption('', '-- select designation --');
            $agentElement->setLabel('Agent')->setAttrib('class', 'styledselect');
            $agentElement->setDecorators($decorators->elementDecorators);
                        
            $chitsElement = new Zend_Form_Element_Select('chits');
            $chitsElement->addMultiOption('', '--Select one--');
            foreach ($this->getChits() as $data) {
                $chitsElement->addMultiOption($data->id, $data->chit_code);
            }
            $chitsElement->setLabel('Chits')->setAttrib('class', 'styledselect');           
            $chitsElement->setRequired();
            $chitsElement->setDecorators($decorators->elementDecorators);
         
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($branchElement, $designationElement, $agentElement, $chitsElement, $submit));

	}
}
