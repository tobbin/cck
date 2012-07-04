<?php
/**
 * chitalwise search form for payment
 * @author Tobin
 * @since 19-01-2012
 * @package Form
 *
 */
class Admin_Form_Transaction_AgentSearch extends Zend_Form
{
	protected $_request;
       
        public function getRequest()
	{
            return $this->_request;
	}
	
	public function setRequest(Zend_Controller_Request_Http $request)
	{
            $this->_request = $request;
	}
        
        
	function init()
	{           
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('agentSearch');
            $this->addAttribs(array("id"=>"agentSearch")); 

            $branchElement = new Zend_Form_Element_Select('branch');
            $branchElement->addMultiOption('1', 'Head office');
            $branchElement->setLabel('Branch')->setAttrib('class', 'styledselect');
            $branchElement->setDecorators($decorators->elementDecorators);

            $designationElement = new Zend_Form_Element_Select('designation');
            $designationElement->addMultiOption('7', 'Agent');
            $designationElement->addMultiOption('6', 'Collection Agent');
            $designationElement->setLabel('Designation')->setAttrib('class', 'styledselect');           
            $designationElement->setRequired();
            $designationElement->setDecorators($decorators->elementDecorators);                                   
                       
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($branchElement, $designationElement, $submit));
	}
}
