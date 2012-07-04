<?php
/**
 * bill search form
 * @author Tobin
 * @since 20-12-2011
 * @package Form
 *
 */
class Admin_Form_employee_Search extends Zend_Form
{
	protected $_request; 
        protected $_designation;       
                                    
	public function getRequest()
	{
            return $this->_request;
	}
	
	public function setRequest(Zend_Controller_Request_Http $request)
	{
            $this->_request = $request;
	}
        
        public function getDesignation()
	{
            return $this->_designation;
	}
	
	public function setDesignation($designation)
	{
            $this->_designation = $designation;
	}
        
	function init()
	{           
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('employeeSearch');
            $this->addAttribs(array("id"=>"employeeSearch"));                        
            
            $designation = new Zend_Form_Element_Select('designation');
            $designation->addMultiOption('', '--Select one--');
            foreach ($this->getDesignation() as $data) {
                $designation->addMultiOption($data->id, $data->designation);
            }
            $designation->setLabel('Designation')->setAttrib('class', 'styledselect');           
            $designation->setRequired();
            $designation->setDecorators($decorators->elementDecorators);
                        
            $status = new Zend_Form_Element_Select('status');           
            $status->addMultiOption('A','Active'); 
            $status->addMultiOption('R','Reviled');
            $status->addMultiOption('S','Suspended');
            $status->setLabel('Status')->setAttrib('class', 'styledselect');
            $status->setDecorators($decorators->elementDecorators);
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($designation, $status, $submit));
	}
}
