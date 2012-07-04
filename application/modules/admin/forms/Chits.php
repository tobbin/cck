<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 17-09-2011
 * @package Form
 *
 */
class Admin_Form_Chits extends Zend_Form
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
        $class = null;
        if($this->getRequest()->getParam('id')) {
            $class = " dateEdit";
        }
        $decorators = new Nisanth_View_Helper_Decorator();
        $this->setDecorators($decorators->formDecorator);
        $this->setName('formChit');

        $objChitsType = new Nisanth_Model_ChitsType();

        $typeElement = new Zend_Form_Element_Select('type_id');
        $typeElement->setLabel('Chit Type');
        foreach ($objChitsType->getAllType('A') as $type) {
            $typeElement->addMultiOption($type['id'], $type['sala'] . '-' . $type['installment']);
        }
        $typeElement->setAttrib('class', 'styledselect');
        $typeElement->setDecorators($decorators->elementDecorators);

        $chitCode = new Zend_Form_Element_Text('chit_code');
        $chitCode->setLabel('Chit Code');
        $chitCode->setRequired()->setAttrib('class', 'inp-form');
        $chitCode->setDecorators($decorators->elementDecorators);

        $chits_number = new Zend_Form_Element_Text('groups');
        $chits_number->setLabel('Number of Groups')->setAttrib('class', 'inp-form');
        $chits_number->setRequired()->addValidator(new Zend_Validate_Int());
        $chits_number->setDecorators($decorators->elementDecorators);

        $current_installment = new Zend_Form_Element_Text('current_installment');
        $current_installment->setLabel('Current installment')->setAttrib('class', 'inp-form');
        $current_installment->setRequired()->addValidator(new Zend_Validate_Int());
        $current_installment->setDecorators($decorators->elementDecorators);

        $payment_duration = new Zend_Form_Element_Text('payment_duration');
        $payment_duration->setLabel('Payment duration')->setAttrib('class', 'inp-form');
        $payment_duration->setRequired()->addValidator(new Zend_Validate_Int());
        $payment_duration->setDecorators($decorators->elementDecorators);

        $start_date = new Zend_Form_Element_Text('start_date');
        $start_date->setLabel('Start date')->setAttrib("class", "inp-form datepicker $class");
        $start_date->setRequired();
        $start_date->addValidator(new Zend_Validate_Date());
        $start_date->setDecorators($decorators->elementDecorators);

        $next_chit_date = new Zend_Form_Element_Text('next_chit_date');
        $next_chit_date->setLabel('Next chit date')->setAttrib("class", "inp-form datepicker $class");
        $next_chit_date->setRequired();
        $next_chit_date->addValidator(new Zend_Validate_Date());
        $next_chit_date->setDecorators($decorators->elementDecorators);

        $chits_date = new Zend_Form_Element_Text('chits_date');
        $chits_date->setLabel('Chits date')->setAttrib('class', 'inp-form');
        $chits_date->setRequired();
        $chits_date->addValidator(new Zend_Validate_Int());
        $chits_date->setDecorators($decorators->elementDecorators);

        $number_of_members = new Zend_Form_Element_Text('number_of_members');
        $number_of_members->setLabel('Number_of_members')->setAttrib('class', 'inp-form');
        $number_of_members->setRequired();
        $number_of_members->addValidator(new Zend_Validate_Int());
        $number_of_members->setDecorators($decorators->elementDecorators);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');
        $submit->setDecorators($decorators->buttonDecorators);

        $this->addElements(array($typeElement, $chitCode, $chits_number, $current_installment, $payment_duration, $start_date, $next_chit_date, $chits_date, $number_of_members, $submit));

	}
}