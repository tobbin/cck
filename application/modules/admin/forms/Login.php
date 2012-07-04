<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 11-01-2010
 * @package Form
 *
 */
class Admin_Form_Login extends Zend_Form
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
	
	function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formLogin');

            $userid = new Zend_Form_Element_Text('userid');
            $userid->setLabel('User name');
            $userid->setAttrib('class', 'inp-form')->setRequired();
            $userid->setDecorators($decorators->elementDecorators);

            $password = new Zend_Form_Element_Password('password');
            $password->setLabel('Password')->setAttrib('class', 'inp-form');
            $password->setRequired();
            $password->setDecorators($decorators->elementDecorators);
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($userid, $password, $submit));

	}
}