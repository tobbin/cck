<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 17-09-2011
 * @package Form
 *
 */
class Admin_Form_Employee_Add extends Zend_Form
{
	protected $_request;
	protected $_designation;
        protected $_users;
        
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
        
//        public function getUsers()
//	{
//            return $this->_users;
//	}
//	
//	public function setUsers($users)
//	{
//            $this->_users = $users;
//	}
        
        public function getBranch() 
        {
            return array(1=>'Head office');
        }


        function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();          
            $objUser     = new Nisanth_Model_User();
            
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formEmployee');
           
            $branchElement = new Zend_Form_Element_Select('branch');
            foreach ($this->getBranch() as $key => $branch) {
                $branchElement->addMultiOption(1, 'Head office');
            }
            $branchElement->setLabel('Branch')->setAttrib('class', 'styledselect');
            $branchElement->setDecorators($decorators->elementDecorators);            

            $designationElement = new Zend_Form_Element_Select('designation_id');
            foreach ($this->getDesignation() as  $designation) {
                $designationElement->addMultiOption($designation->id, $designation->designation);
            }
            $designationElement->setLabel('Designation')->setAttrib('class', 'styledselect');
            $designationElement->setDecorators($decorators->elementDecorators);

            $usersElement = new Zend_Form_Element_Select('user_id');
            foreach ($objUser->getAllUsers() as $user) {
                $usersElement->addMultiOption($user->id, $user->first_name .'-'. $user->last_name);
            }
            $usersElement->setLabel('Customer')->setAttrib('class', 'styledselect');
            $usersElement->setRequired();
            $usersElement->setDecorators($decorators->elementDecorators);
            
            $join_date = new Zend_Form_Element_Text('appoiment_date');
            $join_date->setLabel('Join date')->setAttrib('class', 'inp-form datepicker');
            $join_date->setRequired();
            $join_date->addValidator(new Zend_Validate_Date());
            $join_date->setDecorators($decorators->elementDecorators);
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($branchElement, $designationElement, $usersElement, $join_date, $submit));

	}
}
