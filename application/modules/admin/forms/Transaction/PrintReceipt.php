<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 11-01-2010
 * @package Form
 *
 */
class Admin_Form_Transaction_PrintReceipt extends Zend_Form
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
            $this->setName('formPrintBill');
            
            $chitsElement = new Zend_Form_Element_Select('chits');
            $chitsElement->addMultiOption('', '--Select one--');
            foreach ($this->getChits() as $data) {
                $chitsElement->addMultiOption($data->id, $data->chit_code);
            }
            $chitsElement->setLabel('Chits')->setAttrib('class', 'styledselect')
                    ->setAttrib('onchange', 'getToken()');                      
            $chitsElement->setRequired();
            $chitsElement->setDecorators($decorators->elementDecorators);
            
            $tokenElement = new Zend_Form_Element_Select('customer_id');            
            $tokenElement->addMultiOption(1, 16);            
            $tokenElement->setLabel('Token')
                    ->setAttrib('class', 'styledselect')
                    ->setAttrib('onchange', 'getUser()');           
            $tokenElement->setRequired();
            $tokenElement->setDecorators($decorators->elementDecorators);
            
            $customerNameElement = new Zend_Form_Element_Text('customer_name');
            $customerNameElement->setLabel('Customer Name')->setAttrib('class', 'inp-form');           
            $customerNameElement->setDecorators($decorators->elementDecorators);            
            
            $fromInstallmentElement = new Zend_Form_Element_Text('from_installment');
            $fromInstallmentElement->setLabel('From Installment')->setAttrib('class', 'inp-form');            
            $fromInstallmentElement->setRequired();
            $fromInstallmentElement->setDecorators($decorators->elementDecorators);
            
            $toInstallmentElement = new Zend_Form_Element_Text('to_installment');
            $toInstallmentElement->setLabel('To Installment')->setAttrib('class', 'inp-form');            
            $toInstallmentElement->setRequired();
            $toInstallmentElement->setDecorators($decorators->elementDecorators);            
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);
            $this->addElements(array($chitsElement, $tokenElement, $customerNameElement, $fromInstallmentElement, $toInstallmentElement, $submit));
	}
}