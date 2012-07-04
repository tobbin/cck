<?php
/**
 * Client form
 * @author Nisanth Kumar
 * @since 17-09-2011
 * @package Form
 *
 */
class Admin_Form_Transaction_PayBill extends Zend_Form
{	
        protected $_category;
        protected $_paymentOptions;
        
        public function getCategory()
	{
            return $this->_category;
	}
	
	public function setCategory($category)
	{
            $this->_category = $category;
	}
        
        public function getPaymentOptions()
	{
            return $this->_paymentOptions;
	}
	
	public function setPaymentOptions($paymentOptions)
	{
            $this->_paymentOptions = $paymentOptions;
	}
        
	function init()
	{
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('formPayment');
            
            $category = new Zend_Form_Element_Select('category');
            $category->addMultiOption('', 'Select one');
            foreach ($this->getCategory() as $data) {
                if($data->id != 1 && $data->id != 2) 
                $category->addMultiOption($data->id, $data->name);
            }
            $category->setLabel('Payment For')
                    ->setAttrib('class', 'styledselect');           
            $category->setRequired();
            $category->setDecorators($decorators->elementDecorators);
            
            $payment_option = new Zend_Form_Element_Select('payment_option');
            $payment_option->addMultiOption('', 'Select one');
            foreach ($this->getPaymentOptions() as $optionsdata) {
                
                    $payment_option->addMultiOption($optionsdata->id, $optionsdata->name);
                
            }
            $payment_option->setLabel('Pay By')
                    ->setAttrib('class', 'styledselect');           
            $payment_option->setRequired();
            $payment_option->setDecorators($decorators->elementDecorators);
                                            
            $receipt = new Zend_Form_Element_Text('receipt_no');
            $receipt->setRequired()->setLabel("Boucher No")->setDecorators($decorators->elementDecorators)->setAttrib('class', 'inp-form');
            
            $cheque = new Zend_Form_Element_Text('cheque_no');
            $cheque->setRequired()->setLabel("Cheque No")->setDecorators($decorators->elementDecorators)->setAttrib('class', 'inp-form');
            
            $amount = new Zend_Form_Element_Text('amount');
            $amount->setRequired()->addValidator(new Zend_Validate_Int())->setLabel("Amount")->setDecorators($decorators->elementDecorators)->setAttrib('class', 'inp-form');
            
            
            $pay = new Zend_Form_Element_Submit('pay');
            $pay->setLabel('Pay')->setDecorators($decorators->buttonDecorators);
            $this->addElements(array($category, $payment_option, $receipt, $cheque, $amount, $pay));
        }
        
}
?>
