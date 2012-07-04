<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 11-01-2010
 * @package Form
 *
 */
class Admin_Form_Prize_Payment extends Zend_Form
{
	protected $_request;        
        protected $_chitcode;
        protected $_installment;
        protected $_chitalname;       
	protected $_paymentdate;
        protected $_chitid;
        protected $_chitalid;      
        protected $_chitsCustomer;
        protected $_token;
        protected $_status;
        protected $_statusName;

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
                        
        public function getPaymentDate()
	{
            return $this->_paymentdate;
	}
	
	public function setPaymentDate($payment_date)
	{
            $this->_paymentdate = $payment_date;
	}
      
        public function getChitsCustomer()
	{
            return $this->_chitsCustomer;
	}
	
	public function setChitsCustomer($chitsCustomer)
	{
            $this->_chitsCustomer = $chitsCustomer;
	}
        
        public function getToken()
	{
            return $this->_token;
	}
	
	public function setToken($token)
	{
            $this->_token = $token;
	}
        
        public function getStatus()
	{
            return $this->_status;
        }
        
        public function setStatus($status)
	{
            return $this->_status = $status;
        }
        
        public function getStatusName()
	{
            return $this->_statusName;
        }
        
        public function setStatusName($statusName)
	{
            return $this->_statusName = $statusName;
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
           
            $token = new Zend_Form_Element_Text('token');
            $token->setLabel('Token')->setAttrib('class', 'inp-form');
            $token->setValue($this->getToken());           
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
                                                   
            $prized_date = new Zend_Form_Element_Text('prized_date');  
            $prized_date->setLabel('Prized date')->setAttrib('class', 'inp-form datepicker dateEdit');           
            $prized_date->setRequired();         
            $prized_date->setDecorators($decorators->elementDecorators);
            
           
            $payment_date = new Zend_Form_Element_Text('payment_date');  
            $payment_date->setLabel('Payment date')->setAttrib('class', 'inp-form datepicker dateEdit');
            $payment_date->setValue('2011-10-22');           
            $payment_date->setRequired();         
            $payment_date->setDecorators($decorators->elementDecorators);
            
            $prize_amount = new Zend_Form_Element_Text('prize_amount');
            $prize_amount->setLabel('prize amount')->setAttrib('class', 'inp-form');
            $prize_amount->setRequired();
            $prize_amount->setDecorators($decorators->elementDecorators);
            
            $chequeNoElement = new Zend_Form_Element_Text('cheque_no');
            $chequeNoElement->setLabel('Cheque Number')->setAttrib('class', 'inp-form');
            $chequeNoElement->setRequired();
            $chequeNoElement->setDecorators($decorators->elementDecorators);
            
            $boucherNoElement = new Zend_Form_Element_Text('boucher_no');
            $boucherNoElement->setLabel('Boucher Number')->setAttrib('class', 'inp-form');
            $boucherNoElement->setRequired();
            $boucherNoElement->setDecorators($decorators->elementDecorators);
            
            $statusElement = new Zend_Form_Element_Text('status_name');                        
            $statusElement->setLabel('Status')->setAttrib('class', 'styledselect');           
            $statusElement->setValue($this->getStatusName());
            $statusElement->setRequired();
            $statusElement->setDecorators($decorators->elementDecorators);
           
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);
            $this->addElements(array($chit_id, $chit_code, $installment, $token, $customer_name, $prize_type, $prize_amount, $prized_date, $payment_date, $chequeNoElement, $boucherNoElement, $statusElement, $submit));
	}
}