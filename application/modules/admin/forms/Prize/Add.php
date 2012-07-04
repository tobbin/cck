<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 11-01-2010
 * @package Form
 *
 */
class Admin_Form_Prize_Add extends Zend_Form
{
	protected $_request;        
        protected $_chitcode;
        protected $_installment;
        protected $_chitalname;
        protected $_devident;
	protected $_paymentdate;
        protected $_chitid;
        protected $_chitalid;
        protected $_nextinstallment;
        protected $_chitsCustomer;

        public function getRequest()
	{
            return $this->_request;
	}
	
	public function setRequest(Zend_Controller_Request_Http $request)
	{
            $this->_request = $request;
	}
        
        public function getChitId()
	{
            return $this->_chitid;
	}
	
	public function setChitId($chitid)
	{
            $this->_chitid = $chitid;
	}
        
//        public function getChitalId()
//	{
//            return $this->_chitalid;
//	}
//	
//	public function setChitalId($chitalid)
//	{
//            $this->_chitalid = $chitalid;
//	}
        
        public function getChitCode()
	{
            return $this->_chitcode;
	}
	
	public function setChitCode($chitcode)
	{
            $this->_chitcode = $chitcode;
	}
	
        public function getInstallment()
	{
            return $this->_installment;
	}
	
	public function setInstallment($installment)
	{
            $this->_installment = $installment;
	}
        
        public function getChitalName()
	{
            return $this->_chitalname;
	}
	
	public function setChitalName($chitalname)
	{
            $this->_chitalname = $chitalname;
	}
        
        public function getDevident()
	{
            return $this->_devident;
	}
	
	public function setDevident($devident)
	{
            $this->_devident = $devident;
	}
        
         public function getNextInstallment()
	{
            return $this->_nextinstallment;
	}
	
	public function setNextInstallment($nextinstallment)
	{
            $this->_nextinstallment = $nextinstallment;
	}
        
        public function getPaymentDate()
	{
            return $this->_paymentdate;
	}
	
	public function setPaymentDate($paymentdate)
	{
            $this->_paymentdate = $paymentdate;
	}
        public function getChitsCustomer()
	{
            return $this->_chitsCustomer;
	}
	
	public function setChitsCustomer($chitsCustomer)
	{
            $this->_chitsCustomer = $chitsCustomer;
	}
        
	function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formPrize');
            
            $chit_id     = new Zend_Form_Element_Hidden('chit_id');
            $chit_id->setValue($this->getChitId());
            
//            $customer_id = new Zend_Form_Element_Hidden('customer_id');
//            $customer_id->setValue($this->getChitalId());
            
            $chit_code = new Zend_Form_Element_Text('chit_code');
            $chit_code->setLabel('Chit Code');
            $chit_code->setValue($this->getChitCode());
            $chit_code->setRequired()->setAttrib('class', 'inp-form');           
            $chit_code->setDecorators($decorators->elementDecorators);

            $installment = new Zend_Form_Element_Text('installment');
            $installment->setLabel('Installment')->setAttrib('class', 'inp-form');
            $installment->setValue($this->getInstallment());
            $installment->setRequired();
            $installment->setDecorators($decorators->elementDecorators);
            //echo'<pre>';print_r($this->getChitsCustomer());echo'</pre>';
           
            $token = new Zend_Form_Element_Select('customer_id');
            $token->addMultiOption('', 'Select one');
            foreach ($this->getChitsCustomer() as $data) {
                $token->addMultiOption($data->chital_id, $data->token);
            }
            $token->setLabel('Token')
                    ->setAttrib('class', 'styledselect')
                    ->setAttrib('onchange', 'getUser()');           
            $token->setRequired();
            $token->setDecorators($decorators->elementDecorators);
            
            $customer_name = new Zend_Form_Element_Text('customer_name');
            $customer_name->setLabel('Customer Name')->setAttrib('class', 'inp-form');
            $customer_name->setRequired();
            $customer_name->setDecorators($decorators->elementDecorators);
            
            $prize_type = new Zend_Form_Element_Select('prize_type');
            $prize_type->addMultiOption('A', 'Auction');
            $prize_type->addMultiOption('L', 'Lot');
            $prize_type->setLabel('Prize Type')
                    ->setAttrib('class', 'styledselect');           
            $prize_type->setRequired();
            $prize_type->setDecorators($decorators->elementDecorators);
            
            $prize_amount = new Zend_Form_Element_Text('prize_amount');
            $prize_amount->setLabel('prize amount')->setAttrib('class', 'inp-form');
            $prize_amount->setRequired();
            $prize_amount->setDecorators($decorators->elementDecorators);                      
            
            $prized_date = new Zend_Form_Element_Text('prized_date');  
            $prized_date->setLabel('Prized date')->setAttrib('class', 'inp-form datepicker');
            $prized_date->setValue( date('Y-m-d',time()));
            $prized_date->setRequired();         
            $prized_date->setDecorators($decorators->elementDecorators);
            
            $days         = $this->getPaymentDate();
            $payment_date = new Zend_Form_Element_Text('payment_date');  
            $payment_date->setLabel('Payment date')->setAttrib('class', 'inp-form datepicker');
            $payment_date->setValue(date('Y-m-d', strtotime("+$days days")));
            $payment_date->setRequired();         
            $payment_date->setDecorators($decorators->elementDecorators);
            
            $devident = new Zend_Form_Element_Text('devident');
            $devident->setLabel('Next Devident')->setAttrib('class', 'inp-form');
            $devident->setValue($this->getDevident());
            $devident->setRequired();
            $devident->setDecorators($decorators->elementDecorators);
            
            $amount = new Zend_Form_Element_Text('amount');
            $amount->setLabel('Next Installment')->setAttrib('class', 'inp-form');
            $amount->setValue($this->getNextInstallment());
            $amount->setRequired();
            $amount->setDecorators($decorators->elementDecorators);
            
            $installment_date = new Zend_Form_Element_Text('installment_date');
            $installment_date->setLabel('Next Chit Date')->setAttrib('class', 'inp-form datepicker');
            $installment_date->setValue(date('Y-m-d', strtotime("+1 months")));
            $installment_date->setRequired();
            $installment_date->setDecorators($decorators->elementDecorators);
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);
            $this->addElements(array($chit_id, $chit_code, $installment, $token, $customer_name, $prize_type, $prize_amount, $prized_date, $payment_date,$installment_date, $devident, $amount, $submit));
	}
}