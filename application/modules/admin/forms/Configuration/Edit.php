<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 11-01-2010
 * @package Form
 *
 */
class Admin_Form_Configuration_Edit  extends Zend_Form
{
	protected $_request;        
        protected $_configuration;

        public function getRequest()
	{
            return $this->_request;
	}
	
	public function setRequest(Zend_Controller_Request_Http $request)
	{
            $this->_request = $request;
	}
        
        public function getConfiguration()
	{
            return $this->_configuration;
	}
	
	public function setConfiguration($configuration)
	{
            $this->_configuration = $configuration;
	}
        
        
	function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formConfiguration');
            
            foreach ($this->getConfiguration() as $configuration) {                
                $element = new Zend_Form_Element_Select($configuration['id']);            
                $element->addMultiOption('A', 'Active');
                $element->addMultiOption('D', 'Disable');
                $element->setLabel(ucfirst($configuration['attribute']))->setAttrib('class', 'styledselect');           
                $element->setRequired();
                $element->setDecorators($decorators->elementDecorators);
                $this->addElement($element);
            }
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);
            $this->addElement($submit);
	}
}