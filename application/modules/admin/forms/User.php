<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 11-01-2010
 * @package Form
 *
 */
class Admin_Form_User extends Zend_Form
{
	protected $_request;
        
        protected $_pincode;
	
	public function getRequest()
	{
            return $this->_request;
	}
	
	public function setRequest(Zend_Controller_Request_Http $request)
	{
            $this->_request = $request;
	}
        
        public function getPincode()
	{
            return $this->_pincode;
	}
	
	public function setPincode(Nisanth_Model_Pincode $pincode)
	{
            $this->_pincode = $pincode;
	}
	
	function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formUser');

            /*$name = new Zend_Form_Element_Text('user_name');
            $validateRegex = new Zend_Validate_Regex('/^[\w\d\_\.]{4,}$/');
            $validateRegex->setMessage('String must be valid');
            $name->addValidator($validateRegex);

            $name->setLabel('User Name');
            $name->setRequired();
            $name->setAttrib('class', 'inp-form');
            $name->setDecorators($decorators->elementDecorators);*/

            $emailID = new Zend_Form_Element_Text('email');
            $emailID->setLabel('Email Address');
            $emailID->setAttrib('class', 'inp-form');
            $emailID->addValidator(new Zend_Validate_EmailAddress());
            $emailID->setDecorators($decorators->elementDecorators);

            $first_name = new Zend_Form_Element_Text('first_name');
            $first_name->setLabel('First Name')->setAttrib('class', 'inp-form');
            $first_name->setRequired();
            $first_name->setDecorators($decorators->elementDecorators);

            $last_name = new Zend_Form_Element_Text('last_name');
            $last_name->setLabel('Last Name')->setAttrib('class', 'inp-form');
           // $last_name->setRequired();
            $last_name->setDecorators($decorators->elementDecorators);

            /*$password = null;
            if (!$this->getRequest()->getParam('id')) {
                $password = new Zend_Form_Element_Password('password');
                $password->setLabel('Password')->setAttrib('class', 'inp-form');
                $password->setRequired();
                $password->setDecorators($decorators->elementDecorators);
            }*/


            $house_name = new Zend_Form_Element_Text('house_name');
            $house_name->setLabel('House Name')->setAttrib('class', 'inp-form')->setAttrib('onblur', 'checkUser()');
            $house_name->setRequired();
            $house_name->setDecorators($decorators->elementDecorators);
            
            $dob = new Zend_Form_Element_Text('dob');
            $dob->setLabel('Dob')->setAttrib('class', 'inp-form datepicker1');
            //$dob->setRequired();
            $dob->addValidator(new Zend_Validate_Date());
            $dob->setDecorators($decorators->elementDecorators);
            
            $street = new Zend_Form_Element_Text('street');
            $street->setLabel('Street')->setAttrib('class', 'inp-form');
            //$street->setRequired();
            $street->setDecorators($decorators->elementDecorators);
            
            $landline = new Zend_Form_Element_Text('landphone');
            $landline->setLabel('Landline')->setAttrib('class', 'inp-form');
            //$landline->setRequired();
            $landline->setDecorators($decorators->elementDecorators);
            
            $mobile = new Zend_Form_Element_Text('mobile');
            $mobile->setLabel('Mobile')->setAttrib('class', 'inp-form');
            //$mobile->setRequired();
            $mobile->setDecorators($decorators->elementDecorators);
            
            $pinElement	=	new Zend_Form_Element_Select('pincode');
            foreach ($this->getPincode()->fetchAll() as $pin) {
                $pinElement->addMultiOption($pin['id'], $pin['pin']);
            }
            $pinElement->setLabel('Pincode')->setAttrib('class', 'styledselect');
            $pinElement->setDecorators($decorators->elementDecorators);
      
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($first_name, $last_name, $house_name, $dob, $street, $landline, $mobile, $emailID, $pinElement, $submit));

	}
}