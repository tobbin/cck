<?php
/**
 * chitalwise search form for payment
 * @author Tobin
 * @since 19-01-2012
 * @package Form
 *
 */
class Admin_Form_Prize_PrizeSearch extends Zend_Form
{
        protected $_chits;
        protected $_chitId;
        protected $_status;


        public function getChits()
	{
            return $this->_chits;
	}
	
	public function setChits($chits)
	{
            $this->_chits = $chits;
	}
        
        public function getChitId()
	{
            return $this->_chitId;
	}
	
	public function setChitId($chitId)
	{
            $this->_chitId = $chitId;
	}
        
        public function getStatus()
	{
            return $this->_status = array('P'=> 'Payed', 'N'=> 'No Action', 'D'=> 'Document Submitted', 'C'=> 'Cheque Issued');
	}
        
	function init()
	{           
            $class = 'dateEdit';
            $decorators = new Nisanth_View_Helper_Decorator();
            $this->setDecorators($decorators->formDecorator);
            $this->setName('prizeSearch');
            $this->addAttribs(array("id"=>"prizeSearch")); 

            $chitsElement = new Zend_Form_Element_Select('chits');
            $chitsElement->addMultiOption('', '--Select one--');
            foreach ($this->getChits() as $data) {
                $chitsElement->addMultiOption($data->id, $data->chit_code);
            }
            $chitsElement->setValue($this->getChitId());
            $chitsElement->setLabel('Chits')->setAttrib('class', 'styledselect');           
            $chitsElement->setRequired();
            $chitsElement->setDecorators($decorators->elementDecorators);
            
            $paymentStatusElement = new Zend_Form_Element_Select('paymentStatus');
            $paymentStatusElement->addMultiOption('', '--Select one--');
           // print_r($this->getStatus());exit;
            foreach ($this->getStatus() as $key => $value) {
                $paymentStatusElement->addMultiOption($key, $value);
            }            
            $paymentStatusElement->setLabel('Payment Status');                       
            $paymentStatusElement->setDecorators($decorators->elementDecorators);
            
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Submit');
            $submit->setDecorators($decorators->buttonDecorators);

            $this->addElements(array($chitsElement, $paymentStatusElement, $submit));
	}
}
